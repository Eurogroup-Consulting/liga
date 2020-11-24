<?php

require_once dirname(__FILE__) .'/lib_liga.php'; //enthält Funktionen zur Datenbankverbindung

// Wird genutzt um MAKs mit den entsprechenden Teamnamen anzuzeigen
function getMAKsWithTeamName()
{
    $dbCon = db_connect();
    // "Full outer join "
    // $qryMatchWeeks = "SELECT m.*, t.Teamname FROM MAK as m LEFT JOIN Teams as t ON m.TeamID=t.ID UNION SELECT m.*, t.Teamname FROM MAK as m RIGHT JOIN Teams as t ON m.TeamID=t.ID ORDER BY  t.Teamname";
    $qryMatchWeeks = "SELECT m.*, t.Teamname FROM `mak` as m LEFT JOIN `teams` as t ON m.TeamID=t.ID ORDER BY  t.Teamname";
    $stmnt = $dbCon->prepare($qryMatchWeeks);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $teamdivisions = $res->fetch_all(MYSQLI_ASSOC);
    return $teamdivisions;
}

// Wird genutzt um alle Spielwochen zu laden
function getMatchWeeks()
{
    $dbCon = db_connect();
    $qryMatchWeeks = "SELECT * FROM `spielwochen` ORDER BY SaisonID, SpielwochenNr";
    $stmnt = $dbCon->prepare($qryMatchWeeks);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $teamdivisions = $res->fetch_all(MYSQLI_ASSOC);
    return $teamdivisions;
}

// Wird genutzt um alle Spielwochen einer Saison zu laden
function getMatchWeeksBySaisonId($seasonID)
{
    $dbCon = db_connect();
    $qryMatchWeeks = "SELECT * FROM `spielwochen` WHERE `SaisonID`=? ORDER BY  SpielwochenNr";
    $stmnt = $dbCon->prepare($qryMatchWeeks);
    $stmnt->bind_param("i", $seasonID);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $matchWeeks = $res->fetch_all(MYSQLI_ASSOC);
    return $matchWeeks;
}

// updatet alle MAK einträge zu einer Woche oder allen folgenden Wochen
function updateMAKs($weekID, $maks, $seasonID)
{
    $error = false;
    foreach ($maks as $mak) {
        if (!isset($mak["teamID"]) || !isset($mak["mak"]) || empty($mak["mak"]) || $mak["mak"] <= 0) {
            continue;
        }
        // "Für zukunft übernehmen" wird seperat behandelt
        if (isset($mak["adopt"])) {
            $success = UpdateAndAdoptMAK($weekID, $mak, $seasonID);
            if (!$success) {
                $error = true;
            }
            continue;
        }
        $dbCon = db_connect();
        // Erzeugt neue Einträge für das Team oder Aktualisiert diese, falls sie bereits existieren
        $qryMaks = "INSERT INTO `mak` (SpielWochenID, TeamID, MAK) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE SpielWochenID = ?, TeamID=?, MAK=?;";
        $stmnt = $dbCon->prepare($qryMaks);
        $stmnt->bind_param("iisiis", $weekID, $mak["teamID"], $mak["mak"], $weekID, $mak["teamID"], $mak["mak"]);
        $stmnt->execute();
        if (!$stmnt) {
            $error = true;
        }
    }
    return !$error;
}

// Fügt für alle folgenden Spielwochen innerhalb der Saison das Team mit der eingetragenen MAK ein.
// Falls der Eintrag bereits existiert, wird er geupdatet
function UpdateAndAdoptMAK($weekID, $mak, $seasonID)
{

    $dbCon = db_connect();
    $qryMaks = "INSERT INTO `mak` (SpielWochenID, TeamID, MAK) SELECT DISTINCT s.ID, ?, ? FROM `spielwochen` as s where s.SaisonID=? AND s.ID>=? ON DUPLICATE KEY UPDATE MAK=?";
    $stmnt = $dbCon->prepare($qryMaks);
    $stmnt->bind_param("isiis",  $mak["teamID"], $mak["mak"], $seasonID, $weekID, $mak["mak"]);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}

// löscht die eingetragenen Maks der Spielwoche
function deleteMaksByWeek($weekID)
{
    $dbCon = db_connect();
    $qryweek = "DELETE FROM mak where SpielWochenID=?";
    $stmnt = $dbCon->prepare($qryweek);
    $stmnt->bind_param('i', $weekID);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}
