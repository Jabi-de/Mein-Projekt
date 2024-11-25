<?php
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
// Verbindung zur Datenbank herstellen
$conn=mysqli_connect('localhost','root','','checkliste');
 
// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

// Überprüfen, ob eine numContact-Parameter im GET-Array vorhanden ist
if (isset($_GET['numContact'])) {
    $id = $_GET['numContact'];
    
    // Löschen der referenzierten Zeilen in der Tabelle aufgaben_personen
    $stmt = $conn->prepare("DELETE FROM aufgaben_personen WHERE aufgaben_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    
    // Löschen der Zeile in der Tabelle checkliste
    $stmt = $conn->prepare("DELETE FROM checkliste WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Zurück zur Index-Seite leiten
header('Location: index.php?termin_id=' . $_GET['termin_id']);
exit;
?>
