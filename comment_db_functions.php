<?php
require_once 'liga_db_functions.php';
function getComments(){
    $dbCon = db_connect();
    $qryComment = "SELECT * FROM kommentare WHERE  Datum<=NOW() ORDER BY Datum DESC LIMIT 1";
    $stmnt = $dbCon->prepare($qryComment);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $comment = $res->fetch_assoc();
    return $comment;
}
?>