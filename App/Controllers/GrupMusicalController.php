<?php

namespace App\Controllers;

use App\Models\GrupMusicalGateway;
use Core\Session;

class GrupMusicalController
{
    private $grupMusicalGateway;

    public function __construct()
    {
        $this->grupMusicalGateway = new GrupMusicalGateway();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    /**
     * Método para dar de alta un nuevo grupo musical
     */
    public function altaGrup()
    {
        $nom = trim($_POST["nom"]);
        $dataCreacio = trim($_POST["dataCreacio"]);
        $descripcio = trim($_POST["descripcio"]);

        // Validar que los campos requeridos no estén vacíos
        if (empty($nom) || empty($dataCreacio)) {
            $_SESSION['error_grup'] = "El nombre y la fecha de creación son obligatorios.";
            header("Location: /grup/nou");
            exit;
        }

        // Verificar si el grupo ya existe
        $existingGrup = $this->grupMusicalGateway->getByNom($nom);
        if ($existingGrup) {
            $_SESSION['error_grup'] = "Este grupo musical ya está registrado.";
            header("Location: /grup/nou");
            exit;
        }

        // Insertar en la base de datos
        $grupId = $this->grupMusicalGateway->createGrup($nom, $dataCreacio, $descripcio);

        if ($grupId) {
            $_SESSION['success_grup'] = "Grupo musical creado correctamente.";
            header("Location: /grup/detalle/" . $grupId);
            exit;
        } else {
            $_SESSION['error_grup'] = "Hubo un error al crear el grupo musical.";
            header("Location: /grup/nou");
            exit;
        }
    }

    /**
     * Método para consultar un grupo musical por su ID
     */
    public function consultaGrup($id = null)
    {
        if (!$id && isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        if (!$id) {
            $_SESSION['error_grup'] = "ID de grupo no especificado.";
            header("Location: /grups");
            exit;
        }

        $grup = $this->grupMusicalGateway->getByGrupId($id);
        
        if ($grup) {
            // Aquí se pueden cargar datos adicionales del grupo si es necesario
            return $grup;
        } else {
            $_SESSION['error_grup'] = "Grupo musical no encontrado.";
            header("Location: /grups");
            exit;
        }
    }

    /**
     * Método para modificar un grupo musical existente
     */
    public function modificaGrup()
    {
        $idGrupMusical = trim($_POST["idGrupMusical"]);
        $nom = trim($_POST["nom"]);
        $dataCreacio = trim($_POST["dataCreacio"]);
        $descripcio = trim($_POST["descripcio"]);

        // Validar que los campos requeridos no estén vacíos
        if (empty($idGrupMusical) || empty($nom) || empty($dataCreacio)) {
            $_SESSION['error_grup'] = "El ID, nombre y fecha de creación son obligatorios.";
            header("Location: /grup/editar/" . $idGrupMusical);
            exit;
        }

        // Verificar que el grupo existe antes de modificarlo
        $existingGrup = $this->grupMusicalGateway->getByGrupId($idGrupMusical);
        if (!$existingGrup) {
            $_SESSION['error_grup'] = "El grupo musical no existe.";
            header("Location: /grups");
            exit;
        }

        // Actualizar en la base de datos
        $result = $this->grupMusicalGateway->updateGrup($idGrupMusical, $nom, $dataCreacio, $descripcio);

        if ($result) {
            $_SESSION['success_grup'] = "Grupo musical actualizado correctamente.";
            header("Location: /grup/detalle/" . $idGrupMusical);
            exit;
        } else {
            $_SESSION['error_grup'] = "Hubo un error al actualizar el grupo musical.";
            header("Location: /grup/editar/" . $idGrupMusical);
            exit;
        }
    }

    /**
     * Método para dar de baja un grupo musical
     */
    public function baixaGrup($id = null)
    {
        if (!$id && isset($_POST['id'])) {
            $id = $_POST['id'];
        }

        if (!$id) {
            $_SESSION['error_grup'] = "ID de grupo no especificado.";
            header("Location: /grups");
            exit;
        }

        // Verificar que el grupo existe antes de eliminarlo
        $existingGrup = $this->grupMusicalGateway->getByGrupId($id);
        if (!$existingGrup) {
            $_SESSION['error_grup'] = "El grupo musical no existe.";
            header("Location: /grups");
            exit;
        }

        // Antes de eliminar, verificar si hay relaciones que impidan la eliminación
        // Por ejemplo, conciertos asociados, álbumes, etc.
        
        // Eliminar de la base de datos
        $result = $this->grupMusicalGateway->delete($id);

        if ($result) {
            $_SESSION['success_grup'] = "Grupo musical eliminado correctamente.";
            header("Location: /grups");
            exit;
        } else {
            $_SESSION['error_grup'] = "Hubo un error al eliminar el grupo musical.";
            header("Location: /grup/detalle/" . $id);
            exit;
        }
    }
}