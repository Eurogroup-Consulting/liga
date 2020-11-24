<?php

require_once '../lib_liga.php'; //enthält Funktionen zur Datenbankverbindung


// lädt alle Kommentare
function getComments()
{
        $dbCon = db_connect();
        $qryComments = "SELECT * FROM kommentare ORDER BY Datum DESC";
        $stmnt = $dbCon->prepare($qryComments);
        $stmnt->execute();
        $res = $stmnt->get_result();
        $comments = $res->fetch_all(MYSQLI_ASSOC);
        return $comments;
}

// Aktualisiert/Erstellt/Löscht die Kommentare 
function updateComments($comments)
{
        $error = false;

        foreach ($comments as $comment) {
                // zu löschende Kommentare werden gesondert verarbeitet 
                if (isset($comment['delete']) &&  $comment['delete'] === "1") {
                        $success = deletecomment($comment);
                        if (!$success && $error === false) {
                                $error = "Leider konnte der Kommentar '" . $comment["Teaser"] . "' nicht gelöscht werden.";
                        }
                        continue;
                }

                // überprüft ob alle daten für einen Kommentar vorhanden sind.
                if (empty($comment["Teaser"]) &&  empty($comment["Kommentar"]) && empty($comment["Datum"])) {
                        continue;
                }

                if (empty($comment["Teaser"]) ||  empty($comment["Kommentar"]) || empty($comment["Datum"])) {
                        $error = "Leider waren die eingegebenen Daten nicht vollständig.";
                        continue;
                }

                // neue kommentare werden gesondert verarbeitet
                if (isset($comment['add']) &&  $comment['add'] == true) {
                        $success = createComment($comment);
                        if (!$success && $error === false) {
                                $error = "Leider konnte der neue Kommentar '" . $comment["Teaser"] . "' nicht angelegt werden.";
                        }
                        continue;
                }

                // Umwandlng des eingegebenen Datums
                $datum = date("Y-m-d", strtotime($comment["Datum"]));
                // Updatet den übergebenen Kommentar
                $dbCon = db_connect();
                $qrycomment = "UPDATE kommentare set Teaser=?, Kommentar=?, Datum=? where ID=?";
                $stmnt = $dbCon->prepare($qrycomment);
                $stmnt->bind_param('sssi', $comment["Teaser"], $comment["Kommentar"], $datum, $comment["ID"]);
                $stmnt->execute();
                if (!$stmnt && $error === false) {
                        $error = "Leider konnte der Kommentar '" . $comment["Teaser"] . "' nicht aktualisiert werden.";
                }
        }
        return $error;
}

// löscht den gewählten Kommentar
function deletecomment($comment)
{
        $success = true;
        $dbCon = db_connect();
        $qrycomment = "DELETE FROM kommentare WHERE ID=?";
        $stmnt = $dbCon->prepare($qrycomment);
        $stmnt->bind_param('i', $comment["ID"]);
        $stmnt->execute();
        if ($stmnt->affected_rows !== 1) {
                $success = false;
        }
        return $success;
}

// erstellt einen neuen Kommentar
function createComment($comment)
{
        // Umwandlng des eingegebenen Datums
        $datum = date("Y-m-d", strtotime($comment["Datum"]));

        $dbCon = db_connect();
        $qrycomment = "INSERT INTO kommentare ( Teaser, Kommentar, Datum) VALUES (?,?,?)";
        $stmnt = $dbCon->prepare($qrycomment);
        $stmnt->bind_param('sss', $comment["Teaser"], $comment["Kommentar"], $datum);
        $stmnt->execute();
        if ($stmnt) {
                return true;
        } else {
                return false;
        }
}
