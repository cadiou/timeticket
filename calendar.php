<?php

/*
 * 200921
 * timeticket / calendar
 * bcadiou@videlio-globalservices.com
 *
 */

include("HTML.class.php");

$week = substr("0".(isset($_GET['week'])?$_GET['week']:date('W')),-2);
$year = (isset($_GET['year'])?$_GET['year']:date('Y'));
$n = ((isset($_GET['n'])?$_GET['n']:6)-1);

$date1 = date( "Y-m-d 00:00:00", strtotime($year."W".$week."1") ); // First day of week
$date2 = date( "Y-m-d 23:59:59", strtotime($year."W".$week."7+".$n."week") ); // Last day of week

$html = new HTML(substr($date1,0,10)." &rarr; ".substr($date2,0,10),5);

$html->module_calendar();

############################################################## JOURS

$table = "<table>";

$table.= "<td></td><td>LUN</td><td>MAR</td><td>MER</td><td>JEU</td><td>VEN</td><td>SAM</td><td>DIM</td>";

for ($i = 0; $i <= $n; $i++) {

	$table.="<tr>";
	
	$table.="<td>".date("W",strtotime($year."W".$week."7+".$i."week"))."</td>";
	
	for ($j = 0;$j < 7;$j++) {
	
		$table.="<td>";
		
		$unixdate=strtotime($year."W".$week."+".(($i*7)+$j)."day");
		
		$table.="<h2>".date( "d M", $unixdate)."</h2>";
	
		$query = "SELECT id,name,class_id,format_id,start,stop,client,vendeur,reference,level".
				 " FROM event WHERE station_id = ".CONFIG::ID_STATION.
				 " AND not(unix_timestamp(start)-86400 > ".$unixdate.
				 " AND unix_timestamp(stop)-86400 > ".$unixdate.")".
				 " AND not(unix_timestamp(start) < ".$unixdate.
                                 " AND unix_timestamp(stop) < ".$unixdate.")".
				 " ORDER by class_id DESC,start,level";
	
		$result =  $html->query($query);
		

		if (mysqli_num_rows($result)!=0) {

			$last_class = "";

        		while ($item = mysqli_fetch_array($result)) {
				if (date( "d M",strtotime($item[4]))==date( "d M",$unixdate)) {
					$time = date( "H:i",strtotime($item[4]));
				}else{
					$time="...";
				}
				
				$time.=" - ";

 				if (date( "d M",strtotime($item[5]))==date( "d M",$unixdate)) {
                                        $time.= date( "H:i",strtotime($item[5]));
                                }else{
                                        $time.="...";
                                }

				$time.="<br>";

				if ($item[2]>0) {
					$query = "SELECT class.name ".
					" FROM `class`".
					" WHERE class.id = ".$item[2]." and class.station_id = ".CONFIG::ID_STATION;
					$result2 = $html->query($query);
					if (mysqli_num_rows($result2)>0) {
						$item_class = mysqli_fetch_array($result2);
						if ($item_class[0]<>$last_class) {
							$last_class=$class;
							$class = ($item_class[0])."<br>";
						}else{
							$class = "";
						}
					}
				}else{
					$class = "";
				}

				$last_class=$item_class[0];

				
				$table.= "<h3>".$class."</h3>";
				$table.= "<a href=\"calendar.php?id=".$item[0]."\"><h4 class=\"level".$item[9]."\">";
				$table.= $time;
				$table.= stripslashes(($item[1]!=""?$item[1]:"Lazarus"));
				$table.= "</h4></a>";
       		 	}
			 
		}
	
		$table.="</td>";
	
	}
	
	$table.="</tr><tr><td colspan=8><hr></td></tr>";
	
}

$table.= "</table>";

$html->body($table);

# affichage

$html->out();

?>
