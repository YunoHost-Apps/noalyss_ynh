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

/*!\file
 * \brief manage all the export to CSV or PDF
 *   act can be
 *
 */
define ('ALLOWED',1);
require_once '../include/constant.php';
global $g_user,$cn,$g_parameter;
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$gDossier=dossier::id();
$cn=Dossier::connect();
mb_internal_encoding("UTF-8");
$g_user=new User($cn);
$g_user->Check();
$action=$g_user->check_dossier($gDossier);
set_language();
$hi=new HttpInput();
$action=$hi->get("act");

if ( $action=='X'  || $g_user->check_print($action)==0 )
  {
    echo alert(_('Accès interdit'));
    redirect("do.php?".dossier::get());
    exit();
  }
// get file and execute it

 $prfile=$cn->get_value("select me_file from menu_ref where me_code=$1",array($action));
 if ( $prfile == "") {
     die (_('Export impossible'));
 }
 require_once NOALYSS_INCLUDE."/export/$prfile";
 ?>