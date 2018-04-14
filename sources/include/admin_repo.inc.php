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
/*! \file
 * \brief Administration of the repository : creation of user, folder, security,
 *        templates... Accessible only by the administrator
 */
if ( ! defined ('ALLOWED')) { die (_('Non autorisé'));}
if ( ! defined ('ALLOWED_ADMIN')) { die (_('Non autorisé'));}

include_once NOALYSS_INCLUDE."/class/user.class.php";
require_once NOALYSS_INCLUDE."/lib/user_common.php";
include_once NOALYSS_INCLUDE."/lib/ac_common.php";
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE."/lib/user_menu.php";
require_once NOALYSS_INCLUDE."/lib/http_input.class.php";
require_once NOALYSS_INCLUDE."/lib/icon_action.class.php";
$http=new HttpInput();
$action = $http->request("action","string", "");

$rep=new Database();
$User=new User($rep);
$User->Check();


if ($User->admin != 1)
{
    $theme=(isset($User->theme))?$User->theme:"";
    html_page_start($User->theme);
    echo "<h2 class=\"warning\">";
    echo _("Vous n'êtes pas administateur");
    echo "</h2>";
    $reconnect=http_build_query(array("reconnect"=>1,"backurl"=>"admin-noalyss.php?action=upgrade"));
    echo '<a href="'.NOALYSS_URL.'/index.php?'.$reconnect.'">';
    echo _("Connectez-vous comme administrateur");
    echo '</a>';
    html_page_stop();
    return;
}
// For a backup , we must avoid to send anything before the 
// dump file
if ( $action== 'backup') {
        /* take backup */
        require_once NOALYSS_INCLUDE."/backup.inc.php";
        exit();
}
html_page_start($_SESSION['g_theme']);
load_all_script();
echo '<H2 class="info"> '._('Administration').'</H2>';
echo '<div class="topmenu">';

echo MenuAdmin()."</div>";

?>
<DIV >
<?php
echo js_include("admin.js");
if ( $action=="user_mgt" )
{
    //----------------------------------------------------------------------
    // User management
    //----------------------------------------------------------------------
    require_once NOALYSS_INCLUDE."/user.inc.php";
}
// action=user_mgt
if ( $action=="dossier_mgt")
{
    //-----------------------------------------------------------------------
    // action = dossier_mgt
    //-----------------------------------------------------------------------
    require_once NOALYSS_INCLUDE."/dossier.inc.php";
}
if ( $action== "modele_mgt" )
{
    //-----------------------------------------------------------------------
    //  Template Management
    //-----------------------------------------------------------------------
    require_once NOALYSS_INCLUDE."/modele.inc.php";
} // action is set
if ( $action== 'restore')
{
    // Backup and restaure folders
    require_once NOALYSS_INCLUDE."/restore.inc.php";
}
if ($action== 'audit_log')
{
    /* List the connexion successfull and failed */
    require_once NOALYSS_INCLUDE."/audit_log.php";
}
/*
 * Display information about current installation
 */
if ( $action == "info" && SYSINFO_DISPLAY == true) {
    global $version_noalyss;
    echo "<h2>"._("Version Noalyss")."</h2>";
    echo "Noalyss : ", $version_noalyss;
    
    echo "<h2>"._('Variables').":".NOALYSS_INCLUDE.'/config.inc.php </h2>';
    echo '<ul style="list-style:square">';
    echo "<li>". "NOALYSS_HOME".": ".NOALYSS_HOME."</li>";
    echo "<li>"."NOALYSS_INCLUDE".": ".NOALYSS_INCLUDE."</li>";
    echo "<li>"."NOALYSS_TEMPLATE".": ".NOALYSS_TEMPLATE."</li>";
    echo "<li>"."DEBUG".": ".DEBUG."</li>";
    echo "<li>"."LOGINPUT".": ".LOGINPUT."</li>";
    echo "<li>"."LOCALE".": ".LOCALE."</li>";
    echo "<li>"."MULTI".": ".MULTI."</li>";
    echo "<li>"."DOMAINE".": ".domaine."</li>";
    echo "<li>"."PG_PATH".": ".PG_PATH."</li>";
    echo "<li>"."PG_DUMP".": ".PG_DUMP."</li>";
    echo "<li>"."PG_RESTORE".": ".PG_RESTORE."</li>";
    echo "<li>"."PSQL".": ".PSQL."</li>";
    echo "</ul>";
    echo "<h2>"._("Paramètre base de données")."</h2>";
    $a_option = array ("client_encoding","lc_collate","listen_addresses",
        "server_encoding","work_mem","shared_buffers","server_version",
        "hba_file","config_file","data_directory","effective_cache_size");
    /*
     * For old version of noalyss config file
     */
    $noalyss_user=(defined("noalyss_user"))?noalyss_user:phpcompta_user;
    $port=(defined("noalyss_psql_port"))?noalyss_psql_port:phpcompta_psql_port;
    $host=(!defined("noalyss_psql_host") )?'127.0.0.1':noalyss_psql_host;
    
    echo '<ul style="list-style:square">';
    echo "<li>";
    echo _('Hôte')." = ".$host;
    echo "</li>";
    echo "<li>";
    echo _('Port')." = ".$port;
    echo "</li>";
    echo "<li>";
    echo _('Utilisateur')." = ".$noalyss_user;
    echo "</li>";
    
    for ( $i = 0 ; $i < count($a_option); $i++) {
        $name=$a_option[$i];
        
        $sql="select setting from pg_settings where name=$1";
        $value=$rep->get_value($sql,array($name));
        echo "<li> ".$name." = ".$value."</li>";
    }
    
    echo "</ul>";
    
    echo "<h2>"._('Paramètre PHP')."</h2>";
    ob_start();
    echo phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_ENVIRONMENT | INFO_VARIABLES);
    $r=ob_get_clean();
    $html=new DOMDocument();
    $html->loadHTML($r);
    $nodelist=$html->getElementsByTagName("style");
    $nodelist->item(0)->nodeValue=' 
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold; color: #000000;}
.h {background-color: #9999cc; font-weight: bold; color: #000000;word-wrap:break-word;word-break: break-all;}
.v {background-color: #cccccc; color: #000000;;word-wrap:break-word;word-break: break-all}
.vr {background-color: #cccccc; text-align: right; color: #000000;word-wrap:break-word;word-break: break-all}
img {float: right; border: 0px;}
hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
            ';
    $a_table=$html->getElementsByTagName("table");
    
    // For PHP  < 7 , we must change the attribute "width"
    if ( substr(phpversion(),0,1) != "7" )
    {
        for ( $i = 0 ; $i < $a_table->length;$i++) {
            $node=$a_table->item($i);
            $node->attributes->getNamedItem("width")->nodeValue="100%";

        }
    }
    $a_title = $html->getElementsByTagName("title");
    for ( $i = 0;$i<$a_title->length;$i++) {
        $a_title->item($i)->nodeValue="";
    }
    echo $html->saveHTML();
    
}
//------------------------------------------------------------------------------
// Upgrade
//------------------------------------------------------------------------------
if ( $action == "upgrade" ) {
   
    
    require_once NOALYSS_INCLUDE."/upgrade.inc.php";
}
?>
</DIV>
<?php
html_page_stop();
?>
