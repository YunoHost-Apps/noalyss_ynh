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
 *
 *\brief Plan Analytique
 *
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/anc_plan.class.php';
require_once NOALYSS_INCLUDE.'/class/anc_account_table.class.php';
require_once NOALYSS_INCLUDE.'/database/poste_analytique_sql.class.php';
require_once NOALYSS_INCLUDE.'/lib/inplace_edit.class.php';

$ret="";
$str_dossier=Dossier::get();

global $http;

$sa=$http->request("sa", "string", "anc_menu");
//---------------------------------------------------------------------------
// action
// Compute the redcontent div
//---------------------------------------------------------------------------
// show the form for adding a pa
if ($sa=="add_pa")
{
    $new=new Anc_Plan($cn);
    if ($new->isAppend()==true)
    {
        $ret.= '<div style="position:absolute;top:25%" id="anc_div_add" class="inner_box">';
        $ret.=HtmlInput::title_box(_('Nouveau plan'), 'anc_div_add', 'hide');
        $ret.= '<form method="post">';
        $ret.=dossier::hidden();
        $ret.= $new->form_new();
        $ret.= HtmlInput::hidden("sa", "pa_write");
        $ret.=HtmlInput::submit("submit", _("Enregistre"));
        $ret.=HtmlInput::button_hide("anc_div_add");
        $ret.= '</form>';
        $ret.= '</div>';
    }
    else
    {
        $ret.= '<div class="content">'.
                '<h2 class="notice">'.
                _("Maximum de plan analytique est atteint").
                "</h2></div>";
    }
    $sa="anc_menu";
}
// Add
if ($sa=="pa_write")
{
    $new=new Anc_Plan($cn);


    if ($new->isAppend()==false)
    {
        $ret.= '<h2 class="notice">'.
                _("Maximum de plan analytique est atteint").
                "</h2>";
    }
    else
    {
        $new=new Anc_Plan($cn);
        $new->name=$_POST['pa_name'];
        $new->description=$_POST['pa_description'];
        $new->add();
    }
    $sa="anc_menu";
}

// Update the PA
if ($sa=="pa_update")
{
    $pa_id=$http->get("pa_id","number");

    $new=new Anc_Plan($cn, $pa_id);
    $new->name=$_POST['pa_name'];
    $new->description=$_POST['pa_description'];
    $new->update();
    $sa="anc_menu";
}

/* delete pa */
if ($sa=="pa_delete")
{
    $pa_id=$http->get("pa_id","number");

    $delete=new Anc_Plan($cn, $pa_id);
    $delete->delete();
    $sa="anc_menu";
}
//--------------------------------------------------------------------------------------------------
// show the detail of an analytic axis (=plan)
// 
//--------------------------------------------------------------------------------------------------
if ($sa=="pa_detail")
{
    $pa_id=$http->get("pa_id","number");
    
    $new=new Anc_Plan($cn, $pa_id);
    $wSa=HtmlInput::hidden("sa", "pa_update");

    $new->get();

    $ret.= '<div class="content">';

    $ret.= $new->form();
    $ret.= $wSa;
    $ret.="<p>";
    $ret.=HtmlInput::button_anchor(_('Efface ce plan'), '', 'remove_analytic_plan',
                    'onclick="return confirm_box(\'remove_analytic_plan\',\'Effacer ?\',function () {window.location=\'do.php?ac='.$_REQUEST['ac'].'&pa_id='.$_GET['pa_id'].'&sa=pa_delete&'.$str_dossier.'\';})"',
                    'smallbutton');
    $ret.="</p>";
    //---------------------------------------------------------------------
    //  Detail now
    // Use Manage_Table
    //---------------------------------------------------------------------
    $count=0;

    $new=new Anc_Plan($cn, $pa_id);
    $new->get();
    $ret.='<div class="content">';
    $anc=new Poste_analytique_SQL($cn);
    $anc->pa_id=$pa_id;
    $accounting=new Anc_Account_Table($anc);
    $accounting->set_callback("ajax_misc.php");
    $accounting->add_json_param("op", "anc_accounting");
    $accounting->add_json_param("pa_id", $pa_id);
    $accounting->set_sort_column("po_name");
    ob_start();
    $accounting->display_table(" where pa_id = $1 order by po_name ",array($pa_id));
    $accounting->create_js_script();
    $ret.=ob_get_clean();
    $ret.= '</div>';
}

//---------------------------------------------------------------------------
// Show lmenu
//
//---------------------------------------------------------------------------
if ($sa=='anc_menu')
{

    $obj=new Anc_Plan($cn);
    $list=$obj->get_list();


    $ac=$http->request("ac");

    if (empty($list))
    {
        $url=http_build_query(array("sa"=>"add_pa","ac"=>$ac,
                "gDossier"=>Dossier::id()));
        echo '<div class="content">';
        echo '<TABLE class="vert_mtitle">';
        echo '<TR><TD class="first">';
        echo '<a href="?'.$url.'">'._("Ajout d'un plan comptable").'</a>';
        echo '</TD></TR>';
        echo '</TABLE>';

        echo '</div>';
        if (!isset($_REQUEST['sa']))
            echo '<div class="notice">'.
            _("Aucun plan analytique n'est défini").
            '</div>';
    }
    else
    {
         $url=http_build_query(array("sa"=>"add_pa","ac"=>$ac,
                "gDossier"=>Dossier::id()));
        echo '<div class="content">';

        echo '<table class="vert_mtitle">';
        if ($obj->isAppend()==true)
        {
            echo '<TR><TD class="first">';
            echo '<a href="?'.$url.'">'._("Ajout d'un plan comptable").'</a>';
            echo '</TD></TR>';
        }
        foreach ($list as $line)
        {
             $url=http_build_query(array("sa"=>"pa_detail","ac"=>$ac,"pa_id"=>$line['id'],
                "gDossier"=>Dossier::id()));
            echo '<TR>';
            echo '<TD>'.
            '<a href="?'.$url.'">'.
            h($line['name']);
            echo "&nbsp;";
            echo h($line['description'])."</a>";
            echo "</td>";
            echo "</TR>\n";
        }
        echo '</TABLE>';


        echo '</div>';
    }
}
//---------------------------------------------------------------------------
// show the content part
//
//
//---------------------------------------------------------------------------

echo $ret;
