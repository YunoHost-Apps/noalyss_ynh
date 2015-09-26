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

// Copyright 2015 Author Dany De Bontridder danydb@aevalys.eu
/** 
 * @brief call from ajax : display submenu
 * Security : only user with the menu CFGPRO
 * display the submenu of a menu or a module
 * It expects 2 parameters  = p_profile (profile.p_id) and the dep (menu_ref.me_code)
 */
// require_once '.php';
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

// Security 
if ( $g_user->check_module('CFGPRO') == 0 ) die();

// Check parameter
$module=HtmlInput::default_value_get("dep", "");
$p_level=HtmlInput::default_value_get("p_level", 0);
$p_id=HtmlInput::default_value_get('p_profile',-1);

if ($module == ""
        || $p_id == -1 
        || isNumber($p_id) == 0
        || isNumber($p_level) == 0
        )
{
    echo _('Paramètre invalide');
    return;
}

require_once NOALYSS_INCLUDE.'/class_profile_menu.php';
$p_level++;
$profile=new Profile_Menu($cn);
$profile->p_id=$p_id;
$profile->display_module_menu($module,$p_level);

////////////////////////////////////////////////////////////////////////////////
// EXAMPLE
////////////////////////////////////////////////////////////////////////////////
/*
if ($ac == 'save') // operation
{
    
    $cn=new Database(dossier::id());
    $todo=new Todo_List($cn);
     $id=HtmlInput::default_value_get("id", 0); // get variable
    $todo->set_parameter("id",$id);
    if ($id <> 0 ) { $todo->load(); }
    else
    {
        $todo->set_parameter("owner", $_SESSION['g_user']);
    }
    
    $todo->set_parameter("date", HtmlInput::default_value_get("p_date_todo", ""));
    $todo->set_parameter("title", HtmlInput::default_value_get("p_title", ""));
    $todo->set_parameter("desc", HtmlInput::default_value_get("p_desc", ""));
    $todo->set_is_public(HtmlInput::default_value_get("p_public", "N"));
    
    if ( $todo->get_parameter('owner') == $_SESSION['g_user'] ) $todo->save();
    $todo->load();
 //----------------------------------------------------------------
 // Answer in XML
     header('Content-type: text/xml; charset=UTF-8');
    $dom=new DOMDocument('1.0','UTF-8');
    $tl_id=$dom->createElement('tl_id',$todo->get_parameter('id'));
    $tl_content=$dom->createElement('row',$todo->display_row('class="odd"','N'));
     $root=$dom->createElement("root");
     $todo_class=$todo->get_class();
     $todo_class=($todo_class=="")?' odd ':$todo_class;
     $class=$dom->createElement("style",$todo_class);
    
    $root->appendChild($tl_id);
    $root->appendChild($tl_content);
    $root->appendChild($class);
    $dom->appendChild($root);
   
    echo $dom->saveXML();
    exit();
}
  */
?>        