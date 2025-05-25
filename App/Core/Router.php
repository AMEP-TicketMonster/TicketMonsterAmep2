<?php

namespace Core;

use App\Controllers\UserController;
use App\Controllers\ConcertController;
use App\Controllers\EntradaController;
use App\Controllers\GrupMusicalController;
use App\Controllers\SalaController;
use App\Controllers\ValoracioController;
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
            'mis-reservas',
            'eliminar-reserva',
            'editar-reserva',
            'guardar-edicion',

            'dashboard',
            'logout',
            'profile',
            'delete-user',
            'edit-user',
            'compra-entrada-concert',
            'reserva-entrada-concert',
            'crea-concert',
            'saldo',
            'recargar-saldo',
            'filtroConciertos',
            'grupMusical',
            'grupos',
            'delete-grupos',
            'crea-grup',
            'crea-grup-backend',
            'details_admin',
            'eliminar-valoració',
            'guardarValoracion'
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
            'filtroConciertos' => 'concerts/concerts.php',
            'concierto'  => 'concerts/details.php',
            'salas'      => 'salas/salas.php',
            'crea-concert' => 'concerts/crearConcierto.php',
            'info'       => 'info.php',
            'grupos'    => 'grupmusical/grupmusical.php',
            'grupos_admin' => 'grupmusical/grupmusical_admin.php',
            'crea-grup' => 'grupmusical/crea_grup.php',
            'details_admin' => 'concerts/details_admin.php'
        ];



        if ($requestMethod === 'GET') {

            //excepcionalmente, esta URL
            if (str_contains($requestUri, 'delete-grupos')) {

                $id = $_GET['id'] ?? $_COOKIE['id'] ?? null;
                if ($id !== null) {
                    $controller = new GrupMusicalController();
                    $controller->baixaGrup($id);
                }
            }

            //Para el caso de las vistas
            if (array_key_exists($requestUri, $routes)) {
                if ($requestUri == 'conciertos') {
                    //Si visita esta dirección cargamos los conciertos
                    $concerts = new ConcertController();
                    $concerts->carregaConcerts();
                    $concerts->getDadesCreaConcerts();
                }
                if ($requestUri === 'concierto') {
                    $id = $_GET['id'] ?? $_COOKIE['concert_id'] ?? null;
                    if ($id !== null) {
                        setcookie('concert_id', $id, time() + 3600, "/");
                        $concertController = new ConcertController();
                        $concertController->showConcert($id);
                        if (Auth::isAdmin()) {
                            $requestUri = 'details_admin';
                        }
                    }
                }
                if ($requestUri == 'filtroConciertos') {
                    $concertFiltro = new ConcertController();
                    $concertFiltro->filtroConciertos();
                }

                if ($requestUri == 'crea-grup') {
                    if (Auth::isAdmin()) {
                        $concertDades = new ConcertController();
                        $concertDades->getDadesCreaConcerts();
                    } else {
                        $_SESSION[''];
                        header('location: /dashboard');
                    }
                }
                if ($requestUri == 'salas') {
                    $salaController = new SalaController();
                    $salaController->index(); // Cargar salas y slots
                }
                if ($requestUri === 'mis-reservas') {
                    $controller = new SalaController();
                    $controller->verMisReservas();
                    exit();
                }

                if ($requestUri === 'editar-reserva') {
                    $controller = new SalaController();
                    $controller->editarReserva();
                    exit();
                }

                if ($requestUri == 'crea-concert') {
                    if (Auth::isAdmin()) {
                        $concertDades = new ConcertController();
                        $concertDades->getDadesCreaConcerts();
                    } else {
                        $_SESSION[''];
                        header('location: /dashboard');
                    }
                }
                if ($requestUri == 'grupos') {
                    $grupMusical = new GrupMusicalController();
                    $grupMusical->mostraGrups();
                    if (Auth::isAdmin()) {
                        $requestUri = 'grupos_admin';
                    }
                }

                if ($requestUri == 'dashboard') {
                    $controller = new GrupMusicalController();
                    $controller->mostraGrups();
                    $entrades = new EntradaController();
                    $entrades->consultarEntrades();
                    $saldo = new UserController();
                    $saldo->actualitzaSaldo();
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
            if ($requestUri == 'crea-concert-backend') {
                $controller = new ConcertController();
                $controller->creaConcert();
            }
            if ($requestUri == 'crea-grup-backend') {
                $controller = new GrupMusicalController();
                $controller->altaGrup();
            }
            if ($requestUri == 'recargar-saldo') {
                $controller = new UserController();
                $controller->updateSaldo();
            }
            if ($requestUri === 'reservar-sala') {
                $controller = new SalaController();
                $controller->reservarSala();
                exit();
            }
            if ($requestUri === 'eliminar-reserva') {
                $controller = new SalaController();
                $controller->eliminarReserva();
                exit();
            }

            if ($requestUri === 'guardar-edicion') {
                $controller = new SalaController();
                $controller->guardarEdicion();
                exit();
            }



            if ($requestUri == 'eliminar-valoracio') {
                if (isset($_POST['idValoracio']) && !empty($_POST['idValoracio'])) {
                    $id = $_POST['idValoracio'];

                    $controller = new ValoracioController();
                    $controller->eliminar($id);
                } else {
                    header("Location: /concerts");
                    exit();
                }
            }

            if ($requestUri == 'guardarValoracion') {
                $controller = new ValoracioController();
                $controller->crear();
            }
        }

        return __DIR__ . '/../Views/404.php'; // Si la ruta no está en la lista, mostrar 404
    }
}
