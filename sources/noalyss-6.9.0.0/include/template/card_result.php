<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<fieldset id="asearch" style="height:88%">
   <legend><?php echo _('Résultats'); ?></legend>
<div style="height:88%;overflow:auto;">
<table class="result" >
<?php for ($i=0;$i<sizeof($array);$i++) : ?>
    <?php $class=($i%2==0)?'odd':'even';?>
<tr class="<?php echo $class;?>">
<td style="padding-right:55px">
<a href="javascript:void(0)" class="detail" onclick="<?php echo $array[$i]['javascript']?>">
<?php echo $array[$i]['quick_code']?>
</a>
</td>
<td>
   <?php echo HtmlInput::history_account($array[$i]['accounting'],$array[$i]['accounting']); ?>
</td>
<td>
   <?php echo $array[$i]['name']?>
</td>
<td>
   <?php echo $array[$i]['first_name']?>
</td>
<td>
<?php echo $array[$i]['description']?>
</td>

</tr>


<?php endfor; ?>
</table>
<span style="font-style: italic;">
   <?php echo _("Nombre d'enregistrements:$i"); ?>
</span>
<br>
</div>
</fieldset>
