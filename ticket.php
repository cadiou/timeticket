<?php

/*
 * 211007
 * timeticket / ticket.php
 * Baptiste Cadiou
 */

include("HTML.class.php");

$html = new HTML("Ticket",-1);
# $timegraph = new DB();

$payload = "";

$id =(isset($_GET['id'])?$_GET['id']:0);
$thread=(isset($_GET['thread'])?$_GET['thread']:0);
$level = (isset($_GET['level'])?$_GET['level']:-1);


$thread_post =(isset($_POST['thread'])?$_POST['thread']:0);
$post_level = (isset($_POST['level'])?$_POST['level']:0);
$uid =(isset($_POST['uid'])?$_POST['uid']:"-1");
$body = (isset($_POST['body'])?$_POST['body']:"Vide.");
$initials = (isset($_POST['initials'])?substr($_POST['initials'],0,3):"??");
$deadline = (isset($_POST['deadline'])?$_POST['deadline']:"0000-00-00 00:00:00");

if (isset($_POST['ARCHIVE'])) {
	$query = "UPDATE ticket SET "
				."active=0 "
				."WHERE thread=". $thread_post. " OR id=".$thread_post;

	$result =  $html->query($query);
}


if (isset($_POST['new'])) {
	echo " new" ;
	$thread = $thread_post;
	echo $thread;
	if ($thread_post!=0) {

		$query = "UPDATE ticket SET "
				."active=0 "
				."WHERE thread=". $thread_post. " OR id=".$thread_post;

		$result =  $html->query($query);
	}

	$query = "INSERT INTO `ticket` SET "
				."active=1, "
				."thread="		 	.$thread_post.", "
				."level=".$post_level.", "
				."initials='".($html->uid>0?$html->initials($html->uid):$initials). "', "
				."uid='".$html->uid. "', "
				."ip='".$html->terminal. "', "
				."station_id='".CONFIG::ID_STATION. "', "
				."body='". addslashes(addslashes($body))."'" ;
	if ( isset($_FILES['fic']) ) {

		$ret        = false;
		$img_blob   = '';
		$img_taille = 0;
		$img_type   = '';
		$img_nom    = '';

		$ret        = is_uploaded_file($_FILES['fic']['tmp_name']);
		$img_taille = $_FILES['fic']['size'];

		if ($ret) {
			// Le fichier a bien été reçu
			$img_taille = $_FILES['fic']['size'];

			if ($img_taille < CONFIG::FILE_MAX_SIZE) {

				$img_type = $_FILES['fic']['type'];
				$img_nom  = $_FILES['fic']['name'];

				$img_blob = file_get_contents ($_FILES['fic']['tmp_name']);

				$query .=",type='".$img_type."' , snapshot='" . addslashes ($img_blob) . "'";

			}

		}

	}

	$result =  $html->query($query);
	if ($_POST['new']=="1") {
		$thread=mysqli_insert_id($html->mysqli);
	}
	$level = -1;
	$html->redirect="ticket.php?thread=".$thread;
}


# MISE A JOUR DU SLUG ########################

