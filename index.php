<?php

/*
 * 210904
 * timeticket / index.php
 * Baptiste Cadiou
 *
 */

# CLASSE HTML
include("HTML.class.php");
$html = new HTML("Tickets Actifs",60);

# MARGE
$html->module_login();
$html->module_ticket();
$html->module_calendar(date('Y'),date('W'));
$html->module_dosamco(1);
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
<p /></td></tr><tr><td>
';
$html->ticket_panel($html->ticket_level(4),"LEVEL = 4 and active = 1");
$html->body.='
<p /></td></tr><tr><td>
';
$html->ticket_panel($html->ticket_level(5),"LEVEL = 5 and active = 1");
$html->body.='
</td></tr></table>
';

# PAGE
$html->out();

?>
