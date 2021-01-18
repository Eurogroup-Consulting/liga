<?php
require_once dirname(__FILE__) .'/../config/default.php'; // Enthält Session Funktionen und StandardFarben
require_once dirname(__FILE__) .'/../auth.php'; // Enthält Überprüfungen für Login und Admin Rechte

// für nicht admins ist der Zugang nicht gestattet
if (!isAdmin()) {
    die();
}
require_once dirname(__FILE__) .'/../lib/lib_allocation.php'; // Enthält Zuordnungs-Datenbank-Funktionen 
$success = false;

// Diese Funktion wird nur naah dem Absenden der Formulardaten aufgerufen 
if (isset($_POST["action"]) && isset($_POST["allocations"]) && $_POST["action"] == "Speichern") {
    $success = updateAllocations($_POST["allocations"]);
}
?>
<!doctype html>
<html>

<head>
    <?php
    include dirname(__FILE__). '/../layout/header.html';
    ?>

    <title>Liga Administration - Zuordnung</title>
</head>

<body>
    <!-- NAVIGATION -->
    <?php include dirname(__FILE__). "/../navigation.php"; ?>

    <!-- CONTENT -->
    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Zuordnung verwalten
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <form class="form-signin " action="admin/allocation.php" method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tr>
                                    <th>Kurzbezeichnung</th>
                                    <th>Beschreibung</th>
                                    <th>Kategorie</th>
                                    <th>Pkt. Wert</th>
                                    <th>Löschen</th>
                                </tr>

                                <?php
                                $groups = getGroups();

                                // Erzeugt einmalig die Auswahlliste der bestehenden Zuordnungsgruppen
                                $groupOptions = "";
                                foreach ($groups as $group) {
                                    $groupOptions .= '<option value="' . $group . '"  > ' . $group . ' </option>';
                                }
                                // Erlaubt das Bearbeiten der bestehenden Zuordnungen
                                $key = 0;
                                foreach (getAllocations() as $key => $allocation) {
                                ?>

                                    <tr>
                                        <td class="text-truncate">
                                            <input type="text" class="form-control" name="allocations[<?= $key ?>][Kurzbezeichnung]" value="<?= $allocation['Kurzbezeichnung'] ?>">
                                            <input type="text" name="allocations[<?= $key ?>][id]" value="<?= $allocation['ID'] ?>" hidden>
                                        </td>
                                        <td class="text-truncate">
                                            <input type="text" class="form-control" name="allocations[<?= $key ?>][Beschreibung]" value="<?= $allocation['Beschreibung'] ?>">
                                        </td>

                                        <td>
                                            <select class="custom-select" name="allocations[<?= $key ?>][Gruppe]" value="<?= $allocation["Gruppe"] ?>">
                                                <?= $groupOptions ?>
                                            </select>

                                        </td>
                                        <td class="text-truncate">
                                            <input type="text" class="form-control" name="allocations[<?= $key ?>][Punktewert]" value="<?= $allocation['Punktewert'] ?>">
                                        </td>
                                        <td class="text-center">
                                            <input class="custom-checkbox" type="checkbox" name="allocations[<?= $key ?>][delete]" value="1">
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <!-- erlaubt das Anlegen einer neuen Zuordnung -->
                                <tr>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="allocations[<?= $key + 1 ?>][Kurzbezeichnung]" value="">
                                        <input type="text" name="allocations[<?= $key + 1 ?>][add]" value="true" hidden>
                                    </td>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="allocations[<?= $key + 1 ?>][Beschreibung]" value="">
                                    </td>

                                    <td>
                                        <select class="custom-select" name="allocations[<?= $key + 1 ?>][Gruppe]" value="">
                                            <option value="0" selected>Nicht zugewiesen</option>

                                            <?= $groupOptions ?>
                                        </select>
                                    </td>
                                    <td class="text-truncate">
                                        <input type="text" class="form-control" name="allocations[<?= $key + 1 ?>][Punktewert]" value="">
                                    </td>
                                    <td class="text-center">
                                        Neu
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" name="action" type="submit" value="Speichern">Speichern</button>
                        <?php
                        if ($success) {
                            echo ('<p class="alert-success">Alle Zuordnungen wurden erfolgreich aktualisiert</p>');
                        } else if (isset($_POST["action"])) {
                            echo ('<p class="alert alert-danger">Leider war das aktualisieren der Zuordnungen nicht erfolgreich.</p>');
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