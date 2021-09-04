<?php

/*
 * 210904
 * timeticket / alerte
 * b@cadiou.dev
 *
 */

include("HTML.class.php");

$html = new HTML("DECLENCHEUR D'ALERTE",-1);

$level = (isset($_GET['level'])?$_GET['level']:-1);

####  definition du message en fonction du level

if      ($level==1) {
                $priority = 1;
                $message="URGENCE ABSOLUE";
}elseif ($level==2)  {
                $priority = 1;
                $message="URGENCE OPERATIONNELLE";
}elseif ($level==3) {
                $priority = 0;
                $message="ALARME DE SERVICE";
}elseif ($level==0) {
                $priority = 0;
                $message="INFORMATION";
}elseif ($level==4) {
                $priority = 0;
                $message="FIN D'ALERTE";
}elseif ($level==5) {
                $priority = 0;
                $message="FAUSSE ALERTE";
}else{
                $message="Vous êtes sur le point de déclencher une alerte au prochain click";
                $priority = -1;
}

if (isset($_POST["message"])) {
	$message = $_POST["message"];
	$priority = 0;
	$level=0;
}

$message ="[".$_SERVER["REMOTE_ADDR"]."] ".$message;

$html->h2("Message");
$html->body("<span class=\"CHRONO\">".$message."</span>");
$html->body.= "<form method=\"POST\">";
$html->body.= '<input SIZE="60" TYPE="text" NAME="message" ><input class="bouton_cam" type="submit" />';
$html->body.= "</form>";

$html->h2("NOTIFICATION IMMÉDIATE");
$html->body("<a href=\"?level=0\" class=\"CHRONO\"><span class=\"level0\">*** INFORMATION ***</a>");
$html->body("<a href=\"?level=1\" class=\"CHRONO\"><span class=\"level1\">*** URGENCE ABSOLUE *** PRIORITAIRE</span></a>");
$html->body("<a href=\"?level=2\" class=\"CHRONO\"><span class=\"level2\">*** URGENCE OPÉRATIONNELLE *** </span><span class=\"level1\">PRIORITAIRE</span></a>");
$html->body("<a href=\"?level=3\" class=\"CHRONO\"><span class=\"level3\">*** Alarme de service ***</a>");
$html->body("<a href=\"?level=4\" class=\"CHRONO\"><span class=\"level4\">*** FIN D'ALERTE ***</a>");
$html->body("<a href=\"?level=5\" class=\"CHRONO\"><span class=\"level5\">*** FAUSE ALERTE ***</a>");


# ENVOI VERS PUSHOVER

if ($level<>-1) {
        curl_setopt_array($ch = curl_init(), array(
                CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                CURLOPT_POSTFIELDS => array(
          "token" => CONFIG::PUSHOVER_TOKEN,
           "user" => CONFIG::PUSHOVER_USER,
          "title" => $html->station(CONFIG::ID_STATION),
        "message" => utf8_decode($message),
       "priority" => $priority,
        ),
#        CURLOPT_SAFE_UPLOAD => true,
        CURLOPT_RETURNTRANSFER => true,
        ));
	$html->h2("PUSHOVER");
        $html->body(curl_exec($ch));
	$html->body("<a href=\"alerte.php\">clear</a>");
        curl_close($ch);
}


# affichage

$html->out();

?>
