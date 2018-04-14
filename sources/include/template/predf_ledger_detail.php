<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><fieldset>
<legend><?php echo $f_legend ?>
</legend>
<?php echo $f_type?><?php echo $f_client_qcode?><?php echo $f_client_bt?> <?php echo $f_client?><br>
<input type="hidden" id="p_jrn" name="p_jrn" value="<?php echo $this->jrn_def_id; ?>">    
<?php echo $str_add_button?>
</fieldset>

<fieldset>
<legend><?php echo $f_legend_detail?></legend>
<table id="sold_item" width="100%" border="0">
<tr>
<th style="width:auto"colspan="2">Code <?php echo HtmlInput::infobulle(0)?></th>
      <th><?php echo _('Dénomination')?></th>
<?php if ($flag_tva =='Y') : ?>
      <th><?php echo _('prix/unité htva')?><?php echo HtmlInput::infobulle(6)?></th>
      <th><?php echo _('quantité')?></th>
      <th><?php echo _('Total HTVA')?></th>
	  <th><?php echo _('tva')?></th>
      <th><?php echo _('tot.tva')?></th>
      <th><?php echo _('tvac')?></th>
<?php else: ?>
	  <th><?php echo _('prix/unité ')?><?php echo HtmlInput::infobulle(6)?></th>
      <th><?php echo _('quantité')?></th>
      <th><?php echo _('Total ')?></th>
<?php endif;?>



</tr>
<?php foreach ($array as $item) {
echo '<tr>';
echo $item['quick_code'];
echo '<td>'.$item['bt'].'</td>';
?>
<td style="border-bottom: 1px dotted grey; width: 75%;"><?php echo $item['denom'] ?></td>
<?php 
echo td($item['pu']);
echo td($item['quantity' ]);
echo td($item['htva']);
if ($flag_tva=='Y')  {
	echo td($item['tva']);
	echo td($item['amount_tva'].$item['hidden']);

}
echo td($item['tvac']);
echo '</tr>';
}

?>
</table>

<div style="position:float;float:right;text-align:right;padding-right:5px;font-size:1.2em;font-weight:bold;color:blue">
      <?php echo HtmlInput::button('act',_('Actualiser'),'onClick="compute_all_ledger();"'); ?>
 </div>

    <div style="position:float;float:right;text-align:left;font-size:1.2em;font-weight:bold;color:blue" id="sum">
    <br><span id="htva">0.0</span>
<?php
    if ( $flag_tva=='Y' )  : ?>
     <br><span id="tva">0.0</span>
    <br><span id="tvac">0.0</span>
<?php    endif;     ?>

 </div>

<div style="position:float;float:right;text-align:right;padding-right:5px;font-size:1.2em;font-weight:bold;color:blue">
<?php
	if ( $flag_tva =='Y') :
	?>
  <br><?php echo _('Total HTVA')?>
  <br><?php echo _('Total TVA')?>
  <br><?php echo _('Total TVAC')?>
 <?php else:  ?>
     <br><?php echo _('Total')?>
<?php endif; ?>
</div>

</fieldset>


