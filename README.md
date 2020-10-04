<h1>timeticket</h1>
<p><i>Free Workflow tool for TV managers.</i></p>
<h2>Including files</h2>
<ol>
<li>BASE_STRUCTURE.sql		STRUCTURE DE LA BASE
<li>DB.class.php      		CONNEXION MYSQL			obsolete
<li>HTML.class.php    		CODAGE DES PAGES
<li>README.md			THIS
<li>index.php         		TICKETS ACTIFS
<li>style.css         		STYLE CSS
<li>ticket-chrono-display.php	CHRONO DISPLAY
<li>ticket-image.php		DISPLAY IMAGE
<li>ticket.php			DISPLAY TICKET id
<li>tickets-slug.php		CLASSE LOCALE
<li>tickets.php			DISPLAY TICKETS level
  </ol>
<h2>Not included</h2>
<ol>
<li>../CONFIG.class.php		MANDATORY
<pre>
class Config
{
    const
    ANALYTICS  = '',
	COOKIE_UID   = 'timeticket_uid',
	COOKIE_CON   = 'timeticket_con',
	COOKIE_SEC   = 36000,
	DB_SERVER    = 'localhost',
	DB_NAME      = 'timeticket',
	DB_USERNAME  = 'timeticket',
	DB_PASSWORD  = '',
	DB_CHARSET   = 'UTF8',
	FILE_MAX_SIZE= 16777215;
	ID_GROUP     = 0,                 #int
	ID_CHANNEL   = 0,                 #int
  HTML_HEADER = NULL
}
</pre>
<li>../LOCAL.class.php		OPTIONAL
</ol>
