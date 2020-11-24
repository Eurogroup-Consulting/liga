<?php
    require_once 'config/db_config.php';

    // erstellt eine verbindung zur datenbank
    function db_connect(){
          
        $db_socket=null;
        $db_port=null;
         
        //Definiert duinrch config/db_config.php 
        $mSqlObj=new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $db_port, $db_socket);
        
        // Lokale Variablen
        //$mSqlObj=new mysqli($db_host, $db_user, $db_pwd, $db_name, $db_port, $db_socket);
        
        if (!$mSqlObj->connect_error){
            mysqli_query($mSqlObj, "SET NAMES '" . DB_CHARSET . "'" );
            return $mSqlObj;
        } else{
            
            die("Error Conneting to Liga-Database");
        }

        
    }


    // lädt die liga id der übergebenen saison und des übergebenen teams
    function getUserLiga($SaisonID,$TeamID){
      $conL=db_connect();
      $sqlLiga = "SELECT LigaID FROM `teameinteilung` WHERE SaisonID=? AND TeamID=?";
      $stmtGetLiga = $conL -> prepare($sqlLiga);
      $stmtGetLiga -> bind_param('ii',$SaisonID,$TeamID);
      $stmtGetLiga-> execute(); 
      $stmtGetLiga -> store_result(); 
      $stmtGetLiga -> bind_result($LigaID); 
      if($stmtGetLiga -> num_rows()>=1){ 
        $stmtGetLiga -> fetch();
        $conL -> close();
        return $LigaID;  
      }
        $conL -> close();
        return 0;
    }

    // lädt die späteste saison
    function max_saison(){
      $conSaison=db_connect();
      $sqlMaxSasison = "SELECT ID FROM `saisons` WHERE SaisonBegin = (SELECT MAX(SaisonBegin) FROM saisons)";
      $stmtSaison = $conSaison -> prepare($sqlMaxSasison);
      $stmtSaison -> execute(); 
      $stmtSaison -> store_result(); 
      $stmtSaison -> bind_result($SaisionID); 
      $stmtSaison -> fetch();
      $conSaison -> close();
      return $SaisionID;
    }

    // lädt den aktuellsten kommentar
    function akt_comment(){
      $conC=db_connect();
      $sqlAktCom = "SELECT Teaser FROM `kommentare` WHERE Datum<=NOW() ORDER BY Datum DESC LIMIT 1";
      $stmtC = $conC -> prepare($sqlAktCom);
      $stmtC -> execute(); 
      $stmtC -> store_result(); 
      $stmtC -> bind_result($Teaser); 
      $stmtC -> fetch();
      $conC -> close();
      return $Teaser;
    }


