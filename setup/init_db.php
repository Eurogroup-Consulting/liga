<?php
    require_once '../config.php';
    require_once '../auth_db_functions.php';
    require_once '../admin/user_db_functions.php';
    require_once "init_db_functions.php";
?>
<!doctype html>
<html>
    <head>
    <?php    include '../layout/header.html';    ?>
        <title>Liga Setup - Init Database</title>
    </head>
    <body>
        <div class="container">
        <?php 
            // setzt die Datenbank auf UTF8
            setUTF8();
            // prüft ob bereits taelen zum liga system existieren
            if (tablesExist()): 
            ?> 
            <div class='card mt-3 '>
                <div class='card-header font-weight-bolder'>
                    Datenbank bereits erstellt
                </div>
                <div class='card-body'>
                    <p class='card-text'>Die Datenbank wurde breits erstellt. Zum erneuten Erstellen wenden Sie sich bitte an den Systemadministrator.</p>
                </div>
            </div>
        <?php
            die();
            elseif (!isset($_POST["action"])): 
        ?>
        <div class='card mt-3 '>
            <div class='card-header font-weight-bolder'>
                Datenbank erstellen
            </div>
            <div class='card-body'>
                <form class='form' action='/setup/init_db.php' method='POST'>
                    <p class='card-text'>Um die Liga Datenbank zu erstellen geben Sie bitte einen Administrator Account an.</br>Dieser wird beim Erstellen der Datenbank eingefügt und kann anschließend zur Verwaltung genutzt werden.</p>
                    <div class='form-group'>
                        <label for='admin-mail'>Email</label>
                        <input id='admin-mail' type='email' class='form-control' name='mail' value='' />
                    </div>
                    <div class='form-group'>
                        <label for='admin-password'>Password</label>
                        <input id='admin-password' class='form-control' type='password' name='password' value='' />
                    </div>
                    <div class='form-check'>
                        <input type='checkbox' class='form-check-input' id='add-data' name='add-data'>
                        <label for='add-data'>Demo-Daten mit erstellen? </label>
                    </div>
                    <button class='btn btn-lg btn-primary btn-block' name='action' type='submit' value='init_db'>Datenbank anlegen</button>
                </form>
            </div>
        </div>
            <?php
                die();
                else: 
            ?>
                <div class='card mt-3 '>
                    <div class='card-header font-weight-bolder'>
                        Initializing Database
                    </div>
            <?php
                // ertsellt die leere datenbankstruktur
                $result_db=run_sql_file("liga_db_empty.sql");

                // Card Footer für Init DB grün oder rot
                if ($result_db["success"]== $result_db["total"]) {
                        echo "<div class='card-footer text-success'>";
                }else{
                        echo "<div class='card-footer text-danger'>";    
                    }
                echo "SUCCESS : " . $result_db["success"] . "\n";
                echo "TOTAL   : " . $result_db["total"];
                echo "</div>"; // Card Footer ende
                echo "</div>"; // Card Ende
                
                // ertellt einen account mit den angegebenen nutzerdaten
                liga_register($_POST['mail'], $_POST['password']);
                // weißt dem erstellten account die administrator rechte zu
                $user = [["userGroup"=> "Admin", "userId" => 1, "TeamID" => 0]];
                updateUsers($user);



                if (isset($_POST['add-data'])) {
                    //---- Bootstrap Crad für Filling DB ------
                    echo "<div class='card mt-3 '>";      
                    echo "  <div class='card-header font-weight-bolder'>"; //Card Header
                    echo "    Filling Database"; //Card Header Content
                    echo "  </div>"; // Card Header Ende

                    // fügt demodaten in die datenbank ein
                    $result_data= run_sql_file("liga_demo_data.sql");

                    // Card Footer für Filling DB grün oder rot
                    if ($result_data["success"]== $result_data["total"]) {
                        echo "<div class='card-footer text-success'>";
                    }else{
                        echo "<div class='card-footer text-danger'>";    
                    }

                    echo "SUCCESS : " . $result_data["success"] . "\n";
                    echo "TOTAL   : " . $result_data["total"];

                    echo "</div>"; // Card Footer ende
                    echo "</div>"; // Card Ende
                }
                echo "<a href='/' class='btn btn-primary btn-lg active btn-block' role='button' aria-pressed='true'>Zur Anmeldung</a>";
            endif;
        ?>

        <footer>
        <?php      include '../layout/footer.html';      ?>
        </footer>
        </div> 
    </body>
</html>