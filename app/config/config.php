<?php
/**
 * Define Constant to framework
 **/
define('URL_PUBLIC_FOLDER', 'public');
define('URL_PROTOCOL', 'http://');
define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
/**
 * URL AND LINK http
 **/
define('URL', URL_PROTOCOL . URL_DOMAIN);
define('REQUESTURL', $_SERVER['REQUEST_URI']);
define('LINK', URL_PROTOCOL . URL_DOMAIN . REQUESTURL);
/**
 * Path file on server and ip
 **/
define('PARTH_FILES', $_SERVER['DOCUMENT_ROOT']);
define('MYIP', $_SERVER['REMOTE_ADDR']);
define('OWNER', 'REIMI');
define('YEARS', '2016');
define('COOKIE', 'reimireal');
/**
 * Define mantanane
 **/
define('MTN', '0');
/**
 * Allow display error
 **/
define('DEBUG', true);

error_reporting(E_ALL);

if (DEBUG) {
    ini_set('display_errors', 'On');
} else {
    ini_set('display_errors', 'Off');
}
?>

