<?php

/*
 *   This file is part of NOALYSS.
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
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief display purchase on one line with sum of VAT, Operation, Private exp.
 * @todo prévoir aussi pour les non assujetti : faire disparaître les montants TVA
 */

?>
<table class="result">
    <tr>
        <th>
            <?=_('Date')?>
        </th>
        <th>
            <?=_('Date paiement')?>
        </th>
        <th>
            <?=_('Pièce')?>
        </th>
        <th>
            <?=_('Interne')?>
        </th>
        <th>
            <?=_('Client')?>
        </th>
        <th>
            <?=_('Description')?>
        </th>
        <th>
            <?=_('HTVA')?>
        </th>
        <th>
            <?=_('Non ded')?>
        </th>
        <th>
            <?=_('TVA')?>
        </th>
              
        <th>
            <?=_('TVAC')?>
        </th>
    </tr>
<?php 
$nb_data=count($this->data);
$tot_amount_novat=0;
$tot_amount_vat=0;
$tot_amount_tvac=0;
$tot_amount_private=0;
$tot_nonded_vat=0;
$tot_nonded_amount=0;

for ($i=0;$i<$nb_data;$i++):
    $odd=($i%2==0)?' class="even" ':' class="odd" ';
    $tot_amount_novat=bcadd($tot_amount_novat,$this->data[$i]['novat']);
    $tot_amount_vat=bcadd($tot_amount_vat,$this->data[$i]['vat']);
    $tot_amount_vat=bcsub($tot_amount_vat,$this->data[$i]['tva_sided']);
    $tot_amount_tvac=bcadd($tot_amount_tvac,$this->data[$i]['tvac']);
    $tot_nonded_amount=bcadd($tot_nonded_amount,$this->data[$i]['noded_amount']);
    $tot_nonded_amount=bcadd($tot_nonded_amount,$this->data[$i]['private_amount']);
    $tot_nonded_vat=bcadd($tot_nonded_vat, $this->data[$i]['noded_vat']);
?>
    <tr <?=$odd?> >
        <td>
            <?=$this->data[$i]['jr_date']?>
        </td>
        <td>
            <?=$this->data[$i]['jr_date_paid']?>
        </td>
        <td>
            <?=$this->data[$i]['jr_pj_number']?>
        </td>
        <td>
           <?=HtmlInput::detail_op($this->data[$i]['jr_id'], $this->data[$i]['jr_internal'])?>
        </td>
        <td>
            <?=HtmlInput::history_card($this->data[$i]['qp_supplier'],h($this->data[$i]['name'].' '.$this->data[$i]['first_name']." [ {$this->data[$i]['qcode']} ]"))?>
        </td>
        <td>
            <?=h($this->data[$i]['jr_comment'])?>
        </td>
        <td class="num">
            <?=nbm($this->data[$i]['novat'])?>
        </td>
        <td class="num">
            <?=nbm(bcadd($this->data[$i]['noded_amount'],$this->data[$i]['private_amount']));?>
        </td>
        <td class="num">
            <?=nbm(bcsub($this->data[$i]['vat'],$this->data[$i]['tva_sided']))?>
        </td>
        <td class="num">
            <?=nbm($this->data[$i]['tvac'])?>
        </td>
        <td>
            
        <?php
         $ret_reconcile=$this->db->execute('reconcile_date',array($this->data[$i]['jr_id']));
         $max=Database::num_row($ret_reconcile);
        if ($max > 0) {
            $sep="";
            for ($e=0;$e<$max;$e++) {
                $row=Database::fetch_array($ret_reconcile, $e);
                echo $sep.HtmlInput::detail_op($row['jr_id'],$row['jr_date'].' '. $row['jr_internal']);
                $sep=' ,';
        }
    } ?>
        </td>
    </tr>
<?php 
    endfor;
?>
    <tfoot>
        <tr class="highlight">
            <td>
                <?=_("Totaux")?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="num"><?=nbm($tot_amount_novat)?></td>
            <td class="num"><?=nbm($tot_nonded_amount)?></td>
            <td class="num"><?=nbm($tot_amount_vat)?></td>
            <td class="num"><?=nbm($tot_amount_tvac)?></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>