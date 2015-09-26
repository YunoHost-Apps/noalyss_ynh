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
 * @brief show the form for adding a menu. 
 * $type : 
 *     # me , it is a menu to add , so it could have dependencies
 *     # pr , menu for Printing : no dependency 
 * $p_level:
 *     # 0 it is a module : no dependency p_type = M , refresh the detail of menu
 *     # 1 it is a menu with possible submenu p_type = E, refresh the table with menu
 *     # 2 it is a menu with no submenu , p_type = E 
 * $dep  : is the profile_menu.pm_id of the parent menu
 *     
 * 
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

// Security 
if ($g_user->check_module('CFGPRO')==0)
    die();

$type=HtmlInput::default_value_get('type', 'XX');
$p_level=HtmlInput::default_value_get('p_level', 0);
$dep=HtmlInput::default_value_get('dep', 0);
if ($type=='XX')
{
    throw new Exception('invalid call');
    return;
}
// if type == menu the 
if ($type=='me')
{
    if ( isNumber($p_level)==0 ) throw new Exception('invalid call');
    
    if ($p_level==0)
    {
        // There is no dependency
        // Menu which can be added
        $ame_code=$cn->make_array("
select me_code,me_code||' '||coalesce(me_menu,'')||' '||coalesce(me_description,'')
	||'('|| case when me_type='SP' then 'Special'
		when me_type='PL' then 'Plugin'
		when me_type='ME' and me_file is null and me_javascript is null and me_url is null then 'Module - Menu principal'
		when me_type='ME' then 'Menu'
		else
		me_type
		end||')'
	from
	menu_ref
        where
        me_type<>'PR'
	order by 1
	");
    }
    elseif ($p_level==1)
    {
        // dependency is in dep
        // Menu which can be added
        $ame_code=$cn->make_array("
select me_code,me_code||' '||coalesce(me_menu,'')||' '||coalesce(me_description,'')
	||'('|| case when me_type='SP' then 'Special'
		when me_type='PL' then 'Plugin'
		when me_type='ME' and me_file is null and me_javascript is null and me_url is null then 'Module - Menu principal'
		when me_type='ME' then 'Menu'
		else
		me_type
		end||')'
	from
	menu_ref
        where
        me_type<>'PR'
	order by 1
	");
    }
    elseif ($p_level==2)
    {
        // menu can *NOT* have submenu
        // Menu which can be added
        $ame_code=$cn->make_array("
select me_code,me_code||' '||coalesce(me_menu,'')||' '||coalesce(me_description,'')
	||'('|| case when me_type='SP' then 'Special'
		when me_type='PL' then 'Plugin'
		when me_type='ME' and me_file is null and me_javascript is null and me_url is null then 'Module - Menu principal'
		when me_type='ME' then 'Menu'
		else
		me_type
		end||')'
	from
	menu_ref
        where
        me_type<>'PR' and
       (
          coalesce(me_file,'') <> '' or
          coalesce(me_url,'') <> '' or
          coalesce(me_javascript,'') <> ''
        )
	order by 1
	");
    }
    else
    {
        throw new Exception('LEVEL ERROR');
    }


    $p_order=new INum("p_order", "10");

    $me_code=new ISelect('me_code');
    $me_code->value=$ame_code;


    $pm_default=new ICheckBox('pm_default');
    echo HtmlInput::title_box(_("Nouveau"), $ctl);
    ?>
    <form method="POST" id="menu_new_frm" onsubmit="return confirm_box('menu_new_frm','<?php echo _('Vous confirmez'); ?> ?')">
        <?php
        echo HtmlInput::hidden('tab', 'profile_menu_div');
        ?>
        <?php echo HtmlInput::hidden('p_id', $p_id) ?>
        <?php echo HtmlInput::hidden('add_menu', 1) ?>
        <?php echo HtmlInput::hidden('p_level', $p_level) ?>
        <?php echo HtmlInput::hidden('type', $type) ?>
        <?php echo HtmlInput::hidden('dep', $dep) ?>
        <table>
            <tr>
                <td><?php echo _("Code") ?></td>
                <td><?php echo $me_code->input() ?></td>
            </tr>

            <tr>
                <td><?php echo _("Ordre d'apparition") ?></td>
                <td><?php echo $p_order->input() ?></td>
            </tr>
            <tr>
                <td><?php echo _("Menu par défaut") ?></td>
                <td><?php echo $pm_default->input() ?></td>
            </tr>

        </table>
        <?php
        echo HtmlInput::submit('add_menubt', _("Valider"));
        echo '</form>';
        return;
    }

// for printing menu (export CSV or PDF)
    if ($type=='pr')
    {

        $ame_code=$cn->make_array("
select me_code,me_code||' '||coalesce(me_menu,'')||' '||coalesce(me_description,'')
	from
	menu_ref
	where me_type='PR'
	and me_code not in (select me_code from profile_menu where p_id=$1)
	order by 1
	", 0, array($p_id));

        $me_code=new ISelect('me_code');
        $me_code->value=$ame_code;

        echo HtmlInput::title_box(_("Nouveau menu"), $ctl);
        if (count($ame_code)==0)
        {
            echo h2(_("Aucune impression disponible à ajouter"),
                    'class="notice"');
            return;
        }
        ?>
        <form method="POST" id="menu_new2_frm" onsubmit="return confirm_box('menu_new2_frm','<?php echo _('Vous confirmez ?') ?>">
            <?php
            echo HtmlInput::hidden('tab', 'profile_print_div');
            ?>
            <?php echo HtmlInput::hidden('p_id', $p_id) ?>
            <?php echo HtmlInput::hidden('p_order', 10) ?>
            <?php echo HtmlInput::hidden('me_code_dep', '') ?>
            <?php echo HtmlInput::hidden('p_type', 'PR') ?>
            <?php echo HtmlInput::hidden('add_impress', 1) ?>
            <table>
                <tr>
                    <td><?php echo _("Code") ?></td>
                    <td><?php echo $me_code->input() ?></td>
                </tr>

            </table>
            <?php
            echo HtmlInput::submit('add_impressbt', _("Valider"));
            echo '</form>';
            return;
        }
        ?>
