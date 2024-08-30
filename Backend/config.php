<?php
$envfile = __DIR__ . '/.env';

$env_vars = parse_ini_file($envfile, false,);


// Show all errors except for notices and deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);


define('DB_HOST', $env_vars['DB_HOST']);
define('DB_USER', $env_vars['DB_USER']);
define('DB_PASS', $env_vars['DB_PASS']);
define('DB_NAME', $env_vars['DB_NAME']);
define('PROJECT_NAME', 'RealHouse');


$db_options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

try {

    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $db_options);

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die;
}