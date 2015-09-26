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
            <?php echo $f_client_qcode?><?php echo $f_client_bt?> <?php echo $f_client?></td>
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
<?php echo $str_add_button?>

<h2><?php echo $f_legend_detail?></h2>
<table id="sold_item" style="width:100%;border-width: 0px">
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
<td style="width: 75%;"><?php echo $item['denom'] ?></td>
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

<div style="position:float;float:right;text-align:right;padding-right:5px;font-size:1.2em;font-weight:bold">
      <?php echo HtmlInput::button('act',_('Actualiser'),'onClick="compute_all_ledger();"'); ?>
 </div>

    <div style="position:float;float:right;text-align:left;font-size:1.2em;font-weight:bold;" id="sum">
    <br><span id="htva">0.0</span>
<?php
    if ( $flag_tva=='Y' )  : ?>
     <br><span id="tva">0.0</span>
    <br><span id="tvac" >0.0</span>
<?php    endif;     ?>

 </div>

<div style="position:float;float:right;text-align:right;padding-right:5px;font-size:1.2em;font-weight:bold;">
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



