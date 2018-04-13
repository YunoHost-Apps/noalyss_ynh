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
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE."/class/document.class.php";
require_once NOALYSS_INCLUDE."/lib/inplace_edit.class.php";
/**
 * @file
 * @brief Update description on file
 */
$op=$http->request('op');
global $g_user;

if ($op=='update_comment_followUp')
{
    $input=$http->request('input');
    $action=$http->request('ieaction', 'string', 'display');
    $d_id=$http->request('d_id', "number");

    // Build inplace input
    $inplace_description=Inplace_Edit::build($input);
    $inplace_description->set_callback("ajax_misc.php");
    $inplace_description->add_json_param("d_id", $d_id);
    $inplace_description->add_json_param("gDossier", Dossier::id());
    $inplace_description->add_json_param("op", "update_comment_followUp");
    switch ($action)
    {
        case 'display':
            echo $inplace_description->ajax_input();

            break;
        case 'ok':
            if ($g_user->check_action(VIEWDOC)==1)
            {
                $value=$http->request('value');
                $doc=new Document($cn, $d_id);
                $doc->get();
                if ($g_user->can_write_action($doc->ag_id))
                {
                    // retrieve the document
                    $doc->update_description(strip_tags($value));
                }
                $inplace_description->set_value($value);
            }
            
            echo $inplace_description->value();
            break;
        case 'cancel':
            echo $inplace_description->value();
            break;
        default:
            throw new Exception(__FILE__.':'.__LINE__.'Invalide value');
            break;
    }
}