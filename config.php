<?php

// Debug
if (isset($_ENV['PROD']))
    $app['debug'] = false;
else
    $app['debug'] = true;

// HTTP Cache
$app['http_cache'] = true;

// Buzz client, null uses Buzz\Client\FileGetContents
$app['buzz.client'] = new Buzz\Client\Curl();
$app['buzz.client']->setIgnoreErrors(false);
$app['buzz.client']->setTimeout(3);

// Log level
$app['monolog.level'] = Monolog\Logger::ERROR;

// XHProf for profiling
$app['xhprof'] = false;


if (isset($_ENV['PROD'])){
     //Redis for statistics
    $app['redis.config'] = array(
        'host' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST),
        'port' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT),
        'password' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS),
    );
    $app['mongo.config'] = $_ENV['MONGOSOUP_URL'];
}
else {
    $app['redis.config'] = array('host' => 'localhost', 'port' => 6379);
    $app['mongo.config'] = 'mongodb://localhost:27017/test';
}

// if hosted behind a reverse proxy
$app['proxy'] = false;
