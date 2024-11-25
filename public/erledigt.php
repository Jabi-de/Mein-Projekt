<?php
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
if (isset($_GET['case_erledigt'])) {
    // Die ID des Eintrags, der als erledigt markiert werden soll
    $id = $_GET['case_erledigt'];

    // Verbindung zur Datenbank herstellen
 $conn=mysqli_connect('localhost','root','','checkliste');
 
 // Überprüfen, ob die Verbindung erfolgreich war
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    // Vorbereiteter SQL-Befehl, um das erledigt_am-Datum zu aktualisieren
    $stmt = $conn->prepare("UPDATE checkliste SET erledigt_am = NOW() WHERE id = ?");
    $stmt->bind_param('i', $id);

    // SQL-Befehl ausführen
    $stmt->execute();

    // Statement schließen
    $stmt->close();

    // Verbindung schließen
    $conn->close();

    // Zurück zur Startseite (index.php) weiterleiten
    header('Location: index.php?termin_id=' . $_GET['termin_id']);
    exit;
}

if (isset($_GET['filter_erledigt'])) {
    // Die ID des Eintrags, der als erledigt markiert werden soll
    $id = $_GET['filter_erledigt'];

    // Verbindung zur Datenbank herstellen
 $conn=mysqli_connect('localhost','root','','checkliste');
 
 // Überprüfen, ob die Verbindung erfolgreich war
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    // Vorbereiteter SQL-Befehl, um das erledigt_am-Datum zu aktualisieren
    $stmt = $conn->prepare("UPDATE checkliste SET erledigt_am = NOW() WHERE id = ?");
    $stmt->bind_param('i', $id);

    // SQL-Befehl ausführen
    $stmt->execute();

    // Statement schließen
    $stmt->close();

    // Verbindung schließen
    $conn->close();

    // Weiterleitung zur Filterseite (filter.php)
    header('Location: filter.php');
    exit;
}
?>
