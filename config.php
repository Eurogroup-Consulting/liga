<?php
/* 
Muss in jede PHP-Seite eingebunden werden,
um die Session zu starten und Standards zu definieren */

/* Session */
    session_cache_limiter('nocache');
    $cache_limiter = session_cache_limiter();

    session_cache_expire(10);
    $cache_expire=  session_cache_expire();
    session_start();

/* Definition Farbpalette für Teams in Charts etc.
   Durchlaufend nicht Teamnamengebunden
   Quelle https://wiki.selfhtml.org/wiki/Grafik/Farbpaletten
*/
define('TEAM_COLORS', array(
    "#8B0000", // Darkread
    "#008000", // Green
    "#0000CD", // MediumBlue
    "#FF8C00", // DarkOrange
    "#00FFFF", // Cyan
    "#FF00FF", // Magenta
    "#FFFF00", // Yellow
    "#800080", // Purple
    "#ADFF2F", // GreenYelloa
    "#6A5ACD", // SalteBlue
    )
);
// Funktion zum Abfangen eines Fehlers bei nicht vorhandener Farbe
function getTeamColor($c){
    return (count(TEAM_COLORS)>$c?TEAM_COLORS[$c]:TEAM_COLORS[count($c)-1]);
}
?>
