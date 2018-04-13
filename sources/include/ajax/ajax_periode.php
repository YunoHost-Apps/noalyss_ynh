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
 * @brief display or save a periode variable received $op, $cn $g_user
 * variable : 
 * act 
 *    - close : close a periode
 *    - reopen  : reopen a periode
 *    - show  : display a form for modifying / adding a period    
 *    - remove : delete a period IF not used 
 * 
 * ledger_id is the SQL id of ledger
 * 
 * p_id is either the SQL id of parm_periode of jrn_periode, depending if 
 * ledger_id == 0 or not
 * The answer must be in JSON
 */
require_once NOALYSS_INCLUDE.'/class/periode.class.php';
require_once NOALYSS_INCLUDE.'/class/periode_ledger.class.php';

$err=0;
$a_answer=[];
$a_answer['status']="NOK";
$http=new HttpInput();
try
{
// action to perform
    $act=$http->request("act");
// Periode id 
    $periode_id=$http->request("p_id", "number");
// Ledger id
    $ledger_id=$http->request("ledger_id", "number");
// Name of the javascript variable
    $js_var=$http->request("js_var");
}
catch (Exception $ex)
{
    $a_answer['content']=$ex->getMessage();
    $jsson=json_encode($a_answer,
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    header('Content-Type: application/json;charset=utf-8');
    echo $jsson;
    return;
}
/* we check the security */
if ($g_user->check_module("PERIODE")==0)
{
    return;
}

switch ($act)
{
// Reopen a periode for specific ledger if ledger_id != 0, or all 
// the periodes if ledger_id=0
    case 'reopen':
        try
        {
            if ($ledger_id==0)
            {
                $per=new Periode($cn, $periode_id);
                $per->set_ledger(0);
                $per->reopen();
                $parm_periode=new Parm_periode_SQL($cn, $periode_id);
                ob_start();
                Periode::display_row_global($parm_periode, 0, $js_var);
                $a_answer['content']=ob_get_clean();
            }
            else
            {
                $id=$http->post("p_id", "number");
                $jrn_periode=new Jrn_periode_SQL($cn, $p_id);
                $per_led=new Periode_Ledger($jrn_periode);
                $per_led->reopen();
                ob_start();
                $per_led_table=new Periode_Ledger_Table($periode_id);
                $per_led_table->display_row($per_led_table->get_a_member(), 0,
                        $js_var);
                $a_answer['content']=ob_get_clean();
            }
            $a_answer["status"]="OK";
        }
        catch (Exception $ex)
        {
            $a_answer['content']=$ex->getMessage();
        }
        break;
// Close a periode for specific ledger if ledger_id != 0, or all 
// the periodes if ledger_id=0
    case 'close':
        try
        {
            if ($ledger_id==0)
            {
                $per=new Periode($cn, $periode_id);
                $per->close();
                $parm_periode=new Parm_periode_SQL($cn, $periode_id);
                ob_start();
                Periode::display_row_global($parm_periode, 0, $js_var);
                $a_answer['content']=ob_get_clean();
            }
            else
            {
                $jrn_periode=new Jrn_periode_SQL($cn, $periode_id);
                $per_led=new Periode_Ledger($jrn_periode);
                $per_led->close();
                ob_start();
                $per_led_table=new Periode_Ledger_Table($periode_id);
                $per_led_table->display_row($per_led_table->get_a_member(), 0,
                        $js_var);
                $a_answer['content']=ob_get_clean();
            }
            $a_answer["status"]="OK";
        }
        catch (Exception $ex)
        {
            $a_answer['content']=$ex->getMessage();
        }
        break;
// Add a new periode , only if ledger_id == 0
    case 'show':
        $per=new Periode($cn, $periode_id);
        $per->load();

        $p_exercice=new INum('p_exercice');
        $limit=$per->get_date_limit($periode_id);
        $p_exercice->value=$per->p_exercice;
        $title=_('Modification période');
        $title_par="<p>"._('Modifier les dates de début et fin de période').
                "</p>";
        $title_par.='<p class="notice">'._('Cela pourrait avoir un impact sur les opérations déjà existantes').'</p>';

        $p_start=new IDate('p_start');
        $p_start->value=$limit['p_start'];
        $p_end=new IDate('p_end');
        $p_end->value=$limit['p_end'];

        $html='';
        $html.=HtmlInput::title_box($title, 'mod_periode');
        $html.=$title_par;
        $html.=sprintf('<form method="post" id="mod_periode_frm" onsubmit="%s.save(\'mod_periode_frm\');return false;">',
                $js_var); ;
        $html.=HtmlInput::hidden("js_var", $js_var);
        $html.=HtmlInput::hidden("periode_id", $periode_id);
        $html.=HtmlInput::hidden("ledger_id", $ledger_id);
        $html.=dossier::hidden();
        $html.='<table>';

        $html.=tr(td(_(' Début période : ')).td($p_start->input()));
        $html.=tr(td(_(' Fin période : ')).td($p_end->input()));
        $html.=tr(td(_(' Exercice : ')).td($p_exercice->input()));
        $html.='</table>';
        $html.=HtmlInput::submit('sauver', _('sauver'));
        $html.=HtmlInput::button('close', _('fermer'),
                        'onclick="removeDiv(\'mod_periode\')"');
        $html.=HtmlInput::hidden('p_id', $periode_id);
        $html.='</form>';
        $a_answer['content']=$html;
        break;
// Save a modification of a periode
// @todo must be adapted
    case 'save':
        $per=new Periode($cn, $periode_id);
        $per->load();
        try
        {
            $p_start=$http->post("p_start", "date");
            $p_end=$http->post("p_end", "date");
            $p_exercice=$http->post("p_exercice", "number");
            if ($p_exercice>2099||$p_exercice<1980)
            {
                $html='';
                $html.=_('Erreur exercice invalide');
            }
            else
            {
                $sql="update parm_periode set p_start=to_date($1,'DD.MM.YYYY'),p_end=to_date($2,'DD.MM.YYYY'),p_exercice=$3 where p_id=$4";
                try
                {
                    $cn->exec_sql($sql,
                            array($p_start, $p_end, $p_exercice, $periode_id));
                    $a_answer["status"]="OK";
                }
                catch (Exception $e)
                {
                    record_log($e->getTraceAsString());
                    $html=$e->getTrace();
                    throw $e;
                }
            }
            $parm_periode=new Parm_periode_SQL($cn, $periode_id);
            ob_start();
            Periode::display_row_global($parm_periode, 0, $js_var);
            $a_answer['content']=ob_get_clean();
        }
        catch (Exception $ex)
        {
            $html=$ex->getTrace();
            $a_answer['content']=$html;
        }
        break;
    case "remove":
        try
        {
            $per=new Periode($cn, $periode_id);
            $per->verify_delete();
            $per->delete();
            $a_answer['status']="OK";
        }
        catch (Exception $ex)
        {
            $a_answer["content"]=$ex->getMessage();
        }
        break;
//    case "add_per":
//        $per=new Periode($cn, $periode_id);
//        $per->load();
//
//        $p_exercice=new ISelect('p_exercice');
//        $p_exercice->value=$cn->make_array("select distinct p_exercice,p_exercice from parm_periode order by 1 desc");
//        $title=_('Ajout période');
//        $title_par="<p>"._('On ne peut ajouter une période que sur un exercice qui existe').
//                "</p>";
//
//        $p_start=new IDate('p_start');
//        $p_end=new IDate('p_end');
//
//        $html='';
//        $html.=HtmlInput::title_box($title, 'mod_periode');
//        $html.=$title_par;
//        $html.='<form method="post">' ;
//        $html.=HtmlInput::hidden("ac", $http->post("ac"));
//        $html.=Dossier::hidden();
//        $html.='<table>';
//
//        $html.=tr(td(_(' Début période : ')).td($p_start->input()));
//        $html.=tr(td(_(' Fin période : ')).td($p_end->input()));
//        $html.=tr(td(_(' Exercice : ')).td($p_exercice->input()));
//        $html.='</table>';
//        $html.=HtmlInput::submit('add_per', _('sauver'));
//        $html.=HtmlInput::button('close', _('fermer'),
//                        'onclick="removeDiv(\'mod_periode\')"');
//        $html.='</form>';
//        $a_answer['content']=$html;
//        break;
    case 'insert_periode':
        try
        {
            $p_start=$http->post("p_start", "date");
            $p_end=$http->post("p_end", "date");
            $p_exercice=$http->post("p_exercice", "number");
            $obj=new Periode($cn);
            $p_id=$obj->insert($p_start, $p_end, $p_exercice);
            $parm_periode=new Parm_periode_SQL($cn, $p_id);
            ob_start();
            Periode::display_row_global($parm_periode, 0, $js_var);
            $a_answer['content']=ob_get_clean();
            $a_answer['status']="OK";
            $a_answer['p_id']=$p_id;
            $a_answer['status']="OK";
        }
        catch (Exception $e)
        {
            $a_answer['status']="NOK";
            $a_answer['content']=$e->getMessage();
        }
        break;
    default:
        $a_answer['content']=_("Invalid command")."[$act]";
        break;
}

$jsson=json_encode($a_answer,
        JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
if (!headers_sent())
    header('Content-Type: application/json;charset=utf-8');
echo $jsson;
