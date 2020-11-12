<?php
require_once '../config.php'; // Enthält Session Funktionen und StandardFarben
require_once '../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte
if (!isAdmin()) {
    die();
}
require_once 'leagues_db_functions.php';
require_once 'seasons_db_functions.php';
require_once 'teams_db_functions.php';
require_once 'team_division_db_functions.php';
$success = false;


if (isset($_GET["leagueid"])) {
    $league = getLeague($_GET["leagueid"]);
}

if (isset($_POST["action"]) && isset($_POST["AktSaisonID"]) && isset($_POST["team"])  && $_POST["action"] == "Speichern") {
    $success = updateTeamDivision($_GET["leagueid"], $_POST["AktSaisonID"], $_POST["team"]);
}
?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>

    <title>Liga Administration - Liga bearbeiten</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>


    <!-- CONTENT -->
    <div class="container">
        <?php if (!isset($league)) : ?>
            <div class="card mt-3 ">
                <div class="card-header font-weight-bolder">
                    Keine Liga gefunden
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <p> Leider wurde keine passende Liga gefunden</p>
                    </blockquote>
                </div>
            </div>
        <?php else : ?>
            <div class="card mt-3 ">
                <div class="card-header font-weight-bolder">
                    <?= $league["LigaName"] ?>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <form class="form-signin " action="admin/edit_league.php?leagueid=<?= $_GET["leagueid"] ?>" method="POST">
                            <p><label class="form-check-label" for="seasonSelect">
                                    Teams für Saison
                                </label>
                                <!-- zeigt eine Dropdown für alle Saisons an. Durch diese wird die Teamliste dynamisch erzeugt-->
                                <select class="custom-select" id="seasonSelect" name="AktSaisonID">
                                    <?php
                                    foreach (getSeasons() as $key => $season) {
                                        if ($_POST["AktSaisonID"]) {
                                            $selected = $_POST["AktSaisonID"] == $season["ID"] ? "selected" : "";
                                        } else {
                                            $selected = $league["AktSaisonID"] == $season["ID"] ? "selected" : "";
                                        }
                                        echo ('<option value="' . $season["ID"] . '" ' . $selected . '  >' . $season["SaisonBezeichnung"] . '</option>');
                                    }
                                    ?>
                                </select>
                            </p>
                            <?php
                            $teams = getTeams();
                            $teamsDivision = getTeamsDivisionByLeague($_GET["leagueid"]);
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered " id="teamSelect">
                                    <tr>
                                        <th>Team</th>
                                        <th>Entfernen</th>
                                    </tr>
                                    <!-- zeigt eine zusätzliche Zeile an um neue Teams hinzufügen zu können.-->
                                    <tr>
                                        <td class="text-truncate">
                                            <input type="text" class="form-control" name="team[new][add]" value="true" hidden>
                                            <select class="custom-select" name="team[new][id]">
                                                <option value="-1">Team hinzufügen</option>
                                                <?php
                                                foreach ($teams as $key => $team) {
                                                    echo ('<option value="' . $team["ID"] . '" >' . $team["Teamname"] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                            <?php
                            if ($success === true) {
                                echo ('<p class="alert-success">Die Teameinteilung wurde erfolgreich aktualisiert</p>');
                            } else if (isset($_POST["action"])) {
                                echo ('<p class="alert alert-danger">Leider war das Aktualisieren der Teameinteilung nicht erfolgreich.</p>');
                            }
                            ?>
                        </form>
                    </blockquote>
                </div>
            </div>
        <?php endif ?>
    </div>
</body>
<footer>
    <?php
    include '../layout/footer.html';
    ?>

    <?php if (isset($teamsDivision) && isset($teams)) : ?>

        <script type="text/javascript">
            // Vorbereitung der JS variablen 
            var teams = <?= json_encode($teams) ?>;
            var teamsDivision = <?= json_encode($teamsDivision) ?>;
            var teamoptions = "";

            // Vorbereitung der gesammelten Teamliste
            $.each(teams,
                function(k, team) {
                    teamoptions += "<option value='" + team.ID + "'>" + team.Teamname + "</option>";
                });
            // initiale Teamliste erzeugen 
            $(document).ready(function() {
                setTeamList();
            })

            // Beim Ändern der aktuellen Saison werden die entsprechenden Teams angezeigt
            $("#seasonSelect").change(function() {
                setTeamList();
            });

            function setTeamList() {
                var selectedSeason = $("#seasonSelect").children("option:selected").val()
                // Leert die Teams Tabelle um anschließend nur die aktuellen Daten anzuzeigen
                $("#teamSelect").children('tr').remove()
                $.each(teamsDivision,
                    function(key, team) {
                        // zeige soviele Dropdowns an, wie der Liga &Saison zugeordnet sind
                        if (team.SaisonID == selectedSeason) {
                            $("#teamSelect").append(`
                                <tr>
                                    <td class="text-truncate">
                                        <select class="custom-select" name="team[` + team.ID + `][id]" id="league_` + team.ID + `">
                                            ` + teamoptions + `
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="team[` + team.ID + `][delete]" value="1">
                                    </td>
                                </tr>
                                `);
                            // wähle für jede Dropdown das aktive Team aus
                            $("#league_" + team.ID).val(team.TeamID);
                        }
                    }
                );
            }
        </script>
    <?php endif ?>
</footer>

</html>
