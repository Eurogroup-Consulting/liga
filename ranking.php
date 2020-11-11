<?php
    require_once 'config.php';
    require_once 'auth.php';
    require_once 'liga_db_functions.php';
    
    if (isset($_SESSION['userLiga'])){
        $ligaid=$_SESSION['userLiga'];
    } else{
        $ligaid=0;
    }
    if (isset($_GET['ligaid'])) $ligaid=$_GET['ligaid'];

        $dbCon1= db_connect();
        $dbCon2= db_connect();
        
        //$qryRanking="SELECT TeamID, Teamname, SUM(Punkte) AS sPunkte FROM query_kumkw WHERE LigaID='$ligaid' GROUP BY Teamname ORDER BY sPunkte DESC";
        $qryRanking=QUERY_RANKING; //Definiert in liga_db_functions
        $stmnt=$dbCon1->prepare($qryRanking); 
        $stmnt->bind_param('i',$ligaid);
        $stmnt->execute();       
        $stmnt->bind_result($dbSaisonID,$dbSaisonTxt,$dbLigaID1,$dbLigaName1, $dbTeamID, $dbTeamName,$dbTeamAnzahl,$dbTeamPunkte);
        $stmnt->fetch();

        $qryLigen="SELECT ID, LigaName FROM ligen ORDER By LigaName";
        $stligen=$dbCon2->prepare($qryLigen);
        $stligen->execute();
        $stligen->bind_result($dbLigaID, $dbLigaName);
        $stligen->fetch();

        $ligaid=($ligaid==0)?$dbLigaID:$ligaid;
?>

<!doctype html>
<html>
<head>
    <?php    include 'layout/header.html';    ?>
    
    <title>Liga Ranking <?php echo(date("H:m:s")) ?></title>
</head>

<!-- NAVIGATION -->
<?php include "navigation.php"; ?>

<body>

    <div class="container mt-3">

        <div class="list-group ">
            <select id="ligaselector" class="custom-select custom-select-lg bg-light border-danger">
                <?php do { ?>
                    <option value="<?php print($dbLigaID);?>" <?php print(($dbLigaID==$ligaid)?" selected ":" ");?> > 
                        <?php print($dbLigaName); ?> 
                    </option>
                <?php } while($stligen->fetch()) ?>
            </select>
              
            <?php
                $TeamRang=1;
                do { ?>    
                    <a class="list-group-item list-group-item-action" href="details.php<?php print("?teamid=".$dbTeamID."&ligaid=".$dbLigaID1); ?>" >  
                       <span class="badge badge-pill badge-light" > 
                            <?php print($TeamRang); ?> 
                        </span> 
                        <?php print($dbTeamName) ; ?>
                    </a>
            <?php
                $TeamRang++; 
                } while($stmnt->fetch()) ?>

        </div>
          
    </div><!-- /content -->
<footer>
    <?php include 'layout/footer.html';      ?>
    <script language="javascript">
    $('#ligaselector').on('change', function(e){
        
        window.location.href = "ranking.php?ligaid=" + this.options[this.selectedIndex].value;
        //alert(this.options[this.selectedIndex].value);
    });
    </script>
</footer>
</body>    
</html>