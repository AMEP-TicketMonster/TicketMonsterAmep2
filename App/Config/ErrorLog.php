<?php

namespace Config;

date_default_timezone_set("Europe/Paris");

class ErrorLog{
    public static function activateErrorLog(){
        error_reporting(E_ALL);
        ini_set('ignore_repeated_errors', TRUE);
        ini_set('display_errors', FALSE);
        ini_set('log_errors', TRUE);
        ini_set('error_log', dirname(__DIR__). '/Logs/php-error.log');  //esto es para guardar los errores aquí!
    }
}