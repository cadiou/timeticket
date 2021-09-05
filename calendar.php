<?php

/*
 * 201011
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

$html->module_login();
$html->module_ticket();
$html->module_calendar($year,$week);

# CALENDRIER

$table = "<table width=\"100%\">";
$table.= '<tr><td colspan=4 class=slug><a href="calendar.php?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.
		date("W",strtotime($year."W".$week."7-".($n+1)."week")).
		'">&larr;</a></td><td colspan=4 class=slug_droite><a href="calendar.php?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7+".($n+1)."week")).'&week='.
                date("W",strtotime($year."W".$week."7+".($n+1)."week")).
                '">&rarr;</a></td></tr>';
$table.="<tr><td>Semaine</td>";
for ($i = 0; $i <7 ; $i++) {
	$table.= "<td>";
	if ((date("w")==($i+1)) or (date("w")==0 and $i==6)) {
		$table .= "<h2>";
	}
	$table.= strftime("%A", ($i+4)*24*3600);
	if ((date("w")==($i+1))or (date("w")==0 and $i==6)) {
                $table .= "</h2>";
        }
	$table.= "</td>";
}
$table.="</tr>";
for ($i = 0; $i <= $n; $i++) {
	# CLASSES DE LA SEMAINE
	$unixdate=strtotime($year."W".$week."+".$i."week");
	$query = "SELECT class_id".
        " FROM event WHERE station_id = ".CONFIG::ID_STATION.
        " AND not(unix_timestamp(start)-(86400*7) >= ".$unixdate.
        " AND unix_timestamp(stop)-(86400*7) >= ".$unixdate.")".
        " AND not(unix_timestamp(start) < ".$unixdate.
        " AND unix_timestamp(stop) < ".$unixdate.")".
        " UNION ";
	$query.= "SELECT class_id".
		" FROM slug WHERE station_id = ".CONFIG::ID_STATION.
		" AND unix_timestamp(deadline) >= ".$unixdate.
		" AND unix_timestamp(deadline)-(86400*7) < ".$unixdate.
		" UNION ";
	$query.= "SELECT class_id".
        " FROM time,slug WHERE slug.station_id = ".CONFIG::ID_STATION.
        " AND time.thread = slug.thread".
		" AND unix_timestamp(time.start) >= ".$unixdate.
		" AND unix_timestamp(time.start)-(86400*7) < ".$unixdate.
        " GROUP by class_id";
    $result_class =  $html->query($query);
	# BARRE DE DATES DE LA SEMAINE
	$table.="<tr>";
	$table.="<td><h2><a href=\"calendar.php?n=".($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.date("W",strtotime($year."W".$week."7+".$i."week"))."\">".date("W",strtotime($year."W".$week."7+".$i."week"))."</a></h2></td>";
	for ($j = 0;$j < 7;$j++) {
		$table.="<td width=\"12.5%\">";
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
			# C'EST PARTI POUR LA SEMAINE
        	for ($j = 0;$j < 7;$j++) {
				# DATE DE LA CASE
				$unixdate=strtotime($year."W".$week."+".(($i*7)+$j)."day");
              	# DEBUT DE LA CASE
				$table.="<td>";




				# REQUETE DES EVENEMENTS
				$query = "SELECT id,name,class_id,format_id,start,stop,client,vendeur,reference,level".
				 " FROM event WHERE station_id = ".CONFIG::ID_STATION." and class_id = ".$item_class[0].
				 " AND not(unix_timestamp(start)-86400 > ".$unixdate.
				 " AND unix_timestamp(stop)-86400 > ".$unixdate.")".
				 " AND not(unix_timestamp(start) <= ".$unixdate.
                                 " AND unix_timestamp(stop) <= ".$unixdate.")".
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
						$table.= "<a href=\"calendar.php?id=".$item[0]."&n=".($n+1)."&year=".$year."&week=".$week."\"><h4 class=\"level".$item[9]."\">";
						$table.= $time."</b>";
						$table.= stripslashes(($item[1]!=""?$item[1]:"Lazarus"));
						$table.= "</h4></a>";
       				 	}
				}


				# REQUETE DES TIME ET DEADLINE

				$query = "SELECT thread,name,deadline,NULL,NULL".
				 " FROM slug WHERE station_id = ".CONFIG::ID_STATION." and class_id = ".$item_class[0].
				  " AND unix_timestamp(deadline) >= ".$unixdate.
				 " AND unix_timestamp(deadline)-86400 < ".$unixdate.
				 " UNION ";
				$query.= "SELECT slug.thread,slug.name,time.start as deadline,time.start,time.stop FROM `time`,slug WHERE ".
				 " slug.thread=time.thread AND slug.class_id=".$item_class[0].
				 " AND unix_timestamp(start) >= ".$unixdate.
				 " AND unix_timestamp(start)-86400 < ".$unixdate.
				 " ORDER by deadline";

				$result =  $html->query($query);
				$last_thread=-1;
				if (mysqli_num_rows($result)!=0) {
        			while ($item = mysqli_fetch_array($result)) {
						if ($item[0]<>$last_thread) {
							$table.= "<p><a href=\"ticket.php?thread=".$item[0]."\">";
							$table.= "<b>".stripslashes(($item[1]!=""?$item[1]:"Lazarus"))."</b>";
							$table.= "</a><br>";
							$last_thread = $item[0];
						}
						if ($item[3]<>""){
							if ($item[4]<>"") {
								$time = date( "H:i",strtotime($item[3]));
								$time.=" - ".date( "H:i",strtotime($item[4]));
							}else{
								$time = "<span class=level3>".date( "H:i",strtotime($item[3]))." en cours</span>";
							}

						}else{
							$time = "<span class=level4>".date( "H:i",strtotime($item[2]))." deadline</span>";
						}

						$table.= $time."<br>";
						
   				 	}
				}


				# REQUETE DES CHRONOS
				$query_chrono = "SELECT sec_to_time(sum(unix_timestamp(stop)-unix_timestamp(start))) as duree FROM `time`,slug WHERE stop IS NOT NULL".
				" AND slug.thread=time.thread AND slug.class_id=".$item_class[0].
				" AND unix_timestamp(start) >= ".$unixdate.
				" AND unix_timestamp(start)-86400 < ".$unixdate;
				$result_chrono = $html->query($query_chrono);
				if (mysqli_num_rows($result_chrono)!=0) {
					$item_chrono = mysqli_fetch_array($result_chrono);
					$table.="<div class=\"CHRONO\">".$item_chrono[0]."</div>";
				}



				# FIN DE LA CASE
				$table.="</td>";
			}
			$table.="</tr>";
		}
	}
	$table.="<tr><td colspan=8><hr></td></tr>";
}

$table.= '<tr><td colspan=4 class=slug><a href="calendar.php?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.
		date("W",strtotime($year."W".$week."7-".($n+1)."week")).
		'">&larr;</a></td><td colspan=4 class=slug_droite><a href="calendar.php?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7+".($n+1)."week")).'&week='.
                date("W",strtotime($year."W".$week."7+".($n+1)."week")).
                '">&rarr;</a></td></tr>';

$table.= "</table>";

$html->body($table);


#



# affichage

$html->out();

?>
