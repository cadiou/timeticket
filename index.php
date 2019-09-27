<?php

/*
 * 190927
 * timeticket | index.php
 * bcadiou@videlio-globalservices.com
 *
 */

# CLASSE HTML
include("HTML.class.php");
$html = new HTML("timeticket",60);

# MARGE
$html->module_login();
$html->module_ticket();

# BODY
$html->ticket_panel("Tickets","datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND LEVEL != 4");

# PAGE
$html->out();

?>
