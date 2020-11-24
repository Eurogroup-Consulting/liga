<?php
    require_once 'config/default.php';
    require_once 'auth.php';
    require_once 'lib/lib_liga.php';

// lädt die punkte aller konkurierenden Teams innerhalb dieser liga
function getOtherTeams($TeamID,$LigaID, $kum){
    $mySqlConnection=  db_connect(); 
    if($kum){
        $qryString_2=QUERY_DETAILS_KONKURENZ_KUM;
    } else {
        $qryString_2=QUERY_DETAILS_KONKURENZ;
    }
    $stmnt_2=$mySqlConnection->prepare($qryString_2); 
    $stmnt_2->bind_param('ii',$TeamID,$LigaID);
    $stmnt_2->execute();
    $res = $stmnt_2->get_result();
    $other = $res->fetch_all(MYSQLI_ASSOC);

    foreach ($other as $val) {
        $keyedOther[$val["Kurzbezeichnung"]] = $val["Punkte"];
    }
    return $keyedOther;   
}
// lädt die punkte des eigenen Teams innerhalb dieser liga
function getTeamDetails($TeamID,$LigaID,$kum){
    $mySqlConnection=  db_connect(); 
    if($kum){
        $qryString=QUERY_DETAILS_KUM;
    } else {
        $qryString=QUERY_DETAILS;
    }
    $stmnt=$mySqlConnection->prepare($qryString); 
    $stmnt->bind_param('ii',$TeamID,$LigaID);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $details = $res->fetch_all(MYSQLI_ASSOC);
    return $details;
}

?>