<?php

namespace App\Controllers;

use App\Models\UserGateway;
use Core\Route;
use App\Core\Auth;
use Core\Session;

class UserController
{
    private $userGateway;

    public function __construct()
    {
        $this->userGateway = new UserGateway();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function login()
    {
        $email = trim($_POST["email"]);
        $contrasenya = $_POST["password"];
        $badLogin = false;

        if (empty($email) || empty($contrasenya)) {
            //Esto no haría falta, ya que los campos en el frontend son required, pero por si llega el input de algun otro tipo de forma volvemos a mandar a login...
            $badLogin = true;
        }

        // Obtener el usuario por email
        if (!$badLogin) $user = $this->userGateway->getByEmail($email);

        // var_dump($this->userGateway->verifyPassword($contrasenya, $user['contrasenya']), $contrasenya, $user['contrasenya']);
        //var_dump();
        //die();

        if (!$badLogin && $user != NULL && $this->userGateway->verifyPassword($contrasenya, $user['contrasenya'])) {
            $_SESSION['status'] = true;
            unset($user['contrasenya']);                //CUIDADO que he pasado la contraseña al frontend!!! hay que tratar los datos! Con un DTO por ejemplo!
            $_SESSION['user'] = $user;   //luego hay que recoger y tratar los datos
            $_SESSION['role'] = $user['idRol'];
            header("Location: /dashboard");
            //exit();
        } else {

            $_SESSION['bad_login_data'] = true;
            header("Location: /login");
        }
    }

    private function redirectWithError($mensaje)
    {
        $_SESSION['bad_registration_data'] = $mensaje;
        header("Location: /register");
        exit;
    }

    public function validarCamposRegistro($nom, $cognoms, $email, $contrasenya, $confirma_contrasenya)
    {
        if (empty($nom) || empty($cognoms) || empty($email) || empty($contrasenya) || empty($confirma_contrasenya)) {
            return "Todos los campos son obligatorios.";
        }

        if (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $nom)) {
            return "El nombre contiene caracteres no permitidos.";
        }

        if (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $cognoms)) {
            return "Los apellidos contienen caracteres no permitidos.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El correo electrónico no es válido.";
        }

        if (strlen($contrasenya) < 6) {
            return "La contraseña debe tener al menos 6 caracteres.";
        }

        if ($contrasenya !== $confirma_contrasenya) {
            return "Las contraseñas no coinciden.";
        }

        return null; // Todo OK
    }

    public function register()
    {
        $nom = trim($_POST["nom"]);
        $cognoms = trim($_POST["cognoms"]);
        $email = trim($_POST["email"]);
        $contrasenya = $_POST["contrasenya"];
        $confirma_contrasenya = $_POST["confirma_contrasenya"];

        $error = $this->validarCamposRegistro($nom, $cognoms, $email, $contrasenya, $confirma_contrasenya);
        if ($error) {
            $this->redirectWithError($error);
        }


        //verifica si l'usuari ja existeix!
        $existingUser = $this->userGateway->getByEmail($email);
        if ($existingUser) {
            $this->redirectWithError("Este correo electrónico ya está registrado.");
        }

        //en el login hay que ajustarlo si hacemos esto:
        //$hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);

        //inserta a la bd:
        $userId = $this->userGateway->createUser($nom, $cognoms, $email, $contrasenya);

        if ($userId) {
            // Redirigir al usuario a la página de login
            $_SESSION['status'] = true;
            //unset($userId['contrasenya']);   
            $_SESSION['user'] = $this->userGateway->getByUserId($userId);  // Obtener los datos del usuario registrado
            header("Location: /dashboard");
            exit;
        } else {
            $this->redirectWithError("Hubo un error al crear el usuario.");
        }
    }

    public function updateProfile()
    {
        // Comprobar si el usuario está logueado
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user']['idUsuari'];
        $nom = trim($_POST['nom'] ?? '');
        $cognoms = trim($_POST['cognoms'] ?? '');
        $email = trim($_POST['email'] ?? '');
        // Validaciones simples
        if (empty($nom) || empty($cognoms) || empty($email)) {
            $_SESSION['bad_update_data'] = "Tots els camps són obligatoris.";

            exit;
        }

        if (!$this->isValidEmail($email)) {
            $_SESSION['bad_update_data'] = "Correu electrònic no vàlid.";
            exit;
        }

        // Comprobar si el nuevo email está usado por otro usuario
        $existingUser = $this->userGateway->getByEmail($email);
        if ($existingUser && $existingUser['idUsuari'] != $userId) {
            $_SESSION['bad_update_data'] = "Aquest correu ja està en ús per un altre compte.";
            header("Location: /profile");
            exit;
        }

        // Actualitzar les dades
        $success = $this->userGateway->updateUser($userId, $nom, $cognoms, $email);

        if ($success) {
            $_SESSION['user'] = $this->userGateway->getByUserId($userId); // refrescar dades
            $_SESSION['profile_updated'] = true;
        } else {
            $_SESSION['bad_update_data'] = "Error en actualitzar les dades.";
        }

        header("Location: /profile");
        exit;
    }

    public function logout()
    {
        Session::closeSession();
        header("Location: /login");
        exit();
    }


    // Eliminar un usuario por ID
    public function delete($id)
    {
        //antes de hacer la eliminación habrá que revisar a cuantas tablas afecta i ver si se puede hacer o afectaría a la lógica del programa
        $user = $this->userGateway->getByUserId($id);
        if ($user) {
            $this->userGateway->delete($id);
        }
        //borrar la sesión para que no se pueda acceder a las rutas.
        Session::closeSession();
    }

    public function updateSaldo()
    {
        $nuevoSaldo = (float) $_POST["cantidad"];

        if (!is_float($nuevoSaldo)) {
            $_SESSION['errorSaldo'] = json_encode(["error" => "Datos incorrectos en el formato de los datos"]);
            header("Location: /saldo");
            exit;
        }

        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user']['idUsuari'];

        $saldoAnticArray = $this->userGateway->getSaldoByIdUsuari($userId);
        $saldoAntic = (float) $saldoAnticArray ?? 0;

        $saldoTotal = $saldoAntic + $nuevoSaldo;
        if ($saldoTotal > 0 && $saldoTotal < 99999999.99) {
            $this->userGateway->actualizarSaldo($userId, $saldoTotal);
            $_SESSION['user']['saldo'] = (float)$saldoTotal;
        }
        header("Location: /saldo");
        exit;
    }

    public function actualitzaSaldo()
    {
        $userId = $_SESSION['user']['idUsuari'];
        $saldo = $this->userGateway->getSaldoByIdUsuari($userId);
        $_SESSION['user']['saldo'] = (float)$saldo;
    }
    // Función para validar el correo electrónico
    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }


    public function getUsers()
    {
        $users = $this->userGateway->getUsers();
        $miId = $_SESSION['user']['idUsuari'];

        $usuariosFiltrados = array_filter($users, function ($usuario) use ($miId) {
            return $usuario['idUsuari'] != $miId;
        });

        $usuariosFiltrados = array_values($usuariosFiltrados);

        $_SESSION["usuaris"] = json_encode($usuariosFiltrados, JSON_UNESCAPED_UNICODE);
    }

    public function actualitzaRole()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuarios'])) {
            $usuarios = $_POST['usuarios'];
            foreach ($usuarios as $usuario) {
                $id = intval($usuario['idUsuari']);
                $rol = intval($usuario['idRol']);
                $this->userGateway->actualitzaRoles($id, $rol);
            }
        }
        header("location: /edita-roles");
    }
}
