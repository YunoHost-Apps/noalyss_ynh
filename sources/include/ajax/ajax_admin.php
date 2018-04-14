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

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
/**
 * @file
 * @brief the file contents the code which answer to ajax call from 
 * admin-noalyss.php
 * @see admin-noalyss.php ajax_misc.php admin.js
 */
if ($g_user->Admin()==0)
{
    die();
}
session_write_close();
set_language();
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();
$op=$http->request("op");
// From admin, grant  the access to a folder to an
// user
if ($op=='folder_add') // operation
{

    $cn=new Database();
    try
    {
        $user_id=$http->get("p_user", "number"); // get variable
        $dossier_id=$http->get("p_dossier", "number"); // get variable
        $user=new User($cn, $user_id);
        $user->set_folder_access($dossier_id, true);
        $dossiercn=new Database($dossier_id);
        // By default new user has the profile 1 (admin) and ledger's security
        // + action's security are disabled
        $user=new User($dossiercn, $user_id);
        $user->set_status_security_action(0);
        $user->set_status_security_ledger(0);
        $user->save_profile(1);
        $dossier=new Dossier($dossier_id);
        $dossier->load();
        $content="<td>".h($dossier->dos_name)."</td><td>".h($dossier->dos_description)."</td>".
                "<td>".
                HtmlInput::anchor(_('Enleve'), "", " onclick=\"folder_remove({$user_id},{$dossier_id});\"").
                "</td>";
        $status='OK';
    }
    catch (Exception $exc)
    {
        error_log($exc->getTraceAsString());
        $content=_('Erreur paramètre');
        $status="NOK";
        return;
    }


    //----------------------------------------------------------------
    // Answer in XML
    header('Content-type: text/xml; charset=UTF-8');
    $dom=new DOMDocument('1.0', 'UTF-8');
    $xml_content=$dom->createElement('content', $content);
    $xml_status=$dom->createElement('status', $status);
    $root=$dom->createElement("root");
    $root->appendChild($xml_content);
    $root->appendChild($xml_status);
    $dom->appendChild($root);
    echo $dom->saveXML();
    exit();
}
// From admin, revoke the access to a folder from an
// user
if ($op=='folder_remove') // operation
{
    try
    {
        $cn=new Database();
        $user_id=$http->get("p_user", "number"); // get variable
        $dossier_id=$http->get("p_dossier", "number"); // get variable
        $user=new User($cn, $user_id);
        $user->set_folder_access($dossier_id, false);
        $content="";
        $status='OK';
    }
    catch (Exception $exc)
    {
        error_log($exc->getTraceAsString());
        $content=_('Erreur paramètre');
        $status="NOK";
    }

    //----------------------------------------------------------------
    // Answer in XML
    header('Content-type: text/xml; charset=UTF-8');
    $dom=new DOMDocument('1.0', 'UTF-8');
    $xml_content=$dom->createElement('content', $content);
    $xml_status=$dom->createElement('status', $status);
    $root=$dom->createElement("root");
    $root->appendChild($xml_content);
    $root->appendChild($xml_status);
    $dom->appendChild($root);
    echo $dom->saveXML();
    exit();
}
/**
 * Display the forbidden folders if the request comes from a form
 * with an input text (id:database_filter_input) then this text is 
 * used as a filter
 * 
 */
