<?php

/*
 * 200209
 * timeticket / HTML.class.php
 * Baptiste Cadiou
 *
 */

 class HTML
 {
	public function __construct($page_titre,$timeout)
	{
		# CONFIG

		if (file_exists("CONFIG.class.php")) {
			$this->check_config = include_once("CONFIG.class.php");
		}elseif (file_exists("../CONFIG.class.php")) {
			$this->check_config = include_once("../CONFIG.class.php");
		}

		# DATABASE

		$this->mysqli =
		  new mysqli( CONFIG::DB_SERVER,
					  CONFIG::DB_USERNAME,
					  CONFIG::DB_PASSWORD,
					  CONFIG::DB_NAME);

			$this->mysqli->set_charset(CONFIG::DB_CHARSET);

		# COOKIE UID SETTING

		if (isset($_POST["user_id"]) and $_POST["user_id"]>0) {
			$this->uid =  $_POST["user_id"];
			SetCookie(CONFIG::COOKIE_UID,$this->uid, time()+CONFIG::COOKIE_SEC);
		}elseif (isset($_POST["user_id"]) and $_POST["user_id"]=-1) {
			$this->uid =  $_POST["user_id"];
			setcookie(CONFIG::COOKIE_UID, null, -1);
			$this->con = 1;
			SetCookie(CONFIG::COOKIE_CON,null, -1);
		}else{
			if (isset($_COOKIE[CONFIG::COOKIE_UID])) {
				$this->uid = $_COOKIE[CONFIG::COOKIE_UID];
			}else{
				$this->uid = -1;
			}
		}

		# COOKIE CON SETTING

		if (isset($_POST["vacation"]) and $_POST["vacation"]>0) {
			$this->con =  $_POST["vacation"];
			if ($this->con == -1) {
				$this->con= 1;
			}
			SetCookie(CONFIG::COOKIE_CON,$this->con, time()+CONFIG::COOKIE_SEC);
		}elseif (isset($_POST["vacation"]) and $_POST["vacation"]=-1) {
			$this->con =  $_POST["vacation"];
			setcookie(CONFIG::COOKIE_CON, null, -1);
		}else{
			if (isset($_COOKIE[CONFIG::COOKIE_CON])) {
				$this->con = $_COOKIE[CONFIG::COOKIE_CON];
				if ($this->con == -1) {
					$this->con= 1;
				}
			}else{
				$this->con = 1;
			}
		}

		# TERMINAL SETTING

		if (isset($_SERVER['REMOTE_HOST'])) {
			$this->terminal = $_SERVER['REMOTE_HOST'];
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$this->terminal = $_SERVER['REMOTE_ADDR'];
		}else{
			$this->terminal = "LAZARUS";
		}

		# HEAD LEFT BODY FOOT INIT

		$this->head = "<html>";
		$this->head.= "<head>";
    if (null !== CONFIG::HTML_HEADER) {
      $this->head.= CONFIG::HTML_HEADER;
    }
		$this->head.= "<title>".$page_titre."</title>";
		if ($timeout>0) {
			$this->head.= "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"".$timeout."\">";
		}
		$this->head.=
		  '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.
		  '<link href="style.css" rel="stylesheet" media="all" type="text/css" />'.
		  '<meta Http-Equiv="Cache-Control" Content="no-cache">'.
		  '<meta Http-Equiv="Pragma" Content="no-cache">'.
		  '<meta Http-Equiv="Expires" Content="0">'.
		  '<meta Http-Equiv="Pragma-directive: no-cache">'.
		  '<meta Http-Equiv="Cache-directive: no-cache">'.
		  "</head>";
      $this->head.=
        "\n".'<!--  timeticket  -->'.
        "\n".'<!--  Baptiste Cadiou  -->'.
        "\n".'<!--  https://github.com/cadiou/timeticket/  -->'."\n";
		$this->head.= "<body>";
		$this->head.= "<h1>";
    if (($_SERVER['PHP_SELF'] != "/index.php" )
      and ($_SERVER['PHP_SELF'] != "" ) ) {
			$this->head .= "<a href=\"index.php\">".$this->station(CONFIG::ID_STATION)."</a> ";
		}else{
			$this->head .= $this->station(CONFIG::ID_STATION)." ";
		}
		$this->head.= $page_titre."</h1>";
		$this->foot = "<hr />";
		$this->foot .= $this->group(CONFIG::ID_GROUP)." / ".gethostname()." / <a href=\"http://baptiste-cadiou.pro.dns-orange.fr\">baptiste-cadiou.pro.dns-orange.fr</a>";
		$this->foot.= "</body>";
		$this->foot.= "</html>";
		$this->left = "";
		$this->body = "";

		# VERIFICATION TIMEOUT LOOP

		$query = "SELECT last_time FROM `memory` WHERE time_to_sec(timediff(now(),last_time)) > 60";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				$this->head .= "<span class=\"level1\">Attention le fichier Loop n'a pas été mis à jour depuis ".$item[0]."<br>Vous pouvez l'exécuter temporairement à cette adresse : <a href=\"/loop.php\">loop.php</a></span>";
			}
		}
	}

  # MODULE TICKET

	public function module_ticket() {

		$this->left .= '
			<table>
		';

		$this->left .= '
			<tr><td class="level1" colspan=2>
			<a href="ticket.php?level=1">Nouveau&nbsp;Projet</a>
			</td></tr><tr><td class="level0" colspan=2>
			<a href="ticket.php?level=0">Nouvelle&nbsp;Info</a>
			</td></tr><tr><td class="level5" colspan=2>
			<a href="ticket.php?level=5">Nouvel&nbsp;Incident</a></td></tr>

		';

		$this->left .= '
		  <tr><td class="level" colspan=2>
		  <a href="tickets-slug.php">Liste des Slugs</a>
			  </td></tr>
		';

		$this->left .= '
		  <tr><td class="level" colspan=2>
		  <a href="tickets.php">Tickets Actifs</a>
			  </td></tr>
		';

		$this->left .= "<tr><td><table><tr><td><table>";
		$query = "SELECT id,thread,level".
						" FROM `ticket`".
						" WHERE (level=1 or level=2 or level=3) and active=1".
			" AND station_id=".CONFIG::ID_STATION.
						" ORDER BY datetime ASC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				$this->left .= "<tr><td class=\"level".$item[2]."\">";
				$this->left .= "</td><td class=\"level\">";
				$this->left .= "<a href=\"ticket.php?thread=".($item[1]==0?$item[0]:$item[1])."\">".$this->concept_abr(($item[1]==0?$item[0]:$item[1])).$this->time_tracker(($item[1]==0?$item[0]:$item[1]));"</a>";
				$this->left .= "</td></tr>";
			}
		}
		$this->left .= "</table></td><td><table>";
		$query = "SELECT id,thread,level".
						" FROM `ticket`".
						" WHERE (level=0) and active=1".
			" AND station_id=".CONFIG::ID_STATION.
						" ORDER BY datetime ASC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				$this->left .= "<tr><td class=\"level".$item[2]."\">";
				$this->left .= "</td><td class=\"level\">";
				$this->left .= "<a href=\"ticket.php?thread=".($item[1]==0?$item[0]:$item[1])."\">".$this->concept_abr(($item[1]==0?$item[0]:$item[1])).$this->time_tracker(($item[1]==0?$item[0]:$item[1]));"</a>";
				$this->left .= "</td></tr>";
			}
		}
		$query = "SELECT id,thread,level".
			" FROM `ticket`".
			" WHERE (level=4) and active=1".
			" AND station_id=".CONFIG::ID_STATION.
			" ORDER BY datetime ASC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				$this->left .= "<tr><td class=\"level".$item[2]."\">";
				$this->left .= "</td><td class=\"level\">";
				$this->left .= "<a href=\"ticket.php?thread=".($item[1]==0?$item[0]:$item[1])."\">".$this->concept_abr(($item[1]==0?$item[0]:$item[1])).$this->time_tracker(($item[1]==0?$item[0]:$item[1]));"</a>";
				$this->left .= "</td></tr>";
			}
		}

		$query = "SELECT id,thread,level".
				" FROM `ticket`".
				" WHERE (level=5) and active=1".
				" AND station_id=".CONFIG::ID_STATION.
				" ORDER BY datetime ASC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
		  while ($item = mysqli_fetch_array($result)) {
			 $this->left .= "<tr><td class=\"level".$item[2]."\">";
			 $this->left .= "</td><td class=\"level\">";
			 $this->left .= "<a href=\"ticket.php?thread=".($item[1]==0?$item[0]:$item[1])."\">".$this->concept_abr(($item[1]==0?$item[0]:$item[1])).$this->time_tracker(($item[1]==0?$item[0]:$item[1]));"</a>";
			 $this->left .= "</td></tr>";
		  }
		}

		$this->left .= "</table></td></tr></table></td></tr>";

		$this->left .= '
		  <tr><td class="level" colspan=2><p>Archives</p>
		  <a href="tickets.php?level=1" class="level1">PRJ</a>&nbsp;
		  <a href="tickets.php?level=0" class="level0">INF</a>&nbsp;
		  <a href="tickets.php?level=5" class="level5">INC</a>
		  </td></tr>
		';

		$this->left .= '</table>';
	}

	# Ajoute du texte au corps de page.

	public function body($text)
	{
		$this->body .= "<p>".$text."</p>";
	}

	# Module Catalogue

	public function gfx_catalog()
	{
		$this->left .= "<h2>GFX Catalog</h2>";
		$this->left .= "<ul>";
		$this->left .= "<li><a href=\"/catalog/print.php?concept_id=3\">Collection NEWS</a>	";
		$this->left .= "<li><a href=\"/catalog/snapshot-orphan.php\">Images Orphelines</a>";
		$this->left .= "<li><a href=\"/catalog/index.php\">Liste des templates</a>";
		$this->left .= "<li><a href=\"/catalog/template-vizrt.php\">Templates Vizrt</a>	";
		$this->left .= "<li><a href=\"/list-concept.php\">Concepts</a>	";
		$this->left .= "</ul>"."<p />";  # .$timegraph->catalog_last_image()
	}

	# Module LOGIN ####################################################################################

	public function module_login()
	{
		# CHRONOMETRE ACTIONS
		if (isset($_POST['STOP'])) {
			# on verifie que le time ticket n est pas deja stoppé
			$query="SELECT stop FROM `time` WHERE id=".$_POST['time_id'];
			$result = $this->query($query);
			$item = mysqli_fetch_array($result);
			if ($item[0] == NULL) {
				$query="UPDATE `time` SET stop = now() WHERE id=".$_POST['time_id'];
				$result = $this->query($query);
			}
		}
		if (isset($_POST['START'])) {
			# fermeture des sessions precedentes eventuelles
			$query = "SELECT id FROM time WHERE stop IS NULL and uid=".$this->uid." and station_ID = ".CONFIG::ID_STATION;
			$result = $this->query($query);

			if (mysqli_num_rows($result)!=0) {
				while ($item = mysqli_fetch_array($result)) {
					$query2="UPDATE `time` SET stop = now() WHERE id=".$item[0]." and station_ID = ".CONFIG::ID_STATION;
					$result2 = $this->query($query2);
				}
			}
			$query="INSERT INTO `time` SET"
				." uid = ".$this->uid
				.", thread = ".$_POST['time_thread']
				.", station_id = ".CONFIG::ID_STATION
				.", concept_id = ".$this->con;
			$result = $this->query($query);
		}
		# UTILISATEUR
		if ($this->uid <= 0) {
			$this->left .= "<p class=\"level1\">Identifiez-vous SVP</p>";
		}
		$this->left .= "Utilisateur :<br><FORM method=\"POST\">";

		$sql = "select `id`,`name` from user where name is not null and active = true and station_ID = ".CONFIG::ID_STATION." group by `name` order by `name` asc";
		$result = $this->query($sql);

		$out  = '<SELECT NAME="user_id" onchange="this.form.submit()">';
		$out .= '<OPTION VALUE="-1">N/A</A>';

		while ($item = mysqli_fetch_array($result)) {
			$out .= '<OPTION VALUE="'.$item['id'].'"';
			if (($this->uid == $item['id'])and($this->uid != "")) {
			$out .= " SELECTED";
			}
			$out .= '>'.$item['name'].'</OPTION>'."\n";
		}

		$out .= '</SELECT>';
		$this->left .= $out;
		$this->left .= "</FORM>";
		if ($this->uid > 0) {
  		$this->left .= "Vacation :<br><FORM method=\"POST\">";
  		$sql = "select `id`,`name` from concept where name is not null and active = true and vacation = true and station_ID = ".CONFIG::ID_STATION." group by `name` order by `name` asc";
  		if ($result = $this->query($sql)) {
  		$out  = '<SELECT NAME="vacation" onchange="this.form.submit()">';
  		while ($item = mysqli_fetch_array($result)) {
  			$out .= '<OPTION VALUE="'.$item['id'].'"';
  			if (($this->con == $item['id'])and($this->con != "")) {
  			$out .= " SELECTED";
  			}
  			$out .= '>'.$item['name'].'</OPTION>'."\n";
  		}

		$out .= '</SELECT>';
  		$this->left .= $out;
}else{
  $this->left .= "Erreur SQL";
}

    	$this->left .= "</FORM>";
		}
		if ($this->uid > 0) {
			$this->left .= "<FORM method=\"POST\">";
			$query = "SELECT id,thread,start,timediff(now(),(start))  FROM time WHERE stop IS NULL and uid=".$this->uid;
			$result = $this->query($query);
			if (mysqli_num_rows($result)!=0) {
				$this->left .= "<p>Projet en cours : ";
				while ($item = mysqli_fetch_array($result)) {
					$this->left .= "<span class=\"slug\">".$this->slug($item['thread'])."</span></p>";
					$this->left .= "<!--p class=\"chrono\">".$item[3]."</p-->";
					$this->left .= "<iframe id=\"Chrono\"    title=\"Chrono\"    width=\"100%\"   height=\"50\" scrolling=\"no\" frameborder=\"0\"  src=\"ticket-chrono-display.php?id=$item[0]&refresh=yes\">";
					$this->left .= "</iframe>";
					$this->left .= "<p align=\"center\"><input type=\"submit\" name=\"STOP\" value=\"STOP\" class=\"bouton_RD\" ><input type=\"hidden\" name=\"time_id\" value=".$item['id']."></p>";
					$time_thread=$item[1];
				}
				if (isset($_GET['thread'])) {
					if ($_GET['thread'] <> $time_thread) {
						$this->left .= "Changer de projet : ";
						$this->left .= "<p align=\"center\"><input type=\"submit\" name=\"START\" value=\"TOP CHRONO !\" class=\"bouton_in\" ><input type=\"hidden\" name=\"time_thread\" value=".$_GET['thread']."></p>";
					}
				}
			}else{
				if (isset($_GET['thread'])) {
					$this->left .= "<p align=\"center\"><input type=\"submit\" name=\"START\" value=\"TOP CHRONO !\" class=\"bouton_in\" ><input type=\"hidden\" name=\"time_thread\" value=".$_GET['thread']."></p>";
				}
			}
			$this->left .= "</FORM>";
		}
	}

  # Module DOSAMCO ####################################################################################

	public function module_dosamco($cid)
	{
		# RECUPERE SAMPLE
		if (isset($_POST['SAMPLE']) and isset($_POST['sample_value']) and $_POST['sample_value']>0) {

      $query="INSERT `sample` SET value = '".$_POST['sample_value']."', uid = '".$this->uid."', cid = '".$cid."'";
      $result = $this->query($query);
		}

    $sql = "SELECT collection.name, sample.datetime, sample.value, collection.unit, user.username "
          ." FROM sample, collection, user"
          ." WHERE sample.cid = ".$cid
          ." AND sample.cid = collection.cid AND sample.uid = user.id"
          ." ORDER BY datetime desc LIMIT 1";
    $result = $this->query($sql);
    while ($item = mysqli_fetch_array($result)) {
        $this->left .= "<h2>".$item[0]."</h2>";
        $this->left .= $item[1]." ".$item[4];
        $this->left .= "<p class=\"chrono\">".$item[2].$item[3]."</p>";

    }

		if ($this->uid > 0) {
  			# INPUT FORM
			$this->left .= "<FORM method=\"POST\">";
			$this->left .= '<input type="text" name="sample_value" size="10">' ;
			$this->left .= "<input type=\"submit\" name=\"SAMPLE\" value=\"ENTER\" class=\"bouton_RD\" >";
			$this->left .= "</FORM>";
  	}
	}

	# Retourne le titre de la page

	public function ticket_level($level)
	{
		$titre[0]="Info";
		$titre[1]="Brief de départ";
		$titre[2]="Ordre intermédiaire";
		$titre[3]="WIP / Work In Progress / En cours";
		$titre[4]="Livré et vérifié";
		$titre[5]="Description d'incident";
		return $titre[$level];
	}

	# Fenetre Tickets Hebdo

	public function ticket_board_this_week()
	{
		$timegraph = new DB();

		$sql = "SELECT id,thread,body,level,snapshot ".
									" FROM `ticket`".
									" WHERE active=1 and datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY)".
									" ORDER BY datetime DESC";

		$result = $timegraph->query($sql);
		if (mysqli_num_rows($result)!=0) {
			$this->body.= "<h2>Tickets Postprod</h2>";
			$this->body.= "<p><a href=\"/timegraph/tickets.php\">Tout voir</a> | <a href=\"/timegraph/tickets-formulaire.php?level=0&thread=0\">Nouvelle Info</a> | ";
			$this->body.= "<a href=\"/timegraph/tickets-formulaire.php?level=1\">Nouveau Projet</a> | ";
			$this->body.= "<a href=\"/timegraph/tickets-formulaire.php?level=5\">Déclarer un Incident</a></p>";
			$this->body.= "<table>";
			while ($item = mysqli_fetch_array($result)) {
				$this->body.= "<tr>";
				$this->body.= "<td><a href=\"/timegraph/ticket-print.php?thread=".($item['thread']==0?$item['id']:$item['thread'])."\">Imprimer</a> / <a href=\"/timegraph/tickets-formulaire.php?level=";
				if ($item['level'] == 0) {
					$this->body.= "0";
				} elseif (($item['level'] == 1) or ($item['level'] == 2) or ($item['level'] == 3)) {
					$this->body.= "3";
				} elseif ($item['level'] == 4) {
					$this->body.= "2";
				} elseif ($item['level'] == 5) {
					$this->body.= "5";
				}
				$this->body.= "&thread=".($item['thread']==0?$item['id']:$item['thread'])."\">Développer</a></td>";
				$this->body.= "<td class=\"level".$item['level']."\">".($item['snapshot']?'<img src="/timegraph/image.php?id='.$item['id'].'" align="right" height="200">':"").nl2br(stripslashes($item['body']))."</td>";
				$this->body.= "</tr>";
			}
			$this->body.= "</table>";
		}
	}

	public function ticket_board($query)
	{
		$timegraph = new DB();

		$result = $timegraph->query($query);

		if (mysqli_num_rows($result)!=0) {
				while ($item = mysqli_fetch_array($result)) {
						$this->body.= "<table class=\"level".$item['level']."\">";
						$this->body.= "<tr class=\"slug\"><td>slug</td></tr>";
						$this->body.= "<tr class=\"ticket\"><td><b><a href=\"ticket.php?thread=".($item['thread']==0?$item['id']:$item['thread'])."\">".$item['datetime']." ".$item['initials']."</a></b>";
						$this->body.= "<tr><td>".($item['snapshot']?'<img src="ticket-image.php?id='.$item['id'].'" height="200"><br>':"").nl2br(stripslashes($item['body']));
						$this->body.= "</td></tr></table><p>";
				}
		}
	}

	public function ticket($id)
	{
		# Query ticket
		$query = "SELECT id,thread,datetime,initials,body,level,snapshot,uid ".
				" FROM `ticket`".
				" WHERE id='".$id."' ".
				" ORDER BY datetime DESC";

		$result = $this->query($query);

		if (mysqli_num_rows($result)!=0) {
			$out= "<table width=\"100%\">";
			while ($item = mysqli_fetch_array($result)) {

				$out.= "<tr><td><b>".$item['datetime']." ".($item['uid']!=-1?$this->user($item['uid']):$item['initials'])."</b>";
				$out.= "<tr><td class=\"level".$item['level']."\">".($item['snapshot']?'<img src="ticket-image.php?id='.$item['id'].'" height="200" align="right">':"").nl2br(stripslashes($item['body']))."</td></tr>";
			}
			$out.= "</table>";
			return $out;
		}
	}


	public function thread($id,$active)
	{

		# Query ticket

		$query = "SELECT id,thread,datetime,initials,body,level,snapshot,active,uid ".
				" FROM `ticket`".
				" WHERE ( id='".$id."' or thread='".$id."' ) and station_id = ".CONFIG::ID_STATION.
				" ORDER BY datetime ASC";

		$result = $this->query($query);

		if (mysqli_num_rows($result)!=0) {
			$active_ticket=false;
			$out= "<table width=\"100%\">";
			while ($item = mysqli_fetch_array($result)) {
				$out.= "<tr><td><b>".$item['datetime']." ".($item['uid']!=-1?$this->user($item['uid']):$item['initials'])."</b>";
				$out.= "<tr><td class=\"level".$item['level']."\">".($item['snapshot']?'<img src="ticket-image.php?id='.$item['id'].'" height="200" align="right"><br>':"").nl2br(stripslashes($item['body']))."</td></tr>";
				if ($item['active']==1) {
					$active_ticket=true;
				}
				$this->last_level=$item['level'];
			}
			$out.= "</table>";
			if (($active==$active_ticket) or ($active=="-1")) {
				return $out;
			}
		}
	}


	public function slug($thread)
	{
		$query = "SELECT name ".
				" FROM `slug`".
				" WHERE thread='".$thread."' and station_id = ".CONFIG::ID_STATION;
		$result = $this->query($query);
		if ($thread==0) {
			return;
		}
		elseif(mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
			return ($item[0]==""?"N°".$thread:$item[0]);
		}else{
			return "N°".$thread;
		}
	}


	public function concept($thread)
	{
		$query = "SELECT concept.name ".
				" FROM `slug`,`concept`".
				" WHERE concept.id = slug.concept_id and thread='".$thread."' AND slug.station_id=".CONFIG::ID_STATION." AND concept.station_id=".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
				return ($item[0]);
		}else{
				return;
		}
	}

	public function concept_abr($thread)
	{
		$query = "SELECT concept.code ".
				" FROM `slug`,`concept`".
				" WHERE concept.id = slug.concept_id and thread='".$thread."' and concept.station_id = ".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
				return ($item[0]);
		}else{
				return "N/A";
		}
	}

	public function myclass($thread)
	{
		$query = "SELECT class.name ".
				" FROM `slug`,`class`".
				" WHERE class.id = slug.class_id and thread='".$thread."' and class.station_id = ".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
				return ($item[0]);
		}
	}

	public function mysystem($thread)
	{
		$query = "SELECT system.name ".
				" FROM `slug`,`system`".
				" WHERE system.id = slug.system_id and thread='".$thread."' and system.station_id = ".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
				return ($item[0]);
		}
	}

	public function user($uid)
	{
		$query = "SELECT name ".
				" FROM `user`".
				" WHERE id = '".$uid."' and station_id = ".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
				return ($item[0]);
		}
	}

	public function initials($thread)
	{
		$query = "SELECT username ".
				" FROM `user`".
				" WHERE id='".$thread."' and station_id = ".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)>0) {
			$item = mysqli_fetch_array($result);
				return ($item[0]);
		}
	}

  public function station($station_id)
  {
    $query = "SELECT name ".
        " FROM `station`".
        " WHERE id='".$station_id."'";
    $result = $this->query($query);
    if (mysqli_num_rows($result)>0) {
      $item = mysqli_fetch_array($result);
        return ($item[0]);
    }
  }

  public function group($group_id)
  {
    $query = "SELECT name ".
        " FROM `group`".
        " WHERE id='".$group_id."'";
    $result = $this->query($query);
    if (mysqli_num_rows($result)>0) {
      $item = mysqli_fetch_array($result);
        return ($item[0]);
    }
  }

	public function menuselect($table,$value,$option,$selected) {

		$sql = "select `".$value."`,`".$option."` from ".$table." where `".$value."` is not null and station_id = ".CONFIG::ID_STATION." group by `".$option."` order by `".$option."` asc";
		$result = $this->query($sql);

		$out  = '<SELECT NAME="'.$table."_".$value.'" onchange="this.form.submit()">';
        $out .= '<OPTION VALUE="-1">N/A</A>';

		while ($item = mysqli_fetch_array($result)) {
			$out .= '<OPTION VALUE="'.$item[$value].'"';
			if (($selected == $item[$value])and($selected != "")) {
			$out .= " SELECTED";
			}
			$out .= '>'.$item[$option].'</OPTION>'."\n";
		}

         $out .= '</SELECT>';

         return $out;
	}

	# Affiche la page structurée et gere le user

	public function out()
	{
		if (isset($this->redirect)) {
			header("Location: ".$this->redirect);
		}
		echo $this->head;
		if ($this->left != "") {
			echo "<table>";
			echo "<tr><td>";
			echo $this->left;
			echo "</td><td width=\"100%\">";
			echo $this->body;
			echo "</td></tr>";
			echo "</table>";
		}else{
			echo $this->body;
		}
		echo $this->foot;
	}

	public function ticket_panel($title,$where) {
		$query = "SELECT id,thread".
						" FROM `ticket`".
						" WHERE (".$where.") and station_ID = ".CONFIG::ID_STATION.
						" ORDER BY datetime ASC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			$this->body.="<h2>".$title."</h2>";
			while ($item = mysqli_fetch_array($result)) {
				if ($item['thread']==0) {
					$thread=$item['id'];
				}else{
					$thread=$item['thread'];
				}
				$this->body.= "<table class=\"ticket\">";
				$this->body.= "<tr><td>"; ###################################### LIGNE 1 SLUG + CONCEPT
        $this->body.= "<table width=\"100%\">";
        $this->body.= "<tr><td class=\"slug\">";
        $this->body.= "<a href=\"ticket.php?thread=".$thread."\">".$this->slug($thread)."</a>";
        $this->body.= "</td><td class=\"slug_droite\">";
        $this->body.= $this->concept($thread);
        $this->body.= "</td></tr>";
        $this->body.= "</table>";
        $this->body.= "<table><tr>";
        if ($this->uid > 0) {
					$this->body.= "<td>";
					$this->body .= "<FORM method=\"POST\">";
					$query2 = "SELECT id,thread,start,timediff(now(),(start))  FROM time WHERE stop IS NULL and uid=".$this->uid;
					$result2 = $this->query($query2);
					if (mysqli_num_rows($result2)!=0) {
						$item2 = mysqli_fetch_array($result2);
						$time_thread=$item2[1];
						if ($thread <> $time_thread) {
							$this->body .= "<input type=\"submit\" name=\"START\" value=\"START\" class=\"bouton_in\" ><input type=\"hidden\" name=\"time_thread\" value=".$thread.">";
						}
					}else{
						$this->body .= "<input type=\"submit\" name=\"START\" value=\"START\" class=\"bouton_in\" ><input type=\"hidden\" name=\"time_thread\" value=".$thread.">";
					}
					$this->body .= "</FORM>";
					$this->body.= "</td>";
				}
				$this->body.= "<td class=\"chrono\">";
				$this->body.= $this->time_time($thread);
				$this->body.= "</td></tr></table>";

				$this->body.= "</td>";
				$this->body.= "</tr>";
        $this->body.= "<tr>";
        $this->body.= "<td>";
        $this->body.= $this->ticket($item['id']);
        $this->body.= "</td>";
        $this->body.= "</tr>";
				$this->body.= "</table>";
			}
		}
	}

	public function level($thread) {
		$query = "SELECT ticket.level FROM ticket WHERE ticket.id=".$thread." OR ticket.thread=".$thread." ORDER BY ticket.id DESC LIMIT 1";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				$level=$item[0]."";
			}
		}
		return $level;
	}

	public function time_time($thread) {
		$time= "";
		$query = "SELECT user.name FROM user,time WHERE user.id=time.uid and time.thread=".$thread." GROUP BY user.name";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			$query = "SELECT sec_to_time(sum(unix_timestamp(stop)-unix_timestamp(start))) as duree FROM `time` WHERE stop IS NOT NULL and time.thread=".$thread;
			$result = $this->query($query);
			if (mysqli_num_rows($result)!=0) {
				while ($item = mysqli_fetch_array($result)) {
					$time.=$item[0]."";
				}
			}
		}
		return $time;
	}

	public function time_tracker($thread) {
		$names_actifs=" ";
		$query = "SELECT user.username FROM user,time WHERE user.id=time.uid AND time.stop is NULL and time.thread=".$thread." and user.station_id =".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			$names_actifs.= "<span class=\"onair\">";
			while ($item = mysqli_fetch_array($result)) {
				$names_actifs.=$item[0]." ";
			}
			$names_actifs.="</span>";
		}
		return $names_actifs;
	}


	public function time_tracker_complet($thread) {

		$names_actifs="<table>";

		# LOG

		$query = "SELECT user.name,sec_to_time(unix_timestamp(stop)-unix_timestamp(start)),dayofweek(`start`),`start` FROM user,time WHERE user.id=time.uid and time.thread=".$thread." and user.station_id =".CONFIG::ID_STATION." AND time.stop is not NULL  ORDER by stop";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {

			while ($item = mysqli_fetch_array($result)) {
				$names_actifs.='<tr><td>'.$item[3].'</td><td>'.$item[0].'</td><td>'.$item[1].'</td></tr>';
			}
		}
		# actIFS

		$query = "SELECT user.name FROM user,time WHERE user.id=time.uid AND time.stop is NULL and time.thread=".$thread." and user.station_id =".CONFIG::ID_STATION;
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {

				$names_actifs.='<tr><td class="onair">Maintenant</td><td class="onair" colspan="2">'.$item[0].'</td></tr>';
			}
		}

		# somme

		$query = "SELECT sec_to_time(sum(unix_timestamp(stop)-unix_timestamp(start))) as duree FROM `time` WHERE stop IS NOT NULL and time.thread=".$thread;
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				if ($item[0]<>"") {
					$names_actifs.='<tr><td class="chrono" colspan="2">Total</td><td class="chrono">'.$item[0].'</td></tr>';
				}
			}
		}

		$names_actifs.="</table>";

		return $names_actifs;

	}

	public function ticket_threads($title,$where,$active) {
		$this->body.= "<h2>".$title."</h2>";
		$query = "SELECT id ".
                " FROM `ticket`".
				" WHERE ".$where." and thread=0 and station_ID = ".CONFIG::ID_STATION.
				" ";
		$query.= "UNION SELECT thread ".
                " FROM `ticket`".
				" WHERE ".$where." and thread!=0 and station_ID = ".CONFIG::ID_STATION.
				" GROUP BY 1 ORDER BY 1 DESC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			while ($item = mysqli_fetch_array($result)) {
				$thread = $this->thread($item[0],$active);
				if ($thread!="") {
					$ticket = "<table class=\"ticket\">";
					$ticket .= "<tr><td class=\"slug\">";
					$ticket .= "<a href=\"ticket.php?thread=".$item[0]."\">".$this->slug($item[0])."</a>"."</td><td class=\"slug_droite\">".$this->concept($item[0]);
					$ticket .= "</td></tr><tr><td colspan=\"2\">";
					$ticket .= $this->time_tracker_complet($item[0]);
					$ticket .= $thread;
					$ticket .= "</td></tr>";
					$ticket .= "</table>";
					$this->body($ticket);
				}
			}
		}
	}

	public function ticket_complet($id) {
		$thread = $this->thread($id,-1);
		if ($thread!="") {
			$ticket = "<table class=\"ticket\">";
			$ticket .= "<tr><td class=\"slug\">";
			$ticket .= $this->slug($id)."</td><td class=\"slug_droite\">".$this->concept($id);
			$ticket .= "</td></tr><tr><td colspan=\"2\">";
			$ticket .= $thread;
			$ticket .= "</td></tr>";
			$ticket .= "</table>";
			$this->body($ticket);
		}
	}

	public function h2($header) {
		$this->body.="<h2>".$header."</h2>";
	}

	public function ticket_formulaire($level,$thread,$payload) {
		$this->body .= "<table class=\"level".$level."\"><tr><td colspan=\"2\">";
		$this->body .= '<form enctype="multipart/form-data" method="post">';
		$this->body .= '<input type="hidden" name="new" value="0">';
		$this->body .= '<input type="hidden" name="level" value="'.$level.'">';
		$this->body .= '<input type="hidden" name="thread" value='.$thread.'>';
		$this->body .= '<textarea rows = "8" cols = "80" name = "body">'.$payload.'</textarea></td></tr><tr><td>Image:<input type="hidden" name="MAX_FILE_SIZE" value="'.CONFIG::FILE_MAX_SIZE.'" /><input type="file" name="fic" size=50 /></td><td align="right">';
		if ($this->uid==-1) {
			$this->body .= '<input type="text" name="initials" value="initiales" size="3">';
		}
		$this->body .= '<input type="submit" value="Poster">';
		$this->body .= "</form>";
		$this->body .= "</td></tr></table>";
	}

	public function ticket_formulaire_slug($level,$thread,$payload) {
		$this->body .= "<table class=\"level".$level."\"><tr>";
		$this->body .= '<form enctype="multipart/form-data" method="post">';
		$this->body .= '<input type="hidden" name="new" value="1">';
		$this->body .= '<td>Slug</td><td colspan=2><input SIZE="80" TYPE="text" NAME="slug" VALUE="'.$this->slug($thread).'" ></td></tr>';
		$this->body .= '<input type="hidden" name="level" value="'.$level.'">';
		$this->body .= '<input type="hidden" name="thread" value='.$thread.'>';
		$this->body .= '<tr><td>Ticket</td><td colspan=2><textarea rows = "8" cols = "80" name = "body">'.$payload.'</textarea></td></tr><tr><td>Image</td><td><input type="hidden" name="MAX_FILE_SIZE" value="'.CONFIG::FILE_MAX_SIZE.'" /><input type="file" name="fic" size=50 /></td><td align="right">';
		if ($this->uid==-1) {
			$this->body .= '<input type="text" name="initials" value="initiales" size="3">';
		}
		$this->body .= '<input type="submit" value="Poster" class="bouton_in" >';
		$this->body .= "</form>";
		$this->body .= "</td></tr></table>";
	}

	public function list_concept() {

		# Query ticket

		$query = "SELECT code,name,id,active,(select count(template.id) from template where template.concept_id=concept.id),(select count(slug.thread) from slug where slug.concept_id=concept.id)".
				" FROM `concept` WHERE active = true".
				" ORDER BY name ASC";
		$result = $this->query($query);
		if (mysqli_num_rows($result)!=0) {
			$out= "<table>";
			$out.= "<tr><td>Code</td><td>Nom</td><td>id</td><td>cat</td><td>tic</td></tr>";
			while ($item = mysqli_fetch_array($result)) {
				$out.= "<tr class=\"level".($item['active']?"4":"1")."\">"
					."<td>".$item['code']."</td>"
					."<td>".$item['name']."</td>"
					."<td><a href=\"http://win10-resilio/catalog/index.php?concept_id=".$item['id']."\">".$item['id']."</a></td>"
					."<td>".$item[4]."</td>"
					."<td>".$item[5]."</td>"
					."</tr>";
			}
			$out.= "</table>";
			$this->body .= $out;
		}
	}

	public function list_class() {

		# Query ticket

		$query = "SELECT name,id,(select count(id) from template where template.class_id=class.id),(select count(thread) from slug where slug.class_id=class.id)".
				" FROM `class`".
				" ORDER BY name ASC";

		$result = $this->query($query);

		if (mysqli_num_rows($result)!=0) {
			$out= "<table>";
			$out.= "<tr><td>Nom</td><td>id</td><td>cat</td><td>tic</td></tr>";
			while ($item = mysqli_fetch_array($result)) {
				$out.= "<tr>"
					."<td>".$item['name']."</td>"
					."<td><a href=\"http://win10-resilio/catalog/index.php?class_id=".$item['id']."\">".$item['id']."</a></td>"
					."<td>".$item[2]."</td>"
					."<td>".$item[3]."</td>"
					."</tr>";
			}
			$out.= "</table>";
			$this->body .= $out;
		}
	}

	public function menuConcept($value) {
#		$timegraph = new DB();
		$out  = '<SELECT NAME="concept_id" onchange="this.form.submit()">';
		$out .= '<OPTION VALUE="">Tous</A>';
		$sql = "select `name`,`id` from `concept` where `name` is not null and `station_id`=".CONFIG::ID_STATION." and active = true group by `name` order by `name` asc";
		$result = $this->query($sql);
		if (!$result){
			echo "erreur".$sql;
		}else{
			  while ($item = mysqli_fetch_array($result)) {
			  $out .= '<OPTION VALUE="'.$item['id'].'"';
			  if (($value == $item['id']) and($value != "")) {
				 $out .= " SELECTED";
			  }
			  $out .= '>'.$item['name'].'</OPTION>'."\n";
			  }
		}

		$out .= '</SELECT>';
	  return $out;
	}

	public function menuClass($value) {
#		$timegraph = new DB();
		$out  = '<SELECT NAME="class_id" onchange="this.form.submit()">';
		$out .= '<OPTION VALUE="">Tous</A>';

    $sql = 	"SELECT class.id,class.name FROM `template`,`class` WHERE template.class_id = class.id group by class.name order by class.name asc";
		$result = $timegraph->query($sql);
		if (!$result){
			echo "erreur".$sql;
		}else{
			  while ($item = mysqli_fetch_array($result)) {
			  $out .= '<OPTION VALUE="'.$item['id'].'"';
			  if (($value == $item['id']) and($value != "")) {
				 $out .= " SELECTED";
			  }
			  $out .= '>'.$item['name'].'</OPTION>'."\n";
			  }
		}

		$out .= '</SELECT>';
	  return $out;
	}

	# DATABASE FUNCTIONS

	public function query($query)
	{
		$result = mysqli_query($this->mysqli,$query) ; #or die(mysqli_error($this->mysqli));
		return $result;
	}
}

 ?>
