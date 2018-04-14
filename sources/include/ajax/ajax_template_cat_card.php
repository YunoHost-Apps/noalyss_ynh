<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Manage the template of category of card
 */

require_once NOALYSS_INCLUDE."/class/template_card_category.class.php";
/**
 * ajax_template_cat_card add security , accessible only for CFGCARDCAT
 */
if ( $g_user->check_module ("CFGCARDCAT")==0)
{
    return;
}

$http=new HttpInput();
$action=$http->request("action");
$p_id=$http->request("p_id");
$ctl=$http->request("ctl");

$cat_sql=new Fiche_Def_Ref_SQL($cn, $p_id);
$cat=new Template_Card_Category($cat_sql);
$cat->set_callback("ajax_misc.php");
$cat->add_json_param("gDossier", Dossier::id());
$cat->add_json_param("op", "template_cat_card");
$cat->set_object_name($ctl);

switch ($action)
{
    case "input":
        // Display a box with the data
        header('Content-type: text/xml; charset=UTF-8');
        echo $cat->ajax_input()->saveXML();
        return;
        break;
    case "save":
        header('Content-type: text/xml; charset=UTF-8');
        echo $cat->ajax_save()->saveXML();
        if ( $p_id == -1 )
            $cat->add_mandatory_attr();
        return;
        break;
    case "delete":
        header('Content-type: text/xml; charset=UTF-8');
        echo $cat->ajax_delete()->saveXML();
        return;
        break;
    default:
        break;
}
