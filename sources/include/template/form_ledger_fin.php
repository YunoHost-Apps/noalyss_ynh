<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><fieldset>
	<div id="jrn_name_div">
	<h2  id="jrn_name"> <?php echo $this->get_name()?></h2>
</div>
<legend><?php echo $f_legend ?> </legend>
<?php 
	$wchdate=new ISelect('chdate');
	$wchdate->value=array(
			array('value'=>1,'label'=>_("Avec date d'extrait")),
			array('value'=>2,'label'=>_("Avec date opérations"))
	);
	$wchdate->selected=(isset($chdate))?$chdate:1;
	$wchdate->javascript='onchange="show_fin_chdate(\'chdate\')"';
?>
<?php echo $wchdate->input();?>
<span id="chdate_ext">
   <?php echo _('Date').' '.$f_date ?>
</span>

<?php echo $f_period?><br>
<?php echo $f_jrn?><br>
<?php echo _('Banque')?><?php echo $f_bank ?>

</fieldset>

<fieldset>
<legend><?php echo $f_legend_detail?></legend>
   <fieldset><legend><?php echo _('Extrait de compte')?></legend>
   <?php echo _('Numéro extrait')?> <?php echo $f_extrait?>
   <?php echo _('Solde début') ?> <?php echo $wFirst->input();?>
<?php echo _('Solde Fin')?> <?php echo $wLast->input();?>
</fieldset>
   <fieldset><legend><?php echo _('Opérations')?></legend>
<table id="fin_item" width="100%" border="0">
<tr>
<th id="thdate" style="display:none;text-align: left"><?php echo _('Date')?><?php echo Icon_Action::infobulle(16)?></TH>
<th style="text-align: left;width: auto">code<?Icon_Action::infobulle(0)?></TH>
   <th style="text-align: left"><?php echo _('Fiche')?></TH>
   <th style="text-align: left" class="visible_gt800 visible_gt1155"><?php echo _('Commentaire')?></TH>
   <th style="text-align: left"><?php echo _('Montant')?></TH>
   <th style="text-align: left;width:auto"colspan="2"> <?php echo _('Op. Concernée(s)')?></th>
</tr>

<?php 
$i=0;
foreach ($array as $item) {

echo '<tr>';
// echo td($item['dateop']);
echo td($item['dateop'],' style="display:none" id="tdchdate'.$i.'"');
echo td($item['qcode'].$item['search'].$item['card_add']);
echo td($item['cname']);
echo td($item['comment'],' class="visible_gt800 visible_gt1155" ');
echo td($item['amount']);
echo td($item['concerned']);
echo '</tr>';
$i++;

}
?>
</table>
</fieldset>
</fieldset>
<script>
	show_fin_chdate('chdate');
</script>

