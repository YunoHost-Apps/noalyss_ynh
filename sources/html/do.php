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
define('ALLOWED',1);
/**\file
 * \brief Main file
 */
require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/user_common.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/constant.security.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
mb_internal_encoding("UTF-8");

// if gDossier is not set redirect to form to choose a folder
if ( ! isset($_REQUEST['gDossier']))
{
    redirect('user_login.php');
    exit();
}
if ( ! isset ($_SESSION['g_theme']))
  {
    echo "<h2>"._('Vous  êtes déconnecté')."</h2>";
    $backurl=$_SERVER['REQUEST_URI'];
    $url="index.php?".http_build_query(array('reconnect'=>1,'backurl'=>urlencode($backurl)));
    redirect($url);
    exit();

  }
$style_user=HtmlInput::default_value_post("style_user",$_SESSION['g_theme']);

html_page_start($style_user);
if ( DEBUG ) {
    ?>
<div id="debug_div" style="border:slategray solid 1px;margin-left: 0px;position:absolute;background:white;display:fixed;top:2px;left:25px;z-index:1000;display:none">
    <h2>$_POST</h2>
    <?php        
    var_dump($_POST);
    ?>
    <h2>$_GET</h2>
    <?php        
    var_dump($_GET);
    ?>
</div>
<script>
    function show_debug_request() {
        var visible=document.getElementById('debug_div').style.display;
        console.log(visible);
        var new_state="";
        if ( visible == 'block') { new_state='none';}
        else
        if ( visible == 'none') { new_state='block';}
        else 
            console.log('erreur');
        document.getElementById('debug_div').style.display=new_state;
    }
</script>
<input type="button" class="tinybutton" style="position:absolute;display:fixed;top:40px;left:50px;margin-left:50px;z-index:1000" value="show request" onclick="show_debug_request()">

<?php
}
global $g_user, $cn,$g_parameter;



$cn = new Database(Dossier::id());

/*
 * check that the database is not empty
 */
if ( ! $cn->exist_table('version')) {
    echo '<h2 class="notice">'._('Désolé').'</h2>';
    echo _('Ce dossier est vide');
    echo '<p>';
    echo '<a class="button" href="do.php">'._("Retour à l'accueil").'</a>';
    echo '</p>';
    return;
}

$g_user = new User($cn);

$g_parameter=new Own($cn);

load_all_script();
/*  Check Browser version if < IE6 then unsupported */
$browser = $_SERVER['HTTP_USER_AGENT'];
if (strpos($browser, 'MSIE 6') != false ||
	strpos($browser, 'MSIE 5') != false)
{


    echo <<<EOF
    <!--[if lt IE 7]>
    <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>
    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>
    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
    <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
    <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>
   <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>Vous utilisez un navigateur dépassé depuis près de 8 ans!</div>
    <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>Pour une meilleure expérience web, prenez le temps de mettre votre navigateur à jour.</div>
    </div>
   <div style='width: 75px; float: left;'><a href='http://fr.www.mozilla.com/fr/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
   <div style='width: 73px; float: left;'><a href='http://www.apple.com/fr/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
 <div style='float: left;'><a href='http://www.google.com/chrome?hl=fr' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
     </div>
     </div>
     <![endif]-->
EOF;
    exit();
}
if ($cn->exist_table('version') == false)
{
    echo '<h2 class="error" style="font-size:12px">' . _("Base de donnée invalide") . '</h2>';
    $base = dirname($_SERVER['REQUEST_URI']);
       echo HtmlInput::button_anchor('Retour', $base . '/user_login.php');
    exit();
}
if (DBVERSION < dossier::get_version($cn))
{
    echo '<h2 class="error" style="font-size:12px">' . _("Attention: la version de base de donnée est supérieure à la version du programme, vous devriez mettre à jour") . '</h2>';
}
if (DBVERSION > dossier::get_version($cn))
{
    echo '<h2 class="error" style="font-size:12px">' . _("Votre base de données n'est pas à jour") . '   ';
    $a = _("cliquez ici pour appliquer le patch");
    $base = dirname($_SERVER['REQUEST_URI']);
    if ($base == '/') { $base = ''; }
    $base .= '/admin/setup.php';
    echo '<a hreF="' . $base . '">' . $a . '</a></h2>';
}

