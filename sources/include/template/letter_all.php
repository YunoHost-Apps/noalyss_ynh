<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php
require_once  NOALYSS_INCLUDE.'/class_acc_operation.php';
require_once  NOALYSS_INCLUDE.'/class_acc_reconciliation.php';
$amount_deb=0;$amount_cred=0;
$gDossier=dossier::id();
global $g_failed;

if ( count($this->content) == 0 ) :
?>
  <h2 class="info2"><?php echo _('Désolé aucun résultat trouvé')?></h2>

<?php exit();
  endif;?>
  <table class="result">
<tr>
<th>
   <?php echo _('Lettrage')?>
</th>
<th>
   <?php echo _('Date')?>
</th>
<th>
   <?php echo _('Ref')?>
</th>
<th>
   <?php echo _('Interne')?>
</th>
<th>
   <?php echo _('Description')?>
</th>
<th style="text-align:right">
   <?php echo _('Débit')?>
</th>
<th style="text-align:right">
   <?php echo _('Crédit')?>
</th>
<th style="text-align:center">
  <?php echo _('Op. concernée')?>
</th>
</tr>

<?php
for ($i=0;$i<count($this->content);$i++):
  $class="";
$class= ( ($i % 2) == 0 ) ? "odd":"even";
?>
  <tr <?php echo "class=\"$class\""; ?> >
<td>
<?php
$letter=($this->content[$i]['letter']==-1)?" aucun lettrage ":strtoupper(base_convert($this->content[$i]['letter'],10,36));
$js="this.gDossier=".dossier::id().
  ";this.j_id=".$this->content[$i]['j_id'].
  ";this.obj_type='".$this->object_type."'".
  ";dsp_letter(this)";

?>
<A class="detail" style="text-decoration: underline" href="javascript:void(0)" onclick="<?php echo $js?>"><?php echo $letter?>
<?php if ( $this->content[$i]['letter_diff'] != 0) echo $g_failed;	?>
	</A>
</td>
<td> <?php echo   smaller_date($this->content[$i]['j_date_fmt'])?> </td>
<td> <?php echo $this->content[$i]['jr_pj_number']?> </td>

<?php
$r=sprintf('<A class="detail" style="text-decoration:underline"  href="javascript:void(0)" onclick="viewOperation(\'%s\',\'%s\')" >%s</A>',
	     $this->content[$i]['jr_id'], $gDossier, $this->content[$i]['jr_internal']);
?>
  <td> <?php echo $r?> </td>
  <td> <?php echo h($this->content[$i]['jr_comment'])?> </td>
  <?php if ($this->content[$i]['j_debit']=='t') : ?>
  <td style="text-align:right"> <?php echo nb($this->content[$i]['j_montant'])?> </td>
  <td></td>
  <?php else : ?>
  <td></td>
  <td style="text-align:right"> <?php echo nb($this->content[$i]['j_montant'])?> </td>
  <?php endif ?>
<td style="text-align:center">
<?php
    // Rapprochement
    $rec=new Acc_Reconciliation($this->db);
    $rec->set_jr_id($this->content[$i]['jr_id']);
    $a=$rec->get();
    if ( $a != null ) {
      foreach ($a as $key => $element)
      {
	$operation=new Acc_Operation($this->db);
	$operation->jr_id=$element;
	$l_amount=$this->db->get_value("select jr_montant from jrn ".
					 " where jr_id=$element");
	echo "<A class=\"detail\"  href=\"javascript:void(0)\" onclick=\"viewOperation('".$element."',".$gDossier.")\" > ".$operation->get_internal()." [ ".nb($l_amount)." &euro; ]</A>";
      }//for
    }// if ( $a != null ) {
// compute amount
$amount_deb+=($this->content[$i]['j_debit']=='t')?$this->content[$i]['j_montant']:0;
$amount_cred+=($this->content[$i]['j_debit']=='f')?$this->content[$i]['j_montant']:0;

?>
</td>
</tr>

<?php
    endfor;
?>
</table>
<h2 class="info2" style="margin:0 0"> <?php echo _("Solde débit")?>  : <?php echo nb($amount_deb);?></h2>
<h2 class="info2"  style="margin:0 0"> <?php echo _("Solde crédit")?> : <?php echo nb($amount_cred);?></h2>
  <?php 
bcscale(2);
  $solde=bcsub($amount_deb,$amount_cred);
if ( $solde > 0 ) :
?>
  <h2 class="info2"  style="margin:0 0"> <?php echo _("Solde débiteur")?>       : <?php echo nb($solde)?></h2>
<?php else : ?>
     <h2 class="info2"  style="margin:0 0"> <?php echo _("Solde créditeur")?>       : <?php echo nb(abs($solde))?></h2>
<?php endif; ?>
