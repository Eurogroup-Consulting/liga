<?php
    require_once dirname(__FILE__). '/config/default.php';
    require_once dirname(__FILE__). '/auth.php';
    require_once dirname(__FILE__). '/lib/lib_liga.php';  
    include dirname(__FILE__). '/lib/lib_ranking.php';
?>
<!doctype html>
<html>
<head>
    <?php    include dirname(__FILE__). '/layout/header.html';    ?>
    <title>Liga Home</title>
</head>
<body>
  <!-- NAVIGATION -->
  <?php include dirname(__FILE__). "/navigation.php"; ?>

  <!-- CONTENT --> 
    <div class="container">
    
    <div class="card mt-3 ">
        <div class="card-header font-weight-bolder">
          Aktuelle Mitteilung 
        </div>
        <div class="card-body">
          <blockquote class="blockquote mb-0">
            
              <p><?php
              $comment=akt_comment();
              if (strlen($comment) >0) :     ?> </p>
              <?= $comment?>
            <a href="comment.php" class="stretched-link"></a>

              <?php else: ?>
                <p> Aktuell gibt es keine aktuellen Mitteilungen.
              </p>
              <?php endif ?> 
            
          </blockquote>
        </div>
      </div> 


          <?php if ($_SESSION['userLiga'] == 0): ?> 
          <div class="card mt-3 ">
              <div class="card-header font-weight-bolder">
                  Sie wurden keiner Liga zugeteilt
              </div>
              <div class="card-body">
                  <blockquote class="blockquote mb-0">
                      <p> Bisher wurden Sie keiner Liga zugeteilt.
                          <br>
                          Bitte wenden Sie sich an einen Administrator.</p>
                  </blockquote>
              </div>
          </div>
          <?php else: ?>
            <?php
              $ranking = rankdata($_SESSION['userLiga']);
              $data = $ranking["data"]; //enthält die Datenreihen
              $axis = $ranking["axis"]; //enthält die Axenbeschriftung
              $teams = $ranking["teams"];
            ?>
      <div class="card mt-3 ">
        <div class="card-header font-weight-bolder">
            Wochenranking
        </div>
        <div class="card-body">
          <blockquote class="blockquote mb-0">
               Dein Team <b><?php echo $teams[$_SESSION['teamid']] ?></b> 
               ist in der aktuellen Woche im gesamten Verlauf auf Platz 
               <span class="badge badge-primary"> <?php echo end($data[$_SESSION['teamid']]) ?> </span>
            <a href="ranking.php" class="stretched-link"></a>
          </blockquote>
        </div>
      </div>
            
      <div class="card mt-3 ">
      <div class="card-header font-weight-bolder">
           Gesamtranking der Teams
        </div>
        <div class="card-body">
            <div>
              <canvas id="myChart" height="100"></canvas>
              <a href="graph.php" class="stretched-link"></a>
            </div>
        </div>
      </div>


<!-- Graph JavaScript -->
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
              
              if($teamid==$_SESSION['teamid']){
                echo "borderColor: '#FF0000',\n";  
                echo "pointBackgroundColor: '#FF0000', \n";
                echo "borderWidth: 2, \n";
                echo "pointRadius: 2 \n";
              }else{
                echo "borderColor: '#808080',\n";
                echo "pointBackgroundColor: '#808080', \n";
                echo "borderWidth: 1, \n";
                echo "pointRadius: 0 \n";
              }
              
              
              
              echo "}, \n ";
              $z++;
            
            }  
          
          echo "]"
          
          ?>
          
        
    },
    options: {
        legend:{
            display: false,
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

<?php endif ?>
</div>
  <footer>
    <?php      include dirname(__FILE__). '/layout/footer.html';      ?>
  </footer>


</body>
</html>