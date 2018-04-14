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

if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Manange Accounting
 * @see Acc_Plan_SQL
 */
$http=new HttpInput();
try {
    $table=$http->request('table');
    $action=$http->request('action');
    $p_id=$http->request('p_id', "number");
    $ctl_id=$http->request('ctl');
   
} catch(Exception $e) {
    echo $e->getMessage();
    return;
}
if  ( $g_user->check_module("CFGPCMN") == 0) die();

require_once NOALYSS_INCLUDE."/lib/manage_table_sql.class.php";
require_once NOALYSS_INCLUDE."/class/acc_plan_mtable.class.php";

$obj=new Acc_Plan_SQL($cn);
$obj->set_limit_fiche_qcode(5);
$obj->set_pk_value($p_id);
$obj->load();
$manage_table=new Acc_Plan_MTable($obj);
$manage_table->add_json_param("op","accounting");
$manage_table->set_object_name($ctl_id);
$manage_table->set_callback("ajax_misc.php");
if ($action=="input")
{
    header('Content-type: text/xml; charset=UTF-8');
    echo $manage_table->ajax_input()->saveXML();
    return;
}
elseif ($action == "save") 
{
    $xml=$manage_table->ajax_save();
     header('Content-type: text/xml; charset=UTF-8');
     echo $xml->saveXML();
}
elseif ($action == "delete") 
{
    $xml=$manage_table->ajax_delete();
     header('Content-type: text/xml; charset=UTF-8');
     echo $xml->saveXML();
}