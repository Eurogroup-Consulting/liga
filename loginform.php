<?php 
    require_once dirname(__FILE__). '/config/default.php'; // Liga-Standard immer einbinden !!
    require_once dirname(__FILE__). '/lib/lib_auth.php'; // enthält PHP Liga-Login-Funktion
?>

<!doctype html>
<html>
<head>
<?php    include dirname(__FILE__). '/layout/header.html';    ?>

    <title>Liga Login</title>
</head>
<?php 
    $email="";
    $password="";
    $loginsucess=FALSE;

    if(isset($_POST['loginname'])){
        $email=$_POST['loginname'];
        $password=$_POST['password'];
        
        if (liga_login($email, $password)){
            if (isset($_POST['remember'])) set_token($email);
            $loginsucess=TRUE;
        }else{

            $loginsucess=FALSE;
        }
    } else {
         //Beim Aufruf ohne Anmeldung Herkunft(URL) in der Session speichern   
         $_SESSION['return_to'] = isset($_SERVER['HTTP_REFERER']) 
            && strpos($_SERVER['HTTP_REFERER'], 'registerform') == false 
            && strpos($_SERVER['HTTP_REFERER'], 'loginform') == false
            ? $_SERVER['HTTP_REFERER'] : "./index.php";
    }
?>

<!-- Custom styles for this template -->
<link href="./assets/css/liga_signin.css" rel="stylesheet">
<body class="text-center">
        <form class="form-signin" action="./loginform.php" method="POST">
            <img class="mb-4" src="./assets/images/LigaLogo60r.png" alt="" width="60" height="60">
            <h1 class="h3 mb-3 font-weight-normal">Liga-Login</h1>
            <label class="sr-only">E-Mail:</label>
            <input class="form-control" name="loginname" type="email" placeholder="Email-Addresse" value="<?php echo $email?>" required autofocus>
            <label class="sr-only">Passwort:</label>
            <input class="form-control" name="password" type="password" placeholder="Passwort" value="<?php echo $password?>" required>
            <div class="checkbox mb-3"> 
                <label>
                    <input name="remember" type="checkbox" value="yes"> angemeldet bleiben 
                </label>
            </div>
            <div class="mb-3">
                <a href="registerform.php">Registrieren
                </a>
            </div>
            <button class="btn btn-lg btn-primary btn-block"  name="OK" type="submit" value="Anmelden">Anmelden</button>
    
                <?php            
                 if ($loginsucess){
                    echo("<script language='javascript'>javascript:window.location.href='" . $_SESSION['return_to'] .  "';</script>");  
                }
                if ($email!="" && $loginsucess==FALSE){
                    echo ('<p class="alert alert-danger">Benutzername oder Passwort falsch</p>');
                }
                ?>

        </form>

    </body>
</html>
