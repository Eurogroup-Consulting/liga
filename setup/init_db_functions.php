<?php
    require_once '../config.php';
    require_once '../auth_db_functions.php';
    require_once '../admin/user_db_functions.php';
    require_once "liga_config.php";

$mSqlObj= @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, null, null);

function setUTF8(){
    global $mSqlObj;
    if (!$mSqlObj->connect_error){
        mysqli_query($mSqlObj, "SET NAMES 'utf8'");
    } else{
        die("Error Conneting to Liga-Database");
    }
}
function tablesExist() {
    global $mSqlObj;
    $qryExists = "SELECT count(table_name) as tables FROM information_schema.tables WHERE table_schema = 'liga_db'";
    $stmnt = $mSqlObj->prepare($qryExists);
    $stmnt->execute();
    $res = $stmnt->get_result();
    $count = $res->fetch_assoc();
    if($count["tables"] > 0) {
        return true;
    }
    return false;
}

//* **************************************************
//* Funktion zum Importieren einer SQL-Datei (Backup)
//* **************************************************
function run_sql_file($location){
    global $mSqlObj;
    $total = $success = 0; //Zähler
    $query = '';
    $sqlScript = file($location);
    foreach ($sqlScript as $line)	{
        
        $startWith = substr(trim($line), 0 ,2);
        $endWith = substr(trim($line), -1 ,1);
        
        //Kommentare überspringen
        if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
            continue;
        }
            
        $query = $query . $line;
        if ($endWith == ';') {
            if(mysqli_query($mSqlObj, $query)==false){
                print("<small>");
                print(substr($query,0,25) . "<br>");
                print("</small>");
            } else {
                $success++ ;
            }
            $total += 1;           
            $query= '';		
        }
    }
    //Rückgabe der Anzahl für success und total 
    return array(
    "success" => $success,
    "total" => $total
    );
}


// Benötigte 'startsWith' FunKtion
function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

