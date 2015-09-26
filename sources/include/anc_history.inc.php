<?php

//This file is part of NOALYSS and is under GPL 
//see licence.txt


if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_anc_listing.php';
$list = new Anc_Listing($cn);
$list->get_request();

echo $list->display_form();
//---- result
if (isset($_GET['result']))
{
    echo '<div class="content">';

    //--------------------------------
    // export Buttons
    //---------------------------------
    $result=$list->display_html();
    if ( $list->has_data > 0)
    {
        echo $list->show_button();
        echo $result;
    }
    else
    {
        echo '<p class="notice">';
        echo _('Aucune donnée trouvée');
        echo '</p>';
    }
    echo '</div>';
}
echo '</div>';
?>
