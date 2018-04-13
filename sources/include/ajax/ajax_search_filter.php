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

require NOALYSS_INCLUDE.'/database/user_filter_sql.class.php';
require NOALYSS_INCLUDE.'/class/acc_ledger_search.class.php';
$cn=Dossier::connect();
$dossier_id=Dossier::id();
global $g_user;
/**
 * @file
 * @brief Managed the search filter
 */
$http=new HttpInput();
//---------------------------------------------------------------------------
// Record the the search filter
//---------------------------------------------------------------------------
$op=$http->request("op");
if ($op=='save_filter')
{
    $answer=[];
    $answer['filter_name']="";
    $answer['status']='NOK';
    $answer['filter_id']=0;
    $answer['message']="";
    try
    {
        $new=new User_filter_SQL($cn, -1);
        $new->setp("login", $g_user->login);
        $new->setp("nb_jrn", $http->post("nb_jrn", 'number'));
        $new->setp("date_start", $http->post("date_start", 'string', NULL));
        $new->setp("date_end", $http->post("date_end", 'string', NULL));
        $new->setp("description", $http->post("desc", 'string', NULL));
        $new->setp("amount_min", $http->post("amount_min", 'number', NULL));
        $new->setp("amount_max", $http->post("amount_max", 'number', NULL));
        $new->setp("qcode", $http->post("qcode", 'string', NULL));
        $new->setp("accounting", $http->post("accounting", 'string', NULL));
        $new->setp("date_paid_start",
                $http->post("date_paid_start", 'string', NULL));
        $new->setp("date_paid_end", $http->post("date_paid_end", 'string', NULL));
        $new->setp("ledger_type", $http->post("ledger_type", 'string'));
        $new->setp("unpaid", $http->post("unpaid", 'string', NULL));
        $new->setp("filter_name", h($http->post("filter_name", 'string')));
        $aJrn=[];
        $max=$http->post("nb_jrn");
        for ($i=0; $i<$max; $i++)
        {
            $aJrn[]=$http->post("r_jrn".$i, "number");
        }
        $new->setp("r_jrn", join(',', $aJrn));
        if (strlen($new->getp("filter_name"))==0)
        {
            throw new Exception(_("Nom ne peut être vide"));
        }
        $new->save();
        $rmAction=sprintf("delete_filter('%s','%s','%s')",  trim($http->post('div')), $dossier_id,
                $new->getp('id'));
        $answer['filter_name']=sprintf('<a class="tinybutton" style="display:inline" id="" onclick="'.$rmAction.'">'.SMALLX.'</a>'
        );
        $answer['filter_name'].=sprintf("<a style=\"display:inline\" onclick=\"load_filter('%s','%s','%s')\">%s</a>",
                trim($http->post('div')), $dossier_id, $new->getp('id'),
                $new->getp("filter_name"));
        $answer['filter_id']=$new->getp("id");
        $answer['status']='OK';
    }
    catch (Exception $ex)
    {
        $answer['status']='NOK';
        $answer['message']=$ex->getMessage();
    }
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($answer);
    return;
}
//------------------------------------------------------------------------------
// Load a filter
//------------------------------------------------------------------------------
if ($op=="load_filter")
{
    $filter_id=$http->get("filter_id", "number");
    $div=$http->get("div");
    $answer=[];
    $answer['status']='OK';
    $answer['filter_id']=0;
    $answer['message']="";
    $filter=new User_filter_SQL($cn, $filter_id);
    $record=$filter->to_array();

    $record['desc']=$record['description'];
    $record['r_jrn']=explode(",", $record['r_jrn']);

    $result=array_merge($answer, $record);


    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($result);
    return;
}
//-----------------------------------------------------------------------------
// Display all the existing search filters and allow to load or delete them
// id of the box is "boxfilter"+{p_div}
//------------------------------------------------------------------------------
if ($op=="display_search_filter")
{
    $p_div=$http->get("div");
    $ledger_type=$http->get("ledger_type");
    
    echo HtmlInput::title_box(_("Filtre"), "boxfilter".$p_div);
    


    // Make a list of all search filters with the same ledger_type of the current
    // user
    $result=$cn->get_array("
        select id, filter_name,ledger_type 
        from user_filter 
        where
        login = $1 
        and ledger_type=$2
        order by 2 asc
", [$g_user->login, $ledger_type]);
    $nb_result=count($result);
    printf('<ul class="select_table" id="manage%s">', $p_div);
    $search_filter=new Acc_Ledger_Search($ledger_type,1,$p_div);
    // Button add filter
    echo "<li>";
    echo $search_filter->build_name_filter();
    echo "</li>";
    
    echo "<li>";
    echo HtmlInput::anchor(_("Remise à zéro"), "", "onclick=\"reset_filter('$p_div');removeDiv('boxfilter{$p_div}')\"");
    echo "</li>";
    
    // Link reset
    for ($i=0; $i<$nb_result; $i++)
    {
        printf(' <li id="manageli%s_%d">', $p_div, $result[$i]["id"]);
        $rmAction=sprintf("delete_filter('%s','%s','%s')", $p_div, $dossier_id,
                $result[$i]['id']);
        printf('<a class="tinybutton" style="display:inline" id="" onclick="'.$rmAction.'">'.SMALLX.'</a>'
        );
        printf("<a style=\"display:inline\" onclick=\"load_filter('%s','%s','%s');removeDiv('boxfilter%s')\">",
                $p_div, $dossier_id, $result[$i]["id"],$p_div);
        echo $result[$i]["filter_name"];
        echo '</a>';

        printf("</li>");
    }
    return;
}
//-----------------------------------------------------------------------------
// Delete a filter_id 
// Check if this filter belong to current user
//------------------------------------------------------------------------------
if ($op=="delete_search_operation")
{
    $answer=[];
    $answer['filter_name']="";
    $answer['status']='NOK';
    $answer['filter_id']=0;
    $answer['message']="";
    try
    {
        $p_div=$http->post("div");
        $filter_id=$http->post("filter_id", "number");

        $answer['div']=$p_div;

        $cn->exec_sql("delete from user_filter where id=$1 and login=$2",[$filter_id,$g_user->login]);
        
        $answer['filter_id']=$filter_id;
        $answer['status']="OK";
    }
    catch (Exception $ex)
    {
        $answer['message']=$ex->getMessage();
    }
    header('Content-Type: application/json;charset=utf-8');
    echo json_encode($answer);
    return;
}