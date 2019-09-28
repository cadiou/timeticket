<?php

/*
 * 190927
 * timeticket / DB.class.php
 * Baptiste Cadiou
 *
 */

class DB
{
	public function __construct()
	{
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$this->mysqli = new mysqli(CONFIG::DB_SERVER,CONFIG::DB_USERNAME, CONFIG::DB_PASSWORD, CONFIG::DB_NAME);
		$this->mysqli-> set_charset(CONFIG::DB_CHARSET);
	}

	public function mos_stack_num()
	{
		$query = "SELECT count(*) FROM `pile_mos`";
		$result = mysqli_query($this->mysqli,$query);
		$item = mysqli_fetch_array($result);
		if ($item[0]!=0) {
			return "<span class=\"level1\">".$item[0]."</a>";
		}
	}

	public function catalog_last_image()
	{
		$sql = "select concept.name, class.name, system.name, template.variant,  version, author, rating, template.id".
							" from concept,class,template,system".
							" where system.id = template.system_id and concept.id = template.concept_id".
							" and class.id = template.class_id".
							" ORDER BY template.date DESC LIMIT 1";

		$result = mysqli_query($this->mysqli, $sql);
		if (!$result){
			$out = "erreur".$sql;
		}else{
			$out="";
			while ($item = mysqli_fetch_array($result)) {
				$out.= "<a href=\"catalog/template.php?id=".$item[7]."\">";
				$sql2 = 	"SELECT id,description FROM snapshot WHERE template_id = ".$item[7]." LIMIT 1";
				$result2 = mysqli_query($this->mysqli,$sql2);
				if (!$result2){
					echo "erreur".$sql2;
				}else{
					$out.="<center>";
					while ($item2 = mysqli_fetch_array($result2)) {
						$out.= "<p><img src=\"catalog/image.php?id=".$item2[0]."\" width=\"320\" title=\"".$item[0]."/".$item[1]."/".$item[3]." (".$item[5].")\"><br>".stripslashes($item2[1])."</p>";
					}
					$out.="</center>";
				}
				$out.="</a>";
			}
		}
		return $out;
	}

	public function mos_stack_table()
	{
		$query = "SELECT id, titre, datetime, concept, query FROM `pile_mos` ";
		$result = mysqli_query($this->mysqli,$query) or die(mysqli_error($$this->mysqli));
		if (mysqli_num_rows($result)!=0) {
			$out= "<table class=\"level1\" width=\"100%\">";
			while ($item = mysqli_fetch_array($result)) {
				$out .=  "<tr>";
				$out .=  "<td>".$item['datetime']."</td>";
				$out .=  "<td>".$item['concept']."</td>";
				$out .=  "<td>".$item['titre']."</td>";
				$out .=  "<td>".$item['query']."</td>";
				$out .=  "</tr>";
			}
			$out .=  "</table>";
			return $out;
		}else{
			return "";
		}
	}

	public function query($query)
	{
		$result = mysqli_query($this->mysqli,$query) or die(mysqli_error($$this->mysqli));
		return $result;
	}
}

?>
