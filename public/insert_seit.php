<?php
// Verbindung zur Datenbank herstellen
include('./header.php');
require_once 'db.php';
@session_start();
$terminId = isset($_GET['termin_id']) ? $_GET['termin_id'] : '';
// Sicherheit gegen <script><script>
if(isset($_POST['termin_id']) && isset($_POST['aufgaben'])&& isset($_POST['notiz'])){
    $termin_id = stripcslashes(htmlspecialchars($_POST['termin_id']));
    $aufgaben = stripcslashes(htmlspecialchars(strtolower($_POST['aufgaben'])));
    $notiz = stripcslashes(htmlspecialchars(strtolower($_POST['notiz'])));
}
$result = [];

$reader = $conn->query("SELECT *
                        FROM `cc_mitglieder`
                        GROUP BY id
                        ORDER BY id DESC;
                        ");

// Schleife zum Durchlaufen der abgerufenen Einträge
while ($cc_mitglieder = $reader->fetch_assoc()) {
    $result[] = $cc_mitglieder;
}
$reader->free();


$result1 = [];

$reader = $conn->query("SELECT checkliste.id as cid,
checkliste.aufgaben
FROM checkliste
LEFT JOIN aufgaben_personen ON checkliste.id = aufgaben_personen.aufgaben_id;");

// Schleife zum Durchlaufen der abgerufenen Einträge
while ($checkliste = $reader->fetch_assoc()) {
    $result1[] = $checkliste;
}

$reader->free();

?>


<div>
  <h1 class="display-3 text text-center" > Neuen Eintrag hinzufügen</h1>
</div>
<!-- Formular -->
<div class="container-fluid">
 <form method="POST" action="insert.php" class="needs-validation">
 <?php

if(isset($_SESSION['fehler'])) {
	//sie annzeigen
?>
<div class="alert alert-warning" role="alert"><?= $_SESSION['fehler'] ?></div>
<?php
	//und aus der Session entfernen
	unset($_SESSION['fehler']);
}
?>
 <!-- Termin ID -->
    <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Termin ID</span>
             <input type="text" class="form-control" name="termin_id" aria-label="termin_id" aria-describedby="basic-addon1" value="<?php echo isset($_GET['termin_id']) ? htmlspecialchars($_GET['termin_id']) : ''; ?>"required><br><br>
    </div>
 <!-- Aufgaben -->
 <div class="form-floating mb-3">
  <textarea class="form-control" name="aufgaben" id="floatingTextarea2Disabled" style="height: 100px" required><?php echo isset($_GET['aufgaben']) ? htmlspecialchars($_GET['aufgaben']) : ''; ?></textarea><br><br>
  <label class="text-uppercase" for="floatingTextarea2Disabled">Aufgaben:</label>
 </div>
       <!-- Notizfeld -->
       <div class="form-floating mb-3">
      <textarea class="form-control" name="notiz" id="floatingTextarea2Disabled" style="height: 100px"required><?php echo isset($_GET['notiz']) ? htmlspecialchars($_GET['notiz']) : ''; ?></textarea><br><br>
  <label class="text-uppercase" for="floatingTextarea2Disabled">Notiz</label>
</div>
 <!-- Hauptaufgabe -->
 <div>
 <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="Unteraufgabe_von">
 <option selected>Öffnen Sie dieses Hauptaufgabenmenü</option>
    <?php
 if (count($result1) > 0) {
            foreach ($result1 as $row) {
                $aufgaben = $row["aufgaben"];

                echo '<option value="'.$row["cid"].'">'.$aufgaben.'</option>';
             }
        }
        
         else {
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
                        <input  type="date" class="form-control" id="soll_erledigt_werden" name="soll_erledigt_werden"required>
                        
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
                $vorname = $row["vorname"];
                $name = $row["name"];
                echo "<li class='list-group-item'><input type='checkbox' 
                name='zustaendig[]' class='form-check-input' value='".$row["id"]."'> $vorname $name 
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
         <button class="btn btn-dark" onclick="window.open('index.php','_self')">Abbrechen
         </button>
     </div>

 
 </form>
</div>    

