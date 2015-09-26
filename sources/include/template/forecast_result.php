<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><h1><?php echo $str_name;?></h1>
Période du <?php echo $str_start?> à <?php echo $str_end;?>
<?php  for ($i=0;$i<count($aCat);$i++): // foreach category ?>
<?php if (count($aItem[$i])==0) continue;?>
<fieldset>
<legend>
<?php echo $aCat[$i]['fc_desc'];$tot_cat_estm=0;$tot_cat_real=0;$tot_cum_real=0;?>
</legend>

<?php for ($e=0;$e<count($aItem[$i]);$e++):?>
<table class="result" style="margin-bottom:3px">
<tr>
<td>
   <?php echo '<h2>'.h($aItem[$i][$e]['fi_text']).'</h2>';?>
</td>
</tr>
<tr>
<td>
<table width="100%">
<tr >
<td style="font-weight:bold;border:1px solid black">
<?php echo _('Période')?></td>
<?php for ($h=0;$h<count($aPeriode);$h++):?>
<td style="text-align:center;font-weight:bold;border:1px solid black">
<?php echo $aPeriode[$h]['myear'];?>
</td>
<?php endfor;?>
<td style="text-align:center;font-weight:bold;border:1px solid black;"><?php echo _('Totaux');?></td>
</tr>

<tr>
<td>
<?php echo _('Estimé');$tot_estm=0;?>
</td>
<?php for ($h=0;$h<count($aPeriode);$h++):?>
<td style="text-align:right;">
<?php
$amount=$aItem[$i][$e]['fi_amount'];
if (count($aPerMonth[$i]) != 0 ){
	for ($x=0;$x<count($aPerMonth[$i]);$x++) {
		$amount=$aItem[$i][$e]['fi_amount'];
		if ($aPeriode[$h]['p_id']==$aPerMonth[$i][$x]['fi_pid'] &&
			$aItem[$i][$e]['fi_card']==$aPerMonth[$i][$x]['fi_card'] &&
			$aItem[$i][$e]['fi_account']==$aPerMonth[$i][$x]['fi_account']
			)
			{
				$amount=$aPerMonth[$i][$x]['fi_amount'];
				break;
			}
	}
}
$estm[$i][$e][$h]=$amount;
echo nbm( $amount);

$tot_estm=bcadd($tot_estm,$amount);
$tot_cat_estm=bcadd($amount,$tot_cat_estm);
?>

</td>
<?php endfor;?>
<td style="text-align:right">
<?php echo nbm($tot_estm);?>
</td>
</tr>

<tr>
<td>
<?php echo _('Réel');$tot=0;?>
</td>
<?php for ($h=0;$h<count($aPeriode);$h++):?>
<td align="right">
   <?php echo nbm(  $aReal[$i][$e][$h]);$tot_cat_real=bcadd($tot_cat_real,$aReal[$i][$e][$h]);$tot=bcadd($tot,$aReal[$i][$e][$h]);?>
</td>
<?php endfor;?>
<td align="right">
<?php echo nbm( $tot);?>
</td>
</tr>
		<tr>
			<td>
						<?php echo _('Total réel');

						$tot_cat_real = 0;
						?>
					</td>
				<?php for ($h = 0; $h < count($aPeriode); $h++):?>
				<td align="right">
				<?php
				$tot_cat_real = bcadd($tot_cat_real, $aReal[$i][$e][$h]);
				$tot_cum_real=bcadd($tot_cum_real,$aReal[$i][$e][$h]);
				echo nbm($tot_cat_real);
			?>
			</td>
				<?php endfor;?>

		</tr>
<tr>
<td>
<?php echo _('Différence');?>
</td>
<?php for ($h=0;$h<count($aPeriode);$h++):?>

    <?php
 $diff= bcsub( $aReal[$i][$e][$h],$estm[$i][$e][$h]);
if ( ($aItem[$i][$e]['fi_debit'] == 'C' && $diff < 0) || ($aItem[$i][$e]['fi_debit'] == 'D' && $diff > 0))
  {
    echo '<td style="text-align:right;background-color:red;color:white">';
  }
else if ($diff==0)
  {
    echo '<td style="text-align:right;">';
  }
else
  {
    echo '<td style="text-align:right;background-color:green;color:white">';
  }

echo nbm( $diff);
?>
</td>
<?php endfor;?>
</tr>
<tr>
<td>
<?php echo _('Diff. cumul.'); $cum=0.0; ?>
</td>
<?php for ($h=0;$h<count($aPeriode);$h++):?>

<?php
    $diff= bcsub($aReal[$i][$e][$h],$estm[$i][$e][$h]);
$cum=bcadd($diff,$cum);
if ( ($aItem[$i][$e]['fi_debit'] == 'C' && $cum < 0) || ($aItem[$i][$e]['fi_debit'] == 'D' && $cum > 0))
  {
    echo '<td style="text-align:right;background-color:red;color:white">';
  }
else if ($cum ==0)
  {
    echo '<td style="text-align:right;">';
  }

else
  {
    echo '<td style="text-align:right;background-color:green;color:white">';
  }

echo nbm( $cum);
?>
</td>
<?php endfor;?>
<?php
if ( ($aItem[$i][$e]['fi_debit'] == 'C' && $cum < 0) || ($aItem[$i][$e]['fi_debit'] == 'D' && $cum > 0))
  {
    echo '<td style="text-align:right;background-color:red;color:white">';
  }
else
  {
    echo '<td style="text-align:right;background-color:green;color:white">';
  }
 echo nbm(  $cum);
?>
</td>
</tr>


</table>
</td>
</tr>
<?php endfor;?>
</table>

<table>
<tr>
<?php echo td(_('Total Catégorie estimé'));echo td(nbm($tot_cat_estm),'num');?>
</tr>
<tr>
<?php echo td(_('Total Catégorie réel'));echo td(nbm($tot_cum_real),'num');?>
</tr>
<tr>
<?php echo td(_('Différence'));echo td(nbm($tot_cum_real-$tot_cat_estm),'num');?>
</tr>
</table>
</fieldset>


<?php endfor;?>
<?php if ( ! empty ($error) ) : ?>
<div class="error">
Désolé il y a des formules incorrectes
<ul style="list-style-type:none">

   <?php $last="";?>
   <?php for ($i=0;$i<count($error);$i++) : ?>
<?php
   if ( $last != $error[$i] ) {  echo h($error[$i]); }
$last=$error[$i];
endfor;
?>
</ul>
</div>
<?php endif; ?>