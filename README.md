<h1>timeticket</h1>
<p><i>Free Workflow tool for TV managers.</i></p>
<h2>Including files</h2>
<ol>
<li>BASE_STRUCTURE.sql
<li>DB.class.php      CONNEXION MYSQL
<li>HTML.class.php    CODAGE DES PAGES
<li>index.php         PAGE PRINCIPALE
<li>style.css         STYLE CSS
  </ol>
<h2>Not included</h2>
<ol>
<li>../CONFIG.class.php
  </ol>
<pre>
class Config 
{
    const 
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
}
</pre>
