<?php
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
@session_start();
$termin_id = isset($_POST['termin_id']) ? $_POST['termin_id'] : 0;
$aufgaben = isset($_POST['aufgaben']) ? $_POST['aufgaben'] : null;
$notiz = isset($_POST['notiz']) ? $_POST['notiz'] : null;
$soll_erledigt_werden = isset($_POST['soll_erledigt_werden']) ? $_POST['soll_erledigt_werden'] : 0;
$erledigt_am = isset($_POST['erledigt_am']) ? $_POST['erledigt_am'] : 0;
$Unteraufgabe_von = isset($_POST['Unteraufgabe_von']) ? $_POST['Unteraufgabe_von'] : null;
if (!is_numeric($Unteraufgabe_von)) {
    $Unteraufgabe_von = null;
}

if (empty($termin_id)) {
    $_SESSION['fehler'] = 'Ein Fehler ist aufgetreten: keine Termin ID';
    header('Location: insert_seit.php?insert=');
    exit;
}

// Überprüfen, ob Aufgaben angegeben wurden
if (empty($aufgaben)) {
    $_SESSION['fehler'] = 'Ein Fehler ist aufgetreten: keine Aufgaben';
    header('Location: insert_seit.php?insert=');
    exit;
}

// Überprüfen, ob soll_erledigt_werden angegeben wurde
if (empty($soll_erledigt_werden)) {
    $_SESSION['fehler'] = 'Ein Fehler ist aufgetreten: kein Datum';
    header('Location: insert_seit.php?insert=');
    exit;
}

// Verbindung zur Datenbank herstellen
$conn=mysqli_connect('localhost','root','','checkliste');
 
// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

// Vorbereiten und Ausführen des INSERT-Statements
$stmt = $conn->prepare("INSERT INTO checkliste (termin_id, aufgaben, soll_erledigt_werden, erledigt_am, Unteraufgabe_von,notiz) VALUES (?,?, ?, ?, ?, ?)");
$stmt->bind_param('isssis', $termin_id, $aufgaben, $soll_erledigt_werden, $erledigt_am, $Unteraufgabe_von,$notiz);
$stmt->execute();
//$stmt->insert_id=es ist ein methode zum letzte ID rufen
$aufgaben_id = $stmt->insert_id; // ID des zuletzt eingefügten Datensatzes abrufen
                                   
foreach ($_POST['zustaendig'] as $eintrag) {
    $stmt = $conn->prepare("INSERT INTO aufgaben_personen (aufgaben_id, person_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $aufgaben_id, $eintrag);
    $stmt->execute();


}

// Zurück zur Index-Seite leiten
header('Location: index.php?termin_id=' . $_GET['termin_id']);
exit;
?>
