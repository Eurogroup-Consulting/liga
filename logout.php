<?php 
    require_once 'config.php'; // Liga-Standard immer einbinden !!
    require_once 'auth_db_functions.php'; // enthält PHP Liga-Login-Funktion

    liga_logout();
?>

<!doctype html>
<html>
<head>
<?php    include 'layout/header.html';    ?>

    <title>Liga Abmeldung</title>
</head>



<!-- Custom styles for this template -->
<link href="./css/liga_signin.css" rel="stylesheet">
<body class="text-center">
        
            <div class="form-logout">
                <img class="mb-4" src="./images/LigaLogo60r.png" alt="" width="60" height="60">
                <h1 class="h3 mb-3 font-weight-normal">Sie wurden abgemeldet</h1>
                <a href="./index.php" class="btn btn-lg btn-primary btn-block">Erneut Starten</a>
            </div>
            
    </body>
</html>
