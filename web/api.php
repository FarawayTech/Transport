<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;

use Transport\DB;
use Transport\Entity\Location\Station;
use Transport\Entity\Location\LocationQuery;
use Transport\Entity\Location\NearbyQuery;
use Transport\Web\ConnectionQueryParser;
use Transport\Web\LocationQueryParser;
use Transport\Entity\Schedule\StationBoardQuery;
use Transport\Entity\Schedule\RouteQuery;
use Transport\Normalizer\FieldsNormalizer;

date_default_timezone_set('Europe/Zurich');

ErrorHandler::register();

// init
$app = new Silex\Application();

// default config
$app['debug'] = true;
$app['http_cache'] = false;
$app['buzz.client'] = null;
$app['monolog.level'] = Monolog\Logger::ERROR;
$app['xhprof'] = false;
$app['redis.config'] = false;
$app['proxy'] = false;

/// load config
$config = __DIR__.'/../config.php';
if (stream_resolve_include_path($config)) {
	include $config;
}

// HTTP cache
if ($app['http_cache']) {
	$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
	    'http_cache.cache_dir' => __DIR__.'/../var/cache/',
	    'http_cache.options' => array('debug' => $app['debug']),
	));
}

// Monolog
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/transport.log',
    'monolog.level' => $app['monolog.level'],
    'monolog.name' => 'transport',
));
$app->before(function (Request $request) use ($app) {
    $app['monolog']->addInfo('- ' . $request->getClientIp() . ' ' . $request->headers->get('referer') . ' ' . $request->server->get('HTTP_USER_AGENT'));
    // if hosted behind a reverse proxy
    if ($app['proxy']) {
        Request::setTrustedProxies(array($request->server->get('REMOTE_ADDR')));
    }
});

$app->before(function (Request $request) use ($app) {
    // get correct provider
    $app['provider'] = \Transport\Providers\Provider::getProvider($request->get('country'), $request->get('area'), $request->get('locality'));

    // create Transport API
    $app['api'] = new Transport\API($app['provider'], new Buzz\Browser($app['buzz.client']));
});

// XHProf
if ($app['xhprof']) {
    xhprof_enable();
}

// allow cross-domain requests, enable cache
$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Cache-Control', 's-maxage=30, public');
});

// Serializer
$app['serializer'] = $app->share(function () use ($app) {
    $fields = $app['request']->get('fields') ?: array();
    return new Serializer(array(new FieldsNormalizer($fields)), array('json' => new JsonEncoder()));
});


// statistics
$redis = null;
try {
    if ($app['redis.config']) {
        $redis = new Predis\Client($app['redis.config']);
        $redis->connect();
    }
} catch (Exception $e) {
    $app['monolog']->addError($e->getMessage());
    $redis = null;
}
$app['stats'] = new Transport\Statistics($redis);
$app->after(function (Request $request, Response $response) use ($app) {
    $app['stats']->call();
    $app['stats']->resource($request->getPathInfo());
});


// index
$app->get('/', function(Request $request) use ($app) {
    return file_get_contents('index.html');
});


// home
$app->get('/v1/', function(Request $request) use ($app) {

    return $app->json(array(
        'date' => date('c'),
        'author' => 'Opendata.ch',
        'version' => '1.0',
    ));
});


// locations
$app->get('/v1/locations', function(Request $request) use ($app) {

    $stations = array();

    $lat = $request->get('x') ?: null;
    $lon = $request->get('y') ?: null;
    $limit = $request->get('limit') ?: 5;
    $query = trim($request->get('query'));

    if ($query) {
        if ($app['provider']->isNearByLocal()) {
            // query mongo with nearest stop search
            $stations = DB::findNearbyLocationsQuery($query, $lon, $lat, $limit);
        }
        else{
            $query = new LocationQuery($query, $request->get('type'));
            $stations = $app['api']->findLocations($query);
        }
    }
    else if ($lat && $lon) {
        if ($app['provider']->isNearByLocal())
        {
            $stations = DB::findNearbyLocations($lon, $lat, $limit);
        }
        else {
            $query = new NearbyQuery($lat, $lon, $limit);
            $stations = $app['api']->findNearbyLocations($query);
        }
    }

    // add sms ticketing information
    DB::populateSMSTicketing($stations);

    $result = array('stations' => $stations);

    $json = $app['serializer']->serialize((object) $result, 'json');
    return new Response($json, 200, array('Content-Type' => 'application/json'));
});


