<?php

/*
 * 190927
 * timeticket
 * bcadiou@videlio-globalservices.com
 *
 */

include("HTML.class.php");

$html = new HTML("timeticket",60);
$html->module_login();
$html->module_ticket();
#$html->vizrt();
#$html->gfx_catalog();
#$html->tools();
#$html->agence_photo();
#$html->module_courtesy();
#$html->mos_stack();
$html->ticket_panel("Tickets Postprod","datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND LEVEL != 4");

$html->out();

?>
