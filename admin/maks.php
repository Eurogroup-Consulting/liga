<?php
require_once '../config.php'; // Enthält Session Funktionen und StandardFarben
require_once '../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte
// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once 'lib_seasons.php'; // Enthält Saison-Datenbank-Funktionen 
require_once 'lib_team_division.php'; // Enthält Teameinteilungs-Datenbank-Funktionen
require_once 'lib_maks.php'; // Enthält MAK-Datenbank-Funktionen
$success = false;

// Diese Funktion wird nur nach dem Absenden der Formulardaten aufgerufen 
if (isset($_POST["action"]) && isset($_POST["MatchWeekID"]) && isset($_POST["mak"]) && $_POST["action"] == "Speichern") {
    $success = updateMAKs($_POST["MatchWeekID"], $_POST["mak"], $_POST["AktSaisonID"]);
}

// Setzt alle benötigten variablen
$seasons = getSeasons();
$weeks = getMatchWeeks();
$maks = getMAKsWithTeamName();
?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>

    <title>Liga Administration - MAK</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>


    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                MAKs verwalten
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/maks.php" method="POST">
                        <p><label class="form-check-label" for="seasonSelect">
                                Saison wählen
                            </label>
                            <select class="custom-select" id="seasonSelect" name="AktSaisonID">
                                <?php
                                foreach ($seasons as $key => $season) {
                                    if ($_POST["AktSaisonID"]) {
                                        $selected = $_POST["AktSaisonID"] == $season["ID"] ? "selected" : "";
                                    }
                                    echo ('<option value="' . $season["ID"] . '" ' . $selected . '  >' . $season["SaisonBezeichnung"] . '</option>');
                                }
                                ?>
                            </select>
                        </p>
                        <p><label class="form-check-label" for="weekSelect">
                                Spielwoche wählen
                            </label>
                            <select class="custom-select" id="weekSelect" name="MatchWeekID">
                                <?php
                                foreach ($weeks as $key => $week) {
                                    if ($_POST["MatchWeekID"]) {
                                        $selected = $_POST["MatchWeekID"] == $week["ID"] ? "selected" : "";
                                    }
                                    echo ('<option value="' . $week["ID"] . '" ' . $selected . '  >' . $week["SpielwochenNr"] . '</option>');
                                }
                                ?>
                            </select>
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="teamTable">
                                <tr>
                                    <th>Team</th>
                                    <th>MAK</th>
                                    <th>Für Zukunft übernehmen</th>
                                </tr>
                            </table>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                        <?php
                        if ($success === true) {
                            echo ('<p class="alert-success">Die MAKs wurde erfolgreich aktualisiert</p>');
                        } else if (isset($_POST["action"])) {
                            echo ('<p class="alert alert-danger">Leider war das Aktualisieren der MAKs nicht erfolgreich.</p>');
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

    <?php if (isset($weeks) && isset($maks)) : ?>

        <script type="text/javascript">
            // Vorbereitung der JS variablen 
            var weeks = <?= json_encode($weeks) ?>;
            var maks = <?= json_encode($maks) ?>;

            // füllt die Spielwochenauswahl mit den zur Saison gehörigen Spielwochen
            function setWeekDropdown(season, week) {
                $("#weekSelect").find('option').remove();
                $.each($.grep(weeks, w => w.SaisonID == season),
                    function(k, week) {
                        $("#weekSelect").append("<option value='" + week.ID + "'>" + week.SpielwochenNr + "</option>")
                    });

                // falls eine Spielwoche gerade geupdatet wurde, soll die entsprechende Woche beim neu laden initial ausgewählt werden
                if (week) {
                    $("#weekSelect").val(week);

                }
            }
            // initiale Teamliste erzeugen inkl. eventueller Vorauswahl
            $(document).ready(function() {
                setWeekDropdown($("#seasonSelect").children("option:selected").val(), <?= isset($_POST["MatchWeekID"]) ? $_POST["MatchWeekID"] : '' ?>);
                setTeamList();
            })

            // Beim Ändern der aktuellen Saison werden die entsprechenden Wochen und Teams angezeigt
            $("#seasonSelect").change(function() {
                setWeekDropdown($("#seasonSelect").children("option:selected").val());
                setTeamList();
            });

            // Beim Ändern der aktuellen Woche werden die entsprechenden Teams angezeigt
            $("#weekSelect").change(function() {
                setTeamList();
            });

            // erzeugt die Teamliste mit MAKs und Übernahmemöglichkeit
            function setTeamList() {
                var selectedSeason = $("#seasonSelect").children("option:selected").val()
                var selectedWeek = $("#weekSelect").children("option:selected").val()
                // Filtert die MAK Liste und enthält nur die MAKs der entsprechenden Woche
                var makForWeek = $.grep(maks, m => m.SpielWochenID == selectedWeek);
                // Leert die Teams Tabelle um anschließend nur die aktuellen Daten anzuzeigen
                $("#teamTable").children('tr').remove()
                $.each(makForWeek,
                    function(key, mak) {
                        // zeige alle Teams an, welche der Saison zugeordnet sind
                        $("#teamTable").append(`
                            <tr>
                                <td>
                                ` + mak.Teamname + `
                                </td>    
                                <td class="text-center">
                                    <input type="text" class="form-control" name="mak[` + key + `][mak]" value="` + mak.MAK + `">
                                    <input type="text" name="mak[` + key + `][teamID]" value="` + mak.TeamID + `" hidden>
                                </td>
                                <td class="text-center">
                                <input class="form-check-input" type="checkbox" name="mak[` + key + `][adopt]" value="1">

                                </td>
                            </tr>
                            `);
                    });
            }
        </script>
    <?php endif ?>
</footer>

</html>