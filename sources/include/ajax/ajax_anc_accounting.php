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

require_once NOALYSS_INCLUDE."/class/anc_account_table.class.php";
/**
 * @file
 * @brief Insert , update delete anc accounting
 */
$http=new HttpInput();
$cn=Dossier::connect();

$action=$http->request("action");
$p_id=$http->request("p_id", "number");
$ctl_id=$http->request("ctl");
$pa_id=$http->request("pa_id");

$anc=new Poste_analytique_SQL($cn, $p_id);
$anc->pa_id=$pa_id;
$accounting=new Anc_Account_Table($anc);
$accounting->set_object_name($ctl_id);
$accounting->set_callback("ajax_misc.php");
$accounting->add_json_param("op", "anc_accounting");

if ($action=="input")
{

    $accounting->send_header();
    echo $accounting->ajax_input()->saveXML();
}
elseif ($action=="save")
{
    $accounting->send_header();
    echo $accounting->ajax_save()->saveXML();
}
elseif ($action=="delete")
{
    $accounting->send_header();
    echo $accounting->ajax_delete()->saveXML();
}