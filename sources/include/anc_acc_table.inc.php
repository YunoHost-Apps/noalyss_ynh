<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 * @file
 * @brief Module Printing : Analytic to display relation between card or accounting with
 * Analytic
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/anc_table.class.php';
$tab = new Anc_Table($cn);
$tab->get_request();
echo '<form method="get">';
echo $tab->display_form();
echo '<p>' . HtmlInput::submit('Recherche', _('Recherche')) . '</p>';

echo '</form>';
if (isset($_GET['result']))
{
    echo $tab->show_button("");
    $tab->display_html();
}
?>
