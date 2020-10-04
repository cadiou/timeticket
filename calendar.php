<?php

/*
 * 200928
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

$html = new HTML(substr($date1,0,10)." &rarr; ".substr($date2,0,10),30);

$html->module_calendar();

############################################################## JOURS

$table = "<table>";

$table.= '<tr><td colspan=4 class=slug><a href="calendar.php?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.
		date("W",strtotime($year."W".$week."7-".($n+1)."week")).
		'">&larr;</a></td><td colspan=4 class=slug_droite><a href="calendar.php?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7+".($n+1)."week")).'&week='.
                date("W",strtotime($year."W".$week."7+".($n+1)."week")).
                '">&rarr;</a></td></tr>';

$table.= "<tr><td>SEMAINE</td><td>LUNDI</td><td>MARDI</td><td>MERCREDI</td><td>JEUDI</td><td>VENDREDI</td><td>SAMEDI</td><td>DIMANCHE</td></tr>";

for ($i = 0; $i <= $n; $i++) {

	# CLASSES DE LA SEMAINE

	$unixdate=strtotime($year."W".$week."+".$i."week");

	$query = "SELECT class_id".
                                 " FROM event WHERE station_id = ".CONFIG::ID_STATION.
                                 " AND not(unix_timestamp(start)-(86400*7) > ".$unixdate.
                                 " AND unix_timestamp(stop)-(86400*7) > ".$unixdate.")".
                                 " AND not(unix_timestamp(start) < ".$unixdate.
                                 " AND unix_timestamp(stop) < ".$unixdate.")".
                                 " GROUP by class_id ORDER by class_id";

        $result_class =  $html->query($query);


	# BARRE DE DATES DE LA SEMAINE

	$table.="<tr>";

	$table.="<td><h2>".date("W",strtotime($year."W".$week."7+".$i."week"))."</h2></td>";

	for ($j = 0;$j < 7;$j++) {

		$table.="<td>";

		$unixdate=strtotime($year."W".$week."+".(($i*7)+$j)."day");

		$table.="<h2>".date( "d M", $unixdate)."</h2>";

		$table.="</td>";
	}

	$table.="</tr>";

	# UNE LIGNE PAR CLASSE

	if (mysqli_num_rows($result_class)!=0) {

		while ($item_class = mysqli_fetch_array($result_class)) {

			$table.="<tr>";

			# AFFICAGE DE LA CLASSE

                        if ($item_class[0]>0) {
                        	$query = "SELECT class.name ".
                                	" FROM `class`".
                                        " WHERE class.id = ".$item_class[0]." and class.station_id = ".CONFIG::ID_STATION;
                                $result_classtext = $html->query($query);
                                if (mysqli_num_rows($result_classtext)>0) {
                                	$item_classtext = mysqli_fetch_array($result_classtext);
                                        $class = ($item_classtext[0]);
                                 }
                        }else{
                           	$class = "Lazarus";
                        }

			$table.= "<td>".$class."</td>";


			# JOURS PAR CLASSE

        		for ($j = 0;$j < 7;$j++) {

				$unixdate=strtotime($year."W".$week."+".(($i*7)+$j)."day");

              			$table.="<td>";



				$query = "SELECT id,name,class_id,format_id,start,stop,client,vendeur,reference,level".
				 " FROM event WHERE station_id = ".CONFIG::ID_STATION." and class_id = ".$item_class[0].
				 " AND not(unix_timestamp(start)-86400 > ".$unixdate.
				 " AND unix_timestamp(stop)-86400 > ".$unixdate.")".
				 " AND not(unix_timestamp(start) < ".$unixdate.
                                 " AND unix_timestamp(stop) < ".$unixdate.")".
				 " ORDER by class_id DESC,start,level";

				$result =  $html->query($query);


				if (mysqli_num_rows($result)!=0) {

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

						$table.= "<a href=\"calendar.php?id=".$item[0]."\"><h4 class=\"level".$item[9]."\">";
						$table.= $time;
						$table.= stripslashes(($item[1]!=""?$item[1]:"Lazarus"));
						$table.= "</h4></a>";
       				 	}

				}

				$table.="</td>";

			}



			$table.="</tr>";

		}

	}

	$table.="<tr><td colspan=8><hr></td></tr>";

}

$table.= "</table>";

$html->body($table);

# affichage

$html->out();

?>