// ******************************************
// Abfrage der aktuellen teams einer Liga
Define('QUERY_TEAMS',
        "SELECT teams.ID as TeamID, teams.Teamname as Teamname FROM 
        ligen INNER JOIN teameinteilung 
            ON ligen.AktSaisonID=teameinteilung.SaisonID
          INNER JOIN teams
            ON teameinteilung.TeamID=teams.ID
      WHERE ligen.ID = ?");

#*******************************************
# Abfrage Kumulierte Punkte pro Woche    
Define('QUERY_KUMKW',
    "SELECT
  	saisons.ID AS SaisonID,
  	saisons.SaisonBezeichnung,
  	ligen.ID AS LigaID,
  	ligen.LigaName,
  	spielwochen.ID AS SpielwochenID,
  	spielwochen.SpielwochenNr,
  	teams.ID AS TeamID,
  	teams.Teamname,
  	SUM( daten.Anzahl ) AS sAnzhal,
  	ROUND( SUM( daten.Punkte / mak.MAK ) , 2 ) AS Punkte
  FROM
	  ligen
	INNER JOIN saisons
	 ON ligen.AktSaisonID = saisons.ID
	INNER JOIN teameinteilung
	 ON ligen.ID = teameinteilung.LigaID
	  AND teameinteilung.SaisonID = ligen.AktSaisonID
	INNER JOIN teams
	 ON teameinteilung.TeamID = teams.ID
	INNER JOIN spielwochen
	 ON teameinteilung.SaisonID = spielwochen.SaisonID
	INNER JOIN mak
	 ON spielwochen.ID = mak.SpielWochenID
	  AND mak.TeamID = teams.ID
	INNER JOIN daten
	 ON mak.SpielWochenID = daten.SpielwochenID
	  AND mak.TeamID = daten.TeamID
	INNER JOIN zuordnung
	 ON daten.ZuordnungID = zuordnung.ID
WHERE
	ligen.ID = ?
GROUP BY
	saisons.ID,
	saisons.SaisonBezeichnung,
	ligen.ID,
	ligen.LigaName,
	spielwochen.ID,
	spielwochen.SpielwochenNr,
	teams.ID,
	teams.Teamname
ORDER BY
	saisons.ID ASC,
	ligen.ID ASC,
	spielwochen.SpielwochenNr ASC,
	ROUND(SUM(daten.Punkte/mak.MAK),2) DESC"
);
    
# **** QUERY_RANKING *****************
# Rangliste innerhalb einer Liga über alles
# Parameter LigaID
Define('QUERY_RANKING',
    "SELECT
	    saisons.ID AS SaisonID,
	    saisons.SaisonBezeichnung,
	    ligen.ID AS LigaID,
	    ligen.LigaName,
	    teams.ID AS TeamID,
        teams.Teamname,
	    SUM( daten.Anzahl ) AS sAnzhal,
	    SUM( daten.Punkte/ mak.MAK ) AS Punkte
    FROM
	    ligen
	INNER JOIN saisons
	 ON ligen.AktSaisonID = saisons.ID
	INNER JOIN teameinteilung
	 ON ligen.ID = teameinteilung.LigaID
	  AND teameinteilung.SaisonID = ligen.AktSaisonID
	INNER JOIN teams
	 ON teameinteilung.TeamID = teams.ID
	INNER JOIN spielwochen
	 ON teameinteilung.SaisonID = spielwochen.SaisonID
	INNER JOIN mak
	 ON spielwochen.ID = mak.SpielWochenID
	  AND mak.TeamID = teams.ID
	INNER JOIN daten
	 ON mak.SpielWochenID = daten.SpielwochenID
	  AND mak.TeamID = daten.TeamID
	INNER JOIN zuordnung
	 ON daten.ZuordnungID = zuordnung.ID
WHERE
	ligen.ID = ?
GROUP BY
	saisons.ID,
	saisons.SaisonBezeichnung,
	ligen.ID,
	ligen.LigaName,
	teams.Teamname
ORDER BY
	saisons.ID ASC,
	ligen.ID ASC,
	SUM( daten.Punkte / mak.MAK ) DESC");

# **** QUERY_DETAILS_KUM *****************
# Details eines teams innerhalb einer Liga über alle
# Parameter TeamID, LigaID kumuliert über alle wochen
Define('QUERY_DETAILS_KUM',"SELECT
   saisons.ID AS SaisonID,
   saisons.SaisonBezeichnung,
   ligen.ID AS LigaID,
   ligen.LigaName,
   teams.ID AS TeamID,
   teams.Teamname,
   zuordnung.Gruppe,
   zuordnung.Kurzbezeichnung,
   SUM( daten.Anzahl ) AS sAnzhal,
   SUM( daten.Punkte / mak.MAK ) AS Punkte
FROM
   ligen
   INNER JOIN saisons
    ON ligen.AktSaisonID = saisons.ID
   INNER JOIN teameinteilung
    ON ligen.ID = teameinteilung.LigaID
     AND teameinteilung.SaisonID = ligen.AktSaisonID
   INNER JOIN teams
    ON teameinteilung.TeamID = teams.ID
   INNER JOIN spielwochen
    ON teameinteilung.SaisonID = spielwochen.SaisonID
   INNER JOIN mak
    ON spielwochen.ID = mak.SpielWochenID
     AND mak.TeamID = teams.ID
   INNER JOIN daten
    ON mak.SpielWochenID = daten.SpielwochenID
     AND mak.TeamID = daten.TeamID
   INNER JOIN zuordnung
    ON daten.ZuordnungID = zuordnung.ID
WHERE
   teams.ID = ?
   AND ligen.ID = ?
GROUP BY
   saisons.ID,
   saisons.SaisonBezeichnung,
   ligen.ID,
   ligen.LigaName,
   teams.ID,
   teams.Teamname,
   zuordnung.Gruppe,
   zuordnung.Kurzbezeichnung
ORDER BY
   saisons.ID ASC,
   ligen.ID ASC,
   zuordnung.Gruppe ASC"); 
   
   
# **** QUERY_DETAILS_KONKURENZ_KUM *****************
# Details aller anderen teams innerhalb einer Liga über alle
# Parameter TeamID, LigaID kumuliert über alle wochen
Define('QUERY_DETAILS_KONKURENZ_KUM',"SELECT
zuordnung.Gruppe,
zuordnung.Kurzbezeichnung,
SUM( daten.Anzahl  )/count(DISTINCT teams.ID) AS sAnzhal,
SUM( daten.Punkte / mak.MAK )/count(DISTINCT teams.ID) AS Punkte
FROM
ligen
INNER JOIN saisons
 ON ligen.AktSaisonID = saisons.ID
INNER JOIN teameinteilung
 ON ligen.ID = teameinteilung.LigaID
  AND teameinteilung.SaisonID = ligen.AktSaisonID
INNER JOIN teams
 ON teameinteilung.TeamID = teams.ID
INNER JOIN spielwochen
 ON teameinteilung.SaisonID = spielwochen.SaisonID
INNER JOIN mak
 ON spielwochen.ID = mak.SpielWochenID
  AND mak.TeamID = teams.ID
INNER JOIN daten
 ON mak.SpielWochenID = daten.SpielwochenID
  AND mak.TeamID = daten.TeamID
INNER JOIN zuordnung
 ON daten.ZuordnungID = zuordnung.ID
WHERE
teams.ID not like ?
AND ligen.ID = ?
GROUP BY
saisons.ID,
saisons.SaisonBezeichnung,
ligen.ID,
ligen.LigaName,
zuordnung.Gruppe,
zuordnung.Kurzbezeichnung
ORDER BY
saisons.ID ASC,
ligen.ID ASC,
zuordnung.Gruppe ASC"); 
   
# **** QUERY_DETAILS *****************
# Details eines teams innerhalb einer Liga über alle
# Parameter TeamID, LigaID
Define('QUERY_DETAILS',"SELECT
   saisons.ID AS SaisonID,
   saisons.SaisonBezeichnung,
   ligen.ID AS LigaID,
   ligen.LigaName,
   teams.ID AS TeamID,
   teams.Teamname,
   zuordnung.Gruppe,
   zuordnung.Kurzbezeichnung,
   SUM( daten.Anzahl ) AS sAnzhal,
   SUM( daten.Punkte / mak.MAK ) AS Punkte
FROM
   ligen
   INNER JOIN saisons
    ON ligen.AktSaisonID = saisons.ID
   INNER JOIN teameinteilung
    ON ligen.ID = teameinteilung.LigaID
     AND teameinteilung.SaisonID = ligen.AktSaisonID
   INNER JOIN teams
    ON teameinteilung.TeamID = teams.ID
   INNER JOIN spielwochen
    ON teameinteilung.SaisonID = spielwochen.SaisonID
   INNER JOIN mak
    ON spielwochen.ID = mak.SpielWochenID
     AND mak.TeamID = teams.ID
   INNER JOIN daten
    ON mak.SpielWochenID = daten.SpielwochenID
     AND mak.TeamID = daten.TeamID
   INNER JOIN zuordnung
    ON daten.ZuordnungID = zuordnung.ID
WHERE
   teams.ID = ?
   AND ligen.ID = ?
   AND spielwochen.ID=(
      SELECT 
        s.ID 
      FROM 
        daten d 
      LEFT JOIN 
        spielwochen s 
      ON 
        s.ID=d.SpielwochenID 
      WHERE 
        Stichtag = (
          SELECT 
            MAX(s.Stichtag) 
          FROM 
            daten d 
          LEFT JOIN 
            spielwochen s 
          ON 
            s.ID=d.SpielwochenID 
          AND 
            s.Stichtag <= NOW()
        ) 
      GROUP BY s.ID
      )  
GROUP BY
   saisons.ID,
   saisons.SaisonBezeichnung,
   ligen.ID,
   ligen.LigaName,
   teams.ID,
   teams.Teamname,
   zuordnung.Gruppe,
   zuordnung.Kurzbezeichnung,
   spielwochen.ID
ORDER BY
   saisons.ID ASC,
   ligen.ID ASC,
   zuordnung.Gruppe ASC"); 
   

# **** QUERY_DETAILS_KONKURENZ *****************
# Details aller anderen teams innerhalb einer Liga über alle
# Parameter TeamID, LigaID
Define('QUERY_DETAILS_KONKURENZ',"SELECT
zuordnung.Gruppe,
zuordnung.Kurzbezeichnung,
SUM( daten.Anzahl  )/count(DISTINCT teams.ID) AS sAnzhal,
SUM( daten.Punkte / mak.MAK )/count(DISTINCT teams.ID) AS Punkte
FROM
ligen
INNER JOIN saisons
 ON ligen.AktSaisonID = saisons.ID
INNER JOIN teameinteilung
 ON ligen.ID = teameinteilung.LigaID
  AND teameinteilung.SaisonID = ligen.AktSaisonID
INNER JOIN teams
 ON teameinteilung.TeamID = teams.ID
INNER JOIN spielwochen
 ON teameinteilung.SaisonID = spielwochen.SaisonID
INNER JOIN mak
 ON spielwochen.ID = mak.SpielWochenID
  AND mak.TeamID = teams.ID
INNER JOIN daten
 ON mak.SpielWochenID = daten.SpielwochenID
  AND mak.TeamID = daten.TeamID
INNER JOIN zuordnung
 ON daten.ZuordnungID = zuordnung.ID
WHERE
teams.ID not like ?
AND ligen.ID = ?
AND spielwochen.ID=(
      SELECT 
        s.ID 
      FROM 
        daten d 
      LEFT JOIN 
        spielwochen s 
      ON 
        s.ID=d.SpielwochenID 
      WHERE 
        Stichtag = (
          SELECT 
            MAX(s.Stichtag) 
          FROM 
            daten d 
          LEFT JOIN 
            spielwochen s 
          ON 
            s.ID=d.SpielwochenID 
          AND 
            s.Stichtag <= NOW()
        ) 
      GROUP BY s.ID
      ) 
GROUP BY
saisons.ID,
saisons.SaisonBezeichnung,
ligen.ID,
ligen.LigaName,
zuordnung.Gruppe,
zuordnung.Kurzbezeichnung,
spielwochen.ID
ORDER BY
saisons.ID ASC,
ligen.ID ASC,
zuordnung.Gruppe ASC"); 
   
   