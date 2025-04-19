<?php

namespace App\Controllers;

use App\Models\UserGateway;
use Core\Route;
use Core\Auth;
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
        $contrasenya = trim($_POST["password"]);

        if (empty($email) || empty($contrasenya)) {
            echo "Error: Todos los campos son obligatorios.";
            return;
        }

        // Obtener el usuario por email
        $user = $this->userGateway->getByEmail($email);

        //var_dump($this->userGateway->verifyPassword($contrasenya, $user['contrasenya']), $contrasenya, $user['contrasenya']);
        //var_dump();
        //die();

        if ($user != NULL && $this->userGateway->verifyPassword($contrasenya, $user['contrasenya'])) {
            $_SESSION['status'] = true;
            unset($user['contrasenya']);                //CUIDADO que he pasado la contraseña al frontend!!! hay que tratar los datos! Con un DTO por ejemplo!
            $_SESSION['user'] = $user;   //luego hay que recoger y tratar los datos
            header("Location: /dashboard");
            //exit();
        } else {
            $_SESSION['bad_login_data'] = true;
            header("Location: /login");
        }
    }
    public function register()
    {
        $nom = trim($_POST["nom"]);
        $cognoms = trim($_POST["cognoms"]);
        $email = trim($_POST["email"]);
        $contrasenya = $_POST["contrasenya"];
        $confirma_contrasenya = $_POST["confirma_contrasenya"];

        if (empty($nom) || empty($cognoms) || empty($email) || empty($contrasenya) || empty($confirma_contrasenya)) {
            $_SESSION['bad_registration_data'] = "Todos los campos son obligatorios.";
            header("Location: /register");
            exit;
        }
        if ($contrasenya !== $confirma_contrasenya) {
            $_SESSION['bad_registration_data'] = "Las contraseñas no coinciden.";
            header("Location: /register");
            exit;
        }


        //verifica si l'usuari ja existeix!
        $existingUser = $this->userGateway->getByEmail($email);
        if ($existingUser) {
            $_SESSION['bad_registration_data'] = "Este correo electrónico ya está registrado.";
            header("Location: /register");
            exit;
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
            $_SESSION['bad_registration_data'] = "Hubo un error al crear el usuario.";
            header("Location: /register");
            exit;
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
        //session_destroy();
        header("Location: /login");
        exit();
    }



    public function createUser($name, $email, $password)
    {
        //poner el código del insert

        if ($this->isValidEmail($email)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña
            // Simular la creación de un nuevo usuario
            echo "Nuevo usuario creado: <br>";
            echo "Nombre: " . $name . "<br>";
            echo "Correo: " . $email . "<br>";
            // En un entorno real, deberías guardar estos datos en una base de datos.
        } else {
            echo "Correo electrónico no válido.";
        }
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

    // Función auxiliar para obtener un usuario por su ID (simulada con un array)
    private function getUserById($id)
    {
        // Simulación de una base de datos de usuarios
        $users = [
            1 => ['id' => 1, 'name' => 'Juan Pérez', 'email' => 'juan@ejemplo.com'],
            2 => ['id' => 2, 'name' => 'Ana García', 'email' => 'ana@ejemplo.com']
        ];

        return isset($users[$id]) ? $users[$id] : null;
    }

    // Función para validar el correo electrónico
    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
