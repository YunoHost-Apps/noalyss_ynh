<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 * defined variable $array with the result included from class_anc_group
 */
$prev=''; 
?>

<table class="result">

<?php
$idx=0;bcscale(2);$solde=0;$tot_group_deb=0;$tot_group_cred=0;
for ($i=0;$i<count($array);$i++):
echo '<tr>';
if ( $i==0) {
	$prev=$array[$i]['ga_id'];
	echo '<tr>';
	echo td($array[$i]['ga_id'],' colspan="5" style="width:auto;font-size:1.2em"');
	 echo '</tr>';
	 ?>
	 <tr>
<th>Activité</th>
<th style="text-align:right" >Débit</th>
<th style="text-align:right">Crébit</th>
<th style="text-align:right">Solde</th>
</tr>
	 <?php
	}
if ( $prev != $array[$i]['ga_id'])
{
	$prev=$array[$i]['ga_id'];
	
	echo '<tr>';
	echo td('Solde');
	echo td(nbm($tot_group_deb),' class="num"');
	echo td(nbm($tot_group_cred),' class="num"');
	echo td(nbm(bcsub($tot_group_cred,$tot_group_deb)),' class="num"');
				
	echo '</tr>';
	$tot_group_deb=0;$tot_group_cred=0;
		$prev=$array[$i]['ga_id'];
	echo '<tr>';
	echo td($array[$i]['ga_id'],' colspan="5" style="width:auto;font-size:1.2em"');
	 echo '</tr>';
	 ?>
	 <tr>
<th>Activité</th>
<th style="text-align:right"><?php echo _("Débit");?></th>
<th style="text-align:right" ><?php echo _("Crébit");?></th>
<th style="text-align:right" ><?php echo _("Solde");?></th>
</tr>
<?php
}
if ($idx %2 == 0)
  echo '<tr class="even">';
else
  echo '<tr class="odd">';
		echo td($array[$i]['po_name']);
echo td(nbm($array[$i]['sum_deb']),' class="num"');
echo td(nbm($array[$i]['sum_cred']),' class="num"');
$solde=bcsub($array[$i]['sum_cred'],$array[$i]['sum_deb']);
echo td(nbm($solde),' class="num"');
		$tot_group_deb=bcadd($tot_group_deb,$array[$i]['sum_deb']);
		$tot_group_cred=bcadd($tot_group_cred,$array[$i]['sum_cred']);
echo '</tr>';
$idx++;
endfor;
	
echo '<tr>';

echo td(_('Solde'));
echo td(nbm($tot_group_deb),' class="num"');
echo td(nbm($tot_group_cred),' class="num"');
echo td(nbm(bcsub($tot_group_cred,$tot_group_deb)),' class="num"');
				
echo '</tr>';
?>

</table>