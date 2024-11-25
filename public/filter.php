<?php
$filter = isset($_POST['filter']) ? $_POST['filter'] : '';
include('./header.php');
// Inkludieren der Datei "db.php", die die Datenbankverbindung herstellt
require_once 'db.php';

$checklisten = [];

// Sicherheit gegen <script><script>
if (isset($_POST['filter'])) {
    $filter = stripcslashes(htmlspecialchars($_POST['filter']));
}

// Abfrage an die Datenbank senden, abhängig vom Filterwert
if (empty($filter)) {
    // Wenn kein Filterwert vorhanden ist, alle Einträge abrufen
    $reader = $conn->query("SELECT checkliste.id as cid,
    checkliste.termin_id,
    checkliste.aufgaben,
    checkliste.erstellungszeitpunkt,
    checkliste.soll_erledigt_werden,
    checkliste.erledigt_am,
    checkliste.notiz,
    c2.aufgaben as Unteraufgabe_von,
    aufgaben_personen.*, cc_mitglieder.*
    FROM checkliste
    LEFT JOIN aufgaben_personen ON checkliste.id = aufgaben_personen.aufgaben_id
    LEFT JOIN cc_mitglieder ON aufgaben_personen.person_id = cc_mitglieder.id
    LEFT JOIN checkliste as c2 ON checkliste.Unteraufgabe_von = c2.id
    ORDER BY checkliste.Unteraufgabe_von ASC, checkliste.id ASC;");
} else {
    // Wenn ein Filterwert vorhanden ist, Einträge entsprechend filtern
    $stmt = $conn->prepare("SELECT checkliste.id as cid,
    checkliste.termin_id,
    checkliste.aufgaben,
    checkliste.erstellungszeitpunkt,
    checkliste.soll_erledigt_werden,
    checkliste.erledigt_am,
    checkliste.notiz,
    c2.aufgaben as Unteraufgabe_von,
    aufgaben_personen.*, cc_mitglieder.*
    FROM checkliste
    LEFT JOIN aufgaben_personen ON checkliste.id = aufgaben_personen.aufgaben_id
    LEFT JOIN cc_mitglieder ON aufgaben_personen.person_id = cc_mitglieder.id
    LEFT JOIN checkliste as c2 ON checkliste.Unteraufgabe_von = c2.id
    WHERE checkliste.termin_id LIKE ?");
    $suchkriterium = '%' . $filter . '%';
    $stmt->bind_param('s', $suchkriterium);
    $stmt->execute();
    $reader = $stmt->get_result();
}

// Schleife zum Durchlaufen der abgerufenen Einträge
while ($checkliste = $reader->fetch_object()) {
  if (!isset($checklistMap[$checkliste->Unteraufgabe_von])) {
      $checklistMap[$checkliste->Unteraufgabe_von] = []; 
  }

  $checklistMap[$checkliste->Unteraufgabe_von][] = $checkliste; 
}

$reader->free();
$conn->close();
?>

<!-- Überschrift -->
<div>
    <h1 class="display-1 text text-center">Checklist</h1>
</div>
<!-- Filterformular -->
<div class="container-fluid">
<h2 class="font-monospace">Filter</h2>
<form class="input-group mb-3" action="filter.php" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
      <div class="col-auto">
      <input type="text"  class="form-control" name="filter" value="<?= htmlentities($filter,ENT_COMPAT) ?>" >
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary mb-3">Identität bestätigen</button>
      </div>
</form>
</div>
<!-- Checklisten-Tabelle -->

<div class="container-fluid">
        <table class="table table-hover">
            <thead>
                <tr>
                    
                    <th scope="col">ID Termin</th>
                    <th scope="col">Personen</th>
                    <th scope="col">Aufgaben</th>
                    <th scope="col">notiz</th>
                    <th scope="col">Erstellungszeitpunkt</th>
                    <th scope="col">Soll_erledigt_werden</th>
                    <th scope="col">Erledigt_am</th>
                    <th scope="col">Unteraufgabe_von</th>
                    <th scope="col">Ändern</th>
                    <th scope="col">Erledigt</th>
                    <th scope="col">Löschen</th>
                    <th scope="col">Aufgabe zurücksetzen</th>
                </tr>
            </thead>
             <tbody>
             <?php foreach ($checklistMap as $hauptaufgabe => $eintraege) { ?>
                <tr>
                    <th scope="row" colspan="12"><?= $hauptaufgabe ?></th>
                </tr>
                <?php foreach ($eintraege as $checklist) { ?>
                    <tr <?= $checklist->erledigt_am !== '0000-00-00' ? 'class="table-success"' : ($checklist->soll_erledigt_werden < date('Y-m-d') ? 'class="table-danger"' : '') ?>>
                    <th scope="row"><?= $checklist->termin_id ?></th>
                    
                        <td><?= $checklist->name ?></td>
                        <td><?= $checklist->aufgaben ?></td>
                        <td><?= $checklist->notiz ?></td>
                        <td><?= $checklist->erstellungszeitpunkt ?></td>
                        <td><?= $checklist->soll_erledigt_werden ?></td>
                        <td><?= $checklist->erledigt_am ?></td>
                        <td><?= $checklist->Unteraufgabe_von ?></td>
                        <td>
                            <?php if ($checklist->erledigt_am === '0000-00-00') { ?>
                                <button type="button" class="btn btn-warning">
                                    <a class="text-white" href="aendrung_seit.php?zubearbeiten=<?= $checklist->cid ?>&termin_id=<?= $checklist->termin_id?>">Ändern</a>
                                </button>
                            <?php } ?>
                        </td>
                        <td> 
                            <?php if ($checklist->erledigt_am === '0000-00-00') { ?>
                                <button type="button" class="btn btn-success">
                                    <a class="text-white" href="erledigt.php?case_erledigt=<?= $checklist->cid ?>&termin_id=<?= $checklist->termin_id?>">Erledigt</a>
                                </button>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($checklist->erledigt_am === '0000-00-00') { ?>
                                <button type="button" class="btn btn-danger">
                                    <a class="text-white" href="lochen.php?numContact=<?= $checklist->cid ?>&termin_id=<?= $checklist->termin_id?>">Löschen</a>
                                </button>
                            <?php } ?>
                        </td>
                        <td>
                        <button type="button" class="btn btn-dark">
                                    <a class="text-white" href="admin.php?task_id=<?= $checklist->cid ?>&termin_id=<?= $checklist->termin_id?>">Aufgabe zurücksetzen</a>
                                </button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
           
        </table>
    </div>
<br>
<div class="container-fluid">
<button type="button" class="btn btn-dark">
  <a href="index.php">Abbrechen</a>
</button>
</div>
