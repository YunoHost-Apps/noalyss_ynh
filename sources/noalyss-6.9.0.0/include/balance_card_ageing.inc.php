<?php
/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
require_once NOALYSS_INCLUDE.'/class_exercice.php';
require_once NOALYSS_INCLUDE.'/class_balance_age.php';
$let=( isset ($_GET['p_let']))?'let':'unlet';

$export_csv = '<FORM METHOD="get" ACTION="export.php" style="display:inline">';
$export_csv .=HtmlInput::request_to_hidden(array('gDossier','ac',));
$export_csv.=HtmlInput::hidden('p_date_start', '01.01.2000');
$export_csv .= HtmlInput::hidden('act','CSV:balance_age');
$export_csv .= HtmlInput::hidden('p_let',$let);
$export_csv .= HtmlInput::hidden('p_type','U');
$export_csv .= HtmlInput::hidden('fiche',$_GET['f_id']);
$export_csv .= HtmlInput::submit('csv',_('Export CSV'));
$export_csv.='</FORM>';
?>
<form method="get">
    <?php echo "Tout" ?><input type="checkbox" name="p_let" value="1">
    <?php echo HtmlInput::request_to_hidden(array('ac','gDossier','sb','sc','f_id'));?>
    <input type="submit" class="smallbutton" value="<?php echo _('Valider')?>">
</form>   
<?php

echo '<div class="content" style="width:98%;margin-left:1%">';
echo $export_csv;
$fiche=new Fiche($cn,$_GET['f_id']);
$bal=new Balance_Age($cn);
$bal->display_card('01.01.2000', $fiche->id, $let);
echo $export_csv;

echo '</div>';

?>
