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

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_default_menu.php';

global $cn,$g_failed,$g_succeed;

$a_default=new Default_Menu();

if ( isset($_POST['save_menu_default']) ) {
    $a_default->set('code_follow',$_POST['code_follow']);
    $a_default->set('code_invoice',$_POST['code_invoice']);
    try
    {
        $a_default->save();
        echo h2("Sauvé",'class="notice"',$g_succeed);
    } catch (Exception $ex)
    {
        echo h2("Code menu invalide",'class="notice"',$g_failed);
    }
}

echo '<form method="POST">';
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
echo Dossier::hidden();
$a_default->input_value();
echo HtmlInput::submit('save_menu_default', _("Sauver"));
echo '</form>';
?>