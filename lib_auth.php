
<?php 
require_once 'lib/lib_liga.php';

// prüft ob für den user ein token hinterlegt ist und dieser übereinstimmt
function checktoken(){
    
    $token=isset($_COOKIE["LigaToken"])?$_COOKIE["LigaToken"]:"";
    
    //Nur wenn token Variablen gesetzt sind 
    if ($token<>"" ){
        $msql=  db_connect();
        
        $qryUser="SELECT email, user_id, teamid, userGroup FROM user WHERE token='$token'";
        $stmnt=$msql->prepare($qryUser);
        $stmnt->execute();
        $stmnt->bind_result($email, $uid, $teamid, $userGroup);
        $stmnt->store_result();
        $stmnt->fetch();
        
        if ($stmnt->num_rows==1){                                
            // setzt die benötigten session daten
            $maxSaisonID=max_saison();
            $_SESSION['login_ok']="OK"; 
            $_SESSION['userid']=$uid;
            $_SESSION['userLiga']=getUserLiga($maxSaisonID,$teamid);
            $_SESSION['userGroup']=$userGroup;
            $_SESSION['saison']=$maxSaisonID;
            $_SESSION['teamname']="";
            $_SESSION['teamid']=$teamid;

            return true;            
            
            }else{
            return false ;   
        }
        
        $msql->close();
    }
}

// login für bereits existierende nutzer
function liga_login($login, $pwd) {
    $msql=  db_connect();
    
    $maxSaisonID=max_saison(); //Definiert in lib/lib_liga
    
    $qryLogin="SELECT pwdhash, user_id , TeamID, userGroup ,token from account WHERE email=?";
    $stmnt=$msql->prepare($qryLogin);
    $stmnt->bind_param('s',$login);
    $stmnt->execute();
    $stmnt->bind_result($pwd5, $uid, $teamid, $userGroup, $token);
    $stmnt->fetch();

    if ($pwd5==md5($pwd)){
        // setzt die benötigten session daten
        $_SESSION['login_ok']="OK"; 
        $_SESSION['userid']=$uid;
        $_SESSION['userLiga']=getUserLiga($maxSaisonID,$teamid);
        $_SESSION['userGroup']=$userGroup;
        $_SESSION['saison']=$maxSaisonID;
        $_SESSION['teamname']="";
        $_SESSION['teamid']=$teamid;
    
        return true;
    }
    else{
        return false;
    }
}

// erlaubt es neuen nutzern sich zu registrieren
function liga_register($email, $pwd)
{
    $md5pwd = md5($pwd);
    $msql =  db_connect();
    $qryRegister = "INSERT IGNORE INTO `account`( `email`, `pwdhash`) VALUES (?,?)";
    $stmnt = $msql->prepare($qryRegister);
    $stmnt->bind_param('ss', $email, $md5pwd);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}

// erzeugt einen eindeutigen token und füht ihn in die datenbank ein
function set_token($login) {
       
    $token=uniqid('',true); //Besserer (eindeutigerer) Token
    
    $msql=db_connect();
    $qryLogin="UPDATE account SET token=? WHERE email='$login'";
    $stmnt=$msql->prepare($qryLogin);
    $stmnt->bind_param('s',$token);
    $stmnt->execute();
    
    if ($stmnt->affected_rows==1){
        setcookie("LigaToken",$token,time()+30);
        return $token;
    }
    else{
        return "";
   }
}

// logt einen user aus 
function liga_logout(){
     session_destroy();
     setcookie("LigaToken","");
}
