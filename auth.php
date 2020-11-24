<?php
   require_once 'lib/lib_auth.php';

    // IF you want to use the Liga system  in a subfolder please add the base path  into the baseURL below
    // e.g.            $baseURL = "/Liga";
   $baseURL = "";

//    prüft ob ein login vorhanden ist
  function isloggedin(){
        if (isset($_SESSION['login_ok'])){
            return($_SESSION['login_ok']="OK");
        }
        else{
            return false;
        }

  }
// überprüft ob der user eingeloggt ist und leitet den user weiter
if (!isloggedin()){ 
    
    if(!checktoken()){
        // leitet auf die login eite um, wenn der user nicht eingeloggt ist und nicht registriert ist
        echo "<script language='javascript'>";
        echo "javascript:window.location.href='".$baseURL."/loginform.php';</script>";
        exit; //Egal was passiert, hier ist Schluss
    }
} else if (!checkPendingRegistration()) {
    // leitet auf pending_registration.php um wenn der nutzer noch nicht von einem admin freigeschaltet wurde
    echo "<script language='javascript'>";
    echo "javascript:window.location.href='".$baseURL."/pending_registration.php';</script>";
    die();
} else if (!isAdmin() && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    // leitet auf index.php um, wenn der user kein admin ist aber eine admin seite aufrufen möchte
    echo "<script language='javascript'>";
    echo "javascript:window.location.href='".$baseURL."/index.php';</script>";
    die();
}

// prüft ob ein user von einem admin freigeschaltet wurde
function checkPendingRegistration()
{
    return (isset($_SESSION['userGroup']) && !empty($_SESSION['userGroup'])) || $_SERVER['REQUEST_URI'] == "/pending_registration.php";
}
// prüft ob ein user admin rechte besitzt
function isAdmin()
{
    return isset($_SESSION['userGroup']) && $_SESSION['userGroup'] == "Admin";
}
?>
