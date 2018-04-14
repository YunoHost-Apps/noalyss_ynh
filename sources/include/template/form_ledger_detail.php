<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
	<div id="jrn_name_div">
	<h2 id="jrn_name"> <?php echo $this->get_name()?></h2>
</div>
<table>
    <tr>
        <td> 
            <?php echo _('Journal')?> <?php echo $f_jrn?>
        </td>
    </tr>
    <tr>
        <td> <?php echo _('Modèle opération') ?></td>
        <td>
            <?php echo $str_op_template;?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo _('Date')?> 
        </td>
        <td>
            <?php echo $f_date ?>
        </td>
    </tr>
    <tr>
        <td>
        <?php echo _('Echeance')?>  
        </td>
        <td>
            <?php echo $f_echeance?>
        </td>
    </tr>
    <tr>
        <td><?php echo $f_type?></td>
        <td>
            <?php echo $f_client_qcode?><?php echo $f_client_bt?><?php echo $str_add_button_tiers;?> <?php echo $f_client?></td>
        </td>
    </tr>
    
    <tr>
            <?php echo $f_periode?>
    </tr>
    <tr>
        <td>
            <?php echo _('Numéro Pièce')?> 
        </td>
        <td>
            <?php echo $f_pj?>
        </td>
    </tr>
    <tr>
        <td>
             <?php echo _('Libellé')?> 
             <?php echo $label ; ?> 
        </td>
        <td>
            <?php echo $f_desc?>
        </td>
    </tr>

</table>
     
      
<br>
<h2><?php echo $f_legend_detail?></h2>
<table id="sold_item" >
<tr>
<th style="width:auto"colspan="2">Code <?php echo Icon_Action::infobulle(0)?></th>
      <th class="visible_gt800 visible_gt1155"><?php echo _('Dénomination')?></th>
<?php if ($flag_tva =='Y') : ?>
      <th><?php echo _('prix/unité htva')?><?php echo Icon_Action::infobulle(6)?></th>
      <th><?php echo _('quantité')?></th>
      <th class="visible_gt800" ><?php echo _('Total HTVA')?></th>
	  <th><?php echo _('tva')?></th>
      <th class="visible_gt800"><?php echo _('tot.tva')?></th>
      <th><?php echo _('tvac')?></th>
<?php else: ?>
	  <th><?php echo _('prix/unité ')?><?php echo Icon_Action::infobulle(6)?></th>
      <th><?php echo _('quantité')?></th>
      <th><?php echo _('Total ')?></th>
<?php endif;?>



</tr>
<?php foreach ($array as $item) {
echo '<tr>';
// echo "<td>";
echo $item['quick_code'];
// echo "</td>";
echo '<td>'.$item['bt'].$item['card_add'].'</td>';
?>
<td class="visible_gt800 visible_gt1155"><?php echo $item['denom'] ?></td>
<?php 
echo td($item['pu']);
echo td($item['quantity' ]);
echo td($item['htva'],' class="visible_gt800" ');
if ($flag_tva=='Y')  {
	echo td($item['tva']);
	echo td($item['amount_tva'].$item['hidden'],' class="visible_gt800" ');

}
echo td($item['tvac']);
echo '</tr>';
}

?>
<tfoot id="sum">
    <tr  class="highlight">
    <td> <?php echo _("Total")?>  </td>
    <td>   </td>
    <td class="visible_gt800 visible_gt1155">   </td>
    <td>   </td>
    <td>   </td>
    <td class="num visible_gt800">  <span id="htva">0.0</span></td>
    <td>   </td>
 <?php if ( $flag_tva=='Y' )  : ?>    
    <td class="num visible_gt800">  <span id="tva">0.0</span> </td>
    <td class="num">  <span id="tvac" >0.0</span> </td>
  <?php    endif;     ?>  
    </tr>
</tfoot>
</table>

<?php echo HtmlInput::button('act',_('Actualiser'),'onClick="compute_all_ledger();"'); ?>

 


