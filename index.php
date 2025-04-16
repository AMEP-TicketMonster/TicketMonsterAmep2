<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

#require_once __DIR__ . '/App/Config/Autoload.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/Core/Router.php';
require_once __DIR__ . '/App/Core/Session.php';

//cargar variables privadas
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
//test de un archivo .env
//echo $_ENV['APP_NAME'];
//importante: no iniciaba la sesión antes que el enrutador y no funcionaba el traspaso de datos Controlador->vista
use Core\Session;
Session::sessionStart("ticketmonster_session");

use Core\Route;
$view = Route::route();

// Incluir la plantilla base que cargará la vista dinámica
include __DIR__ . '/App/Views/base.php';



//$router = require_once __DIR__ . '/routes/web.php';

// Resolver la petición
//$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

