<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS isfree software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS isdistributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Aged Balance for card
 *@see Balance_Age
 */
require_once NOALYSS_INCLUDE.'/class/fiche.class.php';
require_once NOALYSS_INCLUDE.'/class/exercice.class.php';
require_once NOALYSS_INCLUDE.'/class/periode.class.php';
require_once NOALYSS_INCLUDE.'/class/balance_age.class.php';
$let=( isset ($_GET['p_let']))?'let':'unlet';
// f_id
$f_id=$http->get('f_id',"number");

// Default date
$periode_user=$g_user->get_periode();
$periode=new Periode($cn,$periode_user);
$default_date=$periode->first_day();

// Input date
$idate=new IDate("date_balag");
$idate->value=$http->get("date_balag","date",$default_date);


$export_csv = '<FORM METHOD="get" ACTION="export.php" style="display:inline">';
$export_csv .=HtmlInput::request_to_hidden(array('gDossier','ac','date_balag'));
$export_csv.=HtmlInput::hidden('p_date_start',$idate->value);
$export_csv .= HtmlInput::hidden('act','CSV:balance_age');
$export_csv .= HtmlInput::hidden('p_let',$let);
$export_csv .= HtmlInput::hidden('p_type','U');
$export_csv .= HtmlInput::hidden('fiche',$f_id);
$export_csv .= HtmlInput::submit('csv',_('Export CSV'));
$export_csv.='</FORM>';
?>
<form method="get">
    <?php echo _("Tout") ?><input type="checkbox" name="p_let" value="1">
    <?php echo _("Date")?> <?php echo $idate->input();?>
    <?php echo HtmlInput::request_to_hidden(array('ac','gDossier','sb','sc','f_id'));?>
    <input type="submit" class="smallbutton" value="<?php echo _('Valider')?>">
</form>   
<?php

echo '<div class="content" style="width:98%;margin-left:1%">';
echo $export_csv;
$fiche=new Fiche($cn,$f_id);
$bal=new Balance_Age($cn);
$bal->display_card($idate->value, $fiche->id, $let);
echo $export_csv;

echo '</div>';

?>
