<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><form method="post">

<table>

<?php
for ($i=0;$i<count($aList);$i++) :
  $row=$aList[$i];
?>

<tr id="row<?php echo $row['dt_id']?>">
<td colspan="2">
<?php echo h($row['dt_value']);?>
</td>
<td colspan="2">
<?php echo h($row['dt_prefix']);?>
</td>
<td>
<?php echo $row['js_mod'];?>
</td>
<td>
<?php echo $row['js_remove'];?>
</td>


</tr>
<?php 
endfor;
?>
<tr>
<td>
<?php echo $str_addCat?>
</td>
<td>
<?php echo $str_addPrefix?>
</td>
<td>
   <?php echo $str_submit?>
</td>
</tr>

</table>
<?php 
echo dossier::hidden();
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
?>
</form>