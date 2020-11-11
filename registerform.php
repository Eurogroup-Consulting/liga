<?php
require_once 'config.php'; // Liga-Standard immer einbinden !!
require_once 'auth_db_functions.php'; // enthält PHP Liga-register-Funktion
?>

<!doctype html>
<html>

<head>
<?php    include 'layout/header.html';    ?>

    <title>Liga Registrierung</title>
</head>
<?php
$email = "";
$password = "";
$registersucess = FALSE;
if (isset($_POST['registername'])) {
    $email = $_POST['registername'];
    $password = $_POST['password'];
    if (liga_register($email, $password)) {
        $registersucess = TRUE;
    } else {
        $registersucess = FALSE;
    }
}
?>

<!-- Custom styles for this template -->
<link href="./css/liga_signin.css" rel="stylesheet">

<body class="text-center">
    <form class="form-signin" action="./registerform.php" method="POST">
        <img class="mb-4" src="./images/LigaLogo60r.png" alt="" width="60" height="60">
        <h1 class="h3 mb-3 font-weight-normal">Liga-Registrierung</h1>
        <label class="sr-only">E-Mail:</label>
        <input class="form-control" name="registername" type="email" placeholder="Email-Addresse" value="<?php echo $email ?>" required autofocus>
        <label class="sr-only">Passwort:</label>
        <input class="form-control" name="password" type="password" placeholder="Passwort" value="<?php echo $password ?>" required>

        <div class="mb-3 ">
            <a href="loginform.php">Zurück zur Anmeldung
            </a>
        </div>
        <button class="btn btn-lg btn-primary btn-block" name="OK" type="submit" value="Registrieren">Registrieren</button>

        <?php
        if ($registersucess) {
            echo ('<p class="alert-success">Vielen Dank für Ihre Registrierung! <br>
            Sie werden sofort weitergeleitet</p>');
            header("refresh:3;url=loginform.php");
            die();
        }
        if ($email != "" && $registersucess == FALSE) {
            echo ('<p class="alert alert-danger">Leider ist bei Ihrer Registrirung etwas schief gelaufen.</p>');
        }
        ?>

    </form>

</body>

</html>