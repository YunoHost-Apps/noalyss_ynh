<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><TABLE BORDER="0" CELLSPACING="0">
<TR>
<TD><?php echo _('Catégorie de fiche')?> </TD>
<TD><INPUT TYPE="INPUT" class="input_text" NAME="nom_mod"></TD>
</TR>
<tr>
	<td style="vertical-align: text-top">
		Description
	</td>
	<td>
		<?php echo $fd_description->input(); ?>
	</td>
</tr>
<TR>
   <TD> <?php echo _('Classe de base')?> </TD>
<TD><?php echo $f_class_base?> </TD>
<td><span id="class_base_label"></span></td>
</TR>
<TR>
<TD colspan='2'> <INPUT TYPE="CHECKBOX" NAME="create" CHECKED><?php echo _("Création automatique du poste comptable uniquement s'il n'y a qu'un seul poste")?></TD>
</TR>
</table>
<h2>Modèles de catégorie</h2>
<ul>
<?php
  if ( sizeof($ref)  ) {
    foreach ($ref as $i=>$v) { ?>
    <li style="list-style-type: none">
<?php echo $iradio->input("FICHE_REF",$v['frd_id']);
   echo $v['frd_text'];
   if ( sizeof ($v['frd_class_base']) != 0 )
	   echo "&nbsp;&nbsp<I>Class base = ".$v['frd_class_base']."</I>";

    }?>
    </li>
  <?php }
?>
</UL>