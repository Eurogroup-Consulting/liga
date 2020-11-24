<?php

require_once '../lib_liga.php';

// Wird genutzt um alle Teams zu laden
function getTeams()
{
    $dbCon = db_connect();
    $qryteams = "SELECT `ID`, `Teamname` FROM `teams` ORDER BY `Teamname`";
    $stmnt = $dbCon->prepare($qryteams);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $teams = $res->fetch_all(MYSQLI_ASSOC);
    return $teams;
}

// updatet alle Teameinträge
function updateTeams($teams)
{
    $error = true;
    foreach ($teams as $team) {
        // zu löschende teams werden gesondert verarbeitet 
        if (isset($team['delete']) &&  $team['delete'] === "1") {
            $success = deleteTeam($team);
            if (!$success && $error === true) {
                $error = "Leider konnte das Team '" . $team["Teamname"] . "' nicht gelöscht werden. (Eventuell wird es noch genutzt!)";
            }
            continue;
        }
        if (!isset($team["Teamname"]) || empty($team["Teamname"])) {
            continue;
        }
        // neue Ligen werden gesondert verarbeitet
        if (isset($team['add']) &&  $team['add'] == true) {
            $success = createTeam($team);
            if (!$success && $error === true) {
                $error = "Leider konnte das neue Team '" . $team["Teamname"] . "' nicht angelegt werden.";
            }
            continue;
        }

        $dbCon = db_connect();
        $qryteam = "UPDATE teams set Teamname=? where ID=?";
        $stmnt = $dbCon->prepare($qryteam);
        $stmnt->bind_param('si', $team["Teamname"], $team["ID"]);
        $stmnt->execute();
        if ($stmnt->affected_rows > 1 && $error === true) {
            $error = false;
        }
    }
    return $error;
}

// löscht das ausgewählten Team
function deleteTeam($team)
{
    // überprüft, ob das Team aktuell in nutzung ist
    if (checkTeamUsage($team) != 0) {
        return false;
    }
    $success = true;
    $dbCon = db_connect();
    $qryteam = "DELETE FROM teams  WHERE ID=?";
    $stmnt = $dbCon->prepare($qryteam);
    $stmnt->bind_param('i', $team["ID"]);
    $stmnt->execute();
    if ($stmnt->affected_rows !== 1) {
        $success = false;
    }
    return $success;
}

// überprüft, ob das Team aktuell in nutzung ist
function checkTeamUsage($team)
{
    $dbCon = db_connect();
    $qryteam = "SELECT count(*) as isUsed FROM teameinteilung t ,account a, mak m where t.TeamId=? OR a.TeamId=? OR m.TeamId=?";
    $stmnt = $dbCon->prepare($qryteam);
    $stmnt->bind_param('iii', $team["ID"], $team["ID"], $team["ID"]);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = $res->fetch_assoc();
    return $count["isUsed"];
}

// Erstellt ein neues Team
function createTeam($team)
{
    $dbCon = db_connect();
    $qryteam = "INSERT INTO teams ( Teamname) VALUES (?)";
    $stmnt = $dbCon->prepare($qryteam);
    $stmnt->bind_param('s', $team["Teamname"]);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}
