<?php

require_once '../liga_db_functions.php';

function getUsers()
{
    $dbCon = db_connect();
    $qryusers = "SELECT user_id, email, userGroup, TeamID FROM account ORDER BY email";
    $stmnt = $dbCon->prepare($qryusers);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $users = $res->fetch_all(MYSQLI_ASSOC);
    return $users;
}

function updateUsers($users)
{
    $error = false;
    foreach ($users as $user) {
        if (isset($user['delete']) &&  $user['delete'] === "1") {
            $success = deleteUser($user);
            if (!$success && $error === false) {
                $error = true;
            }
            continue;
        }
        $dbCon = db_connect();
        $qryusers = "UPDATE account set userGroup=?, TeamID=? where user_id=?";
        $stmnt = $dbCon->prepare($qryusers);
        $stmnt->bind_param('sii', $user["userGroup"], $user["TeamID"], $user["userId"]);
        $stmnt->execute();
        if ($stmnt->affected_rows !== 1 && $error === false) {
            $error = true;
        }
    }
    return $error;
}

function deleteUser($user)
{
    $error = false;
    $dbCon = db_connect();
    $qryusers = "DELETE FROM account  WHERE user_id=?";
    $stmnt = $dbCon->prepare($qryusers);
    $stmnt->bind_param('i', $user["userId"]);
    $stmnt->execute();
    if ($stmnt->affected_rows !== 1) {
        $error = true;
    }
    return $error;
}
