<?php
require_once '../config.php'; // Enthält Session Funktionen und StandardFarben
require_once '../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte
// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once 'lib_user.php';
require_once 'lib_teams.php';
$success = false;
if (isset($_POST["action"]) && isset($_POST["users"]) && $_POST["action"] == "Speichern") {
    $success = updateUsers($_POST["users"]);
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
                Angemeldete Benutzer
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/users.php" method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Benutzer</th>
                                    <th>Benutzer-Gruppe</th>
                                    <th>Team</th>
                                    <th>Löschen</th>
                                </tr>

                                <?php
                                $teams = getTeams();


                                foreach (getUsers() as $key => $user) {
                                ?>

                                    <tr>
                                        <td class="text-truncate">
                                            <?= $user['email'] ?>
                                            <input type="text" name="users[<?= $key ?>][userId]" value="<?= $user['user_id'] ?>" hidden>
                                        </td>
                                        <td>
                                            <select class="custom-select" name="users[<?= $key ?>][userGroup]">
                                                <option value="" selected>Nicht freigeschaltet</option>
                                                <option value="Admin" <?= $user["userGroup"] == "Admin" ? "selected" : "" ?>>Admin</option>
                                                <option value="User" <?= $user["userGroup"] == "User" ? "selected" : "" ?>>User</option>
                                            </select>
                                        </td>
                                        <td>

                                            <select class="custom-select" name="users[<?= $key ?>][TeamID]">
                                                <option value="0" selected>Nicht zugewiesen</option>
                                                <?php
                                                foreach ($teams as $team) {
                                                    $selected = $user["TeamID"] == $team["ID"] ? "selected" : "";
                                                    echo ('<option value="' . $team["ID"] . '" ' . $selected . '  >' . $team["Teamname"] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <input class="custom-checkbox" type="checkbox" name="users[<?= $key ?>][delete]" value="1">
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                        <?php
                        if ($success) {
                            echo ('<p class="alert-success">Alle Nutzer wurden erfolgreich aktualisiert</p>');
                        } else if (isset($_POST["action"])) {
                            echo ('<p class="alert alert-danger">Leider war das aktualisieren der Benutzer nicht erfolgreich.</p>');
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