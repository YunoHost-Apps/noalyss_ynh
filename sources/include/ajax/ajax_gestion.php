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

// Copyright 2015 Author Dany De Bontridder danydb@aevalys.eu
/**
 * @file
 * @brief display a box containing last actions
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

if ($op=='action_show')
{
    /**
     * display action
     */
    require_once NOALYSS_INCLUDE.'/class/follow_up.class.php';
    $gestion=new Follow_Up($cn);
    $array=$gestion->get_last(35);
    $len_array=count($array);
    require_once NOALYSS_TEMPLATE.'/action_show.php';
    return;
}
if ($op=='action_add')
{
    require_once NOALYSS_INCLUDE.'/class/follow_up.class.php';
    $gestion=new Follow_Up($cn);
    $gestion->display_short();
    return;
}
if ($op=='action_save')
{
    require_once NOALYSS_INCLUDE.'/class/follow_up.class.php';

    /**
     * save info from the get
     */
    try
    {
         $date_event=$http->get("date_event","string","");
        $dest=$http->get("dest","string", "");
        $event_group=$http->get("event_group", "string",0);
        $event_priority=$http->get("event_priority", "string",0);
        $title=$http->get("title_event","string", NULL);
        $summary=$http->get("summary","string", "");
        $type_event=$http->get('type_event', "string",-1);
        $hour_event=$http->get('hour_event', "string",null);
        if ($date_event==-1||isDate($date_event)==0)
            throw new Exception(_('Date invalide'));
        if (trim($dest)=="")
            $dest_id=NULL;
        else
        {
            $fiche=new Fiche($cn);
            $fiche->get_by_qcode($dest);
            $dest_id=$fiche->id;
            if ($dest_id==0)
                throw new Exception(_('Destinataire invalide'));
        }
        if ($type_event==-1)
            throw new Exception(_('Type invalide'));
        if (trim($title)=="")
            throw new Exception(_('Aucun titre'));
    }
    catch (Exception $ex)
    {
        record_log($ex->getTraceAsString());
        header('Content-type: text/xml; charset=UTF-8');
        $dom=new DOMDocument('1.0', 'UTF-8');
        $xml_content=$dom->createElement('content', $ex->getMessage());
        $xml_status=$dom->createElement('status', "NOK");
        $root=$dom->createElement("root");
        $root->appendChild($xml_content);
        $root->appendChild($xml_status);
        $dom->appendChild($root);
        echo $dom->saveXML();
        return;
    }
    /*
     * Save data
     */
    $gestion=new Follow_Up($cn);
    $gestion->ag_priority=$event_priority;
    $gestion->ag_title=$title;
    $gestion->ag_dest=$event_group;
    $gestion->ag_type=$type_event;
    $gestion->f_id_dest=$dest_id;
    $gestion->ag_state=3;
    $gestion->dt_id=$type_event;
    $gestion->ag_comment=h($summary);
    $gestion->ag_timestamp=$date_event;
    $gestion->ag_remind_date=$date_event;
    $gestion->ag_hour=$hour_event;
    $content=_('Sauvé');
    $status='OK';
    try {
        $gestion->save_short();
    } catch (Exception $ex)
    {
        record_log($ex->getTraceAsString());
        $content=$ex->getMessage();
        $status='NOK';
    }
    header('Content-type: text/xml; charset=UTF-8');
    $dom=new DOMDocument('1.0', 'UTF-8');
    $xml_content=$dom->createElement('content', $content);
    $xml_status=$dom->createElement('status', $status);
    $root=$dom->createElement("root");
    $root->appendChild($xml_content);
    $root->appendChild($xml_status);
    $dom->appendChild($root);
    echo $dom->saveXML();
    return;
}