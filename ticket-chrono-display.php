<?php

/*
 * 190929
 * timeticket / ticket-chrono-display.php
 * Baptiste Cadiou
 *
 */

include("HTML.class.php");
$html = new HTML("",-1);
$id= ( isset($_GET["id"])?urldecode($_GET["id"]):-1 );
$refresh= ( isset($_GET["refresh"])?urldecode($_GET["refresh"]):"" );
$query = "SELECT timediff(now(),(start)),stop FROM time WHERE id=".$id;
$result = $html->query($query);
if ($item = mysqli_fetch_array($result)) {
  if ($item[1]<>NULL) {
  	$chrono = "STOP";
  	$refresh= "no";
  }else{
  	$chrono = $item[0];
  }
} else {
  $chrono = "ERROR";
  $refresh= "yes";
}
echo "<html>";
if ($refresh=="yes") {
		echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1\">";
}
echo "<link href=\"style.css\" rel=\"stylesheet\" media=\"all\" type=\"text/css\" />";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo "<div class=\"chrono\">".$chrono."</div>";
echo "</html>";

?>
