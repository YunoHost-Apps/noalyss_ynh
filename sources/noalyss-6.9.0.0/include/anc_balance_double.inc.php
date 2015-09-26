<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_anc_balance_double.php';
$bc = new Anc_Balance_Double($cn);
$bc->get_request();
echo '<form method="get">';
echo $bc->display_form();
echo '</form>';
if (isset($_GET['result']))
{
    $result=$bc->display_html();
    if ($bc->has_data > 0)
    {
        echo $bc->show_button();
        echo $result;
    }
    else
    {
        echo '<p class="notice">';
        echo _('Aucune donnée trouvée');
        echo '</p>';
    }
        
}
?>
