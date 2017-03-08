<?php
$app['log.level'] = Monolog\Logger::ERROR;
$app['api.version'] = "v1";
$app['api.endpoint'] = "/api";

/**
 * SQLite database file
 */
$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path' => realpath(ROOT_PATH . '/app.db'),
);

/**
 * MySQL
 */
//$app['db.options'] = array(
//  "driver" => "pdo_mysql",
//  "user" => "root",
//  "password" => "root",
//  "dbname" => "prod_db",
//  "host" => "prod_host",
//);

$app['security.jwt'] = [
    'secret_key' => 'secret',
    'life_time'  => 86400,
    'options'    => [
        'username_claim' => 'name', // default name, option specifying claim containing username
        'header_name' => 'X-Access-Token', // default null, option for usage normal oauth2 header
        'token_prefix' => 'Bearer',
    ]
];
