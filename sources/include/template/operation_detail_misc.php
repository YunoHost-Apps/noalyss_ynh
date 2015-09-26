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
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief how detail of a misc operation
 *
 */
?>
<table class="result">
<tr>
<?php 
    echo th(_('Poste Comptable'));
    echo th(_('Quick Code'));
    echo th(_('Libellé'));
echo th(_('Débit'), 'style="text-align:right"');
echo th(_('Crédit'), 'style="text-align:right"');
echo '</tr>';
$amount_idx=0;
for ($e = 0; $e < count($obj->det->array); $e++)
{
	$row = '';
	$q = $obj->det->array;
	$view_history = sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_account(\'%s\',\'%s\')" >%s</A>', $q[$e]['j_poste'], $gDossier, $q[$e]['j_poste']);

	$row.=td($view_history);

	if ($q[$e]['j_qcode'] != '')
	{
		$fiche = new Fiche($cn);
		$fiche->get_by_qcode($q[$e]['j_qcode']);
		$view_history = sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_card(\'%s\',\'%s\')" >%s</A>', $fiche->id, $gDossier, $q[$e]['j_qcode']);
	}
	else
		$view_history = '';
	$row.=td($view_history);
	$l_lib = $q[$e]['j_text'];

	if ($l_lib != '')
	{
		$l_lib = $q[$e]['j_text'];
	}
	else if ($q[$e]['j_qcode'] != '')
	{
		// nom de la fiche
		$ff = new Fiche($cn);
		$ff->get_by_qcode($q[$e]['j_qcode']);
		$l_lib = $ff->strAttribut(ATTR_DEF_NAME);
	}
	else
	{
		// libellé du compte
		$name = $cn->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1', array($q[$e]['j_poste']));
		$l_lib = $name;
	}
	$l_lib = strip_tags($l_lib);
	$input = new ISpan("e_march" . $q[$e]['j_id'] . "_label");
	$input->value = $l_lib;
	$hidden = HtmlInput::hidden("j_id[]", $q[$e]['j_id']);
	$row.=td($input->input() . $hidden);
	$montant = td(nbm($q[$e]['j_montant']), 'class="num"');
	$row.=($q[$e]['j_debit'] == 't') ? $montant : td('');
	$row.=($q[$e]['j_debit'] == 'f') ? $montant : td('');
	echo tr($row);
}
?>
</table>