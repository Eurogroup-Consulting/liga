<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'lib_liga.php';
require_once 'lib_comment.php';

$comment = getComments()
?>
<!doctype html>
<html>

<head>
  <?php
  include 'layout/header.html';
  ?>

  <title>Liga Kommentar</title>
</head>


<body>
  <!-- NAVIGATION -->
  <?php include "navigation.php"; ?>



  <!-- CONTENT -->
  <div class="container">
    <?php
    if (!$comment) {
      echo ('
            <div class="card mt-3 ">
          <div class="card-header font-weight-bolder">
           Keine aktuellen Mitteilungen
          </div>
          <div class="card-body">
            <blockquote class="blockquote mb-0">
              <p> Aktuell gibt es keine aktuellen Mitteilungen.
              </p>
            </blockquote>
          </div>
          </div>');
      die();
    }
    ?>

    <div class="card mt-3 ">
      <div class="card-header font-weight-bolder">
        Aktuelle Mitteilung - <?= date_format(date_create_from_format("Y-m-d",$comment['Datum']), "d.m.Y") ?>
      </div>


      <div class="card-body">
      <div class="card-title font-weight-bolder"><?php echo $comment['Teaser'] ?></div>
        
        <blockquote class="blockquote mb-0">
          <?php echo $comment['Kommentar'] ?>

        </blockquote>
      </div>
    </div>
    <footer>
      <?php include 'layout/footer.html';      ?>
    </footer>
</body>

</html>