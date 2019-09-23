<?php

/*
 * 190923
 * PORTAIL INFOGRAPHIE
 * bcadiou@videlio-globalservices.com
 *
 */

include("HTML.class.php");
 
$html = new HTML("Portail Infographie",60);
$html->module_login();
$html->vizrt();
$html->module_ticket();
$html->gfx_catalog();
$html->tools();
$html->agence_photo();
$html->module_courtesy();
$html->rt_videlio();
$html->mos_stack();
$html->ticket_panel("Tickets Postprod","datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND LEVEL != 4");

$html->out();
 
?>
