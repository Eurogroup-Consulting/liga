<?php
require_once '../config.php'; // Enthält Session Funktionen und StandardFarben
require_once '../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte

// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once 'lib_comments.php';


$success = false;

if (isset($_POST["action"]) && isset($_POST["comment"]) && $_POST["action"] == "Speichern") {
    $success = updateComments($_POST["comment"]);
}
?>
<!doctype html>
<html>

<head>
    <?php
    include '../layout/header.html';
    ?>

    <title>Liga Administration - Kommentare</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include "../navigation.php"; ?>

    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Kommentare
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/comments.php" method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Teaser</th>
                                    <th>Kommentar</th>
                                    <th>Veröffentlichungsdatum</th>
                                    <th>Löschen</th>
                                </tr>

                                <?php
                                $comments = getComments();

                                $key = 0;
                                foreach ($comments as $key => $comment) {
                                ?>

                                    <tr>
                                        <td class="text-truncate">
                                            <input class="form-control" type="text" name="comment[<?= $key ?>][Teaser]" value="<?= $comment['Teaser'] ?>">
                                            <input type="text" name="comment[<?= $key ?>][ID]" value="<?= $comment['ID'] ?>" hidden>

                                        </td>
                                        <td class="text-truncate">
                                            <textarea class="form-control"  name="comment[<?= $key ?>][Kommentar]" rows="1" ><?= $comment['Kommentar'] ?></textarea>

                                        </td>
                                        <td class="text-truncate">
                                            <input type="date" class="form-control" name="comment[<?= $key ?>][Datum]" value="<?= $comment['Datum'] ?>" />
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input" type="checkbox" name="comment[<?= $key ?>][delete]" value="1">
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="comment[<?= $key + 1 ?>][Teaser]" value="">
                                        <input type="text" class="form-control" name="comment[<?= $key + 1 ?>][add]" value="true" hidden>
                                    </td>
                                    <td class="text-truncate">
                                        <textarea class="form-control" type="text" name="comment[<?= $key + 1 ?>][Kommentar]" rows="1"></textarea>

                                    </td>
                                    <td class="text-truncate">
                                        <input type="date" class="form-control" name="comment[<?= $key + 1 ?>][Datum]" value="" />
                                    </td>
                                    <td class="text-center">
                                        Neu
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                        <?php
                        if ($success === false && isset($_POST["action"])) {
                            echo ('<p class="alert-success">Alle Kommentare wurden erfolgreich aktualisiert</p>');
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