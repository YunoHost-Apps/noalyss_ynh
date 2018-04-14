<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 *@file
 *@brief Print the balance of 1 plan of analytic accountancy separated by group
 * @see Anc_Group
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/anc_group.class.php';

$gr = new Anc_Group($cn);
$gr->get_request();
echo '<form method="get">';
echo $gr->display_form();
echo '<p>' . HtmlInput::submit('Recherche', _('Recherche')) . '</p>';
echo '</form>';
if (isset($_GET['result']))
{
    echo $gr->show_button();

    echo $gr->display_html();
}
?>
