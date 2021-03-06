<?php
    require_once dirname(__FILE__). '/config/default.php';
    require_once dirname(__FILE__). '/auth.php';
    require_once dirname(__FILE__). '/lib/lib_liga.php';
    require_once dirname(__FILE__). '/lib/lib_ranking.php';
    
    if (isset($_SESSION['userLiga'])){
        $ligaid=$_SESSION['userLiga'];
    } else{
        $ligaid=0;
    }
    if (isset($_GET['ligaid'])) $ligaid=$_GET['ligaid'];

    $ranking = getRanking($ligaid);
    $leagues = getLigaInfos();
    $ligaid=($ligaid==0)?$leagues[0]["ID"]:$ligaid;
?>

<!doctype html>
<html>
<head>
    <?php    include dirname(__FILE__). '/layout/header.html';    ?>
    
    <title>Liga Ranking <?php echo(date("H:m:s")) ?></title>
</head>

<!-- NAVIGATION -->
<?php include dirname(__FILE__). "/navigation.php"; ?>

<body>

    <div class="container mt-3">

        <div class="list-group ">
            <select id="ligaselector" class="custom-select custom-select-lg bg-light border-danger">
                <?php
                    foreach ($leagues as $key => $league) {
                ?>
                <option value="<?= $league["ID"]?>" <?=($league["ID"]==$ligaid)?" selected ":" "?> > 
                    <?=$league["LigaName"] ?> 
                </option>
                <?php
                    }
                ?>
            </select>
              
            <?php
                $TeamRang=1;
                foreach ($ranking as $key => $rank) {
            ?>

            <a class="list-group-item list-group-item-action" href="details.php<?="?teamid=".$rank["TeamID"]."&ligaid=".$rank["LigaID"] ?>" >  
                <span class="badge badge-pill badge-light" > 
                    <?=$TeamRang?> 
                </span> 
                <?=$rank["Teamname"] ?>
            </a>
            <?php
                $TeamRang++; 
                }
            ?>

        </div>
          
    </div><!-- /content -->
<footer>
    <?php include dirname(__FILE__). '/layout/footer.html';      ?>
    <script language="javascript">
    $('#ligaselector').on('change', function(e){
        
        window.location.href = "ranking.php?ligaid=" + this.options[this.selectedIndex].value;
        //alert(this.options[this.selectedIndex].value);
    });
    </script>
</footer>
</body>    
</html>