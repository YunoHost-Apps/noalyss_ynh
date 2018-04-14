<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/class_extension.php';
require_once  NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once  NOALYSS_INCLUDE.'/constant.security.php';
require_once  NOALYSS_INCLUDE.'/class_user.php';
echo '<div class="topmenu">';
@html_page_start($_SESSION['g_theme']);

$cn=new Database(dossier::id());
global $g_user;
$g_user=new User($cn);
$g_user->check();
$only_plugin=$g_user->check_dossier(dossier::id());


/* javascript file */
echo load_all_script();

/* show all the extension we can access */
$a=new ISelect('plugin_code');
$a->value=Extension::make_array($cn);
$a->selected=(isset($_REQUEST['plugin_code']))?strtoupper($_REQUEST['plugin_code']):'';

/* no plugin available */
if ( count($a->value) == 0 )
{
    alert(j(_("Aucune extension  disponible")));
    exit;
}

/* only one plugin available then we don't propose a choice*/
if ( count($a->value)==1  )
{
    $_REQUEST['plugin_code']=$a->value[0]['value'];
}
echo '</div>';
/*else
{
	if (!isset($_REQUEST['ac'])) echo_warning ("ac non positionné");
    echo '<form method="get" action="do.php">';
    echo Dossier::hidden();
    echo HtmlInput::request_to_hidden(array('plugin_code','ac'));
    echo _('Extension').$a->input().HtmlInput::submit('go',_("Choix de l'extension"));
    echo '</form>';
    echo '<hr>';
}*/

//if ( isset($_REQUEST['plugin_code']))
//	require_once NOALYSS_INCLUDE.'/extension_get.inc.php';
?>
