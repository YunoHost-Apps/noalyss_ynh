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
 * @brief show detail of a operation of purchase
 *
 */
global $g_parameter;
?>
<table class="result" >
	<?php 
	bcscale(2);
	$total_htva = 0;
	$total_tvac = 0;
	echo th(_('Quick Code'));
	echo th(_('Description'));
	if ($g_parameter->MY_TVA_USE == 'Y')
	{
		echo th(_('Taux TVA'), 'style="text-align:right"');
	}
	else
	{
		echo th('');
	}
	echo th(_('Prix/Un.'), 'style="text-align:right"');
	echo th(_('Quantité'), 'style="text-align:right"');
	echo th(_('Personnel'), 'style="text-align:right"');
	echo th(_('Non ded'), 'style="text-align:right"');

	if ($g_parameter->MY_TVA_USE == 'Y')
	{
		echo th(_('HTVA'), 'style="text-align:right"');
		echo th(_('TVA NP'), 'style="text-align:right"');
		echo th(_('TVA'), 'style="text-align:right"');
		echo th(_('TVAC'), 'style="text-align:right"');
	}else
		echo th(_('Total'), 'style="text-align:right"');

	echo '</tr>';
	for ($e = 0; $e < count($obj->det->array); $e++)
	{
		$row = '';
		$q = $obj->det->array[$e];
		$fiche = new Fiche($cn, $q['qp_fiche']);
		$view_card_detail = HtmlInput::card_detail($fiche->strAttribut(ATTR_DEF_QUICKCODE), "", ' class="line" ');
		$row = td($view_card_detail);
		$sym_tva = '';

		if ($g_parameter->MY_TVA_USE == 'Y' && $q['qp_vat_code'] != '')
		{
			/* retrieve TVA symbol */
			$tva = new Acc_Tva($cn, $q['qp_vat_code']);
			$tva->load();
			$sym_tva = h($tva->get_parameter('label'));
		}
		$input = new ISpan("e_march" . $q['j_id'] . "_label");
		$hidden = HtmlInput::hidden("j_id[]", $q['j_id']);
		$input->value = $fiche->strAttribut(ATTR_DEF_NAME);
		$row.=td($input->input() . $hidden);
		$row.=td($sym_tva, 'style="text-align:center"');
		$pu = 0;
		if ($q['qp_quantite'] != 0)
		$pu = bcdiv($q['qp_price'], $q['qp_quantite']);
		$row.=td(nbm($pu), 'class="num"');
		$row.=td(nbm($q['qp_quantite']), 'class="num"');

		$no_ded = $q['qp_nd_amount'];
		$row.=td(nbm($q['qp_dep_priv']), 'style="text-align:right"');
		$row.=td(nbm($no_ded), ' style="text-align:right"');
		$htva = $q['qp_price'];


		$row.=td(nbm($htva), 'class="num"');
		$tvac = bcadd($htva, $q['qp_vat']);
		$tvac = bcadd($tvac, $q['qp_nd_tva']);
		$tvac = bcadd($tvac, $q['qp_nd_tva_recup']);


		if ($g_parameter->MY_TVA_USE == 'Y')
		{
			$tva_amount = bcadd($q['qp_vat'], $q['qp_nd_tva']);
			$tva_amount = bcadd($tva_amount, $q['qp_nd_tva_recup']);
			$class = "";
			if ($q['qp_vat_sided'] <> 0)
			{
				$class = ' style="text-decoration:line-through"';
				$tvac = bcsub($tvac, $q['qp_vat_sided']);
			}
                        $row.=td(nbm($q['qp_vat_sided']),'class="num"');
			$row.=td(nbm($tva_amount), 'class="num" ' . $class);
			$row.=td(nbm($tvac), 'class="num"');
		}
		$total_tvac+=$tvac;
		$total_htva+=$htva;
		echo tr($row);
	}
	if ($g_parameter->MY_TVA_USE == 'Y')
		$row = td(_('Total'), ' style="font-style:italic;text-align:right;font-weight: bolder;width:auto" colspan="6"');
	else
		$row = td(_('Total'), ' style="font-style:italic;text-align:right;font-weight: bolder;width:auto" colspan="6"');
	$row.=td(nbm($total_htva), 'class="num" style="font-style:italic;font-weight: bolder;"');
	if ($g_parameter->MY_TVA_USE == 'Y')
		$row.=td("") . td(nbm($total_tvac), 'class="num" style="font-style:italic;font-weight: bolder;"');
	echo tr($row);
	?>
</table>
