<?php

$app['debug'] = true;
$app['http_cache'] = true;
$app['buzz.client'] = new Buzz\Client\Curl();
$app['xhprof'] = false;
$app['monolog.level'] = Monolog\Logger::INFO;
$app['redis.config'] = false;//array('host' => $_SERVER['CACHE1_HOST'], 'port' => $_SERVER['CACHE1_PORT']);
$app['proxy'] = true;