// connections
$app->get('/v1/connections', function(Request $request) use ($app) {

    $query = LocationQueryParser::create($request);

    // get stations
    $stations = $app['api']->findLocations($query);

    // get connections
    $connections = array();
    $from = reset($stations['from']) ?: null;
    $to = reset($stations['to']) ?: null;
    $via = array();
    foreach ($stations as $k => $v) {
        if (preg_match("/^via[0-9]+$/", $k) && $v) {
            $via[] = reset($v);
        }
    }

    if ($from && $to) {
        $app['stats']->station($from);
        $app['stats']->station($to);

        $query = ConnectionQueryParser::create($request, $from, $to, $via);

        $errors = ConnectionQueryParser::validate($query);
        if ($errors) {
            return $app->json(array('errors' => $errors), 400);
        }

        $connections = $app['api']->findConnections($query);
    }

    $result = array(
        'connections' => $connections,
        'from' => $from,
        'to' => $to,
        'stations' => $stations,
    );

    $json = $app['serializer']->serialize((object) $result, 'json');
    return new Response($json, 200, array('Content-Type' => 'application/json'));
});


// station board
$app->get('/v1/stationboard', function(Request $request) use ($app) {

    $stationboard = array();

    $limit = $request->get('limit', 40);
    if ($limit > 420) {
        return new Response('Invalid value for Parameter `limit`.', 400);
    }

    $date = $request->get('date');
    if (!$date) {
        $date = $request->get('datetime');
    }
    if ($date) {
        $date = new DateTime($date, new DateTimeZone('Europe/Zurich'));
    }

    $transportations = $request->get('transportations');
    $id = $request->get('id');

    if ($app['provider']->isNearByLocal()) {
        $station = DB::getStation($id);
    }
    else {
        $query = new LocationQuery($id);
        $stations = $app['api']->findLocations($query);
        $station = reset($stations);
    }

    if ($station instanceof Station) {
        $query = new StationBoardQuery($station, $date);
        if ($transportations) {
            $query->transportations = $transportations;
        }
        $query->maxJourneys = $limit;
        $stationboard = $app['api']->getStationBoard($query, $station);
    }

    $result = array('station' => $station, 'stationboard' => $stationboard);

    $app['stats']->stationboardNumbers($stationboard);

    $json = $app['serializer']->serialize((object) $result, 'json');
    return new Response($json, 200, array('Content-Type' => 'application/json'));
});

// route request
$app->get('/v1/route', function (Request $request) use ($app) {
    $route = array();

    $jhandle = $request->get('jhandle');
    $date = $request->get('date');
    if (!$date) {
        $date = $request->get('datetime');
    }
    if ($date) {
        $date = new DateTime($date, new DateTimeZone('Europe/Zurich'));
    }

    $station = $request->get('station') ? : $request->get('id');

    $query = new LocationQuery($station);
    $stations = $app['api']->findLocations($query);
    $station = reset($stations);

    if ($station instanceof Station) {
        $app['stats']->station($station);

        $query = new RouteQuery($station, $jhandle, $date);
        $route = $app['api']->getRoute($query);
    }

    $result = array('station' => $station, 'passList' => $route->passList);

    $json = $app['serializer']->serialize((object)$result, 'json');
    return new Response($json, 200, array('Content-Type' => 'application/json'));
});


// Training data
$app->get('/v1/training', function(Request $request) use ($app) {
    return new Response("OK");
});

// run
if ($app['http_cache']) {
    $app['http_cache']->run();
} else {
	$app->run();
}

// save XHProf run
if ($app['xhprof']) {

    $data = xhprof_disable();

    include_once __DIR__.'/../vendor/facebook/xhprof/xhprof_lib/utils/xhprof_lib.php';
    include_once __DIR__.'/../vendor/facebook/xhprof/xhprof_lib/utils/xhprof_runs.php';

    $xhprof = new XHProfRuns_Default(__DIR__.'/../var/xhprof');
    $run_id = $xhprof->save_run($data, 'transport');
}
