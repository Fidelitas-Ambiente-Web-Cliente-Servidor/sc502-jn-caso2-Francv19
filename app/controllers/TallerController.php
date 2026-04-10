<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Taller.php';
require_once __DIR__ . '/../models/Solicitud.php';

class TallerController
{
    private $tallerModel;
    private $solicitudModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->tallerModel = new Taller($db);
        $this->solicitudModel = new Solicitud($db);
    }

    public function index()
    {
        if (!isset($_SESSION['id'])) {
            header('Location: index.php?page=login');
            return;
        }
        require __DIR__ . '/../views/taller/listado.php';
    }
    
    public function getTalleresJson()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode([]);
            return;
        }
        
        $talleres = $this->tallerModel->getAllDisponibles();
        header('Content-Type: application/json');
        echo json_encode($talleres);
    }
    
    public function solicitar()
{
    header('Content-Type: application/json'); 

    if (!isset($_SESSION['id'])) {
        echo json_encode([
            'response' => "01",
            'message' => 'Debes iniciar sesión'
        ]);
        exit;
    }

    $tallerId = $_POST['taller_id'] ?? 0;
    $usuarioId = $_SESSION['id'];

    $taller = $this->tallerModel->getById($tallerId);

    if (!$taller || $taller['cupo_disponible'] <= 0) {
        echo json_encode([
            'response' => "01",
            'message' => 'No hay cupo disponible'
        ]);
        exit;
    }

    if ($this->solicitudModel->crear($tallerId, $usuarioId)) {

       
        $this->tallerModel->descontarCupo($tallerId);

        echo json_encode([
            'response' => "00",
            'message' => "Solicitud enviada correctamente"
        ]);

    } else {
        echo json_encode([
            'response' => "01",
            'message' => "Ya tienes una solicitud"
        ]);
    }

    exit;
}

}