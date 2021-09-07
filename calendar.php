<?php

/*
 * 210907
 * timeticket / calendar
 * bc@mangrove.tv
 *
 */

include("HTML.class.php");

$week = substr("0".(isset($_GET['week'])?$_GET['week']:date('W')),-2);
$year = (isset($_GET['year'])?$_GET['year']:date('Y'));
$n = ((isset($_GET['n'])?$_GET['n']:6)-1);

$date1 = date( "Y-m-d 00:00:00", strtotime($year."W".$week."1") ); // First day of week
$date2 = date( "Y-m-d 23:59:59", strtotime($year."W".$week."7+".$n."week") ); // Last day of week

$html = new HTML(substr($date1,0,10)." &rarr; ".substr($date2,0,10),((isset($_GET['unixdate']) or isset($_GET['id'])  or isset($_POST['id']) or isset($_POST["new_date_start"]))?-1:30));

$html->module_login();
$html->module_ticket();
$html->module_calendar($year,$week);

$id = 0;

# NOUVEL EVENEMENT
if (isset($_GET['unixdate'])) {

	$html->h2("Nouvel évènement");
	
	# FORMULAIRE
	$formulaire = "<table width=\"100%\">";
	$formulaire.= '<form enctype="multipart/form-data" method="post" action="?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.$week.'">';
#	$formulaire .= '<input type="hidden" name="id" value="'.$id.'">';
	# START DATE ####################################################################
	$unixtimestart= intval($_GET['unixdate'] );
	$anticipation = min( intval( ($unixtimestart-mktime())/24/60/60) , -90 );
	$formulaire.= '<tr><td>Début&nbsp;:</td><td>';
	$formulaire.= '<SELECT NAME="new_date_start">';  //  onchange="this.form.submit()"
	for ($i = $anticipation; $i <= 900; $i++) {
		$unixtime=mktime(0, 0, 0, date("m"), date("d")+$i, date("Y"));
		$formulaire.= '<OPTION VALUE="'.$unixtime.'" '.
			(
				(
					(
						intval($unixtime)          <= $unixtimestart
					)
					and
					(
						intval($unixtime)+24*60*60 >  $unixtimestart
					)
				) ? " SELECTED":""
			)
			.'>'.
			htmlentities(strftime("%A %e %b %Y", $unixtime)).'</OPTION>';
	}
	$formulaire .= '</SELECT>';
	$formulaire .= '</td>';
	# START HEURE ####################################################################
	$formulaire .= '<td>';
	$secondes = date("H")*3600+date("i")*60;
	$formulaire.= '<SELECT NAME="new_time_start">'; //  onchange="this.form.submit()"
	for ($i = 0; $i < 48; $i++) {
		$formulaire.= '<OPTION VALUE="'.strval($i*30*60).'" '.(($i*60*30<=$secondes and ($i+1)*60*30>$secondes)?" SELECTED":"").'>'.
			date('H:i', mktime(0, 30*$i, 0, 1, 1, 1)
			).'</OPTION>';
	}
	$formulaire .= '</SELECT>';
	$formulaire .= '</td><td rowspan="4"><input class="bouton_in" type="submit" /><p>'."<a  class=\"bouton_RD\" href=\"?n=".($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.$week."\">Annuler</a>".'</p></td>';
	# RACK ###########################################################################
	$formulaire .= '</tr>';
	# STOP   DATE ####################################################################
	$formulaire.= "<tr><td>Retour&nbsp;:</td><td>";
	$formulaire.= '<SELECT NAME="new_date_stop">'; //  onchange="this.form.submit()"
	$anticipation = min( intval( ($unixtimestart-mktime())/24/60/60) , -90 );
	for ($i = $anticipation; $i <= 900; $i++) {
		$unixtime=mktime(0, 0, 0, date("m"), date("d")+$i, date("Y"));
		if ($unixtime+24*60*60>$unixtimestart) {
			$formulaire.= '<OPTION VALUE="'.$unixtime.'" '.
				(
					(
						(
							intval($unixtime)          <= $unixtimestart
						)
						and
						(
							intval($unixtime)+24*60*60 >  $unixtimestart
						)
					) ? " SELECTED":""
				)
				.'>'.
				htmlentities(strftime("%A %e %b %Y", $unixtime)).'</OPTION>';
		}
	}
	$formulaire .= '</SELECT>';
	$formulaire .= '</td>';
	# STOP   HEURE ####################################################################
	$formulaire .= '<td>';
	$formulaire.= '<SELECT NAME="new_time_stop">'; //  onchange="this.form.submit()"
	for ($i = 0; $i < 48; $i++) {
		$formulaire.= '<OPTION VALUE="'.strval($i*30*60).'" '.((($i-1)*60*30<=$secondes and ($i)*60*30>$secondes)?" SELECTED":"").'>'.
			date('H:i', mktime(0, 30*$i, 0, 1, 1, 1)
			).'</OPTION>';
	}
	$formulaire .= '</SELECT>';
	$formulaire .= '</td>';
	$formulaire .= '</tr>';

	# TOURNAGE #######################################################################
	$formulaire.= '<tr><td>Slug&nbsp;:</td><td colspan="3">';
	$formulaire.= '<input SIZE="40" TYPE="text" NAME="slug" VALUE="'.'" ></td>';
	$formulaire.= "</tr>";

	# CLASSE      #####################################################################
	$formulaire.= '<tr><td>Classe&nbsp;:</td><td>'.$html->menuselect("class","id"  ,"name",-1).'</td></tr>';
	$formulaire.= "</table></form>";

	$html->body.= $formulaire;

} elseif ( isset( $_POST["new_date_start"] )) {
	
	$date_start= date('Y-m-d H:i:s',intval($_POST['new_time_start'])+intval($_POST['new_date_start']));
	$date_stop = date('Y-m-d H:i:s',intval($_POST['new_time_stop'])+intval($_POST['new_date_stop']));
	$slug = $_POST['slug'];
	$class_id = $_POST['class_id'];
#	echo $start; echo $stop; echo $slug; echo $class_id ;
	# NOUVELLE EVENT = ON LE CREE ET ON RECUPERE UN ID
	$query = 'INSERT INTO `event` SET '
			."station_id='".CONFIG::ID_STATION."',"
			."start='".$date_start."',"
			."stop='".$date_stop."',"
			."name='".addslashes($slug)."',"
			."class_id='".$class_id."'"
			;
	$result =  $html->query($query);
	$query = 'select LAST_INSERT_ID()';
	$result =  $html->query($query);
	$item = mysqli_fetch_array($result);
	$id=$item[0];
} elseif (isset($_POST['id'])) {
	$id=$_POST['id'];
	$date_start= date('Y-m-d H:i:s',intval($_POST['time_start'])+intval($_POST['date_start']));
	$date_stop = date('Y-m-d H:i:s',intval($_POST['time_stop'])+intval($_POST['date_stop']));
	$query = 'UPDATE `event` SET '
			."level=".$_POST['level'].","
            ."start='".$date_start."',"
            ."stop='".$date_stop."',"
			."name='".addslashes($_POST['slug'])."',"
			."client='".addslashes($_POST['client'])."',"
			."vendeur='".addslashes($_POST['vendeur'])."',"
			."reference='".addslashes($_POST['reference'])."',"
			."concept_id=".$_POST['concept_id'].","
			."class_id=".$_POST['class_id'].","
			."system_id=".$_POST['system_id'].","
			."format_id=".$_POST['format_id']
			." WHERE id=".$id." and station_id=".CONFIG::ID_STATION;
    $result =  $html->query($query);
} elseif (isset($_GET['id'])) {
	$id=$_GET['id'];
}

if ($id > 0) {

	$html->body.="<h2>Éditer évènement</h2>";

	$query = "SELECT name,concept_id,class_id,system_id,format_id,start,stop,client,vendeur,reference,level ".
		" FROM event WHERE station_id=".CONFIG::ID_STATION." AND id=".$id;

	$result =  $html->query($query);

	if (mysqli_num_rows($result)!=0) {
		$item = mysqli_fetch_array($result);

		if ($item[5]<=$item[6]) {
			$start=$item[5];
			$stop =$item[6];
		}else{
			$start=$item[6];
			$stop =$item[5];
		}

		# FORMULAIRE DE MODIFICATION
		$formulaire = "<table width=\"100%\">";
		$formulaire.= '<form enctype="multipart/form-data" method="post" action="?n='.($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.$week.'">';
		$formulaire.= '<input type="hidden" name="id" value="'.$id.'">';
		# START DATE ####################################################################
		$unixtimestart= intval( strtotime($start) );
		$anticipation = min( intval( ($unixtimestart-mktime())/24/60/60)-1 , -90 );
		$formulaire.= '<tr><td>Début&nbsp;:</td><td>';
		$formulaire.= '<SELECT NAME="date_start" onchange="this.form.submit()">';   
		for ($i = $anticipation; $i <= 900; $i++) {
			$unixtime=mktime(0, 0, 0, date("m"), date("d")+$i, date("Y"));
			$formulaire.= '<OPTION VALUE="'.$unixtime.'" '.
				(
					(
						(
							intval($unixtime)          <= $unixtimestart
						)
						and
						(
							intval($unixtime)+24*60*60 >  $unixtimestart
						)
					) ? " SELECTED":""
				)
				.'>'.
				htmlentities(strftime("%A %e %b %Y", $unixtime)).'</OPTION>';
		}
		$formulaire .= '</SELECT>';
		$formulaire .= '</td>';
		# START HEURE ####################################################################
		$formulaire .= '<td>';
		$secondes = intval( substr($start,-8,2) )*60*60 + intval( substr($start,-5,2) )*60;
		$formulaire.= '<SELECT NAME="time_start" onchange="this.form.submit()">'; 
		for ($i = 0; $i < 48; $i++) {
			$formulaire.= '<OPTION VALUE="'.strval($i*30*60).'" '.(($i*60*30<=$secondes and ($i+1)*60*30>$secondes)?" SELECTED":"").'>'.
				date('H:i', mktime(0, 30*$i, 0, 1, 1, 1)
				).'</OPTION>';
		}
		$formulaire .= '</SELECT>';
		$formulaire .= '</td><td rowspan="4"><input class="bouton_in" type="submit" /></td>';
		# RACK ###########################################################################
		$formulaire .= '</tr>';
		# STOP   DATE ####################################################################
	#	$unixtimestart=intval( strtotime($start) );
		$formulaire.= "<tr><td>Fin&nbsp;:</td><td>";
		$formulaire.= '<SELECT NAME="date_stop" onchange="this.form.submit()">'; 
		for ($i = $anticipation; $i <= 900; $i++) {
			$unixtime=mktime(0, 0, 0, date("m"), date("d")+$i, date("Y"));
			if ($unixtime+24*60*60>$unixtimestart) {
				$formulaire.= '<OPTION VALUE="'.$unixtime.'" '.
					(
						(
							(
								intval($unixtime)          <= intval( strtotime($stop) )
							)
							and
							(
								intval($unixtime)+24*60*60 >  intval( strtotime($stop) )
							)
						) ? " SELECTED":""
					)
					.'>'.
					htmlentities(strftime("%A %e %b %Y", $unixtime)).'</OPTION>';
			}
		}
		$formulaire .= '</SELECT>';
		$formulaire .= '</td>';
		# STOP   HEURE ####################################################################
		$secondes = intval( substr($stop,-8,2) )*60*60 + intval( substr($stop,-5,2) )*60;
		$formulaire .= '<td>';
		$formulaire.= '<SELECT NAME="time_stop" onchange="this.form.submit()">'; 
		for ($i = 0; $i < 48; $i++) {
			$formulaire.= '<OPTION VALUE="'.strval($i*30*60).'" '.((($i)*60*30<=$secondes and ($i+1)*60*30>$secondes)?" SELECTED":"").'>'.
				date('H:i', mktime(0, 30*$i, 0, 1, 1, 1)
				).'</OPTION>';
		}
		$formulaire .= '</SELECT>';
		$formulaire .= '</td>';
		$formulaire .= '</tr>';

		# TOURNAGE #######################################################################
		$formulaire.= '<tr><td>Slug&nbsp;:</td><td colspan="3">';
		$formulaire.= '<input SIZE="40" TYPE="text" NAME="slug" VALUE="'.$item[0].'" ></td>';
		$formulaire.= "</tr>";

		# STATUS
		$formulaire.= '<tr><td>État:</td><td colspan="3" class="level'.$item[10].'">';
		$formulaire.= '<SELECT NAME="level" onchange="this.form.submit()">';
		$formulaire.= '<option value="0" '.($item[10]==0?"SELECTED":"").'>Previsionnel</option>';
		$formulaire.= '<option value="1" '.($item[10]==1?"SELECTED":"").'>Confirmé</option>';
		$formulaire.= '<option value="2" '.($item[10]==2?"SELECTED":"").'>Option</option>';
		$formulaire.= '<option value="3" '.($item[10]==3?"SELECTED":"").'>En cours</option>';
		$formulaire.= '<option value="4" '.($item[10]==4?"SELECTED":"").'>OK</option>';
		$formulaire.= '<option value="5" '.($item[10]==5?"SELECTED":"").'>Annulé</option>';
		$formulaire.= '</select>';
		$formulaire .= '</td>';
		$formulaire .= '</tr>';		

		# CONCEPT   #####################################################################
		$formulaire.= '<tr><td>Concept&nbsp;:</td><td>'.$html->menuselect("concept","id"  ,"name",$item[1]).'</td></tr>';

		# CLASSE      #####################################################################
		$formulaire.= '<tr><td>Classe&nbsp;:</td><td>'.$html->menuselect("class","id"  ,"name",$item[2]).'</td></tr>';

		# SYSTEME   #####################################################################
		$formulaire.= '<tr><td>Système&nbsp;:</td><td>'.$html->menuselect("system","id"  ,"name",$item[3]).'</td></tr>';

		# FORMAT    #####################################################################
		$formulaire.= '<tr><td>Format&nbsp;:</td><td>'.$html->menuselect("format","id"  ,"name",$item[4]).'</td></tr>';

		# CLIENT #######################################################################
		$formulaire.= '<tr><td>Client&nbsp;:</td><td colspan="3">';
		$formulaire.= '<input SIZE="40" TYPE="text" NAME="client" VALUE="'.$item[7].'" ></td>';
		$formulaire.= "</tr>";

		# VENDEUR ######################################################################
		$formulaire.= '<tr><td>Vendeur&nbsp;:</td><td colspan="3">';
		$formulaire.= '<input SIZE="40" TYPE="text" NAME="vendeur" VALUE="'.$item[8].'" ></td>';
		$formulaire.= "</tr>";

		# REFERENCE #######################################################################
		$formulaire.= '<tr><td>Référence&nbsp;:</td><td colspan="3">';
		$formulaire.= '<input SIZE="40" TYPE="text" NAME="reference" VALUE="'.$item[9].'" ></td>';
		$formulaire.= "</tr>";


		$formulaire.= "</table></form>";
		$html->body.= $formulaire;
	}
}

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
	$table.= htmlentities(strftime("%A", ($i+4)*24*3600));
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
	$table.="<td><h2><a href=\"?n=".($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.date("W",strtotime($year."W".$week."7+".$i."week"))."\">".date("W",strtotime($year."W".$week."7+".$i."week"))."</a></h2></td>";
	for ($j = 0;$j < 7;$j++) {
		$table.="<td width=\"12.5%\">";
		$unixdate=strtotime($year."W".$week."+".(($i*7)+$j)."day");
		$table.="<h2><a href=\"?n=".($n+1).'&year='.date("Y",strtotime($year."W".$week."7-".($n+1)."week")).'&week='.$week.'&unixdate='.$unixdate."\">".htmlentities(strftime("%e %b", $unixdate))."</a></h2>";
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
