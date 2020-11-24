<?php
    
require_once 'lib_liga.php';
 
// lädt die aktuellen daten des rankings
function rankdata($ligaid) {
    
    $dbConRanking= db_connect();
    

    $qryTeams=QUERY_TEAMS; //Definiert in lib_liga
    $stmnt_Teams=$dbConRanking->prepare($qryTeams);
    $stmnt_Teams->bind_param('i',$ligaid);
    $stmnt_Teams->execute();
    $resTeams=$stmnt_Teams->get_result();

    while($team=$resTeams->fetch_assoc()){
        $teams[$team["TeamID"]]=$team["Teamname"];
    }

    $qryRanking=QUERY_KUMKW; //Definiert in lib_liga
    $stmnt=$dbConRanking->prepare($qryRanking); 
    $stmnt->bind_param('i',$ligaid);
    $stmnt->execute();
    $res=$stmnt->get_result();

    $row=$res->fetch_assoc();
    $preWeekNum=$row["SpielwochenNr"];
    $count=0;
    
    do{
        $punkte=$row["Punkte"];
        $teamid=$row["TeamID"];
        $woche=$row["SpielwochenNr"];

        if ($count==0)
            $week[$teamid]=  $punkte; //erste Woche übrnehmen
            else 
            $week[$teamid]+= $punkte; //weitere Wochen addieren
            
        
        if($woche!=$preWeekNum){
            arsort($week);

            $rank=0;
            foreach($week as $key => $value){
                $rank++;
                $data[$key][$count]=$rank;
                $axis[$count]=$preWeekNum;
            }

            $count++;
        }
        $preWeekNum=$row["SpielwochenNr"];
        
    }while($row=$res->fetch_assoc());
    
    //letzte Woche mus noch abgearbeitet werden !!!!!!!
    arsort($week);
    $rank=0;
    foreach($week as $key => $value){
        $rank++;
        $data[$key][$count]=$rank;
        $axis[$count]=$preWeekNum;
    }
    
    $return["data"]=$data;
    $return["axis"]=$axis;
    $return["teams"]=$teams;
    return $return;  
}

function getLigaInfos(){
    $dbCon= db_connect();
    $qryLigen="SELECT ID, LigaName FROM ligen ORDER By LigaName";
    $stligen=$dbCon->prepare($qryLigen);
    $stligen->execute();
    $res = $stligen->get_result();
    $leagues = $res->fetch_all(MYSQLI_ASSOC);
    return $leagues;

}

function getRanking($ligaid){
    $dbCon= db_connect();
    $qryRanking=QUERY_RANKING; //Definiert in lib_liga
    $stmnt=$dbCon->prepare($qryRanking); 
    $stmnt->bind_param('i',$ligaid);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $ranks = $res->fetch_all(MYSQLI_ASSOC);
    return $ranks;
}