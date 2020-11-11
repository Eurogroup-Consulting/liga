<?php
require_once 'config.php';
require_once 'auth.php';
?>
<!doctype html>
<html>

<head>
    <?php include 'layout/header.html';    ?>
    <title>Liga Home</title>
</head>

<body>

    <?php include "navigation.php"; ?>

    <div class="container">
        <div class="card mt-3 ">
            <div class="card-header font-weight-bolder">
                Bitte haben Sie Geduld
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <p> Ihre Registrierung wurde noch nicht bestätigt.
                        <br>
                        Bitte haben Sie etwas Geduld.</p>
                </blockquote>
            </div>
        </div>
    </div>
    <footer>
        <?php include 'layout/footer.html';      ?>
    </footer>
</body>

</html>