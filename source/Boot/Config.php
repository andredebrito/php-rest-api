<?php
/**
 * DEFAULT TIMEZONE
 */
date_default_timezone_set("America/Sao_Paulo");

/**
 * DATABASE
 */
define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "db_drinks",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

/**
 * PROJECT ROOT
 */
define("ROOT", "http://localhost/api-php");

/**
 * TOKEN EXPIRATION
 */
define("TOKEN_EXPIRATION_HOURS", 2);

/**
 * RESULTES PER PAGE LIMIT
 */
define("RESULTS_LIMIT", 10);

/**
 * PASSWORD
 */
define("CONF_PASSWD_MIN_LEN", 8);
define("CONF_PASSWD_MAX_LEN", 40);
define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
define("CONF_PASSWD_OPTION", ["cost" => 10]);

/**
 * MESSAGE
 */
define("CONF_MESSAGE_CLASS", "alert");
define("CONF_MESSAGE_INFO", "alert-info");
define("CONF_MESSAGE_SUCCESS", "alert-success");
define("CONF_MESSAGE_WARNING", "alert-warning");
define("CONF_MESSAGE_ERROR", "alert-danger");
define("CONF_MESSAGE_ICONS", [
    "icon-success" => "<i class='fas fa-check-circle'></i>",
    "icon-info" => "<i class='fas fa-info-circle'></i>",
    "icon-warning" => "<i class='fas fa-exclamation-circle'></i>",
    "icon-error" => "<i class='fas fa-exclamation-triangle'></i>"
]);

