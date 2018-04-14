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


/**
 * @file
 * @brief detail of the list of operation with VAT and items
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
for ($i=0;$i<$nb_data;$i++):
    $odd=' class="odd" ';
    $tot_amount_novat=bcadd($tot_amount_novat,$this->data[$i]['novat']);
    $tot_amount_vat=bcadd($tot_amount_vat,$this->data[$i]['vat']);
    $tot_amount_vat=bcsub($tot_amount_vat,$this->data[$i]['tva_sided']);
    $tot_amount_tvac=bcadd($tot_amount_tvac,$this->data[$i]['tvac']);
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
            <?=HtmlInput::history_card($this->data[$i]['qs_client'],h($this->data[$i]['name'].' '.$this->data[$i]['first_name']))?>
        </td>
        <td>
            <?=h($this->data[$i]['jr_comment'])?>
        </td>
        <td class="num">
            <?=nbm($this->data[$i]['novat'])?>
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
    <tr>
        <td></td>
        <td></td>
        <td class="width:auto" colspan="8" style="border-style: solid;border-width:1px;border-color: blue">
<?php
    /// Detail opération
$det=$this->db->execute("detail_sale",array($this->data[$i]['jr_internal']));
$a_detail=Database::fetch_all($det);
?>
            <table style="width: 100%">
                <tr>
                    <th><?=_("Item")?></th>
                    <th class="num"><?=_("Prix Uni")?></th>
                    <th class="num"><?=_("Quantité")?></th>
                    <th class="num"><?=_("HTVA")?></th>
                    <th class="num"><?=_("TVA")?></th>
                    <th class="num"><?=_("Code TVA")?></th>
                    <th class="num"><?=_("TVAC")?></th>
                </tr>
<?php
$nb_detail=count($a_detail);
for ($j=0;$j<$nb_detail;$j++):
?>  
                <tr>
                    <td><?=$a_detail[$j]['qcode']?>  
                        <?=$a_detail[$j]['name']?>  </td>
                    <td class="num" style="width:10%"><?=$a_detail[$j]['qs_quantite']?>  </td>
                    <td class="num" style="width:10%"><?=$a_detail[$j]['qs_unit']?>  </td>
                    <td class="num" style="width:10%"><?=nbm($a_detail[$j]['qs_price'])?>  </td>
                    <td class="num" style="width:10%"><?=nbm($a_detail[$j]['qs_vat'])?>  </td>
                    <td class="num" style="width:10%"><?=$a_detail[$j]['tva_label']?>  </td>
                    <td class="num" style="width:10%"><?=nbm($a_detail[$j]['tvac'])?>  </td>
                </tr>
<?php
endfor;
?>
                
            </table>
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
            <td><?=nbm($tot_amount_novat)?></td>
            <td><?=nbm($tot_amount_vat)?></td>
            <td><?=nbm($tot_amount_tvac)?></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>