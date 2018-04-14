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

// Copyright Author Dany De Bontridder dany@alchimerys.be 2017

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
//@description:Test the class Inplace_Edit , ajax and javascript


require_once NOALYSS_INCLUDE . '/lib/itext.class.php';
require_once NOALYSS_INCLUDE . '/lib/inum.class.php';
require_once NOALYSS_INCLUDE . '/lib/inplace_edit.class.php';
if (!isset($_REQUEST["TestAjaxFile"])) {
    echo h1(_("Test Inplace_Edit"));
    /***********************************************
     * If TestAjaxFile is not set it is not a ajax call
     *********************************************** */
    $hello = new IText("hello","Click me");
    $hello->id = "hello_ajax";
   
    $ajax_hello = new Inplace_Edit($hello);
    $ajax_hello->set_callback("ajax_test.php");
    $ajax_hello->add_json_param("TestAjaxFile", __FILE__) ;
    $ajax_hello->add_json_param("gDossier", Dossier::id());
    echo "TEXT #".$ajax_hello->input()."#";
    
    $select = new ISelect ("ajax_select");
    $select->id=uniqid("sel");
    $select->value=[ 
        ["value"=>1,"label"=>"ONE"],
        ["value"=>2,"label"=>"TWO"],
        ["value"=>3,"label"=>"THREE"],
        ];
    $select->selected="2";
    $ajax_select = new Inplace_Edit($select);
    $ajax_select->set_callback("ajax_test.php");
    $ajax_select->add_json_param("TestAjaxFile", __FILE__) ;
    $ajax_select->add_json_param("gDossier", Dossier::id());
    echo "SELECT #".$ajax_select->input()."#";
    
    $date=new IDate("today");
    $date->value=date("d.m.Y");
    $date->id=uniqid("date");
     $ajax_date = new Inplace_Edit($date);
    $ajax_date->set_callback("ajax_test.php");
    $ajax_date->add_json_param("TestAjaxFile", __FILE__) ;
    $ajax_date->add_json_param("gDossier", Dossier::id());
    echo "DATE #".$ajax_date->input()."#";
} else {
    /*************************************************
     * Ajax answer
     **************************************************/
    $input=$http->request("input");
    $action=$http->request("ieaction","string","display");
    if ($action=="display") {
        $ajax_hello = Inplace_Edit::build($input);
        $ajax_hello->set_callback("ajax_test.php");
        $ajax_hello->add_json_param("TestAjaxFile", __FILE__);
        $ajax_hello->add_json_param("gDossier", Dossier::id());
        echo " [  ".$ajax_hello->ajax_input()." ] ";
    }
    if ( $action == "ok") {
        $ajax_hello = Inplace_Edit::build($input);
        $ajax_hello->add_json_param("TestAjaxFile", __FILE__);
        $ajax_hello->add_json_param("gDossier", Dossier::id());
        $ajax_hello->set_value($http->request("value"));
        $ajax_hello->set_callback("ajax_test.php");
        echo $ajax_hello->value();
    }
    if ( $action == "cancel") {
        $ajax_hello = Inplace_Edit::build($input);
        $ajax_hello->add_json_param("TestAjaxFile", __FILE__);
        $ajax_hello->add_json_param("gDossier", Dossier::id());
        $ajax_hello->set_callback("ajax_test.php");
        echo $ajax_hello->value();
    }
}