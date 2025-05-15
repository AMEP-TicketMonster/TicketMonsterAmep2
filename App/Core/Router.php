<?php

namespace Core;

use App\Controllers\UserController;
use App\Controllers\ConcertController;
use App\Controllers\EntradaController;
use App\Core\Auth;

class Route
{
    public static function route()
    {
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        //var_dump($requestUri); die();
        $requestMethod = $_SERVER['REQUEST_METHOD'];  // Obtener el método HTTP (GET, POST, etc.)


        //esto hay que recolocarlo bien, y solo es para hacer test de forma puntual
        if ($requestUri == 'test') {
            $controller = new ConcertController();
            $controller->pruebas();
            exit();
        }

        $publicRoutes = [
            '',
            'login',
            'register'
        ];

        $privateRoutes = [
            'conciertos',
            'concierto',
            'salas',
            'dashboard',
            'logout',
            'profile',
            'delete-user',
            'edit-user',
            'compra-entrada-concert',
            'reserva-entrada-concert',
            'crea-concert',
            'saldo',
            'recargar-saldo'
        ];


        if (!isset($_SESSION['status']) && in_array($requestUri, $privateRoutes)) {
            Auth::accessDashboard(); // Redirige al login si no está logueado
            // exit();
        }

        if (isset($_SESSION['status']) && in_array($requestUri, $publicRoutes)) {
            Auth::accessLogin(); // Redirige si ya está logueado
            exit();
        }



        // Rutas válidas y sus archivos de vista
        $routes = [
            ''           => 'home.php',     // Página de llegada
            'login'      => 'users/login.php',
            'register'   => 'users/register.php',
            'dashboard'  => 'users/dashboard.php',
            'profile'    => 'users/perfil.php',
            'saldo'      => 'users/saldo.php',
            'conciertos' => 'concerts/concerts.php',
            'concierto'  => 'concerts/details.php',
            'salas'      => 'salas.php',
            'crea-concert' => 'concerts/crearConcierto.php'
        ];



        if ($requestMethod === 'GET') {

            //Para el caso de las vistas
            if (array_key_exists($requestUri, $routes)) {
                if ($requestUri == 'conciertos') {
                    //Si visita esta dirección cargamos los conciertos
                    $concerts = new ConcertController();
                    $concerts->carregaConcerts();
                }
                if ($requestUri === 'concierto') {
                    $id = $_GET['id'] ?? $_COOKIE['concert_id'] ?? null;
                    if ($id !== null) {
                        setcookie('concert_id', $id, time() + 3600, "/");
                        $concertController = new ConcertController();
                        $concertController->showConcert($id);
                    }
                }






                $file = __DIR__ . "/../Views/" . $routes[$requestUri];

                if (is_readable($file)) {
                    return $file;
                } else {
                    return __DIR__ . '/../Views/404.php'; // Si el archivo no existe, mostrar 404
                }
            }

            if ($requestUri == 'logout') {
                $controller = new UserController();
                $controller->logout();
                exit();
            }
        } else if ($requestMethod === 'POST') {
            if ($requestUri === 'login') {
                $controller = new UserController();

                if ($controller->login()) {
                    //cargar entradas para mostrar en el dashboard
                    $entrades = new EntradaController();
                    $entrades->consultarEntradesAssaig();
                }
            }

            if ($requestUri === 'register') {
                $controller = new UserController();
                $controller->register();
                exit();
            }

            if ($requestUri == 'delete-user') {
                if (isset($_POST['idUsuari']) && !empty($_POST['idUsuari'])) {
                    $id = $_POST['idUsuari'];
                    $controller = new UserController();
                    $controller->delete($id);
                } else {
                    header("Location: /login");
                    exit();
                }
            }
            if ($requestUri == 'edit-user') {
                $controller = new UserController();
                $controller->updateProfile();
                exit();
            }
            if ($requestUri == 'compra-entrada-concert') {
                $controller = new EntradaController();
                $controller->comprarEntradaConcert();
            }
            if ($requestUri == 'reserva-entrada-concert') {
                $controller = new EntradaController();
                $controller->reservarEntradaConcert();
                exit();
            }
            if ($requestUri == 'cancelar-reserva') {
                $controller = new EntradaController();
                $controller->cancelarReserva();
                exit();
            }
            if($requestUri == 'crea-concert-backend'){
                $controller = new ConcertController();
                $controller->creaConcert();
            }
            if($requestUri == 'recargar-saldo'){
                $controller = new UserController();
                //to do...
                echo "to do... (queda por crear función de actualizar saldo y recoger con \$Post...)";
                die();
                //$controller->
            }
            
            
        }

        return __DIR__ . '/../Views/404.php'; // Si la ruta no está en la lista, mostrar 404
    }
}