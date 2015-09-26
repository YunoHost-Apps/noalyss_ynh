<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><fieldset>
<legend><?php echo $msg?>
</legend>
<table>
<tr>
<td>
   <?php echo _('Pour le journal')?>
</td>
<td>
<?php echo $f_source?>
</td>
</tr>

<tr>
<td>
   <?php echo _('Libellé')?>
</td>
<td>
<?php echo $f_lib?>
</td>
</tr>

<tr>
<td>
   <?php echo _('Type de fiche')?>
</td>
<td>
<?php echo $f_type_fiche?>
</td>
</tr>


<tr>
<td>
   <?php echo _('Paiement enregistré dans ')?>
</td>
<td>
<?php echo $f_ledger_record?>
</td>
</tr>

<tr>
<td>
   <?php echo _('Avec la fiche')?>
</td>
<td>
<?php echo $f_qcode?>
</td>
</tr>
</table>

</fieldset>