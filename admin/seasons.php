<?php
require_once dirname(__FILE__) .'/../config/default.php'; // Enthält Session Funktionen und StandardFarben
require_once dirname(__FILE__) .'/../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte
// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once dirname(__FILE__) .'/../lib/lib_seasons.php';
$success = false;
if (isset($_POST["action"]) && isset($_POST["season"]) && $_POST["action"] == "Speichern") {
    $success = updateSeasons($_POST["season"]);
}
?>
<!doctype html>
<html>

<head>
    <?php
    include dirname(__FILE__). '/../layout/header.html';
    ?>

    <title>Liga Administration - Saisons</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include dirname(__FILE__). "/../navigation.php"; ?>

    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Saisons
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/seasons.php" method="POST">
                        <div class="table-responsive form-group">
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Saison Bezeichnung</th>
                                    <th>Beginn</th>
                                    <th>Ende</th>
                                    <th>Löschen</th>
                                    <th>Wochen</th>
                                </tr>

                                <?php
                                $seasons = getSeasons();
                                $key = 0;
                                foreach ($seasons as $key => $season) {
                                ?>
                                    <tr>
                                        <td class="text-truncate">
                                            <input type="text" class="form-control" name="season[<?= $key ?>][SaisonBezeichnung]" value="<?= $season['SaisonBezeichnung'] ?>">
                                            <input type="text" name="season[<?= $key ?>][ID]" value="<?= $season['ID'] ?>" hidden>
                                        </td>

                                        <td>
                                            <div class='input-group'>
                                                <input type="date" class="form-control" name="season[<?= $key ?>][SaisonBegin]" value="<?= $season['SaisonBegin'] ?>" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class='input-group'>
                                                <input class="form-control" type="date" name="season[<?= $key ?>][SaisonEnde]" value="<?= $season['SaisonEnde'] ?>" />
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input" type="checkbox" name="season[<?= $key ?>][delete]" value="1">
                                        </td>
                                        <td class="text-center">
                                            <a href="admin/weeks.php?season=<?=$season['ID']?>" class="btn btn-link" role="button">anzeigen</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>

                                <tr>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="season[<?= $key + 1 ?>][SaisonBezeichnung]" value="">
                                        <input type="text" class="form-control" name="season[<?= $key + 1 ?>][add]" value="true" hidden>
                                    </td>
                                    <td>
                                        <div class='input-group'>
                                            <input type="date" class="form-control" name="season[<?= $key + 1 ?>][SaisonBegin]" value="" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class='input-group'>
                                            <input type="date" class="form-control" name="season[<?= $key + 1 ?>][SaisonEnde]" value="" />
                                        </div>

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
                            echo ('<p class="alert-success">Alle Saisons wurden erfolgreich aktualisiert</p>');
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
    include dirname(__FILE__). '/../layout/footer.html';
    ?>
</footer>

</html>