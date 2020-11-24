<?php

require_once '../lib/lib_liga.php';

// Wird genutzt um Teameinteilungen einzelner Ligen gezielt zu laden
function getTeamsDivisionWithName()
{
    $dbCon = db_connect();
    $qryteamdivisions = "SELECT * FROM `teameinteilung` as te LEFT JOIN `teams` as t on te.TeamID=t.ID  ORDER BY SaisonID";
    $stmnt = $dbCon->prepare($qryteamdivisions);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $teamdivisions = $res->fetch_all(MYSQLI_ASSOC);
    return $teamdivisions;
}

// Wird genutzt um Teameinteilungen einzelner Ligen gezielt zu laden
function getTeamsDivisionByLeague($leagueID)
{
    $dbCon = db_connect();
    $qryteamdivisions = "SELECT * FROM `teameinteilung` where LigaID=? ORDER BY SaisonID";
    $stmnt = $dbCon->prepare($qryteamdivisions);
    $stmnt->bind_param('i', $leagueID);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $teamdivisions = $res->fetch_all(MYSQLI_ASSOC);
    return $teamdivisions;
}

// updatet alle Team einträge zu einer Liga & Saison
function updateTeamDivision($leagueID, $season, $teams)
{

    $error = false;
    foreach ($teams as $divisionID => $team) {
        // zu löschende teams werden gesondert verarbeitet 
        if (isset($team['delete']) &&  $team['delete'] === "1") {
            $success = deleteTeamDivision($divisionID);
            if (!$success) {
                $error = true;
            }
            continue;
        }
 
        if (!isset($team["id"]) || empty($team["id"]) || $team["id"] == -1) {
            continue;
        }
        // neue Ligen werden gesondert verarbeitet
        if (isset($team['add']) &&  $team['add'] == true) {
            $success = createTeamDivision($team, $season, $leagueID);
            if (!$success) {
                $error = true;
            }
            continue;
        }

        $dbCon = db_connect();
        $qryusers = "UPDATE teameinteilung set LigaID=?, SaisonID=?, TeamID=? where ID=?";
        $stmnt = $dbCon->prepare($qryusers);
        $stmnt->bind_param('iiii', $leagueID, $season, $team["id"], $divisionID);
        $stmnt->execute();
        if (!$stmnt) {
            $error = true;
        }
    }

    return !$error;
}
// löscht den ausgewählten Teameintrag
function deleteTeamDivision($id)
{
    $success = true;
    $dbCon = db_connect();
    $qryleague = "DELETE FROM teameinteilung WHERE ID=?";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('i', $id);
    $stmnt->execute();
    if ($stmnt->affected_rows !== 1) {
        $success = false;
    }
    return $success;
}
// Erstellt einen neuen Eintrag in der Teameinteilung
function createTeamDivision($team, $seasonID, $leagueID)
{
    $dbCon = db_connect();
    $qryleague = "INSERT INTO teameinteilung ( LigaID, SaisonID, TeamID) VALUES (?,?,?)";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('iii', $leagueID, $seasonID, $team["id"]);
    $stmnt->execute();
    if ($stmnt->affected_rows !== 1) {
        return false;
    } else {
        return true;
    }
}
