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


    // Crear un nuevo usuario (con rol y contraseña encriptada)
    public function createUser($nom, $cognoms, $email, $contrasenya, $rol = 'client')
      {
          $hashedPassword = password_hash($contrasenya, PASSWORD_DEFAULT);
  
          $sql = "INSERT INTO Usuaris (nom, cognom, email, contrasenya, rol) VALUES (?, ?, ?, ?, ?)";
          $stmt = $this->pdo->prepare($sql);
          $stmt->execute([$nom, $cognoms, $email, $hashedPassword, $rol]);
  
          return $this->pdo->lastInsertId();
      }
    
       // Actualizar datos de perfil
    public function updateUser($id, $nom, $cognoms, $email)
    {
        $sql = "UPDATE Usuaris SET nom = ?, cognom = ?, email = ? WHERE idUsuari = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $cognoms, $email, $id]);
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
        return password_verify($password,$this->password);
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
    
    //Devuelve el usuario que tiene la entrada = idEntrada 
    public function getByTicketId($idEntrada)
    {
        $stmt = $this->pdo->prepare(
        "SELECT u.idUsuari, u.nom, u.cognom, u.email, u.contrasenya 
        FROM Entrades e 
        INNER JOIN Usuaris u 
        ON e.idUsuari = u.idUsuari 
        WHERE e.idEntrada = ?"
        );
        $stmt->execute([$idEntrada]);
        //cambiar a fetch
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function actualizarSaldo($idUsuari, $nouSaldo)
    {
        $sql = "UPDATE Usuaris SET saldo = ? WHERE idUsuari = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nouSaldo, $idUsuari]);
    }
}
