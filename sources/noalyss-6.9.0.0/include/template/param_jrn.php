<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><TABLE style="width: auto">
<TR>

		<TD><?php echo _('Nom journal')?> </TD>
		<TD> <INPUT TYPE="text" class="input_text" NAME="p_jrn_name" VALUE="<?php	echo $name;	?>"></TD>
                <td></td>
</TR>
<?php
if ( $new || $type=='ODS' ):
?>
<TR id="type_ods">
<td><?php echo _('Postes utilisables journal (débit/crédit) ')?>
</TD>
<td>
<?php echo $search;?>
</TD>
<TD CLASS="notice">
<?php echo _("Uniquement pour les journaux d'Opérations Diverses, les valeurs sont séparées par des espaces, on peut aussi
	utiliser le * pour indiquer 'tous les postes qui en dépendent' exemple: 4*")?>
</TD>
</TR>
<?php
endif;
?>
<?php
if ( $new|| $type=='FIN') {
?>
<tr id="type_fin">
<td>
    <?php echo _('Numérotation de chaque opération')?>
</td>
<td>
    <?php echo $num_op->input();?>
</td>
</tr>
<tr id="type_fin2">
<TD>
<?php echo _('Compte en banque')?>
</td>
<TD>
<?php
$card=new ICard();
$card->name='bank';
$card->extra=$cn->make_list('select fd_id from fiche_def where frd_id=4');
$card->set_dblclick("fill_ipopcard(this);");
$card->set_function('fill_data');
$card->set_attribute('ipopup','ipop_card');
$list=$cn->make_list('select fd_id from fiche_def where frd_id=4');
$card->set_attribute('typecard',$list);

$card->value=$qcode_bank;
echo $card->search();
echo $card->input();
echo $str_add_button;
?>
</td>
<td class="notice">
<?php echo _("Obligatoire pour les journaux FIN : donner ici la fiche de la banque utilisée")?>
</td>
<?php
}
?>
</TR>
<tr>
	<td>Minimum de lignes à afficher</td>
<td><?php echo $min_row->input()?></td>
</tr>
<tr>
<td><INPUT TYPE="hidden" id="p_jrn_deb_max_line" NAME="p_jrn_deb_max_line" VALUE="10"></td>
</tr>
<tr><td><INPUT TYPE="hidden" id="p_ech_lib" NAME="p_ech_lib" VALUE="echeance"></td>
</tr>

<TR>
<TD><?php echo _('Type de journal')?> </TD>
<TD>
<?php echo $type;?>
</TD>
</TR>
<TR>
<TD><?php echo _('Préfixe code interne')?> </TD><TD>
<?php echo $code?> </TD>
</TR>
<TR>
<TD><?php echo _('Préfixe pièce justificative')?>
    <?php echo HtmlInput::infobulle(39);?>
</TD>
<TD>
<?php echo $pj_pref; ?>
</TD>

</TR>
<?php if ( $new == 0 ) : ?>
<TR>
<TD>
  <?php echo _('Dernière pièce numérotée')?>
  <?php echo HtmlInput::infobulle(40);?>
</TD>
<TD>
<?php echo $last_seq?>
</TD>
</TR>

<tr>
<TD><?php echo _('N° pièce justificative')?>
    <?php echo HtmlInput::infobulle(38);?>
</TD>
<TD>
    <?php echo $pj_seq; ?>
   
</TD>
</tr>
<?php endif; ?>
<tr>
    <td style="width: 200px">
    <?php echo _('Description') ?>
    </TD>
    <td style="width: 500px">
     <?php echo $str_description; ?>   
    </td>
</tr>    
</TABLE>
<hr>
    <?php
    /////////////////// ACH //////////////////////////////////
    if ( $new ==1 || $type=='ACH' ) : 
        ?>
    <div id='ACH_div' >
    <h2 > Fiches </h2>
    <TABLE class="result" style="width:80%;margin-left:10%;">
        <tr>
            <th>
                Services, fournitures ou biens  achetés (D)
            </th>
            <th>
                Fournisseurs (C)
            </th>
        </tr>
    
        
    <?php
    // Show the fiche in deb section
    $Res=$cn->exec_sql("select fd_id,fd_label from fiche_def order by fd_label");
    $num=$cn->size();
    // default card for ACH
    if ($new == 1)
    {
        $rdeb=$default_deb_purchase;
        $rcred=$default_cred_purchase;
    }
    
    for ($i=0;$i<$num;$i++) {
      $res=$cn->fetch($i);
      $CHECKED=" unchecked";
      foreach ( $rdeb as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
            echo '<tr>';
      printf ('<TD> <INPUT TYPE="CHECKBOX" VALUE="%s" NAME="FICHEDEB[]" %s>%s</TD>',
              $res['fd_id'],$CHECKED,$res['fd_label']);
      $CHECKED=" unchecked";
      foreach ( $rcred as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
      printf ('<TD> <INPUT TYPE="CHECKBOX" VALUE="%s" NAME="FICHECRED[]" %s>%s</TD>',
              $res['fd_id'],$CHECKED,$res['fd_label']);
      echo '</TR>';
    }
    ?>
    </TABLE>
</div>
<?php /////////////////// ACH //////////////////////////////////
 endif; 
 ?>
<?php
    /////////////////// VEN //////////////////////////////////
    if ( $new ==1  || $type=='VEN' ) : 
        ?>
    <div id='VEN_div' >
    <h2> Fiches </h2>
    <TABLE class="result" style="width:80%;margin-left:10%;">
        
        <tr>
            <th>
                Clients (C)
            </th>
            <th>
                Services, fournitures ou biens  vendus (D)
            </th>
        </tr>
    
        
    <?php
    // Show the fiche in deb section
    $Res=$cn->exec_sql("select fd_id,fd_label from fiche_def order by fd_label");
    $num=$cn->size();
    // default card for VEN
    if ($new == 1)
    {
        $rdeb=$default_deb_sale;
        $rcred=$default_cred_sale;
    }

    for ($i=0;$i<$num;$i++) {
      $res=$cn->fetch($i);
      $CHECKED=" unchecked";
      foreach ( $rdeb as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
            echo '<tr>';
      printf ('<TD> <INPUT TYPE="CHECKBOX" VALUE="%s" NAME="FICHEDEB[]" %s>%s</TD>',
              $res['fd_id'],$CHECKED,$res['fd_label']);
      $CHECKED=" unchecked";
      foreach ( $rcred as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
      printf ('<TD> <INPUT TYPE="CHECKBOX" VALUE="%s" NAME="FICHECRED[]" %s>%s</TD>',
              $res['fd_id'],$CHECKED,$res['fd_label']);
      echo '</TR>';
    }
    ?>
    </TABLE>
</div>
<?php /////////////////// VEN //////////////////////////////////
 endif; 
 ?>
   <?php
    /////////////////// ODS //////////////////////////////////
    if ( $new ==1 || $type=='ODS' ) : 
        ?>
    <div id='ODS_div' >
    <h2> Fiches </h2>
   <TABLE class="result" style="width:60%;margin-left:20%;">
        <tr>
            <th>
                Fiches utilisables (D/C)
            </th>
           
        </tr>
    
        
    <?php
    // Show the fiche in deb section
    $Res=$cn->exec_sql("select fd_id,fd_label from fiche_def order by fd_label");
    $num=$cn->size();
    // default card for ODS
    if ($new == 1)
    {
        $rdeb=$default_ods;
    }
    for ($i=0;$i<$num;$i++) {
      $res=$cn->fetch($i);
      $CHECKED=" unchecked";
      foreach ( $rdeb as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
            echo '<tr>';
      printf ('<TD> <INPUT TYPE="CHECKBOX" VALUE="%s" NAME="FICHEDEB[]" %s>%s</TD>',
              $res['fd_id'],$CHECKED,$res['fd_label']);
      $CHECKED=" unchecked";
      foreach ( $rcred as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
      echo '</TR>';
    }
    ?>
    </TABLE>
</div>
<?php /////////////////// ODS //////////////////////////////////
 endif; 
 ?>
   <?php
    /////////////////// FIN //////////////////////////////////
    if ( $new ==1 || $type=='FIN' ) : 
        ?>
    <div id='FIN_div' >
    <h2> Fiches </h2>
     <TABLE class="result" style="width:60%;margin-left:20%;">
        <tr>
            <th>
                Tiers (D/C)
            </th>
           
        </tr>
    
        
    <?php
    // Show the fiche in deb section
    $Res=$cn->exec_sql("select fd_id,fd_label from fiche_def order by fd_label");
    $num=$cn->size();
    // default card for ACH
    if ($new == 1)
    {
        $rdeb=$default_fin;
    }
    for ($i=0;$i<$num;$i++) {
      $res=$cn->fetch($i);
      $CHECKED=" unchecked";
      foreach ( $rdeb as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
            echo '<tr>';
      printf ('<TD> <INPUT TYPE="CHECKBOX" VALUE="%s" NAME="FICHEDEB[]" %s>%s</TD>',
              $res['fd_id'],$CHECKED,$res['fd_label']);
      $CHECKED=" unchecked";
      foreach ( $rcred as $element) {
        if ( $element == $res['fd_id'] ) {
          $CHECKED="CHECKED";
          break;
        }
      }
      echo '</TR>';
    }
    ?>
    </TABLE>
</div>
<?php /////////////////// FIN //////////////////////////////////
 endif; 
 ?>
<?php if ( $new == 1 ) : ?>
<script>
    var a_div=Array('VEN_div','ODS_div','ACH_div','FIN_div');
    function hide_ledger()
    {
        for (var i=0;i<a_div.length;i++)
        {
            $(a_div[i]).style.display='none';
        }
    }
    function hide_row()
    {
        $('type_ods').style.display='none';
        $('type_fin').style.display='none';
        $('type_fin2').style.display='none';
    }
   function show_ledger_div()
   {
       hide_ledger();
       var ch=$('p_jrn_type_select_id').options[$('p_jrn_type_select_id').selectedIndex].value;
       console.log(" div = "+ch);
       $(ch+'_div').style.display='block';
       switch (ch) {
           case 'FIN':
             hide_row();
             $('type_fin').style.display='table-row';
             $('type_fin2').style.display='table-row';         
             break;
           case 'ODS':
               hide_row();
               $('type_ods').style.display='table-row';
               break;
           default:
               hide_row();
       }
   }
    hide_ledger();
    hide_row();
    <?php
    if (isset ($previous_p_jrn_type)  ):
    ?>
      show_ledger_div();              
    <?php
    endif;
    ?>
</script>
<?php endif; ?>
