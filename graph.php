<?php
    require_once 'config/default.php';
    require_once 'auth.php';
    require_once 'lib_liga.php';  
    include 'lib_ranking.php';
?>
<!doctype html>
<html>
<head>
<?php    include 'layout/header.html';    ?>
    <link href="assets/css/landscape.css" rel="stylesheet">



    <title>Liga Home</title>
</head>
<?php
  $ranking=rankdata($_SESSION['userLiga']);
  $data=$ranking["data"]; //enthält die Datenreihen
  $axis=$ranking["axis"]; //enthält die Axenbeschriftung
  $teams=$ranking["teams"];
?>
<body>
  <!-- NAVIGATION -->
  <?php include "navigation.php"; ?>



  <!-- CONTENT --> 
    <div id="container" class="container">
      <div class="card mt-3 h-100">
        <div class="card-body">
              <canvas id="myChart"></canvas>
          </div>
      </div>
    </div>
    <div id="turn" class="container">
      <div class="card mt-3 h-100">
        <div class="card-body text-center">
              <p>SmartPhone bitte drehen!</p>
          </div>
      </div>
    </div>
<!-- Graph JavaScript -->
<?php
  $ranking=rankdata($_SESSION['userLiga']);
  $data=$ranking["data"]; //enthält die Datenreihen
  $axis=$ranking["axis"]; //enthält die Axenbeschriftung
  $teams=$ranking["teams"];
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>

    <script>
      var ctx = document.getElementById('myChart').getContext('2d');
      var myChart = new Chart(ctx, {
          type: 'line',
          data: {
          
          <?php

            echo "labels: [";
            foreach($axis as $c => $week){
               echo "\"$week\", ";
            } 
            echo " ], \n" ;
          //  labels: ["KW1", "KW2", "KW3", "KW4", "KW5", "KW6", "KW7"],
          
          $z=0;
          echo "datasets: [ \n";
            foreach($data as $teamid => $row){
              echo "{ \n";
              echo "label: '$teams[$teamid]' ,";
              echo "data: [";
              foreach($row as $r => $v){
                echo "$v, ";
              }
              echo "], \n";
            
              echo "lineTension: 0.2, \n";
              echo "backgroundColor: 'transparent', \n";
              echo "borderColor: '" . getTeamColor($z) . "',\n";
              echo "borderWidth: 2, \n";
              echo "pointRadius: 2, \n";
              echo "pointBackgroundColor: '" . getTeamColor($z) . "' \n";
              echo "}, \n ";
              $z++;
            
            }  
          
          echo "]"
          
          ?>
          
        
    },
    options: {
        legend:{
            display: true,
            position: 'bottom',
            labels:{
              usePointStyle: true,
              boxWidth: 10
            }
        },
        scales: {
            yAxes: [{
              ticks: {
                reverse: true,
                stepSize: 1
                }
            }]
        }
    }
});
</script>
</body>
<footer>
      <?php      include 'layout/footer.html';      ?>
  </footer>
</html>