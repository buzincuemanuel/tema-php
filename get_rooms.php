<?php
header('Content-Type: application/json');
require 'db.php';
$sql = "SELECT id, hotel, category, price FROM rooms";
$result = $conn->query($sql);
$rooms = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}
echo json_encode($rooms);
$conn->close();
?>