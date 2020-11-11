<?php
   require_once 'auth_db_functions.php';

    // IF you want to use the Liga system  in a subfolder please add the base path  into the baseURL below
    // e.g.            $baseURL = "/Liga";
   $baseURL = "";

  function isloggedin(){
        if (isset($_SESSION['login_ok'])){
            return($_SESSION['login_ok']="OK");
        }
        else{
            return false;
        }

  }
    
if (!isloggedin()){ 
    //$_SESSION['login_referer']=$_SERVER['SCRIPT_URI'];
    // Debug echo "Sie sind nicht angemeldet!";
    
    if(!checktoken()){
    
        echo "<script language='javascript'>";
        echo "javascript:window.location.href='".$baseURL."/loginform.php';</script>";
        exit; //Egal was passiert, hier ist Schluss
    }
} else if (!checkPendingRegistration()) {
    echo "<script language='javascript'>";
    echo "javascript:window.location.href='".$baseURL."/pending_registration.php';</script>";
    die();
} else if (!isAdmin() && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    echo "<script language='javascript'>";
    echo "javascript:window.location.href='".$baseURL."/index.php';</script>";
    die();
}



function checkPendingRegistration()
{
    return (isset($_SESSION['userGroup']) && !empty($_SESSION['userGroup'])) || $_SERVER['REQUEST_URI'] == "/pending_registration.php";
}

function isAdmin()
{
    return isset($_SESSION['userGroup']) && $_SESSION['userGroup'] == "Admin";
}
?>
