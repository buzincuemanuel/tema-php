<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tratăm cererile de tip "preflight" pe care le face React automat
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
header('Content-Type: application/json');
require 'db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 4;
$offset = ($page - 1) * $limit;

$hotelFilter = isset($_GET['hotel']) ? $_GET['hotel'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

$sql = "SELECT id, hotel, category, price FROM rooms WHERE 1=1";
$params = [];
$types = "";

if (!empty($hotelFilter)) {
    $sql .= " AND hotel LIKE ?";
    $params[] = "%" . $hotelFilter . "%";
    $types .= "s";
}

if (!empty($categoryFilter)) {
    $sql .= " AND category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

if (!empty($priceFilter)) {
    $sql .= " AND price <= ?";
    $params[] = (float)$priceFilter;
    $types .= "d";
}

$sql .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $rooms = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
    }

    echo json_encode($rooms);
    $stmt->close();
} else {
    echo json_encode(["error" => "Query error: " . $conn->error]);
}

$conn->close();
?>