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

 */
// Copyright Author Dany De Bontridder danydb@aevalys.eu
/*! \file
 * \brief handle your own report: create or view report
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once  NOALYSS_INCLUDE.'/lib/user_menu.php';
require_once NOALYSS_INCLUDE.'/lib/ifile.class.php';
require_once NOALYSS_INCLUDE.'/lib/ibutton.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_report.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/lib/ipopup.class.php';
global $http;

$gDossier=dossier::id();
$str_dossier=dossier::get();

/* Admin. Dossier */
$rep=Dossier::connect();


$cn=Dossier::connect();

$rap=new Acc_Report($cn);
$menu=0;
if (isset($_POST["del_form"]))
{
    $rap->id=$http->post("fr_id","number");
    $rap->delete();
    $menu=1;
}
if (isset($_POST["record"]))
{
    $rap->from_array($_POST);
    $rap->save();
    $menu=1;
}
if (isset($_POST['update']))
{
    $rap->from_array($_POST);
    $rap->save($_POST);
    $menu=0;
}
if (isset($_POST['upload']))
{
    $rap->upload();
    $menu=1;
}

if (isset($_REQUEST["action"]) && $menu == 0)
{

    $action=$_REQUEST ["action"];
    $rap->id=(isset($_REQUEST ['fr_id']))?$_REQUEST['fr_id']:0;

    if ($action=="add"&&!isset($_REQUEST['fr_id']))
    {

        echo '<DIV class="content">';
        echo '<h1>'._('Définition').'</h1>';
        echo '<form method="post" >';
        echo dossier::hidden();
        $rap->id=0;
        echo $rap->form(15);

        echo HtmlInput::submit("record", _("Sauve"));
        echo '</form>';
        echo '<span class="notice">'._("Les lignes vides seront effacées").'</span>';
        echo "</DIV>";
        echo '<DIV class="content">';

        echo '<form method="post" enctype="multipart/form-data">';
        echo '<h1> Importation</h1>';
        echo dossier::hidden();
        $rap->id=0;
        $wUpload=new IFile();
        $wUpload->name='report';
        $wUpload->value='report_value';
        echo _('Importer ce rapport').' ';
        echo $wUpload->input();
        echo HtmlInput::submit("upload", _("Sauve"));
        echo '</form>';
        echo '<span class="notice">'._("Les lignes vides seront effacées").'</span>';
        echo "</DIV>";
    }
    if ($action=="view" || $action == "record")
    {
        echo '<DIV class="content">';
        $rap->id=$http->request("fr_id","number");
        echo '<form method="post" style="display:inline">';
        $rap->load();
        echo h1($rap->name);
        echo $rap->form();
        echo HtmlInput::hidden("fr_id", $rap->id);
        echo HtmlInput::hidden("action", "record");
        echo HtmlInput::submit("update", _("Mise a jour"));
        echo HtmlInput::submit("del_form", _("Effacement"));

        echo '</form>';
        echo '<form method="get" action="export.php" style="display:inline">';
        echo dossier::hidden();
        echo HtmlInput::hidden("act", "CSV:reportinit");
        echo HtmlInput::hidden('f', $rap->id);
        echo HtmlInput::submit('bt_csv', "Export CSV");
        echo HtmlInput::request_to_hidden(array('ac', 'action', 'p_action', 'fr_id'));
        $href=http_build_query(array('ac'=>$_REQUEST['ac'],'gDossier'=>$_REQUEST['gDossier']));
        echo '<a style="display:inline" class="smallbutton" href="do.php?'.$href.'">'._('Retour').'</a>';
        echo '</form>';
        echo '<span class="notice">'._("Les lignes vides seront effacées").'</span>';
        echo "</DIV>";
    }
}
else
{

    $lis=$rap->get_list();
    $ac="&ac=".$_REQUEST['ac'];
    $p_action='p_action=defreport';
    echo '<div class="content">';
   echo _('Cherche')." ".HtmlInput::filter_table("rapport_table_id", '0', 1);

    echo '<TABLE id="rapport_table_id" class="vert_mtitle">';
    echo '<TR><TD class="first"><A HREF="?'.$p_action.$ac.'&action=add&'.$str_dossier.'">Ajout</A></TD></TR>';

    foreach ($lis as $row)
    {
        printf('<TR><TD><A  HREF="?'.$p_action.$ac.'&action=view&fr_id=%s&%s">%s</A></TD></TR>', $row->id, $str_dossier, $row->name);
    }
    echo "</TABLE>";
    echo '</div>';
}
html_page_stop();
?>
