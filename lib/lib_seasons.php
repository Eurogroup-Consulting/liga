<?php

require_once dirname(__FILE__) .'/lib_liga.php';
// lädt alle saisons
function getSeasons()
{
    $dbCon = db_connect();
    $qryseason = "SELECT ID, SaisonBezeichnung, SaisonBegin, SaisonEnde FROM saisons ORDER BY SaisonBegin";
    $stmnt = $dbCon->prepare($qryseason);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $seasons = $res->fetch_all(MYSQLI_ASSOC);
    return $seasons;
}

// Erstellt/aktualisiert/löscht alle Saisons
function updateSeasons($seasons)
{
    $error = true;
    foreach ($seasons as $season) {
        // zu löschende Saisons werden gesondert verarbeitet 
        if (isset($season['delete']) &&  $season['delete'] === "1") {
            $success = deleteSeason($season);
            if (!$success && $error === true) {
                $error = "Leider konnte Saison " . $season["SaisonBezeichnung"] . " nicht gelöscht werden. (Möglicherweise ist diese Saison noch in Benutzung.)";
            }
            continue;
        }
        if (empty($season["SaisonBegin"]) || empty($season["SaisonEnde"])) {
            if (!empty($season["SaisonBezeichnung"])) {
                $error = "Leider konnte Saison '" . $season["SaisonBezeichnung"] . "' nicht erstellt werden. (Das angegebene Datum ist ungültig!)";
            }
            continue;
        }
        // zu erstellende Saisons werden gesondert verarbeitet 
        if (isset($season['add']) &&  $season['add'] == true) {
            $success = createSeason($season);
            if (!$success && $error === true) {
                $error = "Leider konnte die neue Saison " . $season["SaisonBezeichnung"] . " nicht angelegt werden.";
            }
            continue;
        }
        $start = date("Y-m-d", strtotime($season["SaisonBegin"]));
        $end = date("Y-m-d", strtotime($season["SaisonEnde"]));
        $dbCon = db_connect();
        $qryseason = "UPDATE saisons set SaisonBezeichnung=?, SaisonBegin=?, SaisonEnde=? where ID=?";
        $stmnt = $dbCon->prepare($qryseason);
        $stmnt->bind_param('sssi', $season["SaisonBezeichnung"], $start, $end, $season["ID"]);
        $stmnt->execute();

        if ($stmnt->affected_rows > 1 && $error === true) {
            $error = "Leider konnte die Saison " . $season["SaisonBezeichnung"] . " nicht aktualisiert werden.";
        }
    }
    return $error;
}

// löscht die gewählte saison und die dazugehörigen spielwochen
function deleteSeason($season)
{
    if (checkUsage($season) != 0) {
        return false;
    }
    $dbCon = db_connect();
    $qryseason = "DELETE FROM saisons WHERE ID=?";
    $stmnt = $dbCon->prepare($qryseason);
    $stmnt->bind_param('i', $season["ID"]);
    $stmnt->execute();
    if (!$stmnt) {
        return false;
    }
    $qryweeks = "DELETE FROM spielwochen WHERE SaisonID=?";
    $stmnt = $dbCon->prepare($qryweeks);
    $stmnt->bind_param('i', $season["ID"]);
    $stmnt->execute();
    if (!$stmnt) {
        return false;
    }
    return true;
}

// prüft ob die saison genutzt wird
function checkUsage($season)
{
    $dbCon = db_connect();
    $qryseason = "SELECT count(*) as isUsed FROM saisons as s Join spielwochen as sw ON s.ID=sw.SaisonID Join teameinteilung as t ON s.ID=t.SaisonID Join ligen as l ON s.ID=l.AktSaisonID Join mak as m ON sw.ID=m.SpielwochenID where s.ID=?";
    $stmnt = $dbCon->prepare($qryseason);
    $stmnt->bind_param('i', $season["ID"]);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = $res->fetch_assoc();
    return $count["isUsed"];
}
// erstellt eine neue saison
function createSeason($season)
{
    $start = date("Y-m-d", strtotime($season["SaisonBegin"]));
    $end = date("Y-m-d", strtotime($season["SaisonEnde"]));
    $dbCon = db_connect();
    $qryseason = "INSERT INTO saisons ( SaisonBezeichnung, SaisonBegin, SaisonEnde) VALUES (?,?,?)";
    $stmnt = $dbCon->prepare($qryseason);
    $stmnt->bind_param('sss', $season["SaisonBezeichnung"], $start, $end);
    $stmnt->execute();
    if ($stmnt) {
        $success = createWeeks($season, $stmnt->insert_id);
        return $success;
    } else {
        return false;
    }
}
// erstellt alle spielwochen für eine saison
function createWeeks($season, $id)
{
    $success = true;
    $startWeek = date("W", strtotime($season["SaisonBegin"]));
    $year = date("Y", strtotime($season["SaisonBegin"]));
    $endWeek = date("W", strtotime($season["SaisonEnde"]));
    for ($i = $startWeek; $i <= $endWeek; $i++) {
        $day = (new DateTime())->setISODate($year, $i, 7)->format('Y-m-d');
        $week = 'KW ' . ($i < 10 && substr($i, 0, 1) != 0 ? '0' . $i : $i);
        $dbCon = db_connect();
        $qryweek = "INSERT INTO spielwochen( SaisonId, SpielwochenNr, Stichtag) VALUES (?,?,?)";
        $stmnt = $dbCon->prepare($qryweek);
        $stmnt->bind_param('sss', $id, $week, $day);
        $stmnt->execute();
        if (!$stmnt) {
            $success = false;
        }
    }
    return $success;
}
