<?php

/*
 * 191016
 * timeticket / ticket.php
 * Baptiste Cadiou
 *
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
$initials = (isset($_POST['initials'])?$_POST['initials']:"??");

if (isset($_POST['ARCHIVE'])) {
	$query = "UPDATE ticket SET "
				."active=0 "
				."WHERE thread=". $thread_post. " OR id=".$thread_post;

	$result =  $html->query($query);
}



if (isset($_POST['new'])) {

	$thread = $thread_post;

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
	$query = "INSERT INTO `slug` SET thread='".$thread."', concept_id='".$_POST["concept_id"]."' ON DUPLICATE KEY UPDATE concept_id='".$_POST["concept_id"]."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["class_id"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', class_id='".$_POST["class_id"]."' ON DUPLICATE KEY UPDATE class_id='".$_POST["class_id"]."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["system_id"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', system_id='".$_POST["system_id"]."' ON DUPLICATE KEY UPDATE system_id='".$_POST["system_id"]."'" ;
	$result =  $html->query($query);
}

if (isset($_POST["format_id"]) ) {
	$query = "INSERT INTO `slug` SET thread='".$thread."', format_id='".$_POST["format_id"]."' ON DUPLICATE KEY UPDATE format_id='".$_POST["format_id"]."'" ;
	$result =  $html->query($query);
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
#$html->ticket_complet($thread);

if (($level == -1) and ($thread == 0)) {
	$html->body("nouveau ticket");
}elseif (($level == -1) and ($thread > 0)) {
  $html->ticket_complet($thread);
	$query = "SELECT concept_id,class_id,system_id,format_id FROM slug WHERE thread=".$thread;
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
