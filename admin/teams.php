<?php
require_once '../config.php';
require_once '../auth.php';
if (!isAdmin()) {
    die();
}
require_once 'teams_db_functions.php';
$success = false;
if (isset($_POST["action"]) && isset($_POST["teams"]) && $_POST["action"] == "Speichern") {
    $success = updateTeams($_POST["teams"]);
}
?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>

    <title>Liga Administration - User</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>

    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Teams
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/teams.php" method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Teamname</th>
                                    <th>Löschen</th>
                                </tr>

                                <?php
                                foreach (getTeams() as $key => $team) {
                                ?>

                                    <tr>
                                        <td class="text-truncate">
                                            <input class="form-control" type="text" name="teams[<?= $key ?>][Teamname]" value="<?= $team['Teamname'] ?>">

                                            <input type="text" name="teams[<?= $key ?>][ID]" value="<?= $team['ID'] ?>" hidden>
                                        </td>
                                        <td class="text-center">
                                            <input class="custom-checkbox" type="checkbox" name="teams[<?= $key ?>][delete]" value="1">
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>

                                <tr>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="teams[<?= $key + 1 ?>][add]" value="true" hidden>
                                        <input class="form-control" type="text" name="teams[<?= $key + 1 ?>][Teamname]" value="">
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
                            echo ('<p class="alert-success">Alle Teams wurden erfolgreich aktualisiert</p>');
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