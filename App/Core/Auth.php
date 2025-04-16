<?php

//clase que se ocupa de comprobar si el usuario está o no logueado (es como un middleware de laravel)

namespace App\Core;
#require_once __DIR__ . '/../config/url_base.php';
use App\Config\UrlBase;

class Auth
{
    /* Validar si el usuario está logeado */
    public static function accessLogin()
    {
        if (isset($_SESSION['status']) && $_SESSION['status'] === true) {
            header("location: " . UrlBase::urlBase . "/dashboard");
            exit();
        }
    }
    public static function accessDashboard()
    {
        if (isset($_SESSION['status']) && $_SESSION['status'] === true) {
            return;
        }
        // Si no está logueado, go to login
        header("Location: " . UrlBase::urlLogin);
        exit();
    }

    public static function isLogged()
    {

        return (isset($_SESSION['status']) && $_SESSION['status'] === true);
    }

    public static function checkRole(array $rolesPermitidos)
    {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $rolesPermitidos)) {
            header("Location: " . UrlBase::urlLogin . "");    //si no tiene los permisos necesarios vuelve al login
            exit();
        }
    }
}
