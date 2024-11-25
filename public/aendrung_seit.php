<?php
// Verbindung zur Datenbank herstellen
include('./header.php');
require_once 'db.php';
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
$zubearbeiten = isset($_GET['zubearbeiten']) ? (int)$_GET['zubearbeiten'] : 0;
// Sicherheit gegen <script><script>
if (isset($_POST['termin_id']) && isset($_POST['aufgaben']) && isset($_POST['Unteraufgabe_von'])
    && isset($_POST['soll_erledigt_werden']) && isset($_POST['zustaendig[]'])) {
    $termin_id = stripcslashes(htmlspecialchars($_POST['termin_id']));
    $aufgaben = stripcslashes(htmlspecialchars(strtolower($_POST['aufgaben'])));
    $Unteraufgabe_von = stripcslashes(htmlspecialchars($_POST['Unteraufgabe_von']));
    $soll_erledigt_werden = stripcslashes(htmlspecialchars($_POST['soll_erledigt_werden']));
    $zustaendig = isset($_POST['zustaendig']) ? $_POST['zustaendig'] : [];
}

$result = [];

$reader = $conn->prepare("SELECT *
                        FROM `cc_mitglieder`
                        GROUP BY id
                        ORDER BY id DESC");

// Schleife zum Durchlaufen der abgerufenen Einträge
$reader->execute();
$reader_result = $reader->get_result();
while ($cc_mitglieder = $reader_result->fetch_assoc()) {
    $result[] = $cc_mitglieder;
}
$reader_result->free();

$result1 = [];

$reader = $conn->prepare("SELECT checkliste.id,
                                 checkliste.aufgaben
                        FROM checkliste
                        GROUP BY id
                        ORDER BY id DESC");

// Schleife zum Durchlaufen der abgerufenen Einträge
$reader->execute();
$reader_result = $reader->get_result();
while ($aufgaben_personen = $reader_result->fetch_assoc()) {
    $result1[] = $aufgaben_personen ;
}
$reader_result->free();

$alle = [];

$reader = $conn->prepare("SELECT checkliste.id as cid,
checkliste.termin_id,
checkliste.aufgaben,
checkliste.erstellungszeitpunkt,
checkliste.soll_erledigt_werden,
checkliste.erledigt_am,
checkliste.notiz,
c2.aufgaben as Unteraufgabe_von,
aufgaben_personen.*
FROM checkliste
LEFT JOIN aufgaben_personen ON checkliste.id = aufgaben_personen.aufgaben_id
LEFT JOIN checkliste as c2 ON checkliste.Unteraufgabe_von = c2.id
WHERE checkliste.id = ?");
$reader->bind_param('i', $zubearbeiten);
$reader->execute();
$readerset = $reader->get_result();

while ($checkliste = $readerset->fetch_assoc()) {
    $alle[] = $checkliste;
}
$readerset->free();

$reader->close();
$conn->close();

?>



<div>
  <h1 class="display-3 text text-center" >Eintrag Ändern</h1>
</div>
<!-- Formular -->
<div class="container-fluid">
 <form method="POST" action="speichern.php?termin_id=<?=$_GET['termin_id']?>" class="needs-validation">

 <!-- ID -->
<div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">ID</span>
            <input type="hidden" class="form-control" name="cid" aria-label="cid" aria-describedby="basic-addon1"  value="<?= htmlentities($alle[0]["cid"] ?? '',ENT_COMPAT) ?>"><br><br>
        </div>

<!-- Termin ID -->
 <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Termin ID</span>
            <input type="text" class="form-control" name="termin_id" aria-label="termin_id" aria-describedby="basic-addon1"  value="<?= htmlentities($alle[0]["termin_id"] ?? '',ENT_COMPAT) ?>"><br><br>
        </div>
   <!-- Aufgaben -->
   <h5 class="text">Aufgaben</h5>
   <div class="form-floating mb-3">
   <label class="text-uppercase" for="floatingTextarea2Disabled"></label>
   <textarea name="aufgaben" id="aufgaben" style="width: 400px; height: 200px; resize: none;"><?= htmlentities($alle[0]['aufgaben'] ?? '', ENT_COMPAT) ?></textarea><br><br>
    
        </div>
        </div>
     <!-- Notizfeld -->
     <h5 class="text">Notiz</h5>
   <div class="form-floating mb-3">
   <label class="text-uppercase" for="floatingTextarea2Disabled"></label>
   <textarea name="notiz" id="notiz" style="width: 400px; height: 200px; resize: none;"><?= htmlentities($alle[0]['notiz'] ?? '', ENT_COMPAT) ?></textarea><br><br>
     </div>
 <!-- Unteraufgabe -->
 <div>
    <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="Unteraufgabe_von">
        <option selected>Öffnen Sie dieses Hauptaufgabenmenü</option>
        <?php
        
        if (count($result1) > 0) {
            foreach ($result1 as $row) {
                $Unteraufgabe_von = isset($row["Unteraufgabe_von"]) ? htmlentities($row["Unteraufgabe_von"], ENT_COMPAT) : '';
                $checklisteAufgaben = isset($row['aufgaben']) ? htmlentities($row['aufgaben'], ENT_COMPAT) : '';
        
                 //$selected = ($Unteraufgabe_von == $result1[0]["Unteraufgabe_von"]) ? 'selected' : '';
        
                echo '<option value="' . $row["id"] . '" ' . $selected . '>' . $checklisteAufgaben . '</option>';
            }
        } else {
            echo "<li>Keine Ergebnisse gefunden.</li>";
        }
        
        ?>
    </select>
</div>

 <!-- Soll erledigt werden -->
 <div class="container-fluid">
    <section >
        <h3 class="pt-4 pb-2">Soll erledigt werden:</h3>
        
            <div class="row form-group">
                <label for="soll_erledigt_werden" class="col-sm-1 col-form-label">Date</label>
                <div class="col-sm-4">
                    <div class="input-group date" id="soll_erledigt_werden">
                        <input type="date" class="form-control" id="soll_erledigt_werden_input" name="soll_erledigt_werden" value="<?= htmlentities($alle[0]["soll_erledigt_werden"] ?? '',ENT_COMPAT) ?>">

                    </div>
                </div>
            </div>
        
    </section>

    <script type="text/javascript">
        $(function() {
            $('#datepicker').datepicker();
        });
    </script>
 </div>
 <!-- erledigt_am -->
 <div class="container-fluid">
    <section >
        <h3 class="pt-4 pb-2">erledigt_am:</h3>
        
            <div class="row form-group">
                <label for="erledigt_am" class="col-sm-1 col-form-label">Date</label>
                <div class="col-sm-4">
                    <div class="input-group date" id="soll_erledigt_werden">
                        <input type="date" class="form-control" id="erledigt_am_input" name="erledigt_am" value="<?= htmlentities($alle[0]["erledigt_am"] ?? '',ENT_COMPAT) ?>"disabled>

                    </div>
                </div>
            </div>
        
    </section>

    <script type="text/javascript">
        $(function() {
            $('#datepicker').datepicker();
        });
    </script>
 </div>
 <div>
 <ul>
        <?php
        // Ergebnisse verarbeiten und in Liste anzeigen
        if (count($result) > 0) {
            foreach ($result as $row) {
                $vorname = isset($row["vorname"]) ? htmlentities($row["vorname"], ENT_COMPAT) : '';
                $name = isset($row["name"]) ? htmlentities($row["name"], ENT_COMPAT) : '';
                $vornameCheckliste = isset($row['vorname']) ? htmlentities($row['vorname'], ENT_COMPAT) : '';
                $nameCheckliste = isset($row['name']) ? htmlentities($row['name'], ENT_COMPAT) : '';

                echo "<li class='list-group-item'><input type='checkbox' 
                name='zustaendig[]' class='form-check-input' value='".$row["id"]."'> $vornameCheckliste $nameCheckliste 
                </li>";
            }
        }
        
         else {
            echo "<li>Keine Ergebnisse gefunden.</li>";
        }
        ?>
    </ul>
 </div>
 <!-- Speichern-Button -->
 <div>
 <button class="btn btn-success" type="submit" value="Submit">
Speichern
 </button> 
 </div><br>
 <!-- Abbrechen-Button --> 
 <div>        
 <button type="button" class="btn btn-dark">
 Abbrechen
 </button>
 </div> 
 
 </form>
</div>    

