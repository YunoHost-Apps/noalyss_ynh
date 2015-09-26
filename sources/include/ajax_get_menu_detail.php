<?php

/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief
 *
 */
// retrieve data
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
$profile=$cn->get_value("select p_id from profile_menu where pm_id=$1",array($pm_id));
$a_value=$cn->make_array("select me_code,me_code||' '||me_menu||' '||coalesce(me_description,'') from menu_ref",0);

$array=$cn->get_array("select p_id,pm_id,me_code,me_code_dep,p_order,p_type_display,pm_default
	from profile_menu
	where pm_id=$1",array($pm_id));
if ( empty($array)) {
		alert("Code invalide");
		exit();
}


echo HtmlInput::title_box($array[0]['me_code'],'divdm'.$pm_id);

$me_code=new ISelect('me_code');
$me_code->value=$a_value;
$me_code->selected=$array[0]['me_code'];

$p_order=new Inum('p_order',$array[0]['p_order']);
$pm_default=new ICheckBox('pm_default','1');
$pm_default->set_check($array[0]['pm_default']);

?>
<form method="POST" id="ajax_get_menu_detail_frm" onsubmit="return confirm_box(this,'<?php echo _("Vous confirmez")?> ?')">
	<?php echo HtmlInput::hidden('pm_id',$array[0]['pm_id'])?>
	<?php echo HtmlInput::hidden('p_id',$array[0]['p_id'])?>
	<?php echo HtmlInput::hidden('tab',"profile_menu_div")?>
	<?php echo HtmlInput::hidden('mod',"1")?>
<table>
<tr>
	<td><?php echo _("Code");?></td>
	<td><?php echo $me_code->input()?></td>
</tr>
<?php 
if ($array[0]['p_type_display']!='P'):
?>
<tr>
	<td><?php echo _("Ordre d'apparition");?></td>
	<td><?php echo $p_order->input()?></td>
</tr>
<tr>
	<td><?php echo _("Menu par défaut");?></td>
	<td><?php echo $pm_default->input()?></td>
</tr>
<?php endif;?>
</table>
<?php 
echo HtmlInput::submit('modbt',_("Valider"));
echo '</form>';


?>
