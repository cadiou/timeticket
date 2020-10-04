<?php
 
 /*
 * 190930 
 * timeticket / ticket-image.php
 * Baptiste Cadiou
 *
 */
 
include("HTML.class.php");

$html = new HTML("",-1);

    if ( isset($_GET['id']) ){
        $id = intval ($_GET['id']);
        
        $req = "SELECT id, type ,snapshot " . 
               "FROM ticket WHERE id = " . $id;
        $ret = $html->query($req);
        $col = mysqli_fetch_row ($ret);
        
        if ( !$col[0] ){
            echo "Id d'image inconnu";
        } else {
            header ("Content-type: " . $col[1]);
            echo $col[2];
        }

    } else {
        echo "id d'image non defini";
    }

?>