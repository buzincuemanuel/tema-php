<?php
// Configurare pentru XAMPP (Local)
$host = "localhost";
$user = "root"; // XAMPP folosește mereu 'root' ca utilizator principal
$pass = ""; // XAMPP nu are parolă setată implicit
$dbname = "hotel_booking"; // Asigură-te că așa ai numit baza de date în phpMyAdmin-ul din XAMPP

// Creăm conexiunea
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificăm dacă a funcționat
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
?>