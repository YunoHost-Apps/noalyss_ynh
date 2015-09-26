<?php
/**
 *  This file is part of NOALYSS under GPL
 * 
 */
/**
 * @brief this file is used for the ajax from the extension, it will the ajax.php file from the plugin directory
 * all the variable are in $_REQUEST
 * The code (of the plugin) is required
 * Required variable in $_REQUEST
 *  - gDossier
 *  - plugin_code
 */
if ( ! defined ('ALLOWED') ) define ('ALLOWED',1);
require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_extension.php';
if ( !isset ($_REQUEST['gDossier'])) exit();

require_once NOALYSS_INCLUDE.'/class_own.php';
mb_internal_encoding("UTF-8");

global $g_user,$cn,$g_parameter;
$cn=new Database(dossier::id());
$g_parameter=new Own($cn);
$g_user=new User($cn);
$g_user->check(true);
set_language();
/* if a code has been asked */
if (isset($_REQUEST['plugin_code']) )
{
    if ( LOGINPUT)
    {
        $file_loginput=fopen($_ENV['TMP'].'/scenario-'.$_SERVER['REQUEST_TIME'].'.php','a+');
        fwrite ($file_loginput,"<?php \n");
        fwrite ($file_loginput,'//@description:'.$_REQUEST['plugin_code']."\n");
        fwrite($file_loginput, '$_GET='.var_export($_GET,true));
        fwrite($file_loginput,";\n");
        fwrite($file_loginput, '$_POST='.var_export($_POST,true));
        fwrite($file_loginput,";\n");
        fwrite($file_loginput, '$_POST[\'gDossier\']=$gDossierLogInput;');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput, '$_GET[\'gDossier\']=$gDossierLogInput;');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput,' $_REQUEST=array_merge($_GET,$_POST);');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput,"include '".basename(__FILE__)."';\n");
        fclose($file_loginput);
    }

    $ext=new Extension($cn);

    if ( $ext->search($_REQUEST['plugin_code']) != -1)
    {
        /* security */
        if ( !isset ($_SESSION['g_user']) || $ext->can_request($_SESSION['g_user']) == 0 )
        {
            exit();
        }
        /* call the ajax script */
        require_once(NOALYSS_PLUGIN.DIRECTORY_SEPARATOR.dirname(trim($ext->getp('me_file'))).DIRECTORY_SEPARATOR.'ajax.php');
    }
    else
    {
        alert(j(_("Cette extension n'existe pas ")));
        exit();
    }

}
?>