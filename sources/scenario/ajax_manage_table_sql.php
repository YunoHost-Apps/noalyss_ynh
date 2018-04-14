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
if ( ! defined ("AJAX_TEST")) {
     echo "Can not be called directly but via AJAX";
     return;
}
/**
 * @file
 * @brief Test the ajax call from test_manage_table_sql.php
 */
$http=new HttpInput();
try {
$table=$http->request('table');
$action=$http->request('action');
$p_id=$http->request('p_id', "number");
$ctl_id=$http->request('ctl');
} catch(Exception $e) {
   
    echo $e->getMessage();
}
require_once NOALYSS_INCLUDE."/lib/manage_table_sql.class.php";
require_once NOALYSS_INCLUDE."/database/acc_plan_sql.class.php";



if ($action=="input")
{
    $obj=new Acc_Plan_SQL($cn);
    $obj->set_limit_fiche_qcode(5);
    $obj->set_pk_value($p_id);
    $obj->load();
    $manage_table=new Manage_Table_SQL($obj);
    $manage_table->add_json_param("TestAjaxFile",
        NOALYSS_HOME."/../scenario/ajax_manage_table_sql.php");

    $manage_table->set_object_name($ctl_id);
    $manage_table->set_col_label('pcm_val', "Poste");
    $manage_table->set_col_label('parent_accounting', "Dépend");
    $manage_table->set_col_label('pcm_lib', "Libellé");
    $manage_table->set_col_label('pcm_type',
            "Type de menu".Icon_Action::infobulle(33));
    header('Content-type: text/xml; charset=UTF-8');
    echo $manage_table->ajax_input()->saveXML();
    return;
} elseif ($action=="save")
{
$obj=new Acc_Plan_SQL($cn);
    $obj->set_limit_fiche_qcode(5);
    $obj->set_pk_value($p_id);
    $obj->load();
    $manage_table=new Manage_Table_SQL($obj);
    $manage_table->set_object_name($ctl_id);
    $manage_table->add_json_param("TestAjaxFile",
        NOALYSS_HOME."/../scenario/ajax_manage_table_sql.php");
     header('Content-type: text/xml; charset=UTF-8');
    echo $manage_table->ajax_save()->saveXML();
    return;
}