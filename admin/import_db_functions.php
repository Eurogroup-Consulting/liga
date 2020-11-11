<?php

require_once '../liga_db_functions.php'; //enthält Funktionen zur Datenbankverbindung
require_once 'teams_db_functions.php'; //enthält Funktionen zur Teamverwaltung

// Wird genutzt um Punktewertungen aus einer CSV Datei zu importieren
function importCSV($filename, $week)
{
    // Holt einmalig die Daten der Zuordnungs Tabelle
    $zuordnung = getZuordnung();

    // Holt einmalig die Daten der Teams Tabelle
    $teamshelper = getTeams();

    // Vereinfacht die Teams-Zuordnung über ein Assoziatives Array
    foreach ($teamshelper as  $val) {
        $teams[$val["Teamname"]] = $val;
    }

    $errors = false;

    // Öffnet die CSV als temporäre Datei 
    if (($handle = fopen($filename, "r")) !== FALSE) {

        // Liest die Header Zeile des CSVs aus
        $keys = fgetcsv($handle, 0, ";");

        // Setzt die aktuelle Woche aus der Daten Tabelle zurück indem die einträge gelöscht werden
        deleteWeekData($week);

        // Solange noch Zeilen in der CSV vorhanden sind wird folgendes Ausgeführt
        while (!feof($handle)) {
            $arr = [];

            // Liest die aktuelle CSV Zeile aus
            $val =  fgetcsv($handle, 0, ";");

            // Prüft ob diese Zeile Daten enthält
            if ($val) {
                // Erstelle das zu updatende Array
                for ($i = 0; $i < count($keys); $i++) {
                    // Encoding wird benötigt für alle Sonderzeichen Fälle (Ä/Ö/Ü...)
                    $arr[$keys[$i]] = utf8_encode($val[$i]);
                }
            }
            // Falls die Zeile nicht alle Daten enthält wird sie überprungen
            if (!isset($arr["Zuordnung"]) || !isset($arr["Team"]) || !isset($arr["Anzahl"])) {
                continue;
            }
            // Falls die Zeile keinem Team oder einer "Zuordnung" zugeordnet werden kann wird sie dem Errors Array hinzugefügt und übersprungen
            if (!isset($teams[$arr["Team"]]) || !isset($zuordnung[$arr["Zuordnung"]])) {
                $errors[] = $arr;
                continue;
            }
            // Fügt die aktuelle Zeile in die Daten Tabelle ein
            $success = addLine($arr, $week, $zuordnung[$arr["Zuordnung"]], $teams[$arr["Team"]]);
            // Bei Fehlern wird die Zeile dem Errors Array hinzugefügt
            if (!$success) {
                $errors[] = $arr;
            }
        }
        // Schließt den File Handler
        fclose($handle);
    }
    return $errors;
}

// Wird genutzt um eine Zeile der CSV zu importieren
function addLine($data, $week, $zuordnung, $team)
{
    // Prüft ob zum aktuellen Team & Woche MAKs existieren 
    if (!checkMAKs($team, $week)) {
        return false;
    }
    // Berechnet die Punkte mit der aktuellen Zuordnung
    $punkte = $data["Anzahl"] * $zuordnung["Punktewert"];
    $dbCon = db_connect();
    $qrydata = "INSERT INTO daten ( SpielwochenID, ZuordnungID, TeamID,Anzahl,Punkte) VALUES (?,?,?,?,?)";
    $stmnt = $dbCon->prepare($qrydata);
    $stmnt->bind_param('iiiii', $week, $zuordnung["ID"], $team["ID"], $data["Anzahl"], $punkte);
    $stmnt->execute();
    if ($stmnt) {
        return true;
    } else {
        return false;
    }
}

// Prüft ob zum aktuellen Team & Woche MAKs existieren 
function checkMAKs($team, $week)
{
    $dbCon = db_connect();
    $qrymaks = "SELECT * FROM `mak` where `SpielWochenID`=? and `TeamID`=?";
    $stmnt = $dbCon->prepare($qrymaks);
    $stmnt->bind_param('ii', $week, $team["ID"]);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = count($res->fetch_all());
    return $count > 0;
}
// Wird genutzt um alle Zuordnungen zu laden
function getZuordnung()
{
    $dbCon = db_connect();
    $qryzuordnung = "SELECT `Kurzbezeichnung`, `Punktewert`, `ID` FROM `zuordnung`";
    $stmnt = $dbCon->prepare($qryzuordnung);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $zuordnung = $res->fetch_all(MYSQLI_ASSOC);
    // Vereinfacht die "Zuordnungs"-Zuordnung über ein Assoziatives Array
    foreach ($zuordnung as $val) {
        $keyedZuornung[$val["Kurzbezeichnung"]] = $val;
    }
    return $keyedZuornung;
}

// Löscht die Daten der entsprechenden Spielwoche
function deleteWeekData($week)
{
    $dbCon = db_connect();
    $qryweeks = "DELETE FROM daten  WHERE SpielwochenID=?";
    $stmnt = $dbCon->prepare($qryweeks);
    $stmnt->bind_param('i', $week);
    $stmnt->execute();
}


// Wird genutzt um alle Spielwochen mit Saisonbezeichnung zu laden
function getMatchWeeksWithSaisonName()
{
    $dbCon = db_connect();
    $qryMatchWeeks = "SELECT s.ID, s.SpielwochenNr, sa.SaisonBezeichnung FROM `spielwochen` s JOIN `saisons` sa on s.SaisonID=sa.ID ORDER BY SaisonID, SpielwochenNr";
    $stmnt = $dbCon->prepare($qryMatchWeeks);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $teamdivisions = $res->fetch_all(MYSQLI_ASSOC);
    return $teamdivisions;
}
