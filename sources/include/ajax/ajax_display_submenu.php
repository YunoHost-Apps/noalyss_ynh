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
 * @file
 * @brief call from ajax : display submenu
 * Security : only user with the menu CFGPRO
 * display the submenu of a menu or a module
 * It expects 2 parameters  = p_profile (profile.p_id) and the dep (menu_ref.me_code)
 */
// require_once '.php';
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

// Security 
if ( $g_user->check_module('CFGPRO') == 0 ) die();

require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

ob_start();
try
{
    $module=$http->get("dep");
    $p_level=$http->get("p_level", "number",0);
    $p_id=$http->get('p_profile',"number");

}
catch (Exception $exc)
{
    error_log($exc->getTraceAsString());
    return;
}

require_once NOALYSS_INCLUDE.'/class/profile_menu.class.php';
$p_level++;
$profile=new Profile_Menu($cn);
$profile->p_id=$p_id;
$profile->display_module_menu($module,$p_level);

?>        