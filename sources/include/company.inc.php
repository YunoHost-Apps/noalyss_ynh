<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright Author Dany De Bontridder danydb@aevalys.eu

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
global $g_user;
echo '<div class="content">';
require_once NOALYSS_INCLUDE.'/class_own.php';
if (isset($_POST['record_company']))
{
	$m = new Own($cn);
	extract($_POST);
	$m->MY_NAME = $p_name;
	$m->MY_TVA = $p_tva;
	$m->MY_STREET = $p_street;
	$m->MY_NUMBER = $p_no;
	$m->MY_CP = $p_cp;
	$m->MY_COMMUNE = $p_Commune;
	$m->MY_TEL = $p_tel;
	$m->MY_FAX = $p_fax;
	$m->MY_PAYS = $p_pays;
	$m->MY_CHECK_PERIODE = $p_check_periode;
	$m->MY_DATE_SUGGEST = $p_date_suggest;
	$m->MY_ANALYTIC = $p_compta;
	$m->MY_STRICT = $p_strict;
	$m->MY_TVA_USE = $p_tva_use;
	$m->MY_PJ_SUGGEST = $p_pj;
	$m->MY_ALPHANUM = $p_alphanum;
	$m->MY_UPDLAB = $p_updlab;
	$m->MY_STOCK = $p_stock;

	$m->Update();
}

$my = new Own($cn);
///// Compta analytic
$array = array(
	array("value" => "ob", 'label' => _("obligatoire")),
	array("value" => "op", 'label' => _("optionnel")),
	array("value" => "nu", 'label' => _("non utilisé"))
);
$strict_array = array(
	array('value' => 'N', 'label' => _('Non')),
	array('value' => 'Y', 'label' => _('Oui'))
);

$alpha_num_array[0] = array('value' => 'N', 'label' => _('Non'));
$alpha_num_array[1] = array('value' => 'Y', 'label' => _('Oui'));

$updlab_array[0] = array('value' => 'N', 'label' => _('Non'));
$updlab_array[1] = array('value' => 'Y', 'label' => _('Oui'));

$compta = new ISelect();
$compta->table = 1;
$compta->selected = $my->MY_ANALYTIC;

$strict = new ISelect();
$strict->table = 1;
$strict->selected = $my->MY_STRICT;

$tva_use = new ISelect();
$tva_use->table = 1;
$tva_use->selected = $my->MY_TVA_USE;

$pj_suggest = new ISelect();
$pj_suggest->table = 1;
$pj_suggest->selected = $my->MY_PJ_SUGGEST;

$date_suggest = new ISelect();
$date_suggest->table = 1;
$date_suggest->selected = $my->MY_DATE_SUGGEST;

$check_periode = new ISelect();
$check_periode->table = 1;
$check_periode->selected = $my->MY_CHECK_PERIODE;
$alpha_num = new ISelect();
$alpha_num->table = 1;
$alpha_num->value = $alpha_num_array;
$alpha_num->selected = $my->MY_ALPHANUM;

$updlab = new ISelect();
$updlab->table = 1;
$updlab->value = $updlab_array;
$updlab->selected = $my->MY_UPDLAB;

$stock = new ISelect('p_stock');
$stock->value = array(
	array('value' => 'N', 'label' => _('Non')),
	array('value' => 'Y', 'label' => _('Oui'))
);
$stock->selected = $my->MY_STOCK;
$stock->table = 1;

// other parameters
$all = new IText();
$all->table = 1;
echo '<form method="post" >';
echo dossier::hidden();
echo "<table class=\"result\" style=\"width:auto\">";
echo "<tr>" . td(_('Nom société'), 'style="text-align:right"') . $all->input("p_name", $my->MY_NAME) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Téléphone"), 'style="text-align:right"') . $all->input("p_tel", $my->MY_TEL) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Fax"), 'style="text-align:right"') . $all->input("p_fax", $my->MY_FAX) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Rue "), 'style="text-align:right"') . $all->input("p_street", $my->MY_STREET) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Numéro"), 'style="text-align:right"') . $all->input("p_no", $my->MY_NUMBER) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Code Postal"), 'style="text-align:right"') . $all->input("p_cp", $my->MY_CP) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Commune"), 'style="text-align:right"') . $all->input("p_Commune", $my->MY_COMMUNE) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Pays"), 'style="text-align:right"') . $all->input("p_pays", $my->MY_PAYS) . "</tr>";
$all->value = '';
echo "<tr>" . td(_("Numéro de Tva"), 'style="text-align:right"') . $all->input("p_tva", $my->MY_TVA) . "</tr>";
echo "<tr>" . td(_("Utilisation de la compta. analytique"), 'style="text-align:right"') . $compta->input("p_compta", $array) . "</tr>";
echo "<tr>" . td(_("Utilisation des stocks"), 'style="text-align:right"') . $stock->input() . "</tr>";

echo "<tr>" . td(_("Utilisation du mode strict "), 'style="text-align:right"') . $strict->input("p_strict", $strict_array) . "</tr>";
echo "<tr>" . td(_("Assujetti à la tva"), 'style="text-align:right"') . $tva_use->input("p_tva_use", $strict_array) . "</tr>";
echo "<tr>" . td(_("Suggérer le numéro de pièce justificative"), 'style="text-align:right"') . $pj_suggest->input("p_pj", $strict_array) . "</tr>";
echo "<tr>" . td(_("Suggérer la date"), 'style="text-align:right"') . $date_suggest->input("p_date_suggest", $strict_array) . "</tr>";
echo '<tr>' . td(_('Afficher la période comptable pour éviter les erreurs de date'), 'style="text-align:right"') . $check_periode->input('p_check_periode', $strict_array) . '</tr>';
echo '<tr>' . td(_('Utilisez des postes comptables alphanumérique'), 'style="text-align:right"') . $alpha_num->input('p_alphanum') . '</tr>';
echo '<tr>' . td(_('Changer le libellé des détails'), 'style="text-align:right"') . $updlab->input('p_updlab') . '</tr>';

echo "</table>";
echo HtmlInput::submit("record_company", _("Sauve"));
echo "</form>";
echo '</div>';
return;
?>