/*
 * Set a correct periode for the user
 */
$periode = $g_user->get_periode();
$oPeriode = new Periode($cn, $periode);

if ($oPeriode->load() == -1)
{
    $periode = $cn->get_value('select p_id from parm_periode order by p_start asc limit 1');
    $g_user->set_periode($periode);
}

$module_selected = -1;

/*
 * Set the user preference
 */
if ( isset ($_POST['set_preference'])) {
    //// Save value
    extract($_POST);

    if (strlen(trim($pass_1)) != 0 && strlen(trim($pass_2)) != 0)
    {
	$g_user->save_password($pass_1,$pass_2);
        
    }
    $g_user->set_periode($period);
    $g_user->save_global_preference('THEME', $style_user);
    $g_user->save_global_preference('LANG', $lang);
    $g_user->save_global_preference('PAGESIZE', $p_size);
    $g_user->set_mini_report($minirap);
    $_SESSION['g_theme']=$style_user;
    $_SESSION['g_pagesize']=$p_size;
    $_SESSION['g_lang']=$lang;
    $g_user->save_email($p_email);
}

/*
 * if an action is requested
 */
if (isset($_REQUEST['ac']))
{
    // When debugging save all the input in a file
    if ( LOGINPUT)
    {
        $file_loginput=fopen($_ENV['TMP'].'/scenario-'.$_SERVER['REQUEST_TIME'].'.php','a+');
        $tmp_ac=explode('/',trim(strtoupper($_REQUEST['ac'])));
        $last=count($tmp_ac);
        if ($last > 0) $last--;
        fwrite ($file_loginput,"<?php \n");
        fwrite ($file_loginput,'//@description:'.$tmp_ac[$last]."\n");
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
        fclose($file_loginput);
    }

    $_REQUEST['ac']=  trim(strtoupper($_REQUEST['ac']));
    $AC=$_REQUEST['ac'];
    $user_profile=$g_user->get_profile();
    
    
    $amenu_id=$cn->get_array('select 
      pm_id_v3,pm_id_v2,pm_id_v1
    from v_menu_profile where code= upper($1)  and p_id=$2',
            array($AC,$user_profile));
    try {        
        if ( count($amenu_id) != 1 ) {
            // if AC is a simple code and this menu can be accessed 
            // we should find the first menu which used it and change the
            // request AC to it
            $pm_id=$cn->get_array('select pm_id from profile_menu '
                    . ' where lower(me_code)=lower($1) and p_id=$2',
                    array($AC,$user_profile));
            if ( count($pm_id) > 0 ) {
                show_menu($pm_id[0]['pm_id']);
            } else {
                throw new Exception(_('Erreur menu'),10);
            }
        }
        
        $module_id=$cn->get_value('select case when pm_id_v3 = 0 then (case when pm_id_v2 = 0 then pm_id_v1 else pm_id_v2 end) else pm_id_v3 end 
            from v_menu_profile where p_id=$1 and upper(code)=upper($2)',
                array($user_profile,$AC));
        $g_user->audit();
        // Show module and highligt selected one
        show_module($module_id);
        show_menu( $amenu_id[0]['pm_id_v3']);
        show_menu( $amenu_id[0]['pm_id_v2']);
        show_menu($amenu_id[0]['pm_id_v1']);
    } catch (Exception $e) {
        if ( $e->getCode() == 10 ) {
            alert(_('Accès menu impossible'));
        }
        else {
            alert($e->getTraceAsString());
        }
    }
}
else
{
    $default = find_default_module();
    $user_profile=$g_user->get_profile();
    
    if ( $user_profile == "" ) 
        throw new Exception (_('Aucun profil utilisateur'));
    
    $menu_id=$cn->get_value('select 
        case when pm_id_v3 = 0 then 
            (case when pm_id_v2 = 0 then pm_id_v1 else pm_id_v2 end) 
       else pm_id_v3 end 
    from v_menu_profile where code= upper($1)  and p_id=$2',
            array($default,$user_profile));
    $_GET['ac']=$default;
    $_POST['ac']=$default;
    $_REQUEST['ac']=$default;
    show_module($menu_id);
    $all[0] = $default;
    show_menu($menu_id);
}


