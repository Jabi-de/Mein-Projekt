<?php
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
// Überprüfen, ob die Aufgaben-ID übergeben wurde
if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Aufgabe zurücksetzen
    resetTask($taskId);
}
// Funktion zum Zurücksetzen einer Aufgabe
function resetTask($taskId) {
    // Datenbankverbindung herstellen
    $db = new mysqli("localhost", "root", "", "checkliste");

    // Überprüfen, ob die Verbindung erfolgreich hergestellt wurde
    if ($db->connect_error) {
        die("Verbindungsfehler: " . $db->connect_error);
    }

    // SQL-Abfrage zum Aktualisieren des Aufgabenstatus
    $query = "UPDATE checkliste SET erledigt_am= 0000-00-00  WHERE id=$taskId";



    // Query ausführen
    if ($db->query($query) === TRUE) {
        $db->close();
        header('Location: index.php?termin_id=' . $_GET['termin_id']);
        exit;

    } 

    
   
}
// Überprüfen, ob die Aufgaben-ID übergeben wurde
if (isset($_GET['task_id1'])) {
    $taskId = $_GET['task_id1'];

    // Aufgabe zurücksetzen
    resetTask1($taskId);
}
// Funktion zum Zurücksetzen einer Aufgabe
function resetTask1($taskId) {
    // Datenbankverbindung herstellen
    $db = new mysqli("localhost", "root", "", "checkliste");

    // Überprüfen, ob die Verbindung erfolgreich hergestellt wurde
    if ($db->connect_error) {
        die("Verbindungsfehler: " . $db->connect_error);
    }

    // SQL-Abfrage zum Aktualisieren des Aufgabenstatus
    $query = "UPDATE checkliste SET erledigt_am= 0000-00-00  WHERE id=$taskId";



    // Query ausführen
    if ($db->query($query) === TRUE) {
        $db->close();
        header('Location: filter.php');
        exit;
    } 

    // Datenbankverbindung schließen
   
}
?>

