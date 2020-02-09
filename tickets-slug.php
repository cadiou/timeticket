<?php

/*
 * 200209
 * timeticket / tickets-slug.php
 * Baptiste Cadiou
 *
 */

include("HTML.class.php");

 if (isset($_GET["concept_id"])) {
	$concept_id=$_GET["concept_id"];
}else{
	$concept_id=(isset($_POST["concept_id"])?$_POST["concept_id"]:0);
}

if (isset($_GET["class_id"])) {
	$class_id=$_GET["class_id"];
}else{
	$class_id=(isset($_POST["class_id"])?$_POST["class_id"]:0);
}

$html = new HTML("Liste des Slugs",60);

$html->module_login();
$html->module_ticket();

$out="<FORM ACTION=\"tickets-slug.php\" method=\"GET\"><table>";
$out.='<th>Date</th><th>Slug</th><th>Concept</th><th>Classe</th><th>System</th><th>Time</th><th>Activité</th></tr><tr>';
$out.='<tr><td></td><td></td><td>'.$html->menuConcept($concept_id).'</td><td></td><td></td></tr>';

$sql = "SELECT slug.thread, slug.name, ticket.datetime, slug.concept_id, slug.class_id, slug.system_id"
		." FROM slug, ticket"
		." WHERE ticket.id=slug.thread"
		.($concept_id>0?' AND slug.concept_id='.$concept_id:'')
        ." and ticket.station_ID = ".CONFIG::ID_STATION
		." and slug.station_ID = ".CONFIG::ID_STATION
		." ORDER by slug.thread DESC"
		." LIMIT 100";

$result = $html->query($sql);

while ($item = mysqli_fetch_array($result)) {
	$out .= '<tr class="THEME"><td>'.$item['datetime']
         .'</td><td><a href="ticket.php?thread='.$item['thread'].'">'.$item[1].'</a></td><td>'
         .$html->concept($item[0]).'</td><td>'.$html->myclass($item[0]).'</td><td>'.$html->mysystem($item[0]).'</td><td>'
         .$html->time_time($item[0]).'</td><td class="level'.$html->level($item[0]).'">'.$html->time_tracker($item[0])
         .'</td></tr>'."\n";
}

$out.='</table>';

$html->body($out);

$html->out();

?>
