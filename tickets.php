<?php

/*
 * 190312
 * timeticket / tickets.php
 * Baptiste Cadiou
 *
 */

include("HTML.class.php");

$html = new HTML("Tickets",30);

$level =(isset($_GET['level'])?$_GET['level']:-1);

######## POUR INFO

if ($level==-2) {
	$html->ticket_threads("Cette semaine","datetime >= DATE_SUB(NOW(), INTERVAL 8 DAY)",true);
}

if ($level==-1) {
	$html->ticket_panel($html->ticket_level(0),"level=0 and active=1");
	$html->ticket_panel("à faire ou en cours","(level=1 or level=2 or level=3) and active=1");
	$html->ticket_panel($html->ticket_level(4),"level=4 and active=1");
	$html->ticket_panel($html->ticket_level(5),"level=5 and active=1");
	$html->ticket_threads("Archives -30 jours","datetime >= DATE_SUB(NOW(), INTERVAL 30 DAY)",false);
}


if ($level>=0) {
	$html->ticket_threads($html->ticket_level($level),"(level=".$level.") and datetime >= DATE_SUB(NOW(), INTERVAL 30 DAY)",-1);
}

$html->module_login();
$html->module_ticket();
# $html->tools();

$html->out();

?>
