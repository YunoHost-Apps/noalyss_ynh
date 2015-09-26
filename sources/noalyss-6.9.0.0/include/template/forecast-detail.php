<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<h2><?php  echo $str_name;?></h2>
<fieldset>
<legend><?php  echo $str_action;?></legend>

<table id="fortable">
<tr>
<th><?php echo _('Catégorie');?></th>
<th style="width:40%"><?php echo _('Formules');?></th>
<th><?php echo _('ou QuickCode');?></th>
<th><?php echo _('Libellé');?></th>
<th><?php echo _('Montant');?></th>
<th><?php echo _('Débit ou Crédit');?></th>
</tr>
<?php for ($i=0;$i<count($aCat);$i++):?>
<tr>
<td><?php echo $aCat[$i]['cat'];?></td>
<td><?php echo $aCat[$i]['account'];?></td>
<td><?php echo $aCat[$i]['qc'];?></td>
<td><?php echo $aCat[$i]['name'];?></td>
<td><?php echo $aCat[$i]['per'];?></td>

<td><?php echo $aCat[$i]['amount'];?></td>
<td><?php echo $aCat[$i]['deb'];?></td>
</tr>
<?php endfor;?>
</table>
<?php echo $f_add_row ?>
</fieldset>


