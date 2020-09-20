<?php

/*
 * 200920
 * timeticket / calendar
 * bcadiou@videlio-globalservices.com
 *
 */

include("HTML.class.php");

$week = substr("0".(isset($_GET['week'])?$_GET['week']:date('W')),-2);
$year = (isset($_GET['year'])?$_GET['year']:date('Y'));
$n = ((isset($_GET['n'])?$_GET['n']:1)-1);

$date1 = date( "Y-m-d 00:00:00", strtotime($year."W".$week."1") ); // First day of week
$date2 = date( "Y-m-d 23:59:59", strtotime($year."W".$week."7+".$n."week") ); // Last day of week

$jour[1]="DIM";
$jour[2]="LUN";
$jour[3]="MAR";
$jour[4]="MER";
$jour[5]="JEU";
$jour[6]="VEN";
$jour[7]="SAM";

$html = new HTML("Calendrier ".substr($date1,0,10)." &rarr; ".substr($date2,0,10),3600);

############################################################## CONTROL

$html->body($date1);

############################################################## JOURS

$table = "<table><tr>";



$table.= "</tr></table>";

$html->body($table);

# affichage

$html->out();

?>
