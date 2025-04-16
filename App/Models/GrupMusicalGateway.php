<?php
namespace App\Models;
use App\Config\Database;

class GrupMusicalGateway
{
    private $pdo;
    private $idGrupMusical;
    private $nom;
    private $dataCreacio;
    private $descripcio;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // patrón singleton
    }

    // Crear un nuevo grupo musical
    public function createGrup($nom, $dataCreacio, $descripcio)
    {
        $sql = "INSERT INTO GrupsMusicals (nom, dataCreacio, descripcio) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([$nom, $dataCreacio, $descripcio]);
        
        return $this->pdo->lastInsertId();
    }

    // Obtener un grupo musical por su ID
    public function getByGrupId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM GrupsMusicals WHERE idGrupMusical = ?");
        $stmt->execute([$id]);
        $grup = $stmt->fetch();
        
        if ($grup) {
            $this->idGrupMusical = $grup["idGrupMusical"];
            $this->nom = $grup["nom"];
            $this->dataCreacio = $grup["dataCreacio"];
            $this->descripcio = $grup["descripcio"];
        }
        
        return $grup;
    }

    // Obtener un grupo musical por su nombre
    public function getByNom($nom)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM GrupsMusicals WHERE nom = ?");
        $stmt->execute([$nom]);
        $grup = $stmt->fetch();
        
        if ($grup) {
            $this->idGrupMusical = $grup["idGrupMusical"];
            $this->nom = $grup["nom"];
            $this->dataCreacio = $grup["dataCreacio"];
            $this->descripcio = $grup["descripcio"];
        }
        
        return $grup;
    }

    // Obtener todos los grupos musicales
    public function getAllGrups()
    {
        $sql = "SELECT * FROM GrupsMusicals ORDER BY nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Actualizar un grupo musical existente
    public function updateGrup($id, $nom, $dataCreacio, $descripcio)
    {
        try {
            $sql = "UPDATE GrupsMusicals SET nom = ?, dataCreacio = ?, descripcio = ? WHERE idGrupMusical = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nom, $dataCreacio, $descripcio, $id]);
            
            if ($stmt->rowCount() > 0) {
                // Si se ha actualizado una fila
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

    // Eliminar un grupo musical
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM GrupsMusicals WHERE idGrupMusical = ?");
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

    // Getters para acceder a las propiedades
    public function getId()
    {
        return $this->idGrupMusical;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getDataCreacio()
    {
        return $this->dataCreacio;
    }

    public function getDescripcio()
    {
        return $this->descripcio;
    }
}