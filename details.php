<?php
    require_once 'config.php';
    require_once 'auth.php';
    require_once 'liga_db_functions.php';

    
    if (isset($_GET['teamid'])){
        $TeamID=$_GET['teamid'];
    } else{
        $TeamID=0;
    }
    if (isset($_GET['ligaid'])){
        $LigaID=$_GET['ligaid'];
    } else{
        $LigaID=0;
    }

    if (isset($_GET['kum'])){
        $kum=$_GET['kum'];
    } else{
        $kum=0;
    }

    $details=getTeamDetails($TeamID,$LigaID,$kum);
    $keyedOther=getOtherTeams($TeamID,$LigaID,$kum);
?>

<!doctype html>
<html>
<head>
<?php    include 'layout/header.html';    ?>
    <title>Team Details</title>
    <script type="text/javascript">
        function changeURL(field){
                value = field.checked?1:0
                url = location.href
                if(url.match(/kum=/)){
                    newUrl = url.replace(/kum=[0-1]/, "kum=" + value)
                } else {
                    newUrl = url +"&kum=" + value
                }
                window.location = newUrl
        }
    </script>
</head>

<body>
  <!-- NAVIGATION -->
  <?php include "navigation.php"; ?>
    <div class="container">
        <ul class="list-group">
            <li class="list-group-item list-group-item-secondary d-flex justify-content-between align-items-center">
                <span class="font-weight-bold">  <?=$details[0]["Teamname"] . " - " . $details[0]["LigaName"]?> </span>
                <span class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitches" onchange="changeURL(this)" <?=$kum?'checked':''?> value="1">
                    <label class="custom-control-label" for="customSwitches">Kumuliert</label>
                </span>
            </li>
        <?php
            $prvGruppe="";
            foreach($details as $entry){
            if($entry["Gruppe"]!=$prvGruppe){ 
                $prvGruppe=$entry["Gruppe"]?>
            <li class="list-group-item list-group-item-danger d-flex justify-content-between align-items-center">     
                <span class="mr-auto font-weight-bold p-1">  
                    <?=$entry["Gruppe"] ?>
                </span>
                <div class="p-2 align-items-center">
                        <span class="badge badge-light badge-pill" style="width:45px" title="Punkte Ihres Teams">&Sigma;</span>
                    </div>|
                    <div class="p-2 align-items-center">
                        <span class="badge badge-light badge-pill"  style="width:45px" title="Durchschnittliche Punkte anderer Teams Ihrer Liga">&Oslash;</span>
                    </div>    
    
            </li>
            <?php }?>        
            <li class="list-group-item d-flex justify-content-between align-items-center">    
                <span class="mr-auto font-weight-bold p-2">  
                    <?=$entry["Kurzbezeichnung"] ?>
                </span>
                    <div class="p-2 align-items-center">
                        <span class="badge badge-danger badge-pill" style="width:45px" title="Punkte Ihres Teams"><?=round($entry["Punkte"])  ?></span>
                    </div>|
                    <div class="p-2 align-items-center">
                        <span class="badge badge-secondary badge-pill"  style="width:45px" title="Durchschnittliche Punkte anderer Teams Ihrer Liga"><?=round($keyedOther[$entry["Kurzbezeichnung"]]) ?></span>
                    </div>    
            </li>
        
        <?php }  ?>
        
        </ul> 
    </div>

</body>
<footer>
      <?php      include 'layout/footer.html';      ?>
  </footer>
</html>
