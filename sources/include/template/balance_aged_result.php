<?php
/*
 * * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

 * 
 */


/**
 * @file
 * @brief show the result of balance ageing, included from Balance_Age::display_purchase
 * @see Balance_Age
 */
bcscale(2);
?>

<?php 
    $nb_fiche=count($a_fiche);
    for ($i=0;$i<$nb_fiche;$i++):
        $card = new Lettering_Card($this->cn,$a_fiche[$i]['quick_code']);
        $card->set_parameter('start', $p_date_start);
        $card->get_balance_ageing($p_let);
        if ( empty ($card->content)) continue;
?>
<?php echo HtmlInput::card_detail($a_fiche[$i]['quick_code'],h($a_fiche[$i]['name']).' '. h($a_fiche[$i]['first_name']));
?>
<table class="result">
    <tr>
        <th> 
            <?php echo _('Date opération');?>
        </th>
        <th>
            <?php echo _('Pièce');?>
        </th>
        <th>
            <?php echo _('Libellé');?>
        </th>
        <th>
            <?php echo _('Interne');?>
        </th>
        <th>
            <?php echo _('Fin/ OD');?>
        </th>
        <th>
            <?php echo _('< 30 jours');?>
        </th>
        <th>
            <?php echo _('entre 31 et 60 jours');?>
        </th>
        <th>
            <?php echo _('entre 61 et 90 jours');?>
        </th>
        <th>
            <?php echo _('> 90 jours');?>
        </th>
    </tr>
    <?php
    $nb_row=count($card->content);
    $sum_lt_30=0;
    $sum_gt_30_lt_60=0;
    $sum_gt_60_lt_90=0;
    $sum_gt_90=0;
    $sum_fin=0;
    for ($j=0;$j < $nb_row;$j++):

          $class=($j%2 == 0)?'even':'odd';
          $show=true;
    ?>
    <tr class="<?php echo $class;?>">
        <td>
            <?php echo $card->content[$j]['j_date_fmt'] ?>
        </td>
        <td>
            <?php echo HtmlInput::detail_op($card->content[$j]['jr_id'], $card->content[$j]['jr_pj_number']) ?>
        </td>
        <td>
            <?php echo $card->content[$j]['jr_comment'] ?>
        </td>
        <td>
            <?php echo HtmlInput::detail_op($card->content[$j]['jr_id'],$card->content[$j]['jr_internal']) ?>
        </td>
        <td style="text-align: right">
            <?php
                $side=($card->content[$j]['j_debit']=='t')?'D':'C';
                if ( $card->content[$j]['jrn_def_type'] == 'FIN' || $card->content[$j]['jrn_def_type'] == 'ODS') :
                    echo nbm($card->content[$j]['j_montant'])." ".$side;
                    if ( $card->content[$j]['j_debit']=='t'):
                        $sum_fin=bcadd($sum_fin,$card->content[$j]['j_montant']);
                    else:
                        $sum_fin=bcsub($sum_fin,$card->content[$j]['j_montant']);
                    endif;
                    $show=false;
                endif;
            ?>
        </td>
        <td style="text-align: right">
            <?php
                if ($show && $card->content[$j]['day_paid'] <= 30) :
                    echo nbm($card->content[$j]['j_montant'])." ".$side;
                    if ( $card->content[$j]['j_debit']=='t'):
                        $sum_lt_30=bcadd($sum_lt_30,$card->content[$j]['j_montant']);
                    else:
                        $sum_lt_30=bcsub($sum_lt_30,$card->content[$j]['j_montant']);
                    endif;
                endif;
            ?>
        </td>
        <td style="text-align: right">
            <?php
                if ( $show &&$card->content[$j]['day_paid'] > 30 && $card->content[$j]['day_paid'] <= 60) :
                    echo nbm($card->content[$j]['j_montant'])." ".$side;
                    if ( $card->content[$j]['j_debit']=='t'):
                       $sum_gt_30_lt_60=bcadd($sum_gt_30_lt_60,$card->content[$j]['j_montant']);
                    else:
                       $sum_gt_30_lt_60=bcsub($sum_gt_30_lt_60,$card->content[$j]['j_montant']);
                   endif;
                endif;
            ?>
        </td>
        <td style="text-align: right">
            <?php
                if ( $show && $card->content[$j]['day_paid'] > 60 && $card->content[$j]['day_paid'] <= 90) :
                    echo nbm($card->content[$j]['j_montant'])." ".$side;
                    if ( $card->content[$j]['j_debit']=='t'):
                        $sum_gt_60_lt_90=bcadd($sum_gt_60_lt_90,$card->content[$j]['j_montant']);
                    else:
                        $sum_gt_60_lt_90=bcsub($sum_gt_60_lt_90,$card->content[$j]['j_montant']);
                    endif;  
                endif;
            ?>
        </td>
        <td style="text-align: right">
            <?php
                if ($show && $card->content[$j]['day_paid'] > 90) :
                    echo nbm($card->content[$j]['j_montant'])." ".$side;
                    if ( $card->content[$j]['j_debit']=='t'):   
                        $sum_gt_90=bcadd($sum_gt_90,$card->content[$j]['j_montant']);
                    else:
                        $sum_gt_90=bcsub($sum_gt_90,$card->content[$j]['j_montant']);
                    endif;

                endif;
            ?>
        </td>
    </tr>
    <?php
      endfor;
    ?> 
    <tr class="highlight">
        <td>
            
        </td>
        <td>
            
        </td>
        <td>
            
        </td>
        <td>
            
        </td>
        <td style="text-align: right">
            <?php echo nbm(abs($sum_fin));echo ($sum_fin < 0)?'C':($sum_fin == 0)?'=':'D'; ?>
        </td>
        <td style="text-align: right">
            <?php echo nbm($sum_lt_30);echo ($sum_lt_30 < 0)?'C':($sum_lt_30 == 0)?'=':'D';?>
        </td>
        <td style="text-align: right">
            <?php echo nbm($sum_gt_30_lt_60);echo ($sum_gt_30_lt_60 < 0)?'C':($sum_gt_30_lt_60 == 0)?'=':'D';?>
        </td>
        <td style="text-align: right">
            <?php echo nbm($sum_gt_60_lt_90);echo ($sum_gt_60_lt_90 < 0)?'C':($sum_gt_60_lt_90 == 0)?'=':'D';?>
        </td>
        <td style="text-align: right">
            <?php echo nbm($sum_gt_90);echo ($sum_gt_90 < 0)?'C':($sum_gt_90 == 0)?'=':'D';?>
        </td>
    </tr>
</table>    
<?php
    endfor;
?>
