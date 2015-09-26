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
// Copyright 2014 Author  Dany De Bontridder ddebontridder@yahoo.fr

/**
 * @file
 * @brief  manage distribution keys for Analytic accountancy, this file is called by 
 * do.php
 * @see do.php
 * 
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
global $cn, $g_user;
require_once NOALYSS_INCLUDE.'/class_anc_key.php';
$op=HtmlInput::default_value_request("op", "list");

switch ($op)
{
    case 'list':
        Anc_Key::display_list();
        Anc_Key::key_add();
        break;
    case 'consult':
        $id=HtmlInput::default_value_request("key", "0");
        if (isNumber($id)==0||$id==0)
        {
            die(_('Clef invalide'));
        }
        $key=new Anc_Key($id);
        if (isset($_POST['save_key']))
        {
            try
            {
                $key->save($_POST);
                Anc_Key::display_list();
                Anc_Key::key_add();

                break;
            }
            catch (Exception $e)
            {
                echo span($e->getMessage(),' class="notice"');
            }
        }
        $key->input();
        break;
    case 'delete_key':
        $id=HtmlInput::default_value_request("key", "0");
        $key=new Anc_Key($id);
        $key->delete();
        Anc_Key::display_list();
        Anc_Key::key_add();
}
?>
