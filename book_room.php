<?php
// 1. Spunem browserului că îi vom răspunde tot cu un JSON
header('Content-Type: application/json');

// 2. Așa citește PHP-ul un pachet JSON trimis prin AJAX modern (fetch)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 3. Verificăm dacă am primit datele
if ($data) {
    // AICI VOM FACE INSERT-ul ÎN BAZA DE DATE MAI TÂRZIU

    // 4. Returnăm un mesaj de succes înapoi către JavaScript
    $response = [
        "status" => "success",
        "message" => "Booking confirmed for " . $data['nume_client'] . "!"
    ];
    echo json_encode($response);
} else {
    // Dacă ceva a mers prost și nu am primit date
    echo json_encode(["status" => "error", "message" => "No data received."]);
}
?>