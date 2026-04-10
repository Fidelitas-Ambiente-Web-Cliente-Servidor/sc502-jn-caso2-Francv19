<?php
class Solicitud
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function crear($tallerId, $usuarioId) {
   
    $query = "SELECT * FROM solicitudes 
              WHERE taller_id = ? AND usuario_id = ? 
              AND estado IN ('pendiente', 'aprobada')";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $tallerId, $usuarioId);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        return false;
    }


    $query = "INSERT INTO solicitudes (taller_id, usuario_id) VALUES (?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $tallerId, $usuarioId);
    return $stmt->execute();
}

public function rechazar($id) {
    $query = "UPDATE solicitudes SET estado = 'rechazada' WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

public function aprobar($id) {
    $query = "UPDATE solicitudes SET estado = 'aprobada' WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

public function getPendientes() {
    $query = "SELECT 
                s.id AS id,
                t.nombre AS taller,
                u.username AS username,
                s.usuario_id AS usuario_id,
                s.fecha_solicitud AS fecha_solicitud
              FROM solicitudes s
              INNER JOIN talleres t ON s.taller_id = t.id
              INNER JOIN usuarios u ON s.usuario_id = u.id
              WHERE s.estado = 'pendiente'";

    $result = $this->conn->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

public function getById($id) {
    $query = "SELECT * FROM solicitudes WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

}