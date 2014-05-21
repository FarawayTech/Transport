<?php

// Debug
$app['debug'] = false;

// HTTP Cache
$app['http_cache'] = true;

// Buzz client, null uses Buzz\Client\FileGetContents
$app['buzz.client'] = new Buzz\Client\Curl();

// Log level
$app['monolog.level'] = Monolog\Logger::ERROR;

// XHProf for profiling
$app['xhprof'] = false;

// Redis for statistics
$app['redis.config'] = array(
    'host' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST),
    'port' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT),
    'password' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS),
);

// if hosted behind a reverse proxy
$app['proxy'] = false;
