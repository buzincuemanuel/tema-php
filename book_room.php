<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    require 'db.php';

    $sql = "INSERT INTO reservations (room_id, client_name, start_date, end_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isss", $data['id_camera'], $data['nume_client'], $data['data_start'], $data['data_final']);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Booking confirmed for " . $data['nume_client'] . "!"
            ];
        } else {
            $response = ["status" => "error", "message" => "Save error: " . $stmt->error];
        }

        $stmt->close();
    } else {
        $response = ["status" => "error", "message" => "SQL error: " . $conn->error];
    }

    $conn->close();
    echo json_encode($response);

} else {
    echo json_encode(["status" => "error", "message" => "No data received."]);
}
?>