if (isset($_POST["slug"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', name='".addslashes($_POST["slug"])."' ON DUPLICATE KEY UPDATE name='".addslashes($_POST["slug"])."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["concept_id"])) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', concept_id='".$_POST["concept_id"]."' ON DUPLICATE KEY UPDATE concept_id='".$_POST["concept_id"]."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["class_id"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', class_id='".$_POST["class_id"]."' ON DUPLICATE KEY UPDATE class_id='".$_POST["class_id"]."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["system_id"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', system_id='".$_POST["system_id"]."' ON DUPLICATE KEY UPDATE system_id='".$_POST["system_id"]."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["format_id"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', format_id='".$_POST["format_id"]."' ON DUPLICATE KEY UPDATE format_id='".$_POST["format_id"]."'" ;
	$result =  $html->query($query);
}

# Determination de la date

if (isset($_POST['only_date_start'])) {

	if ($_POST['only_date_start']<>"-1") {
		$deadline = 	date('Y-m-d H:i:s',intval($_POST['only_time_start'])+intval($_POST['only_date_start']));
		$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', deadline='".$deadline."' ON DUPLICATE KEY UPDATE deadline='".$deadline."'" ;
		$result =  $html->query($query);
	}else{
		$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', deadline=NULL ON DUPLICATE KEY UPDATE deadline=NULL" ;
        $result =  $html->query($query);
	}

}

/*
$date_start=	(isset($_POST['only_time_start'])?	date('Y-m-d H:i:s',intval($_POST['only_time_start'])+intval($_POST['only_date_start'])):	"2021-02-02 12:00:00");
*/

if (!empty($_POST["deadline"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', deadline='".$_POST["deadline"]."' ON DUPLICATE KEY UPDATE deadline='".$_POST["deadline"]."'" ;
	$result =  $html->query($query);
}else{
#        $query = "INSERT INTO `slug` SET thread='".$thread."', station_id='".CONFIG::ID_STATION."', deadline=NULL ON DUPLICATE KEY UPDATE deadline=NULL" ;
#        $result =  $html->query($query);
}



# if (($thread==0) and ($level==1)) $payload="RÉPERTOIRE DE TRAVAIL : \nCONTACT DEMANDEUR : \nDESCRIPTION : ";

if ($level==4) {

	$query = "SELECT body ".
                " FROM `ticket`".
				" WHERE id=".$thread.
				" ORDER BY datetime ASC".
				"";

	$result =  $html->query($query);

	if (mysqli_num_rows($result)!=0) {

        	while ($item = mysqli_fetch_array($result)) {
				$payload = stripslashes($item[0]);
       		 }
	}
}

if ($thread==0) $thread=$id;

$html->module_login();
$html->module_ticket();
$html->module_calendar(date('Y'),date('W'));

if (($level == -1) and ($thread == 0)) {
	$html->body("nouveau ticket");
}elseif (($level == -1) and ($thread > 0)) {
  $html->ticket_complet($thread);
	$query = "SELECT concept_id,class_id,system_id,format_id FROM slug WHERE thread=".$thread." and station_id=".CONFIG::ID_STATION;
	$result =  $html->query($query);
	if (mysqli_num_rows($result)!=0) {
		$item = mysqli_fetch_array($result);
		$concept_id = $item['concept_id'];
		$class_id = $item['class_id'];
		$system_id = $item['system_id'];
		$format_id = $item['format_id'];
	}else{
		$concept_id = 0;
		$class_id = 0;
		$system_id = 0;
		$format_id = 0;
	}

	if ($html->uid>0) {
		$html->body.= "<table width=\"100%\">";
		$html->body.= "<form method=\"POST\">";
		$html->body.= '<tr><td>Slug&nbsp;:</td><td><input SIZE="60" TYPE="text" NAME="slug" VALUE="'.$html->slug($thread).'" ></td><td rowspan="5"><input class="bouton_in" type="submit" />';

		if ($html->uid == 1) {
			$html->body.="<p><input type=\"submit\" name=\"ARCHIVE\" value=\"ARCHIVE\" class=\"bouton_RD\" ></p>";
		}

		$html->body.= '</td></tr>';
		$html->body.= '<input type="hidden" name="thread" value='.$thread.'>';
		$html->body.= '<tr><td>Concept&nbsp;:</td><td>'.$html->menuselect("concept","id","name",$concept_id).'</td></tr>';
		$html->body.= '<tr><td>Classe&nbsp;:</td><td>'.$html->menuselect("class","id"  ,"name",$class_id).'</td></tr>';
		$html->body.= '<tr><td>Système&nbsp;:</td><td>'.$html->menuselect("system","id" ,"name",$system_id).'</td></tr>';
		$html->body.= '<tr><td>Format&nbsp;:</td><td>'.$html->menuselect("format","id" ,"name",$format_id).'</td></tr>';
		$html->body.= '<tr><td>Deadline&nbsp;:</td><td>';
		
		# TEST
		#$html->body.= '<input SIZE="60" TYPE="text" NAME="deadline" VALUE="'.$html->deadline($thread).'" >';

		# DATES		
		$html->body.= '<SELECT NAME="only_date_start" onchange="this.form.submit()">';
		$unixtimestart= intval( strtotime($html->deadline($thread)) );
		if (($unixtimestart>0 ) and ($unixtimestart<mktime())){
			$html->body.= '<OPTION VALUE="'.$unixtimestart.'" SELECTED>'.htmlentities(strftime("%A %e %b %Y", $unixtimestart)).'</OPTION>';
		}
		$html->body.= '<OPTION VALUE="-1" '.($html->deadline($thread)==""?'SELECTED':'').'>N/A</OPTION>';
		for ($i = 0; $i <= 900; $i++) {
			$unixtime=mktime(0, 0, 0, date("m"), date("d")+$i, date("Y"));
			$html->body.= '<OPTION VALUE="'.$unixtime.'" '.
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
		$html->body.= '</SELECT>';

		#HEURES
		$secondes = intval( substr($html->deadline($thread),-8,2) )*60*60 + intval( substr($html->deadline($thread),-5,2) )*60;
		$html->body.= '<SELECT NAME="only_time_start" onchange="this.form.submit()">';
		if ($secondes>0) {
			for ($i = 0; $i < 48; $i++) {
				$html->body.= '<OPTION VALUE="'.strval(($i)*30*60).'" '.(($i*60*30<=$secondes and ($i+1)*60*30>$secondes)?" SELECTED":"").'>'.
					date('H:i', mktime(0, 30*$i, 0, 1, 1, 1)
					).'</OPTION>';
			}
		}else{
			$secondes = date("H")*3600+date("i")*60; 
			for ($i = 0; $i < 48; $i++) {
				$html->body.= '<OPTION VALUE="'.strval(($i)*30*60).'" '.(($i*60*30<=$secondes and ($i+1)*60*30>$secondes)?" SELECTED":"").'>'.
					date('H:i', mktime(0, 30*$i, 0, 1, 1, 1)
					).'</OPTION>';
			}
		}
		$html->body .= '</SELECT>';

		$html->body.= '</td></tr>';
		$html->body.= "</form>";
		$html->body.= "</table>";
	}

	if ($html->last_level==0) {
		$html->body.="<table><tr><td class=\"level0\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=0\" class=\"slug\">Ajouter des informations à ce ticket</a></td></tr></table>";
	}elseif ($html->last_level==1) {
		$html->body.="<table><tr><td class=\"level2\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=2\">Compléter le brief</a></td></tr>";
		$html->body.="<tr><td class=\"level3\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=3\">Développer : décrire les premières actions entreprises</a></td></tr>";
		$html->body.="<tr><td class=\"level4\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=4\">Livré et vérifié - Fin du ticket</a></td></tr></table>";
	}elseif ($html->last_level==2) {
		$html->body.="<table><tr><td class=\"level3\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=3\">Développer : décrire les corrections en cours</a></td></tr>";
		$html->body.="<tr><td class=\"level4\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=4\">Livré et vérifié - Fin du ticket à nouveau</a></td></tr></table>";
	}elseif ($html->last_level==3) {
		$html->body.="<table><tr><td class=\"level3\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=3\" class=\"slug\">Développer : décrire les actions en cours</a></td></tr>";
		$html->body.="<tr><td class=\"level4\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=4\" class=\"slug\">Livré et vérifié - Fin du ticket</a></td></tr></table>";
	}elseif ($html->last_level==4) {
		$html->body.="<table><tr><td class=\"level2\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=2\">Apporter des corrections à ce ticket</a></td></tr></table>";
	}elseif ($html->last_level==5) {
		$html->body.="<table><tr><td class=\"level5\">&nbsp;</td><td><a href=\"".$_SERVER['PHP_SELF']."?thread=".$thread."&level=5\" class=\"slug\">Compléter les informations sur cet incident</a></td></tr></table>";
	}
	
	$html->body .= $html->time_tracker_complet($thread);
	
}elseif (($level != -1) and ($thread > 0)) {
	$html->ticket_complet($thread);
	$html->h2($html->ticket_level($level));
	$html->ticket_formulaire($level,$thread,$payload);
}else{
	$html->h2($html->ticket_level($level));
	$html->ticket_formulaire_slug($level,$thread,$payload);
}
$html->out();

?>
