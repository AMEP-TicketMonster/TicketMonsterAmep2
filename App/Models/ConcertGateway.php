<?php

namespace App\Models;

use App\Config\Database;
use Exception;

class ConcertGateway
{
    private $pdo;
    private $id;
    private $nomConcert;
    private $data;
    private $aforament;
    private $preu;
    private $idUsuariOrganitzador;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // patrón singleton
    }

    // Cargar todos los conciertos
    public function getConcertList()
    {
        //Habría que poner un LIMIT 'int, sin las comillas'
        // idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere, Sales.nom AS sala, Sales.ciutat AS ubicacio
        /*$stmt = $this->pdo->prepare("SELECT idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere, Sales.nom AS sala, Sales.ciutat AS ubicacio  FROM Concerts JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup JOIN  Sales ON Concerts.idSala = Sales.idSala JOIN Generes ON Concerts.idGenere = Generes.idGenere and DataSala.dia > CURDATE();
        ");
       */
        $stmt = $this->pdo->prepare("SELECT idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere, Sales.nom AS sala, Sales.ciutat AS ubicacio  FROM Concerts JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup JOIN  Sales ON Concerts.idSala = Sales.idSala JOIN Generes ON Concerts.idGenere = Generes.idGenere and DataSala.dia > CURDATE();
        ");
        $stmt->execute();
        $concerts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $concerts;
    }

    public function getConcertListFiltered($ids)
    {
        // Asegúrate de que $ids no esté vacío
        if (empty($ids)) {
            return []; // O lanza una excepción si lo prefieres
        }

        // Generamos tantos signos de interrogación como IDs haya
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere,
                   Sales.nom AS sala, Sales.ciutat AS ubicacio  
            FROM Concerts 
            JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala 
            JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup 
            JOIN Sales ON Concerts.idSala = Sales.idSala 
            JOIN Generes ON Concerts.idGenere = Generes.idGenere 
            WHERE DataSala.dia > CURDATE()
            AND idConcert IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);

        $concerts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $concerts;
    }

    public function getByConcertId($id)
    {
        $stmt = $this->pdo->prepare("
        SELECT * 
        FROM Concerts
        JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala
        JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup
        JOIN Sales ON Concerts.idSala = Sales.idSala
        JOIN Generes ON Concerts.idGenere = Generes.idGenere
        JOIN Entrades ON Concerts.idConcert = Entrades.idConcert
        WHERE Concerts.idConcert = ?
    ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user;
    }

    public function getGeneres()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Generes");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getGrupMusical()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM GrupsMusicals");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createConcert($idGrup, $idSala, $nomConcert, $idGenere, $idDataSala, $aforamentSala)

    {


        $stmt = $this->pdo->prepare("INSERT INTO Concerts (idGrup, idSala, nomConcert, entrades_disponibles, idGenere, idDataSala, imatge) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([(int)$idGrup, (int)$idSala, $nomConcert, (int)$aforamentSala, (int)$idGenere, (int)$idDataSala, '../../public/img/default.png']);

        //devuelve id del concierto

        return $this->pdo->lastInsertId();
    }

    public function getDataSala($idDataSala)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM DataSala WHERE idDataSala = ?");
        $stmt->execute([$idDataSala]);
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        var_dump($res);
    }



    // Nota: aquesta funció no actualitza les entrades disponibles del concert pq es complica la lògica per actualitzar les entrades
    //       però sí modifica el preu de totes les entrades disponibles d'aquest concert
    public function modificaConcert($idConcert, $idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere)
    {
        // Modifica el concert
        $sql = "UPDATE Concerts
                SET idGrup = ?, 
                idSala = ?, 
                nomConcert = ?, 
                dia = ?, 
                hora = ?, 
                preu = ?, 
                idGenere = ?, 
                idUsuari = ?
                WHERE idConcert = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere, $idUsuariOrganitzador, $idConcert]);

        // Obtenim el id del estat "Disponible"
        $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = 'Disponible'");
        $stmt->execute();
        $idEstatEntrada = $stmt->fetch(\PDO::FETCH_ASSOC)['idEstatEntrada'];

        // Actualitzem els preus de totes les entrades per aquest concert que encara no s'han venut ni reservat
        $sql = "UPDATE EntradesConcert SET preu = ? WHERE idConcert = ? AND idEstatEntrada = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$preu, $idConcert, $idEstatEntrada]);
    }


    public function guardaValoracio($idConcert, $puntuacio, $comentari)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO Valoracions (idConcert, puntuacio, comentari) 
            VALUES (?, ?, ?)"
        );
        $stmt->execute([$idConcert, $puntuacio, $comentari]);
        return true;
    }

    public function consultaImatge($img)
    {
        $rutaImg = trim($img);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Concerts WHERE imatgeURL = :ruta");
        $stmt->bindParam(':ruta', $rutaImg, \PDO::PARAM_STR);
        $stmt->execute();

        $existe = $stmt->fetchColumn();
        return $existe > 0;
    }

    public function guardaImatge($idConcert, $img)
    {
        if ($this->consultaImatge($img)) {
            $img = "../../public/img/default.png";
        }

        $rutaImg = trim($img);

        $stmt = $this->pdo->prepare("UPDATE Concerts SET imatgeURL = ? WHERE idConcert = ?");
        $stmt->execute([$rutaImg, $idConcert]);

        if ($stmt->rowCount() == 0) {
            throw new Exception("No s'ha pogut actualitzar la imatge del concert.");
        }
        return true;
    }

    /*Nombre concierto
    Nombre grupo
    Num entradas
    Genero
    Sala*/
    public function concertFiltre($filtres)
    {

        $sql = "SELECT * FROM Concerts c WHERE 1=1";

        $params = [];

        if (!empty($filtres['search'])) {

            $sql .= " AND c.nomConcert = :nomConcert";
            $params[':nomConcert'] = $filtres['search'];
        }

        if (!empty($filtres['genere'])) {
            $sql .= " AND c.idGenere = :idGenere";
            $params[':idGenere'] = (int) $filtres['genere'];
        }

        if (!empty($filtres['sala'])) {
            $sql .= " AND c.idSala = :idSala";
            $params[':idSala'] = (int) $filtres['sala'];
        }

        if (!empty($filtres['entradas'])) {
            $sql .= " AND c.entrades_disponibles >= :entrades_disponibles";
            $params[':entrades_disponibles'] = (int) $filtres['entradas'];
        }


        if (empty($params)) {

            return [];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);


        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /*
        NO SE PUEDE:
        1) Dos conciertos con mismo nombre
        2) Dos conciertos con mismo grupo Y mismo dia Y hora
        3) Dos conciertos con misma sala Y mismo dia Y hora
        4) Dos grupos con dos conciertos simultaneos
        5) Una sala no puede tener más de un evento al mismo tiempo
        6) Dia y hora de un concierto deben ser válidas y futuras
        7) Precio no puede ser negativo o cero
        8) Dos conciertos con misma combinación de grupo, sala, dia y hora
        9) idGrup, idSala o idGenere ya existe
    */
    public function validarParametrosCrearConcert($idGrup, $idSala, $nomConcert, $dia, $horaInici, $horaFi, $preu, $idGenere)
    {
        // Validación 1: Campos requeridos
        if (empty($idGrup) || empty($idSala) || empty($nomConcert) || empty($dia) || empty($horaInici) || empty($horaFi) || $preu == '' || empty($idGenere)) {
            return "Tots els paràmetres són obligatoris.";
        }

        // Validación 2: Precio
        if ($preu <= 0) {
            return "El preu ha de ser superior a 0.";
        }

        if (strtotime($horaFi) <= strtotime($horaInici)) {
            return "L'hora de finalització ha de ser superior a l'hora d'inici.";
        }

        // Validación 3: Fecha futura
        $timestamp = strtotime("$dia $horaInici");
        if ($timestamp <= time()) {
            return "No pots crear un concert en el passat.";
        }

        // Validación 4: Nombre único
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Concerts WHERE nomConcert = ?");
        $stmt->execute([$nomConcert]);
        if ($stmt->fetchColumn() > 0) {
            return "Ja existeix un concert amb aquest nom.";
        }

        // Validación 5: Conflicto de sala en DataSala
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM DataSala
            WHERE dia = :dia
            AND idSala = :idSala
            AND (
                (hora_inici < :horaFi AND hora_fi > :horaInici)
            )
        ");
        $stmt->execute([
            ':dia' => $dia,
            ':idSala' => $idSala,
            ':horaInici' => $horaInici,
            ':horaFi' => $horaFi
        ]);
        if ($stmt->fetchColumn() > 0) {
            return "La sala ja està ocupada en aquest horari.";
        }

        // Validación 6: Conflicto de grupo en la misma franja
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM Concerts c
            JOIN DataSala ds ON c.idDataSala = ds.idDataSala
            WHERE c.idGrup = :idGrup
            AND ds.dia = :dia
            AND (
                (ds.hora_inici < :horaFi AND ds.hora_fi > :horaInici)
            )
        ");
        $stmt->execute([
            ':idGrup' => $idGrup,
            ':dia' => $dia,
            ':horaInici' => $horaInici,
            ':horaFi' => $horaFi
        ]);
        if ($stmt->fetchColumn() > 0) {
            return "Aquest grup ja té un concert programat en aquest horari.";
        }

        // Validación 7: FK válidas
        foreach (
            [
                ['table' => 'GrupsMusicals', 'field' => 'idGrup', 'value' => $idGrup],
                ['table' => 'Sales', 'field' => 'idSala', 'value' => $idSala],
                ['table' => 'Generes', 'field' => 'idGenere', 'value' => $idGenere]
            ] as $check
        ) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$check['table']} WHERE {$check['field']} = ?");
            $stmt->execute([$check['value']]);
            if ($stmt->fetchColumn() == 0) {
                return "Valor no vàlid per a " . $check['field'];
            }
        }

        return null; // Todo OK
    }
}
