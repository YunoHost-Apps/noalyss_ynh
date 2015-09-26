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
 * @brief show the profile detail, included from ajax_misc.php
 * @see ajax_misc.php scripts.js profile.inc.php
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

// Security 
if ( $g_user->check_module('CFGPRO') == 0 ) die();

require_once NOALYSS_INCLUDE.'/class_profile_sql.php';
require_once NOALYSS_INCLUDE.'/class_profile_menu.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
$p_id=HtmlInput::default_value_request('p_id', -1);
$profile=new Profile_sql($cn,$p_id);
$gDossier=Dossier::id();
$add_impression=HtmlInput::button("add", _("Ajout Menu"),"onclick=\"add_menu({dossier:$gDossier,p_id:$p_id,type:'pr'})\"");
$call_tab=HtmlInput::default_value_post('tab', 'none');
$a_tab=array('profile_gen_div'=>'tabs','profile_menu_div'=>'tabs','profile_print_div'=>'tabs','profile_gestion_div'=>'tabs','profile_repo_div'=>'tabs');
$a_tab[$call_tab]='tabs_selected';
?>
<h1>Profil <?php echo $profile->p_name?></h1>
<?php
    echo HtmlInput::anchor(_('Retour'), "", " onclick = \" $('detail_profile').hide();$('list_profile').show(); \" ", 'class="line"');
?>
<?php if ($p_id > 0 ) : ?>
<ul class="tabs">
    
    <li class="<?php echo $a_tab['profile_gen_div']?>"><a href="javascript:void(0)"  onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';profile_show('profile_gen_div')"><?php echo _('Nom')?></a></li>
    <li class="<?php echo $a_tab['profile_menu_div']?>"><a href="javascript:void(0)"  onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';profile_show('profile_menu_div')"><?php echo _('Détail Menus')?></a></li>
    <li class="<?php echo $a_tab['profile_print_div']?>"><a href="javascript:void(0)" onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';profile_show('profile_print_div')"><?php echo _('Détail Impressions')?></a></li>
    <li class="<?php echo $a_tab['profile_gestion_div']?>"><a href="javascript:void(0)" style="" onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';profile_show('profile_gestion_div')"><?php echo _('Action Gestion')?> </a></li>
    <li class="<?php echo $a_tab['profile_repo_div']?>"><a href="javascript:void(0)"  onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';profile_show('profile_repo_div')"><?php echo _('Dépôts')?></a>&nbsp;
</ul>
<?php endif; ?>

<?php 
$id=HtmlInput::hidden('p_id',$profile->p_id);
$name=new IText("p_name",$profile->p_name);
$desc=new IText("p_desc",$profile->p_desc);
$with_calc=new ICheckBox("with_calc","t");
$with_calc->set_check($profile->with_calc);

$with_direct_form=new ICheckBox("with_direct_form","t");
$with_direct_form->set_check($profile->with_direct_form);

// If $p_id == -1 it is a new profile
if ( $p_id > 0 )
{
	echo '<div style="display:none" id="profile_gen_div">';
}
else
{
	echo '<div  class="myfieldset" id="profile_gen_div">';
}
echo '<form method="POST" id="profile_save_name_frm" onsubmit="return confirm_box(this,\'vous confirmez\')">';
echo HtmlInput::hidden('tab','profile_gen_div');
echo HtmlInput::hidden('p_id',$profile->p_id);
echo HtmlInput::hidden('save_name',1);
require_once("template/profile.php");
echo HtmlInput::submit("save_namebt",_("Modifier"));
echo '</form>';
if ($profile->p_id > 0)
{
	echo '<form method="POST" id="profile_clone_frm" onsubmit="return confirm_box(this,\''._("vous confirmez").'\')">';

	echo _('Vous pouvez aussi copier ce profil et puis le corriger');

	echo HtmlInput::hidden('p_id', $profile->p_id);
	echo HtmlInput::hidden('clone', 1);
	echo HtmlInput::submit("clonebt", "Copier");
	echo '</form>';

	echo '<form method="POST" id="delete_profile_frm" onsubmit="return confirm_box(this,\''._("vous confirmez").'\')">';

	echo _('Effacer ce profil');

	echo HtmlInput::hidden('p_id', $profile->p_id);
	echo HtmlInput::hidden('delete_profil', 1);
	echo HtmlInput::submit("delete_profil", _("Effacer ce profil"));
	echo '</form>';
        echo '</div>';
        echo '<div class="myfieldset"  style="display:none" id="profile_menu_div">';
	//Menu / Module /plugin in this profile
	echo "<h1 class=\"legend\">"._("Menu")."</h2>";
	$profile_menu = new Profile_Menu($cn);
        $profile_menu->p_id=$p_id;
	$profile_menu->display_profile_menu_detail();
        echo '</div>';
        echo '<div class="myfieldset"  style="display:none" id="profile_print_div">';
	echo "<h1 class=\"legend\">"._("Impression")."</h1>";
	$profile_menu->printing();
	echo $add_impression;
        echo '</div>';
        echo '<div class="myfieldset"  style="display:none" id="profile_gestion_div">';
	echo "<h1 class=\"legend\">".('Groupe gestion')."</h1>";
	$profile_menu->available_profile();
        echo '</div>';
        echo '<div class="myfieldset"  style="display:none" id="profile_repo_div">';
	echo "<h1 class=\"legend\">"._("Dépôt de stock accessible")."</h1>";
	$profile_menu->available_repository();
        echo '</div>';
        if ( isset ($_POST['tab']))
        {
            echo create_script("profile_show('".$_POST['tab']."');");
        }
}
else
{
        echo '</div>';
}
?>


