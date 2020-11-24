<?php

require_once '../lib_liga.php'; //enthält Funktionen zur Datenbankverbindung

// Holt alle Zuordnungen aus der Datenbank
function getAllocations()
{
    $dbCon = db_connect();
    $qryAllocation = "SELECT * FROM zuordnung ORDER BY Gruppe, Kurzbezeichnung";
    $stmnt = $dbCon->prepare($qryAllocation);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $allocation = $res->fetch_all(MYSQLI_ASSOC);
    return $allocation;
}

// Holt alle Gruppenbezeichnungen aus der Datenbank
function getGroups()
{
    $dbCon = db_connect();
    $qryGroup = "SELECT DISTINCT Gruppe FROM zuordnung ORDER BY Gruppe";
    $stmnt = $dbCon->prepare($qryGroup);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $group = $res->fetch_all(MYSQLI_ASSOC);
    // um ein "flaches" Array als antwort zu bekommen wird das Ergebniss hier verkleinert siehe Beispiel
    // VORHER:  [0] => [[0]=> [[Gruppe]=> "XYZ"]], [1] => [[0]=> [[Gruppe]=> "ABC"]]
    // NACHHER: [0] => "XYZ", [1]=> "ABC"
    return array_column($group, "Gruppe");
}

// Aktualisiert/Erstellt/Löscht die Zuordnungen 
function updateAllocations($allocations)
{
    $error = false;
    foreach ($allocations as $allocation) {
        // zu löschende Zuordnungen werden gesondert behandelt
        if (isset($allocation['delete']) &&  $allocation['delete'] === "1") {
            $success = deleteAllocation($allocation);
            if (!$success) {
                $error = true;
            }
            continue;
        }

        if (
            !isset($allocation["Kurzbezeichnung"]) || empty($allocation["Kurzbezeichnung"]) || !isset($allocation["Beschreibung"]) || empty($allocation["Beschreibung"]) || !isset($allocation["Gruppe"]) || empty($allocation["Gruppe"]) || !isset($allocation["Punktewert"]) || empty($allocation["Punktewert"])
        ) {
            continue;
        }

        // neue Zuordnungen werden gesondert behandelt
        if (isset($allocation['add']) &&  $allocation['add'] == true) {
            $success = createAllocation($allocation);
            if (!$success) {
                $error = true;
            }
            continue;
        }
        // Bestehende Zuordnungen werden geupdatet
        $dbCon = db_connect();
        $qryusers = "UPDATE zuordnung set Kurzbezeichnung=?, Beschreibung=?, Gruppe=?, Punktewert=? where ID=?";
        $stmnt = $dbCon->prepare($qryusers);
        $stmnt->bind_param('sssii', $allocation["Kurzbezeichnung"], $allocation["Beschreibung"], $allocation["Gruppe"], $allocation["Punktewert"], $allocation["id"]);
        $stmnt->execute();
        if (!$stmnt) {
            $error = true;
        }
    }

    return !$error;
}


// löscht die aktuelle Zuordnung
function deleteAllocation($allocation)
{

        // überprüft, ob die Zuordnung aktuell in nutzung ist
        if (checkAllocationUsage($allocation) != 0) {
            return false;
        }

    $success = true;
    $dbCon = db_connect();
    $qryAllocation = "DELETE FROM zuordnung WHERE ID=?";
    $stmnt = $dbCon->prepare($qryAllocation);
    $stmnt->bind_param('i', $allocation["id"]);
    $stmnt->execute();
    if ($stmnt->affected_rows !== 1) {
        $success = false;
    }
    return $success;
}

// prüft ob die übergebene allocation in benutzung ist
function checkAllocationUsage($allocation)
{
    $dbCon = db_connect();
    $qryallocation = "SELECT count(*) as isUsed from daten where ZuordnungID=?";
    $stmnt = $dbCon->prepare($qryallocation);
    $stmnt->bind_param('i', $allocation["id"]);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = $res->fetch_assoc();

    return $count["isUsed"];
}



// Erzeugt eine neue Zuordnung
function createAllocation($allocation)
{
    $dbCon = db_connect();
    $qryleague = "INSERT INTO zuordnung ( Kurzbezeichnung, Beschreibung, Gruppe, Punktewert) VALUES (?,?,?,?)";
    $stmnt = $dbCon->prepare($qryleague);
    $stmnt->bind_param('sssi', $allocation["Kurzbezeichnung"], $allocation["Beschreibung"], $allocation["Gruppe"], $allocation["Punktewert"]);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}
