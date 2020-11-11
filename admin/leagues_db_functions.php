<?php

require_once '../liga_db_functions.php';

function getLeagues()
{
    $dbCon = db_connect();
    $qryleagues = "SELECT * FROM ligen ORDER BY AktSaisonID";
    $stmnt = $dbCon->prepare($qryleagues);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $leagues = $res->fetch_all(MYSQLI_ASSOC);
    return $leagues;
}

// Wird genutzt um einzelne Ligen gezielt zu laden
function getLeague($leagueID)
{
    $dbCon = db_connect();
    $qryleague = "SELECT LigaName, AktSaisonID FROM ligen where ID=? LIMIT 1";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('i', $leagueID);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $league = $res->fetch_assoc();
    return $league;
}

function updateLeagues($leagues)
{
    $error = false;
    foreach ($leagues as $league) {
        // zu löschende Ligen werden gesondert verarbeitet 
        if (isset($league['delete']) &&  $league['delete'] === "1") {
            $success = deleteLeague($league);
            if (!$success && $error === false) {
                $error = "Leider konnte die Liga '" . $league["LigaName"] . "' nicht gelöscht werden.";
            }
            continue;
        }
        if (empty($league["LigaName"]) &&  empty($league["AktSaisonID"]) ) {
            continue;
        } 
        else if (!empty($league["LigaName"]) && empty($league["AktSaisonID"]) || empty($league["LigaName"]) && !empty($league["AktSaisonID"])) {
            $error = "Leider war die Eingabe der neuen Liga unvollständig.";
            continue;
        }


        // neue Ligen werden gesondert verarbeitet
        if (isset($league['add']) &&  $league['add'] == true) {
            $success = createLeague($league);
            if (!$success && $error === false) {
                $error = "Leider konnte die neue Liga '" . $league["LigaName"] . "' nicht angelegt werden.";
            }
            continue;
        }
        $dbCon = db_connect();
        $qryusers = "UPDATE ligen set LigaName=?, AktSaisonID=?, Kommentar=? where ID=?";
        $stmnt = $dbCon->prepare($qryusers);
        $stmnt->bind_param('sisi', $league["LigaName"], $league["AktSaisonID"], $league["Kommentar"], $league["ID"]);
        $stmnt->execute();
        if ($stmnt->affected_rows !== 1 && $error === false) {
            $error = true;
        }
    }
    return $error;
}

function deleteLeague($league)
{
    // überprüft, ob die Liga aktuell in nutzung ist
    if (checkLeagueUsage($league) != 0) {
        return false;
    }

    $error = false;
    $dbCon = db_connect();
    $qryleague = "DELETE FROM ligen WHERE ID=?";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('i', $league["ID"]);
    $stmnt->execute();
    if ($stmnt->affected_rows !== 1) {
        $error = true;
    }
    return !$error;
}


function checkLeagueUsage($league)
{
    $dbCon = db_connect();
    $qryleague = "SELECT count(*) as isUsed from teameinteilung where LigaID=?";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('i', $league["ID"]);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = $res->fetch_assoc();
    return $count["isUsed"];
}


function createLeague($league)
{
    $dbCon = db_connect();
    $qryleague = "INSERT INTO ligen ( LigaName, AktSaisonID, Kommentar) VALUES (?,?,?)";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('sis', $league["LigaName"], $league["AktSaisonID"], $league["Kommentar"]);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}
