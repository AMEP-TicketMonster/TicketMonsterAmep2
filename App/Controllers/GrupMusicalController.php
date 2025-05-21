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
        $nomGrup = trim($_POST["nomGrup"]);
        $dataCreacio = trim($_POST["dataCreacio"]);
        $descripcio = trim($_POST["descripcio"]);

        // Validar que los campos requeridos no estén vacíos
        if (empty($nomGrup) || empty($dataCreacio)) {
            $_SESSION['error_grup'] = "El nombre y la fecha de creación son obligatorios.";
            header("Location: /grup/nou");
            exit;
        }

        // Verificar si el grupo ya existe
        $existingGrup = $this->grupMusicalGateway->getByNomGrup($nomGrup);
        if ($existingGrup) {
            $_SESSION['error_grup'] = "Este grupo musical ya está registrado.";
            header("Location: /grup/nou");
            exit;
        }

        // Insertar en la base de datos
        $idGrup = $this->grupMusicalGateway->createGrup($nomGrup, $dataCreacio, $descripcio);

        if ($idGrup) {
            $_SESSION['success_grup'] = "Grupo musical creado correctamente.";
            header("Location: /grup/detalle/" . $idGrup);
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
    public function consultaGrup($idGrup = null)
    {
        if (!$idGrup && isset($_GET['idGrup'])) {
            $idGrup = $_GET['idGrup'];
        }

        if (!$idGrup) {
            $_SESSION['error_grup'] = "ID de grupo no especificado.";
            header("Location: /grups");
            exit;
        }

        $grup = $this->grupMusicalGateway->getByIdGrup($idGrup);
        
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
        $idGrup = trim($_POST["idGrup"]);
        $nomGrup = trim($_POST["nomGrup"]);
        $dataCreacio = trim($_POST["dataCreacio"]);
        $descripcio = trim($_POST["descripcio"]);

        // Validar que los campos requeridos no estén vacíos
        if (empty($idGrup) || empty($nomGrup) || empty($dataCreacio)) {
            $_SESSION['error_grup'] = "El ID, nombre y fecha de creación son obligatorios.";
            header("Location: /grup/editar/" . $idGrup);
            exit;
        }

        // Verificar que el grupo existe antes de modificarlo
        $existingGrup = $this->grupMusicalGateway->getByIdGrup($idGrup);
        if (!$existingGrup) {
            $_SESSION['error_grup'] = "El grupo musical no existe.";
            header("Location: /grups");
            exit;
        }

        // Actualizar en la base de datos
        $result = $this->grupMusicalGateway->updateGrup($idGrup, $nomGrup, $dataCreacio, $descripcio);

        if ($result) {
            $_SESSION['success_grup'] = "Grupo musical actualizado correctamente.";
            header("Location: /grup/detalle/" . $idGrup);
            exit;
        } else {
            $_SESSION['error_grup'] = "Hubo un error al actualizar el grupo musical.";
            header("Location: /grup/editar/" . $idGrup);
            exit;
        }
    }

    /**
     * Método para dar de baja un grupo musical
     */
    public function baixaGrup($idGrup = null)
    {
        if (!$idGrup && isset($_POST['idGrup'])) {
            $idGrup = $_POST['idGrup'];
        }

        if (!$idGrup) {
            $_SESSION['error_grup'] = "ID de grupo no especificado.";
            header("Location: /grups");
            exit;
        }

        // Verificar que el grupo existe antes de eliminarlo
        $existingGrup = $this->grupMusicalGateway->getByIdGrup($idGrup);
        if (!$existingGrup) {
            $_SESSION['error_grup'] = "El grupo musical no existe.";
            header("Location: /grups");
            exit;
        }

        // Antes de eliminar, verificar si hay relaciones que impidan la eliminación
        // Por ejemplo, conciertos asociados, álbumes, etc.
        
        // Eliminar de la base de datos
        $result = $this->grupMusicalGateway->delete($idGrup);

        if ($result) {
            $_SESSION['success_grup'] = "Grupo musical eliminado correctamente.";
            header("Location: /grups");
            exit;
        } else {
            $_SESSION['error_grup'] = "Hubo un error al eliminar el grupo musical.";
            header("Location: /grup/detalle/" . $idGrup);
            exit;
        }
    }
}