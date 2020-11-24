<?php !defined("PHP_BASE_PATH") && define("PHP_BASE_PATH", dirname(__FILE__));?> 
<?php include_once PHP_BASE_PATH.'/lib_auth.php'; ?>
<?php include_once PHP_BASE_PATH.'/auth.php'; ?>

<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light" style="min-height:60px; padding-top: 10px; padding-left: 18px; padding-right: 10px;">
        <!-- Navbar Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="images/LigaLogo60r.png" width="40" height="40" class="d-inline-block align-center" alt="">
            Liga
        </a>
        
        <!-- Navbar Hamburger-Menubutton -->
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Inhalt -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                
                <?php if (isAdmin()) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                            <a class="dropdown-item" href="admin/users.php">User</a>
                            <a class="dropdown-item" href="admin/maks.php">MAK</a>
                            <a class="dropdown-item" href="admin/leagues.php">Ligen</a>
                            <a class="dropdown-item" href="admin/seasons.php">Saisons</a>
                            <a class="dropdown-item" href="admin/teams.php">Teams</a>
                            <a class="dropdown-item" href="admin/allocation.php">Zuordnung</a>
                            <a class="dropdown-item" href="admin/import.php">CSV Import</a>
                            <a class="dropdown-item" href="admin/comments.php">Kommentare</a>

                        </div>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Abmelden</a>
                </li>
            </ul>
        </div>
</nav>
