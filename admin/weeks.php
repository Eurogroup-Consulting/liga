<?php
require_once '../config.php';
require_once '../auth.php';
if (!isAdmin()) {
    die();
}
require_once 'weeks_db_functions.php';
$success = false;


if (isset($_POST["action"]) && isset($_POST["week"])  && $_POST["action"] == "Speichern") {
    $success = updateWeeks($_POST["week"]);
}

if (isset($_POST["deleteMak"])) {
    require_once 'maks_db_functions.php';
    $makSuccess = deleteMaksByWeek($_POST["deleteMak"]);
}
if (isset($_POST["deleteData"])) {
    $dataSuccess = deleteDataByWeek($_POST["deleteData"]);
}

if (isset($_GET["season"])) {
    $weeks = getWeeksBySeason($_GET["season"]);
}

?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>

    <title>Liga Administration - Wochen bearbeiten</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>


    <!-- CONTENT -->
    <div class="container">
        <?php if (!isset($weeks)) : ?>
            <div class="card mt-3 ">
                <div class="card-header font-weight-bolder">
                    Keine Wochen gefunden
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <p> Leider wurde keine passenden Wochen gefunden</p>
                    </blockquote>
                </div>
            </div>
        <?php else : ?>
            <div class="card mt-3 ">
                <div class="card-header font-weight-bolder">
                    Saison "<?= $weeks[0]["SaisonBezeichnung"] ?>"
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <form class="form-signin " action="admin/weeks.php?season=<?= $_GET["season"] ?>" method="POST">
                            <p><label class="form-check-label" for="seasonSelect">
                                    <div class="table-responsive">
                                        <table class="table table-bordered " id="teamSelect">
                                            <tr>
                                                <th>Name</th>
                                                <th>Stichtag</th>
                                                <th>MAKs</th>
                                                <th>Datensätze</th>
                                                <th>Entfernen</th>
                                            </tr>
                                            <!-- zeigt eine zusätzliche Zeile an um neue Teams hinzufügen zu können.-->
                                            <?php
                                            foreach ($weeks as $key => $week) {
                                            ?>
                                                <tr>
                                                    <td class="text-truncate">
                                                        <input type="text" class="form-control" name="week[<?= $key ?>][SpielwochenNr]" value="<?= $week['SpielwochenNr'] ?>">
                                                        <input type="text" name="week[<?= $key ?>][ID]" value="<?= $week['ID'] ?>" hidden>
                                                        <input type="text" name="week[<?= $key ?>][seasonID]" value="<?= $_GET['season'] ?>" hidden>
                                                    </td>

                                                    <td>
                                                        <div class='input-group'>
                                                            <input type="date" class="form-control" name="week[<?= $key ?>][Stichtag]" value="<?= $week['Stichtag'] ?>" />
                                                        </div>
                                                    </td>
                                                    
                                                    <td>
                                                        <?=$week["Maks"]?>
                                                        <button class="btn btn-outline-primary btn-sm float-right" name="deleteMak" type="submit" value="<?=$week["ID"]?>">löschen</button>
                                                    </td>
                                                    <td>
                                                        <?=$week["Daten"]?>
                                                        <button class="btn btn-outline-primary btn-sm float-right" name="deleteData" type="submit" value="<?=$week["ID"]?>">leeren</button>
                                                    </td>
                                                    
                                                    

                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" name="week[<?= $key ?>][delete]" value="1">
                                                    </td>

                                                </tr>
                                            <?php
                                            }
                                            ?>

                                            <tr>
                                                <td class="text-truncate">
                                                    <input type="text" class="form-control" name="week[<?= $key + 1 ?>][SpielwochenNr]" value="">
                                                    <input type="text" class="form-control" name="week[<?= $key + 1 ?>][add]" value="true" hidden>
                                                    <input type="text" name="week[<?= $key + 1 ?>][seasonID]" value="<?= $_GET['season'] ?>" hidden>
                                                </td>
                                                <td>
                                                    <div class='input-group'>
                                                        <input type="date" class="form-control" name="week[<?= $key + 1 ?>][Stichtag]" value="" />
                                                    </div>
                                                </td>
                                                <td>
                                                    0
                                                </td>
                                                <td>
                                                    0
                                                </td>
                                                <td class="text-center">
                                                    Neu
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                                    <?php
                                    if ($success === true) {
                                        echo ('<p class="alert-success">Die Spielwochen wurde erfolgreich aktualisiert</p>');
                                    } else if (isset($_POST["action"])) {
                                        echo ('<p class="alert alert-danger">'.$success.'</p>');
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