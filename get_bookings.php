<?php
header('Content-Type: application/json');
require 'db.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tratăm cererile de tip "preflight" pe care le face React automat
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$sql = "SELECT b.id, b.room_id, b.client_name, b.start_date, b.end_date, r.hotel 
        FROM reservations b 
        JOIN rooms r ON b.room_id = r.id 
        ORDER BY b.id DESC";

$result = $conn->query($sql);
$bookings = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

echo json_encode($bookings);
$conn->close();
?>