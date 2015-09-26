<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<?php 
$html=HtmlInput::title_box(_("Ajout d'une catégorie ").$msg, $ctl);
?>

<div class="content">
<form id="newcat" name="newcat" method="get" onsubmit="this.ipopup='<?php echo $ipopup?>';save_card_category(this);return false;">
<?php 
 echo HtmlInput::get_to_hidden(array('gDossier','cat'));
 ?>

	<TABLE BORDER="0" CELLSPACING="0">
<TR>
   <TD><?php echo _('Nom de la catégorie de fiche')?> </TD>
<TD><?php echo $nom_mod->input()?></TD>
</TR>
<TR>
   <TD> <?php echo _('Classe de base')?> </TD>
<TD>
<?php echo $str_poste?>
</TD>
<td><span id="class_base_label"></span></td>
</TR>
<tr>
    <td> <?php echo _('Description')?> </td>
    <td> <input type="text" class="input_text" name="fd_description" style="width: 100%"></td>
</tr>
<TR>
   <TD> <INPUT TYPE="CHECKBOX" NAME="create" UNCHECKED><?php echo _('Création automatique du poste comptable')?></TD>
</TR>
</TABLE>
<p class="info">
   <?php echo _('Si vous utilisez la création automatique de poste, chaque nouvelle fiche de cette catégorie aura son propre poste comptable. Ce poste comptable sera la classe de base augmenté de 1.')?>
</p>
<p class="info">
   <?php echo _('Si vous n\'utilisez pas la création automatique, toutes les nouvelles fiches auront par défaut le même poste comptable. Ce poste comptable par défaut est la classe de base.')?>
</p>
<p class="info">
<?php echo _(' A moins qu\'en créant la fiche, vous forcez un autre poste comptable')?>
</p>

<p>
<?php echo $submit?> <?php echo HtmlInput::button_close($ipopup)?>
</p>
</form>
</div>