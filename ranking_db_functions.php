<?php
    
require_once 'liga_db_functions.php';
 
// lädt die aktuellen daten des rankings
function rankdata($ligaid) {
    
    $dbConRanking= db_connect();
    

    $qryTeams=QUERY_TEAMS; //Definiert in liga_db_functions
    $stmnt_Teams=$dbConRanking->prepare($qryTeams);
    $stmnt_Teams->bind_param('i',$ligaid);
    $stmnt_Teams->execute();
    $resTeams=$stmnt_Teams->get_result();

    while($team=$resTeams->fetch_assoc()){
        $teams[$team["TeamID"]]=$team["Teamname"];
    }

    $qryRanking=QUERY_KUMKW; //Definiert in liga_db_functions
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

