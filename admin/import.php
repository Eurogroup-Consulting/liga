<?php
require_once '../config/default.php'; // Enthält Session Funktionen und StandardFarben
require_once '../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte
// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once '../lib/lib_import.php';; // Enthält Punkte-Import-Datenbank-Funktionen 
require_once '../lib/lib_seasons.php'; // Enthält Saison-Datenbank-Funktionen
require_once '../lib/lib_maks.php'; // Enthält MAK-Datenbank-Funktionen (inklusive der Spielwochen)
require_once '../../lib/lib_liga.php'; // Enthält Grundlegenden Datenbank Funktionen

if (isset($_POST["action"]) && isset($_FILES["csvFile"]) && !empty($_FILES["csvFile"]["tmp_name"]) && $_POST["week"] && $_POST["action"] == "Speichern") {
    $errors = importCSV($_FILES["csvFile"]["tmp_name"], $_POST["week"]);
}

$seasons = getSeasons();
$activeSeason = isset($_POST['season']) ? $_POST['season'] : max_saison();

$weeks = getMatchWeeksBySaisonId($activeSeason);
$activeWeek = isset($_POST['week']) ? $_POST['week'] : '';
?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>
    <title>Liga Administration - CSV Import</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>

    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Datenimport per CSV
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/import.php" method="POST" enctype="multipart/form-data">
                        <p><label class="form-check-label" for="seasonSelect">
                                Saison wählen
                            </label>
                            <select class="custom-select" id="seasonSelect" name="season" onchange="this.form.submit()">
                                <?php
                                foreach ($seasons as $key => $season) {
                                    $selected = $activeSeason == $season["ID"] ? "selected" : "";
                                    echo ('<option value="' . $season["ID"] . '"  ' . $selected . '   >' . $season["SaisonBezeichnung"] . '</option>');
                                }
                                ?>
                            </select>
                        </p>

                        <p><label class="form-check-label" for="weekSelect">
                                Spielwoche wählen
                            </label>
                            <select class="custom-select" id="weekSelect" name="week">
                                <?php
                                foreach ($weeks as $key => $week) {
                                    $selected = $activeWeek == $week["ID"] ? "selected" : "";
                                    echo ('<option value="' . $week["ID"] . '"  ' . $selected . ' >' . $week["SpielwochenNr"] . '</option>');
                                }
                                ?>
                            </select>
                        </p>
     
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <a href="assets/csv/Liga_Vorlage.csv" class="btn btn-outline-secondary"  download role="button">Vorlage herunterladen</a>
                            </div>
 
                            <div class="custom-file">
                                <!-- Erlaubt den Datei Upload für CSV Dateien -->
                                <input type="file" class="custom-file-input" id="csvFile" name="csvFile" accept=".csv">
                                <label class="custom-file-label" for="csvFile">CSV auswählen</label>
                            </div>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                        <?php
                        if (isset($errors) && !$errors) {
                            echo ('<p class="alert-success">Alle Nutzer wurden erfolgreich aktualisiert</p>');
                        } else if (isset($_POST["action"]) && isset($errors)) {
                            // Jeder Fehler wird einzeln ausgegeben
                            foreach ($errors as $key => $value) {
                                echo ('<p class="alert alert-danger">Leider war das Hinzufügen von Team "' . $value["Team"] . '" mit ' . $value["Anzahl"] . ' mal "' . $value["Zuordnung"] . '"  nicht erfolgreich.</p>');
                            }
                        }
                        ?>
                    </form>
                </blockquote>
            </div>
        </div>
    </div>
</body>
<footer>
    <?php
    include '../layout/footer.html';
    ?>
</footer>

</html>