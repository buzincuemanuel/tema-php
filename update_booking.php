<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tratăm cererile de tip "preflight" pe care le face React automat
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data && isset($data['id'])) {
    require 'db.php';

    $sql = "UPDATE reservations SET client_name = ?, start_date = ?, end_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssi", $data['client_name'], $data['start_date'], $data['end_date'], $data['id']);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Booking updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Incomplete data received for editing."]);
}
?>