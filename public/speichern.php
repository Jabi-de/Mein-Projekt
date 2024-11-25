<?php
require_once 'db.php';
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sicherheitsprüfungen und Daten aus dem Formular erhalten
    // ...

    $cid = isset($_POST['cid']) ? (int)$_POST['cid'] : 0;
    $termin_id = isset($_POST['termin_id']) ? (int)$_POST['termin_id'] : 0;
    $aufgaben = isset($_POST['aufgaben']) ? $_POST['aufgaben'] : '';
    $notiz = isset($_POST['notiz']) ? $_POST['notiz'] : '';
    $Unteraufgabe_von = isset($_POST['Unteraufgabe_von']) ? (int)$_POST['Unteraufgabe_von'] : 0;
    $soll_erledigt_werden = isset($_POST['soll_erledigt_werden']) ? $_POST['soll_erledigt_werden'] : '0000-00-00';
    $zustaendig = isset($_POST['zustaendig']) ? $_POST['zustaendig'] : [];

    // SQL-Statement für das Update
    $sql = "UPDATE checkliste 
            SET termin_id = ?, aufgaben = ?, soll_erledigt_werden = ?, notiz = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssi', $termin_id, $aufgaben, $soll_erledigt_werden, $notiz, $cid);

    // Ausführen des Statements
    if ($stmt->execute()) {
        // Erfolgreiches Update
        // ...

        // Aufgaben-Personen aktualisieren (Löschen der alten Zuordnungen)
        $deleteSql = "DELETE FROM aufgaben_personen WHERE aufgaben_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $cid);
        $deleteStmt->execute();
        $deleteStmt->close();

        // Neue Aufgaben-Personen-Zuordnungen einfügen
        foreach ($zustaendig as $person_id) {
            $insertSql = "INSERT INTO aufgaben_personen (aufgaben_id, person_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param('ii', $cid, $person_id);
            if ($insertStmt->execute()) {
                // Update für "Unteraufgabe_von" ausführen
                $updateSql = "UPDATE checkliste SET Unteraufgabe_von = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param('ii', $Unteraufgabe_von, $cid);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

    }

    $stmt->close();
    $conn->close();
 

    header('Location: index.php?termin_id=' . $_GET['termin_id']);
    exit;
}