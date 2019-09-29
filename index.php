<?php

/*
 * 190927
 * timeticket / index.php
 * Baptiste Cadiou
 *
 */

# CLASSE HTML
include("HTML.class.php");
$html = new HTML("timeticket",60);

# MARGE
$html->module_login();
$html->module_ticket();

# BODY
$html->body.='
<table><tr><td rowspan="3">
';
$html->ticket_panel("Projets en cours","(level = 1 or level = 2 or level = 3) and active = 1");
$html->body.='
</td><td>
';
$html->ticket_panel($html->ticket_level(0),"LEVEL = 0 and active = 1");
$html->body.='
</td></tr><tr><td>
';
$html->ticket_panel($html->ticket_level(4),"LEVEL = 4 and active = 1");
$html->body.='
</td></tr><tr><td>
';
$html->ticket_panel($html->ticket_level(5),"LEVEL = 5 and active = 1");
#$html->body.='
#</td></tr><tr><td colspan="3">
#';
# $html->ticket_panel("Archives 10 jours","datetime >= DATE_SUB(NOW(), INTERVAL 10 DAY) and active = 0");
$html->body.='
</td></tr></table>
';

# PAGE
$html->out();

?>
