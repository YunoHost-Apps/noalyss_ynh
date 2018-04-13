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

// Copyright Author Dany De Bontridder dany@alchimerys.be
  /**
   *\file 
   *\brief ajax answer to update or change name or description of an analytic plan
   */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE . "/lib/inplace_edit.class.php";

if ( $g_user->check_module("PLANANC ") ) die("forbidden");

$input = $http->request("input");
$action = $http->request("ieaction", "string", "display");
$pa_id=$http->post("id","number");
$answer = Inplace_Edit::build($input);
$answer->add_json_param("gDossier", Dossier::id());
$answer->set_callback("ajax_misc.php");
$answer->add_json_param("action","anc_updatedescription");
$answer->add_json_param("op","anc_updatedescription");
$answer->add_json_param("id",$pa_id);
    
$input=$answer->get_input();
if ($action=="display") {
    echo $answer->ajax_input() ;
}
if ($action=="ok") {
    $value=$http->post("value");
    if ( $input->name=="pa_name" && trim($input->value) == "") 
    {
        echo _("Le nom ne peut être vide"),$answer->ajax_input();
    }else {
        if ($input->name=="pa_name") {
            $cn->exec_sql(" update plan_analytique set pa_name=$1 where pa_id=$2",array($value,$pa_id));
        }
        if ($input->name=="pa_description") {
            if ( trim($value ) == "" ) $value=NULL;
            $cn->exec_sql(" update plan_analytique set pa_description=$1 where pa_id=$2",array($value,$pa_id));
            if ( trim($value ) == NULL ) $value=_("Aucune description");
        }
        $answer->set_value($value);
        echo $answer->value();
    }
}
if ($action=="cancel") {
    echo $answer->value();
}