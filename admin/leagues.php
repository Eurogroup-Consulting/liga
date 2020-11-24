<?php
require_once '../config/default.php'; // Enthält Session Funktionen und StandardFarben
require_once '../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte
// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once 'lib_leagues.php';
require_once 'lib_seasons.php';
$success = false;

if (isset($_POST["action"]) && isset($_POST["league"]) && $_POST["action"] == "Speichern") {
    $success = updateLeagues($_POST["league"]);
}
?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>

    <title>Liga Administration - Ligen</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>

    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Ligen
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/leagues.php" method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Name</th>
                                    <th>Aktive Saison</th>
                                    <th>Kommentar</th>
                                    <th>Löschen</th>
                                    <th>Teams</th>
                                </tr>

                                <?php
                                $seasons = getSeasons();


                                foreach (getLeagues() as $key => $league) {
                                ?>

                                    <tr>
                                        <td class="text-truncate">
                                            <input class="form-control" type="text" name="league[<?= $key ?>][LigaName]" value="<?= $league['LigaName'] ?>">
                                            <input type="text" name="league[<?= $key ?>][ID]" value="<?= $league['ID'] ?>" hidden>

                                        </td>
                                        <td class="text-truncate">

                                            <select class="custom-select" name="league[<?= $key ?>][AktSaisonID]">
                                                <option value="0" selected>Nicht zugewiesen</option>
                                                <?php
                                                foreach ($seasons as $season) {
                                                    $selected = $league["AktSaisonID"] == $season["ID"] ? "selected" : "";
                                                    echo ('<option value="' . $season["ID"] . '" ' . $selected . '  >' . $season["SaisonBezeichnung"] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="text-truncate">
                                            <input class="form-control" type="text" name="league[<?= $key  ?>][Kommentar]" value="<?= $league['Kommentar'] ?>">
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input" type="checkbox" name="league[<?= $key ?>][delete]" value="1">
                                        </td>
                                        <td class="text-center">
                                            <a href="admin/edit_league.php?leagueid=<?= $league['ID'] ?>" class="btn btn-link" role="button">anzeigen</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="league[<?= $key + 1 ?>][LigaName]" value="">
                                        <input type="text" class="form-control" name="league[<?= $key + 1 ?>][add]" value="true" hidden>
                                    </td>
                                    <td class="text-truncate">

                                        <select class="custom-select" name="league[<?= $key + 1 ?>][AktSaisonID]">
                                            <option value="0" selected>Nicht zugewiesen</option>
                                            <?php
                                            foreach ($seasons as $season) {
                                                echo ('<option value="' . $season["ID"] . '"  >' . $season["SaisonBezeichnung"] . '</option>');
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="text-truncate">
                                        <input class="form-control" type="text" name="league[<?= $key + 1 ?>][Kommentar]" value="">
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
                            echo ('<p class="alert-success">Alle Ligen wurden erfolgreich aktualisiert</p>');
                        } else if (isset($_POST["action"])) {
                            echo ('<p class="alert alert-danger">' . $success . '</p>');
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