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
 * @brief show detail of a operation of sale
 *
 */
global $g_parameter;
?>
<table class="result">
<?php 
  bcscale(2);
  $total_htva=0;$total_tvac=0;
  echo th(_('Quick Code'));
echo th(_('Description'));
echo th(_('Prix/Un'), 'style="text-align:right"');
echo th(_('Quantité'), 'style="text-align:right"');
if ( $g_parameter->MY_TVA_USE == 'Y')
  echo th(_('Taux TVA'), 'style="text-align:right"');
else
  echo th('');
if ( $g_parameter->MY_TVA_USE == 'Y') {
  echo th(_('HTVA'), 'style="text-align:right"');
  echo th(_('TVA NP'), 'style="text-align:right"');
  echo th(_('TVA'), 'style="text-align:right"');
  echo th(_('TVAC'), 'style="text-align:right"');
} else
  echo th(_('Total'), 'style="text-align:right"');


echo '</tr>';
  for ($e=0;$e<count($obj->det->array);$e++) {
    $row='';
    $q=$obj->det->array[$e];
    $fiche=new Fiche($cn,$q['qs_fiche']);
	$view_card_detail=HtmlInput::card_detail($fiche->strAttribut(ATTR_DEF_QUICKCODE),"", ' class="line" ');
	$row.=td($view_card_detail);
        $input = new ISpan("e_march" . $q['j_id'] . "_label");
        $hidden = HtmlInput::hidden("j_id[]", $q['j_id']);
        $input->value = $fiche->strAttribut(ATTR_DEF_NAME);

    $row.=td($input->input().$hidden);
    $sym_tva='';
	$pu=0;
	if ($q['qs_quantite'] != 0)	$pu=bcdiv($q['qs_price'],$q['qs_quantite']);
    $row.=td(nbm($pu),'class="num"');
    $row.=td(nbm($q['qs_quantite']),'class="num"');
	$sym_tva='';
   if ( $g_parameter->MY_TVA_USE=='Y' && $q['qs_vat_code'] != '') {
     /* retrieve TVA symbol */
     $tva=new Acc_Tva($cn,$q['qs_vat_code']);
     $tva->load();
     $sym_tva=(h($tva->get_parameter('label')));
     //     $sym_tva=$sym
   }

   $row.=td($sym_tva,'style="text-align:center"');

    $htva=$q['qs_price'];

    $row.=td(nbm($htva),'class="num"');
    $tvac=bcadd($htva,$q['qs_vat']);
    if ($g_parameter->MY_TVA_USE=='Y')
      {
		$class="";
		if ($q['qs_vat_sided'] != 0) {
			$class=' style="text-decoration:line-through"';
			$tvac=bcsub($tvac,$q['qs_vat']);
		}
		$row.=td(nbm($q['qs_vat_sided']),'class="num"');
		$row.=td(nbm($q['qs_vat']),'class="num"'.$class);
		$row.=td(nbm($tvac),'class="num"');
      }
    $total_tvac=bcadd($total_tvac,$tvac);
    $total_htva=bcadd($total_htva,$htva);
    echo tr($row);

  }
  if ($g_parameter->MY_TVA_USE=='Y')
	$row= td(_('Total'),' style="font-style:italic;text-align:right;font-weight: bolder;" colspan="5"');
  else
	$row= td(_('Total'),' style="font-style:italic;text-align:right;font-weight: bolder;" colspan="5"');
$row.=td(nbm($total_htva),'class="num" style="font-style:italic;font-weight: bolder;"');
if ($g_parameter->MY_TVA_USE=='Y')
  $row.=td("").td(nbm($total_tvac),'class="num" style="font-style:italic;font-weight: bolder;"');
echo tr($row);
?>
</table>