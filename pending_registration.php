<?php
require_once dirname(__FILE__). '/config/default.php';
require_once dirname(__FILE__). '/auth.php';
?>
<!doctype html>
<html>

<head>
    <?php include dirname(__FILE__). '/layout/header.html';    ?>
    <title>Liga Home</title>
</head>

<body>

    <?php include dirname(__FILE__). "/navigation.php"; ?>

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
        <?php include dirname(__FILE__). '/layout/footer.html';      ?>
    </footer>
</body>

</html>