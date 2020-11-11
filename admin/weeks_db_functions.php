<?php

require_once '../liga_db_functions.php'; //enthält Funktionen zur Datenbankverbindung

// Holt alle Spielwochen inkl. Mak und Datensatz Anzahl einer Saison aus der Datenbank
function getWeeksBySeason($seasonID)
{
    $dbCon = db_connect();
    $qryWeeks = "SELECT sw.*, s.SaisonBezeichnung, (SELECT cast(IFNULL(sum(MAK),0) as decimal(10,2)) FROM `mak` WHERE SpielWochenID=sw.ID) as Maks, (SELECT IFNULL(count(*),0) FROM `daten` where SpielwochenID=sw.ID) as Daten FROM spielwochen sw JOIN saisons s on sw.SaisonID=s.ID WHERE sw.SaisonID=? Group by sw.ID ORDER BY sw.ID";
    $stmnt = $dbCon->prepare($qryWeeks);
    $stmnt->bind_param('i', $seasonID);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $weeks = $res->fetch_all(MYSQLI_ASSOC);
    return $weeks;
}

// Aktualisiert/Erstellt/Löscht die Spielwochen 
function updateWeeks($weeks)
{
    $error = true;
    foreach ($weeks as $week) {
        if (isset($week['delete']) &&  $week['delete'] === "1") {
            // zu löschende Spielwochen werden gesondert behandelt
            $success = deleteWeek($week);
            if (!$success && $error === true) {
                $error = "Leider konnte die Woche " . $week["SpielwochenNr"] . " nicht gelöscht werden. (Möglicherweise ist diese Woche noch in Benutzung.)";
            }
            continue;
        }
        if (!isset($week["SpielwochenNr"]) || empty($week["SpielwochenNr"])) {
            continue;
        }

        if (isset($week['add']) &&  $week['add'] == true) {
            // neue Spielwochen werden gesondert behandelt
            $success = createWeek($week);
            if (!$success && $error === true) {
                $error = "Leider konnte die neue Woche " . $week["SpielwocheNr"] . " nicht angelegt werden.";
            }
            continue;
        }

        // Bestehende Spielwochen werden geupdatet
        $stichtag = date("Y-m-d", strtotime($week["Stichtag"]));
        $dbCon = db_connect();
        $qryweek = "UPDATE spielwochen set SpielwochenNr=?, Stichtag=? where ID=?";
        $stmnt = $dbCon->prepare($qryweek);
        $stmnt->bind_param('sss', $week["SpielwochenNr"], $stichtag, $week["ID"]);
        $stmnt->execute();
        if ($stmnt->affected_rows > 1 && $error === true) {
            $error = "Leider konnte die Woche " . $week["SpielwochenNr"] . " nicht aktualisiert werden.";
        }
    }
    return $error;
}

// löscht die aktuelle Spielwoche
function deleteWeek($week)
{
    // überprüft, ob die Spielwoche aktuell in Nutzung ist
    if (checkUsage($week) != 0) {
        return false;
    }
    $dbCon = db_connect();
    $qryweeks = "DELETE FROM spielwochen WHERE ID=?";
    $stmnt = $dbCon->prepare($qryweeks);
    $stmnt->bind_param('i', $week["ID"]);
    $stmnt->execute();
    if (!$stmnt) {
        return false;
    }
    return true;
}
// überprüft, ob die Spielwoche aktuell in Nutzung ist
function checkUsage($week)
{
    $dbCon = db_connect();
    $qryweek = "SELECT count(*) as isUsed FROM daten  d , mak  m  where d.SpielWochenID=? or m.SpielWochenID=?";
    $stmnt = $dbCon->prepare($qryweek);
    $stmnt->bind_param('ii', $week["ID"],$week["ID"]);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = $res->fetch_assoc();
    return $count["isUsed"];
}

// Erzeugt eine neue Spielwoche
function createWeek($week)
{
    $stichtag  = date("Y-m-d", strtotime($week["Stichtag"]));

    $dbCon = db_connect();
    $qryweek = "INSERT INTO spielwochen ( SpielwochenNr, Stichtag, SaisonID) VALUES (?,?,?)";
    $stmnt = $dbCon->prepare($qryweek);
    $stmnt->bind_param('sss', $week["SpielwochenNr"], $stichtag, $week["seasonID"]);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}

// löscht die eingetragenen Daten der Spielwoche
function deleteDataByWeek($weekID)
{
    $dbCon = db_connect();
    $qryAllocation = "DELETE FROM daten WHERE SpielwochenID=?";
    $stmnt = $dbCon->prepare($qryAllocation);
    $stmnt->bind_param('i', $weekID);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    }
    return false;
}
