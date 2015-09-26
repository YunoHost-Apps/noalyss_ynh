<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php
echo Dossier::hidden();
echo HtmlInput::hidden('t_id',$data->t_id);
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
$uos=new Tool_Uos('tag');
echo $uos->hidden();
$t_tag=new IText('t_tag',$data->t_tag);
$t_description=new ITextarea('t_description',$data->t_description);
$t_description->style=' class="itextarea" style="height:5em;vertical-align: top;"';
?>
<p>
   <?php echo _("Etiquette (tag)")?> : <?php echo $t_tag->input(); ?>
</p>
<p>
<?php echo _("Description")?> : <?php echo $t_description->input(); ?>
</p>
<?php
// If exist you can remove it
if ( $data->t_id != '-1') : 
?>
<p><?php echo _("Cochez pour cette case pour effacer ce tag")?><input type="checkbox" name="remove">
</p>

<?php
endif;
?>