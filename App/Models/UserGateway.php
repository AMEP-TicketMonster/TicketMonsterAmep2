<?php

namespace App\Models;

use App\Config\Database;
#require_once __DIR__ . "/../../config/config.php"; // config.php para usar db


class UserGateway
{
    private $pdo;
    private $id;
    private $email;
    private $password;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // patrón singleton
    }

    // Cargar un usuario x email
    public function getByEmail($email)
    {

        $stmt = $this->pdo->prepare("SELECT * FROM Usuaris WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $this->id = $user["idUsuari"];
            $this->email = $user["email"];
            $this->password = $user["contrasenya"];
        }
        return $user;
    }

    public function createUser($nom, $cognoms, $email, $contrasenya)
    {
        $sql = "INSERT INTO Usuaris (nom, cognom, email, contrasenya) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
    
        $stmt->execute([$nom, $cognoms, $email, $contrasenya]);
    
        return $this->pdo->lastInsertId();
    }
    


    public function getByUserId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Usuaris WHERE idUsuari = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            $this->id = $user["idUsuari"];
            $this->email = $user["email"];
            $this->password = $user["contrasenya"];
        }
        return $user;
    }

    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Usuaris WHERE idUsuari = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                // Si se ha borrado una fila
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            //Habría que guardar el log con la clase errorLog
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Verificar la contraseña
    public function verifyPassword($password)
    {
        //echo"<br>esto es verifyPassword";
        //return password_verify($password, $this->password);
        return ($password == $this->password);
    }

    // Obtener datos del usuario autenticado
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
