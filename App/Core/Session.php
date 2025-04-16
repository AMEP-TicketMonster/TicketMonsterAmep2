<?php

namespace Core;

use App\Config\UrlBase;

class Session{
    /** Iniciar sessión de usuario**/
    public static function sessionStart(string $name){
        session_name(hash('sha256', $name));
        session_start(); //inicializa las variables de sessión
    }

    /** Cerrar sessión de usuario**/
    public static function closeSession(){
        session_regenerate_id(true);
        //Borramos las cookies 
        if(isset($_SERVER['HTTP_COOKIE'])){
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie){
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
        //destruyo las variables y la sessión
        session_unset();
        session_destroy();
        session_write_close();
        header("location: ".UrlBase::urlBase."");   //devolvemos a la raíz del proyecto
    }
}