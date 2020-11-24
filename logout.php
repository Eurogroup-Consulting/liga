<?php 
    require_once dirname(__FILE__). '/config/default.php'; // Liga-Standard immer einbinden !!
    require_once dirname(__FILE__). '/lib/lib_auth.php'; // enthält PHP Liga-Login-Funktion

    liga_logout();
?>

<!doctype html>
<html>
<head>
<?php    include dirname(__FILE__). '/layout/header.html';    ?>

    <title>Liga Abmeldung</title>
</head>



<!-- Custom styles for this template -->
<link href="./assets/css/liga_signin.css" rel="stylesheet">
<body class="text-center">
        
            <div class="form-logout">
                <img class="mb-4" src="./assets/images/LigaLogo60r.png" alt="" width="60" height="60">
                <h1 class="h3 mb-3 font-weight-normal">Sie wurden abgemeldet</h1>
                <a href="./index.php" class="btn btn-lg btn-primary btn-block">Erneut Starten</a>
            </div>
            
    </body>
</html>
