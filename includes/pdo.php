<?php
/*******************************************************************************
    pdo.php

    PDO connection helper for MySQL.
    Reuses the database constants defined for the existing application.

*******************************************************************************/

if (!defined('BASE_URL')) {
    define('BASE_URL', $_SERVER['DOCUMENT_ROOT'] . '/');
}

if (!defined('DEPLOYMENT_UNKNOWN')) {
    define('DEPLOYMENT_UNKNOWN', 0);
}

if (!defined('DEPLOYMENT_PRODUCTION')) {
    define('DEPLOYMENT_PRODUCTION', 1);
}

if (!defined('DEPLOYMENT_TEST')) {
    define('DEPLOYMENT_TEST', 2);
}

if (!defined('DEPLOYMENT_LOCAL')) {
    define('DEPLOYMENT_LOCAL', 3);
}

if (!defined('DATABASE_HOST') || !defined('DATABASE_USER') || !defined('DATABASE_PASSWORD') || !defined('PRIMARY_DATABASE')) {
    require_once(BASE_URL . 'includes/database.php');
}

$dsn = 'mysql:host=' . DATABASE_HOST . ';dbname=' . PRIMARY_DATABASE . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, DATABASE_USER, DATABASE_PASSWORD, $options);

// END OF FILE /////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
