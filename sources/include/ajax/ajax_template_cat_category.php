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
 * @brief manage attribut of a Template of Category of card. The answer must be 
 * in json
 */
$answer=[];
$answer['status']="NOK";
$answer['content']="";
$answer['message']=_("Commande inconnue");


/**
 * security 
 */
try
{
    if ($g_user->check_module("CFGCARDCAT")==0)
        throw new Exception(_("Accès non autorisé"));
    $http=new HttpInput();
    $action=$http->request("action");
    $ad_id=$http->request("ad_id", "number");
    $frd_id=$http->request("frd_id", "number");
    $objname=$http->request("objname");
}
catch (Exception $ex)
{
    $answer['message']=_("Accès non autorisé");
    header("Content-type: text/json; charset: utf8", true);
    echo json_encode($answer,
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    return;
}
switch ($action)
{
    case "add_attribute":
        try
        {
            if ($cn->get_value("select count(*) from attr_min where frd_id=$1 and ad_id=$2",
                            [$frd_id, $ad_id])>0)
                throw new Exception(_("Attribut déjà ajouté"));
            $cn->exec_sql("insert into attr_min (frd_id,ad_id) values ($1,$2)",
                    [$frd_id, $ad_id]);
            $answer['status']="OK";
            $answer['message']="";
            $js=sprintf("category_card.remove_attribut('%s','%s','%s',%d)",
                    Dossier::id(), $frd_id, $objname, $ad_id);
            $answer['content']=$cn->get_value("select ad_text from attr_def where ad_id=$1",
                            [$ad_id]).
                    HtmlInput::anchor(SMALLX, "javascript:void(0)", $js,
                            ' class="smallbutton" style="padding:0px;display:inline" ');
        }
        catch (Exception $exc)
        {
            echo $exc->getMessage();
            error_log($exc->getTraceAsString());
            $answer['message']=$exc->getMessage();
        }


        break;
    case "remove_attribute":
        try
        {
            if ($cn->get_value("select count(*) from jnt_fic_attr 
                    join fiche_def using (fd_id)
                    where frd_id=$1 and ad_id=$2",
                            [$frd_id, $ad_id])>0)
                throw new Exception(_("Attribut déjà utilisé"));
            if (in_array($ad_id, [ATTR_DEF_NAME,ATTR_DEF_QUICKCODE]) )
            {
                throw new Exception(_("Attribut obligatoire"));
            }
            $answer['content']=$cn->get_value("select ad_text from attr_def where ad_id=$1",
                    [$ad_id]);
            $answer['status']="OK";
            $answer['message']="";
            $cn->exec_sql("delete from attr_min where frd_id=$1 and ad_id=$2",
                    [$frd_id,$ad_id]);
        }
        catch (Exception $exc)
        {
            echo $exc->getMessage();
            error_log($exc->getTraceAsString());
            $answer['message']=$exc->getMessage();
        }
        break;

    default:
        break;
}


header("Content-type: text/json; charset: utf8", true);
echo json_encode($answer,
        JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
return;
