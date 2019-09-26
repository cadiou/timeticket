<?php

/*
 * 190926 
 * TICKET CHRONO DISPLAY
 * bcadiou@videlio-globalservices.com
 *
 */

include("DB.class.php");
 
$id= ( isset($_GET["id"])?urldecode($_GET["id"]):"" );	
$refresh= ( isset($_GET["refresh"])?urldecode($_GET["refresh"]):"" );	

$timegraph = new DB();

$query = "SELECT timediff(now(),(start)),stop FROM time WHERE id=".$id;
$result = $timegraph->query($query);
$item = mysqli_fetch_array($result);
if ($item[1]<>NULL) {
	$chrono = "STOP";
	$refresh= "no";
}else{
	$chrono = $item[0];
}
echo "<html>";
if ($refresh=="yes") {
		echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1\">";
}
echo "<link href=\"/style.css\" rel=\"stylesheet\" media=\"all\" type=\"text/css\" />";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo "<div class=\"chrono\">".$chrono."</div>";
echo "</html>";

?>