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
 * @brief from Acc_Ledger_History_Sale::export_html_oneline
 * @todo prévoir aussi pour les non assujetti : faire disparaître les montants TVA
 */
?>
<TABLE class="result">
    <tr>
        <th><?php echo _("Pièce")?></th>
        <th><?php echo _("Date")?></th>
        <th><?php echo _("Paiement")?></th>
        <th><?php echo _("Ref")?></th>
        <th><?php echo _("Client")?></th>
        <th><?php echo _("Description")?></th>
        <th style="text-align:right">HTVA</th>
        
        
<?php
$col_tva="";

 if ( $own->MY_TVA_USE=='Y')
        {
            $a_Tva=$this->db->get_array("select tva_id,tva_label from tva_rate where tva_rate != 0.0000 order by tva_id");
            foreach($a_Tva as $line_tva)
            {
                $col_tva.='<th style="text-align:right">Tva '.$line_tva['tva_label'].'</th>';
            }
        }
echo $col_tva;      
?>
        <th style="text-align:right">TVAC</th>
        <th><?php echo _("Opérations rapprochées")?></th>
    </tr>
<?php
$i = 0;
$tot['htva']=0;
$tot['dep_priv']=0;
$tot['dna']=0;
$tot['tva_nd']=0;
$tot['tvac']=0;
$tot['tva']=array();
bcscale(4);
foreach ($this->data as $line) {
    $i++;
    /*
     * Get date of reconcile operation
     */
    $ret_reconcile=$this->db->execute('reconcile_date',array($line['jr_id']));
    $class = ($i % 2 == 0) ? ' class="even" ' : ' class="odd" ';
    echo "<tr $class>";
    
    // Receipt number
    echo "<TD>" . h($line['jr_pj_number']) . "</TD>";
    
    // Date
    echo "<TD>" . smaller_date($line['jr_date']) . "</TD>";
    echo "<TD>" . smaller_date($line['jr_date_paid']) . "</TD>";
    
    // Internal with detail
    echo "<TD>" . HtmlInput::detail_op($line['jr_id'], $line['jr_internal']) . "</TD>";
    
    // find the tiers (normally in $this->data ! 
    $tiers =HtmlInput::history_card($line['qs_client'],h($line['name'].' '.$line['first_name'])."[{$line['qcode']}]");

    echo td($tiers);
    
    // Label
    echo "<TD>" . h($line['jr_comment']) . "</TD>";
    

    // HTVA amount 
    echo "<TD class=\"num\">" . nbm(round($line['novat'],2),2) . "</TD>";
    $tot['htva']=bcadd($tot['htva'],  round(floatval($line['novat']),2));
    
    //--------------------------------------------------------------------------
    // If VAT then display it
    //--------------------------------------------------------------------------
    if ($own->MY_TVA_USE == 'Y' )
    {
        $a_tva_amount=array();
        
        foreach ($line['detail_vat'] as $lineTVA)
        {
                foreach ($a_Tva as $idx=>$line_tva)
                {

                    if ($line_tva['tva_id'] == $lineTVA['qs_vat_code'])
                    {
                        $a=$line_tva['tva_id'];
                        $a_tva_amount[$a]=$lineTVA["vat_amount"];
                    }
                }
            }
        foreach ($a_Tva as $line_tva)
        {
            $a=$line_tva['tva_id'];
            if ( isset($a_tva_amount[$a]) && $a_tva_amount[$a] != 0) {
                echo '<td class="num">'.nbm(round($a_tva_amount[$a],2)).'</td>';
                $tot['tva'][$a]=(isset($tot['tva'][$a]))?bcadd($tot['tva'][$a],round(floatval($a_tva_amount[$a]),2)):round(floatval($a_tva_amount[$a]),2);
            }
            else
                printf("<td class=\"num\"></td>");
        }
    }
    
    echo '<td class="num">'.nbm($line['tvac'],2).'</td>';
    $tot['tvac']=bcadd($tot['tvac'], round(floatval($line['tvac']),2));
    /*
     * If reconcile print them
     */
    echo '<td>';
    $max=Database::num_row($ret_reconcile);
    if ($max > 0) {
        $sep="";
        for ($e=0;$e<$max;$e++) {
            $row=Database::fetch_array($ret_reconcile, $e);
            echo $sep.HtmlInput::detail_op($row['jr_id'],$row['jr_date'].' '. $row['jr_internal']);
            $sep=' ,';
        }
    }
    echo '</td>';
    echo "</tr>";
}
/** 
 * summary
 */
?>
    <tr class="highlight">
        <td>
            <?php echo _('Totaux')?>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="num"><?php echo nbm($tot['htva']); ?></td>
        <?php if ($own->MY_TVA_USE == 'Y' ): ?>
            <?php  foreach ($a_Tva as $line_tva) :
                        $a=$line_tva['tva_id'];
                        if ( isset($tot['tva'][$a])) :
                    ?>
                        <td class="num"><?php echo nbm($tot['tva'][$a])?></td>
                    <?php else : ?>
                        <td>

                        </td>
                    <?php endif; ?>
                <?php endforeach;?>
        <?php endif; ?>
        <td class="num"><?php echo nbm($tot['tvac'])?></td>
        <td></td>
    </tr>
        
        
</table>