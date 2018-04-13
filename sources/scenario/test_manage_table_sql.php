<?php

/*
 *   This file is part of PhpCompta.
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
 ini_set('disable_functions', 'exit,die,header');
 //@description:Test the class manage_table_sql and javascript
 $_GET=array (
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
$_REQUEST=array_merge($_GET,$_POST);
require_once NOALYSS_INCLUDE."/database/acc_plan_sql.class.php";
require_once NOALYSS_INCLUDE."/lib/manage_table_sql.class.php";

$jrn=new Acc_Plan_SQL($cn);

$manage_table=new Manage_Table_SQL($jrn);
$manage_table->set_callback("ajax_test.php");

echo $manage_table->get_json();
echo " Function add_json_param : add param TestAjaxFile";
$manage_table->add_json_param("TestAjaxFile",NOALYSS_HOME."/../scenario/ajax_manage_table_sql.php");
echo " <br>";
$r =json_decode($manage_table->get_json(),TRUE);
if ( isset($r['TestAjaxFile']) && $r['TestAjaxFile'] == NOALYSS_HOME."/../scenario/ajax_manage_table_sql.php") { echo "$g_succeed ok <br>";} else {echo "$g_failed not ok";}
echo " <br>";


$manage_table->create_js_script();

// Test the column header
$manage_table->set_col_label('pcm_val', "Poste");
$manage_table->set_col_label('parent_accounting', "Dépend");
$manage_table->set_col_label('pcm_lib', "Libellé");
$manage_table->set_col_label('pcm_type', "Type de menu".Icon_Action::infobulle(33));
$manage_table->set_sort_column("pcm_lib");
// Change visible property
function test_visible_update(Manage_Table_SQL $p_manage_table,$p_property,$p_visible,$p_update) {
    global $g_failed,$g_succeed;
    // test visibility
    $p_manage_table->set_property_visible($p_property, $p_visible);
    
    echo "$p_property VISIBLE ($p_visible)" .$p_manage_table->get_property_visible($p_property);
    if (  $p_manage_table->get_property_visible($p_property) == $p_visible) echo "$g_succeed OK"; else echo " $g_failed FAIL";echo "<br>";
    
    // test update
    echo "$p_property UPDATE ($p_update)" .$p_manage_table->get_property_updatable($p_property);
    $p_manage_table->set_property_updatable($p_property, $p_update);
    if (  $p_manage_table->get_property_updatable($p_property) == $p_update) echo "$g_succeed OK"; else echo "$g_failed FAIL";echo "<br>";
}

test_visible_update($manage_table, "pcm_type", FALSE, FALSE);
test_visible_update($manage_table, "pcm_type", FALSE, TRUE);
test_visible_update($manage_table, "pcm_type", TRUE, FALSE);
test_visible_update($manage_table, "pcm_type", TRUE, TRUE);
test_visible_update($manage_table, "parent_accounting", FALSE, FALSE);
test_visible_update($manage_table, "parent_accounting", FALSE, TRUE);
test_visible_update($manage_table, "parent_accounting", TRUE, FALSE);
test_visible_update($manage_table, "parent_accounting", TRUE, TRUE);
echo "<h1>"."Icon MODIFY place"."</h1>";
echo "<h2>"." Mod left"."</h2>";
$manage_table->set_icon_mod("left");
$manage_table->display_table("where pcm_val::text >= '400' order by pcm_val::text limit 10");
return;

echo "<h2>"." Mod right"."</h2>";
$manage_table->set_icon_mod("right");
$manage_table->display_table("where pcm_val::text >= '400'  order by pcm_val::text limit 10");

echo "<h2>"." Mod first"."</h2>";
$manage_table->set_icon_mod("first");
$manage_table->display_table("where pcm_val::text >= '400'  order by pcm_val::text limit 10");

echo "<h1>"."Icon DELETE place"."</h1>";
echo "<h2>"." Delete left"."</h2>";
$manage_table->set_icon_del("left");
$manage_table->display_table("where pcm_val::text >= '400'  order by pcm_val::text limit 10");

echo "<h2>"." Delete right"."</h2>";
$manage_table->set_icon_del("right");
$manage_table->display_table("where pcm_val::text >= '400'  order by pcm_val::text limit 10");


 ?>
