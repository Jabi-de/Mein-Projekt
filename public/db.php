<?php

// Verbindung zur Datenbank herstellen
 $conn=mysqli_connect('localhost','root','','checkliste');
 
 // Überprüfen, ob die Verbindung erfolgreich war
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>