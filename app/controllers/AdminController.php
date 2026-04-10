<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Solicitud.php';
require_once __DIR__ . '/../models/Taller.php';

class AdminController
{
    private $solicitudModel;
    private $tallerModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->solicitudModel = new Solicitud($db);
        $this->tallerModel = new Taller($db);
    }

    public function solicitudes()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: index.php?page=login');
            return;
        }
        require __DIR__ . '/../views/admin/solicitudes.php';
    }
    
    // Aprobar solicitud
    public function aprobar()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $solicitudId = $_POST['id_solicitud'] ?? 0;
        
    try {

       
        $solicitud = $this->solicitudModel->getById($solicitudId);

        if (!$solicitud) {
            throw new Exception("Solicitud no encontrada");
        }

       
        $taller = $this->tallerModel->getById($solicitud['taller_id']);

        if (!$taller) {
            throw new Exception("Taller no encontrado");
        }

        
        if ($taller['cupo_disponible'] <= 0) {
            throw new Exception("No hay cupo disponible");
        }

       
        $this->tallerModel->descontarCupo($taller['id']);

        
        $this->solicitudModel->aprobar($solicitudId);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
    public function rechazar()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $solicitudId = $_POST['id_solicitud'] ?? 0;
        
        if ($this->solicitudModel->rechazar($solicitudId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al rechazar']);
        }
    }
    public function getSolicitudesJson() {
        $data = $this->solicitudModel->getPendientes();
        echo json_encode($data);
    }    

}