if ($op=='folder_display') // operation
{

    $cn=new Database();
    try
    {
        $user_id=$http->get("p_user", "number"); // get variable
        $p_filter=$http->get('p_filter', "string", '');
        ob_start();
        $user=new User($cn, $user_id);
        $a_dossier=Dossier::show_dossier('X', $user->id, $p_filter, MAX_FOLDER_TO_SHOW);
        echo HtmlInput::title_box(_("Liste dossier"), 'folder_list_div');
        ?>
        <form method="get" onsubmit="folder_display('<?php echo $user_id ?>');
                return false">
            <p style="text-align: center">
                <?php echo _('Recherche'); ?>

                <input type="text" id="database_filter_input" class="input_text" autofocus="true" autocomplete="off" nohistory autocomplete="false" value="<?php echo $p_filter ?>" 
                       onkeyup="filter_table(this, 'folder_display_tb', '1,2,3', 0)"  >
                <input type="button" class="smallbutton" onclick="$('database_filter_input').value = '';filter_table($('database_filter_input'), 'folder_display_tb', '1,2,3', 0);" value="X">
                <input type="submit" class="smallbutton" value="<?php echo _('Rechercher') ?>">
            </p>
        </form>    
        <p>
            <?php
            $nb_dossier=count($a_dossier);
            $max=( $nb_dossier>=MAX_FOLDER_TO_SHOW)?MAX_FOLDER_TO_SHOW:$nb_dossier;
            echo _('Dossiers trouvés').':'.$nb_dossier." "._('Dossiers affichés').$max.' '._('Limite dossiers').":".MAX_FOLDER_TO_SHOW;
            ?>
        </p>
        <?php
        require NOALYSS_TEMPLATE.'/folder_display.php';
        $content=ob_get_clean();
        $status='OK';
    }
    catch (Exception $exc)
    {
        error_log($exc->getTraceAsString());
        $content=_('Erreur paramètre');
        $status="NOK";
    }




    //----------------------------------------------------------------
    // Answer in XML
    header('Content-type: text/xml; charset=UTF-8');
    $dom=new DOMDocument('1.0', 'UTF-8');
    $xml=escape_xml($content);
    $xml_content=$dom->createElement('content', $xml);
    $xml_status=$dom->createElement('status', $status);
    $root=$dom->createElement("root");
    $root->appendChild($xml_content);
    $root->appendChild($xml_status);
    $dom->appendChild($root);
    echo $dom->saveXML();
    exit();
}
// For the operation 'modele_drop','modele_modify','folder_modify','folder_drop'
// the p_dossier parameter is mandatory
if (in_array($op, array('modele_drop', 'modele_modify', 'folder_modify', 'folder_drop')))
{
    try
    {
        $dossier=$http->get('p_dossier', "number");
        $content=_('Erreur paramètre');
        $status="NOK";
    }
    catch (Exception $exc)
    {
        error_log($exc->getTraceAsString());
        $content=_('Erreur paramètre');
        $status="NOK";
        //----------------------------------------------------------------
        // Answer in XML
        header('Content-type: text/xml; charset=UTF-8');
        $dom=new DOMDocument('1.0', 'UTF-8');
        $xml=escape_xml($content);
        $xml_content=$dom->createElement('content', $xml);
        $xml_status=$dom->createElement('status', $status);
        $root=$dom->createElement("root");
        $root->appendChild($xml_content);
        $root->appendChild($xml_status);
        $dom->appendChild($root);
        echo $dom->saveXML();
        exit();
    }

    // Modify the description or the name of folder
    if ($op=='folder_modify')
    {
        $dos=new Dossier($dossier);
        ob_start();
        $dos->load();
        echo HtmlInput::title_box(_('Modification'), 'folder_admin_div');
        $wText=new IText();
        echo '<form action="admin-noalyss.php" method="post">';
        echo HtmlInput::hidden('action', 'dossier_mgt');
        echo HtmlInput::hidden('d', $dos->get_parameter("id"));
        echo _('Nom').' : ';
        echo $wText->input('name', $dos->get_parameter('name'));
        echo '<br>';
        $wDesc=new ITextArea();
        $wDesc->heigh=5;
        echo _('Description').' : <br>';
        echo $wDesc->input('desc', $dos->get_parameter('desc'));
        echo '<br>';

        echo _('Max. email / jour (-1 = illimité)');
        $max_email_input=new INum('max_email');
        $max_email_input->value=$dos->get_parameter('max_email');
        $max_email_input->prec=0;
        echo $max_email_input->input();
        echo '<br>';
        echo HtmlInput::submit('upd', _('Modifie'));
        echo '</form>';
        $content=ob_get_clean();
        $status='OK';
    }
    else if ($op=='folder_drop')
    {
        // ask to confirm the removal a folder
        $dos=new Dossier($dossier);
        ob_start();
        echo HtmlInput::title_box(_('Efface'), 'folder_admin_div');
        $dos->load();
        echo '<form action="admin-noalyss.php" method="post">';
        echo HtmlInput::hidden('action', 'dossier_mgt');
        echo HtmlInput::hidden('d', $dossier);
        echo HtmlInput::hidden('sa', 'remove');
        echo '<h2 class="error">'._('Etes vous sûr et certain de vouloir effacer ').$dos->dos_name.' ???</h2>';
        $confirm=new ICheckBox();
        $confirm->name="p_confirm";
        echo '<p>';
        echo _('Cochez la case si vous êtes sûr de vouloir effacer ce dossier');
        echo $confirm->input();
        echo '</p>';
        echo '<p style="text-align:center">';
        echo HtmlInput::submit('remove', _('Effacer'));
        echo '</p>';
        echo '</form>';
        $content=ob_get_clean();
        $status='OK';
    }
    else if ($op=='modele_drop')
    {
        // ask to confirm the removal a folder
        $cn=new Database();
        $name=$cn->get_value('select mod_name from modeledef where mod_id=$1', array($dossier));
        ob_start();
        echo HtmlInput::title_box(_('Efface'), 'folder_admin_div');
        echo '<form  action="admin-noalyss.php" method="post">';
        echo HtmlInput::hidden('m', $dossier);
        echo HtmlInput::hidden('sa', 'remove');
        echo HtmlInput::hidden('action', 'modele_mgt');
        echo '<h2 class="error">'._('Etes vous sure et certain de vouloir effacer ').$name.' ?</h2>';
        $confirm=new ICheckBox();
        $confirm->name="p_confirm";
        echo '<p>';
        echo _('Cochez la case si vous êtes sûr de vouloir effacer ce modèle');
        echo $confirm->input();
        echo '</p>';
        echo '<p style="text-align:center">';
        echo HtmlInput::submit('remove', _('Effacer'));
        echo '</p>';
        echo '</form>';
        $content=ob_get_clean();
        $status='OK';
    }
    else if ($op=='modele_modify')
    {
        // Modify the description or the name of a template
        $cn=new Database();
        ob_start();
        echo HtmlInput::title_box(_('Modification'), 'folder_admin_div');
        echo '<form method="post">';
        $name=$cn->get_value(
                "select mod_name from modeledef where ".
                " mod_id=$1", array($dossier));

        $desc=$cn->get_value(
                "select mod_desc from modeledef where ".
                " mod_id=$1", array($dossier));
        $wText=new IText();
        echo 'Nom : '.$wText->input('name', $name);
        $wDesc=new ITextArea();
        $wDesc->heigh=5;
        echo '<br>Description :<br>';
        echo $wDesc->input('desc', $desc);
        echo HtmlInput::hidden('m', $dossier);
        echo HtmlInput::hidden('action', 'modele_mgt');
        echo '<br>';
        echo HtmlInput::submit('upd', _('Modifie'));
        echo '</form>';
        $content=ob_get_clean();
        $status='OK';
    }
    //----------------------------------------------------------------
    // Answer in XML
    header('Content-type: text/xml; charset=UTF-8');
    $dom=new DOMDocument('1.0', 'UTF-8');
    $xml=escape_xml($content);
    $xml_content=$dom->createElement('content', $xml);
    $xml_status=$dom->createElement('status', $status);
    $root=$dom->createElement("root");
    $root->appendChild($xml_content);
    $root->appendChild($xml_status);
    $dom->appendChild($root);
    echo $dom->saveXML();
    exit();
}
//------------------------------------------------------------------
// Upgrade Core
//------------------------------------------------------------------
if ($op=='upgradeCore')
{
    require_once NOALYSS_INCLUDE.'/lib/progress_bar.class.php';
    require_once NOALYSS_INCLUDE.'/class/package_repository.class.php';
    $task_id=$http->request("task_id");
    $progress=new Progress_Bar($task_id);
    $progress->set_value(2);
    $repo=new Package_Repository();
    $core=$repo->make_object("core", " ");
    try {
        $progress->set_value(5);
        $core->download();
        $progress->set_value(55);
        if (!DEBUG )
        {
            $core->install();
        }
        $progress->set_value(100);

        $url=sprintf('<a href="%s"> install.php</a>', NOALYSS_URL."/install.php");
        printf(_("Afin de terminer l'installation aller sur %s , à la fin de la procédure , demandez à effacer le fichier install.php"),
                $url);
    } catch (Exception $ex ) {
        echo '<p class="notice">';
        echo $ex->getMessage();
        echo '</p>';
        $progress->set_value(100);
    }
    return;
}
//---------------------------------------------------------------------------------------------------------
// Upgrade or install plugin
//---------------------------------------------------------------------------------------------------------
if ($op=='upgradePlugin')
{
    require_once NOALYSS_INCLUDE.'/lib/progress_bar.class.php';
    require_once NOALYSS_INCLUDE.'/class/package_repository.class.php';
    $task_id=$http->request("task_id");
    $code=$http->post("code_plugin");
    $progress=new Progress_Bar($task_id);
    $progress->set_value(2);
    $repo=new Package_Repository();
    $plugin=$repo->make_object("plugin", $code);
    $progress->set_value(5);
    $plugin->download();
    $progress->set_value(55);
    $plugin->install();
    $progress->set_value(100);
    echo _("L'extension doit être activée dans le dossier avec CFGPLUGIN");
    return;
}
//------------------------------------------------------------------------------------------------------------------
// Install template
//------------------------------------------------------------------------------------------------------------------
if ($op=="installTemplate")
{
    require_once NOALYSS_INCLUDE.'/lib/progress_bar.class.php';
    require_once NOALYSS_INCLUDE.'/class/package_repository.class.php';
    $task_id=$http->request("task_id");
    $name=$http->post("code");
    $progress=new Progress_Bar($task_id);
    $progress->set_value(2);
    $package_repository=new Package_Repository();
    $progress->set_value(4);
    $template=$package_repository->make_object("template", $name);
    $progress->set_value(30);
    $template->download();
    $progress->set_value(70);
    $template->install();
    $progress->set_value(100);
    echo _("Modèle installé");
    return;
}
?>        