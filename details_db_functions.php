<?php
    require_once 'config.php';
    require_once 'auth.php';
    require_once 'liga_db_functions.php';

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