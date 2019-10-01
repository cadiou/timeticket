<?php

/*
 * 190924
 * timetickets TICKETS HELP
 * bcadiou@videlio-globalservices.com
 *
 */

include("HTML.class.php");

$html = new HTML("Documentation",0);
$html->module_login();
$html->module_ticket();


$html->h2('Objectif');

$html->body('
<ul>
<li>Suivre tous les projets transversaux qui ne sont ni des MOS ni des demandes Dalet.
<li>Décompter le temps d\'activité des équipes, en vacation SERVICE ou PRESTATION.
</ul>
');

$html->h2('Dès votre arrivée');

$html->body('
<ol>
<li>Depuis la page principale, en haut à gauche, sélectionnez votre nom dans la liste déroulante.
<li>Ne pas modifier l\'indicateur de vacation sauf si vous êtes en prestation supplémentaire. Lors d\'un détachement, vous devez sélectionner le nom du concept pour lequel vous travaillez. Une fois choisi en début de journée, ne plus changer cet indicateur quelle que soit la nature de votre travail. Il est en effet possible d\'être en <i>SERVICE</i> mais de retoucher un projet C\'EST CASH ! On peut aussi être en vacation TVCQJVD et travailler sur un projet NEWS.
<li>Prendre connaissance des tickets en cours en cliquant sur <a href="tickets.php">Tableau Général</a>
<li>Toujours compléter une ticket par une information qui n\'y figurerait pas en cliquant sur le titre du ticket puis en poursuivant par <a href="tickets.php">Développer : décrire les actions en cours</a>
</ol>
');

$html->h2('Comment retrouver un ticket facilement ?');

$html->body('
<ul>
<li>Les tickets actifs ainsi que les archives des 30 derniers jours se retrouvent sur le <a href="tickets.php">Tableau Général</a>.
<li>Il est possible de retrouver un ticket grace à son titre depuis la page <a href="tickets-slug.php">Slugs</a>
<li>On peut également retrouver tous les tickets développés sur 1 an afin de faire une recherche par CTRL+F sur <a href="tickets.php?level=1">Travaux</a>, <a href="tickets.php?level=0">Informations</a> ou <a href="tickets.php?level=5">Incidents</a>
<li>Les projets en cours apparaissent dans la boite Tickets de la barre latérale
</ul>
');

$html->h2('Quand faut-il créer un nouveau ticket ?');

$html->body('
<ul>
<li>Toute nouvelle demande qui n\'est pas écrite dans Dalet doit faire l\'objet d\'un ticket.
<li>À la réception d\'une demande orale, par mail ou téléphone, il est important de saisir le brief de départ dans un nouveau <a href="ticket.php?level=1">Ticket Projet (Job / travail)</a>
<li>Lorsqu\'un ordinateur ne fonctionne pas correctement, ou qu\'un incident intervient, il faut alors créer un <a href="ticket.php?level=5">Ticket Incident</a>
<li>Si l\'information ne nécessite pas d\'action on peut rédiger un ticket informatif en cliquant sur <a href="ticket.php?level=0">Ticket Information</a>
<li>Quand aucun ticket affiché dans "Projets en cours" ne correspond au travail à faire, alors il faut rédiger un nouveau ticket.
</ul>
');

$html->h2('Comment créer un ticket ?');

$html->body('
<ol>
<li>Vérifiez en haut à gauche que la session est bien ouverte à votre nom et que la vacation active est SERVICE sauf si vous êtes sur une vacation particulière. Dans ce dernier cas vous devez sélectionner le bon concept dans le menu Vacation.
<li>Selon qu\'il s\'agisse d\'un projet, d\'un incident ou d\'une information, cliquez sur <b>Créer un nouveau...</b> <a href="ticket.php?level=1" class="level1">Ticket Projet (Job / travail)</a>, <a href="ticket.php?level=5" class="level5">Ticket Incident</a> ou <a href="ticket.php?level=0" class="level0">Ticket Information</a>
<li>Remplissez les informations du ticket. Le "Slug" est un titre. Soyez clair !
<li>Cliquer sur <input type="submit" value="Poster" class="bouton_in" >
<li>Renseignez si possible le Concept, la Classe, le Système et le Format approprié pour le projet.
<li>Il n\'est pas possible d\'effacer ou éditer un ticket.
</ol>
');

$html->h2('Compléter un ticket');

$html->body('
<ul>
<li>Il est toujours possible d\'ajouter des informations à tout ticket en cliquant dessous du ticket sur "développer".
<li>Possibilité d\'ajouter une image en cliquant sur <b>Choisir un fichier</b>.
<li>Cliquer sur <input type="submit" value="Poster">
</ul>
');

$html->h2('Déclenchement du chronomètre');

$html->body('
<ul>
<li>Au départ de tout travail qui n\'est ni dans DALET ni dans les MOS vous devez retrouver ou créer le ticket correspondant et ouvrir sa page en cliquant sur son titre "Slug".
<li>Vérifiez que votre nom et votre vacation sont corrects et cliques sur <input type="submit" name="START" value="TOP CHRONO !" class="bouton_in" >
<li>Attention le chronomètre s\'arrête après 3 heures.
<li>À la fin du travail simplement cliquez sur <input type="submit" name="STOP" value="STOP" class="bouton_RD" >
</ul>
');

$html->h2('Fermer un ticket');

$html->body('
<ul>
<li>Vérifier que le travail est fini et rendu.
<li>Cliquer sur "Livré et vérifié - fin du ticket".
<li>Précisez le chemin du rendu.
<li>Cliquer sur <input type="submit" value="Poster">
<li>On peut toujours rouvrir le ticket en cliquant sur "Apporter des corrections à ce ticket"
</ul>
');

$html->out();

?>
