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
 * \brief Set the security for an user
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/sort_table.class.php';
require_once NOALYSS_INCLUDE.'/lib/inplace_edit.class.php';
require_once NOALYSS_INCLUDE.'/lib/inplace_switch.class.php';

$http=new HttpInput();

$gDossier=dossier::id();
$str_dossier=dossier::get();

/* Admin. Dossier */
$cn=Dossier::connect();
global $g_user;
$g_user->Check();
$g_user->check_dossier($gDossier);

require_once  NOALYSS_INCLUDE.'/lib/user_menu.php';

/////////////////////////////////////////////////////////////////////////
// List users
/////////////////////////////////////////////////////////////////////////
if ( ! isset($_REQUEST['action']))
{
	$base_url=$_SERVER['PHP_SELF']."?ac=".$_REQUEST['ac']."&".dossier::get();

    echo '<DIV class="content" >';
	$header=new Sort_Table();
	$header->add(_('Login'),$base_url,"order by use_login asc","order by use_login desc",'la','ld');
	$header->add(_('Nom'),$base_url,"order by use_name asc,use_first_name asc","order by use_name desc,use_first_name desc",'na','nd');
	$header->add(_("Type d'utilisateur"),$base_url,"order by use_admin asc,use_login asc","order by use_admin desc,use_login desc",'ta','td');


	$order=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'la';

	$ord_sql=$header->get_sql_order($order);


	$repo=new Database();
	/*  Show all the active users, including admin */
	$user_sql = $repo->exec_sql("select use_id,
                                            use_first_name,
                                            use_name,
                                            use_login,
                                            use_admin
                                                from ac_users left join jnt_use_dos using (use_id)
					where use_login != $2 and use_active=1
					and (dos_id=$1  or (dos_id is null and use_admin=1))" . $ord_sql, array($gDossier,NOALYSS_ADMINISTRATOR));

    $MaxUser = Database::num_row($user_sql);


    echo '<TABLE class="result" style="width:80%;margin-left:10%">';
	echo "<tr>";
	echo '<th>'.$header->get_header(0).'</th>';
	echo '<th>'.$header->get_header(1).'</th>';
	echo th(_('prénom'));
	echo th(_('profil'));
	echo th(_('Séc. Journaux actif'));
	echo th(_('Séc. Action actif'));
	echo '<th>'.$header->get_header(2).'</th>';
    for ($i = 0;$i < $MaxUser;$i++)
    {
		echo '<tr>';
        $l_line=Database::fetch_array($user_sql,$i);


	$str="";
        $str=_('Utilisateur Normal');
        if ( $l_line['use_admin'] == 1 )
            $str=_('Administrateur');

		// get profile
		$profile=$cn->get_value("select p_name from profile
				join profile_user using(p_id) where user_name=$1",array($l_line['use_login']));

		$url=$base_url."&action=view&user_id=".$l_line['use_id'];
		echo "<td>";
		echo HtmlInput::anchor($l_line['use_login'], $url);
		echo "</td>";
		echo td($l_line['use_name']);
		echo td($l_line['use_first_name']);
		echo td($profile);
                // status of security on ledger and action 
                $a_sec=$cn->get_row("select us_ledger,us_action from user_active_security where us_login =$1",
                        [$l_line['use_login']]);
                echo td($a_sec['us_ledger']);
                echo td($a_sec['us_action']);
		echo td($str);
		echo "</TR>";
    }
    echo '</TABLE>';
}
$action="";

if ( isset ($_GET["action"] ))
{
    $action=$http->get("action");

}





//--------------------------------------------------------------------------------
// Action == View detail for users
//--------------------------------------------------------------------------------

if ( $action == "view" )
{
    $l_Db=sprintf("dossier%d",$gDossier);
    $return= HtmlInput::button_anchor(_('Retour à la liste'),'?&ac='.$_REQUEST['ac'].'&'.dossier::get(),_('retour'),"",'smallbutton');

    $repo=new Database();
    $user_id=$http->get('user_id',"number");
    $User=new User($repo,$user_id);
    $admin=0;
    $access=$User->get_folder_access($gDossier);

    $str=_("Aucun accès");

	if ($access=='R')
    {
        $str=_('Utilisateur normal');
    }

    if ( $User->admin==1 )
    {
        $str=_('Administrateur');
        $admin=1;
    }
    $str=" ".$str;
    echo '<h2>'.h($User->first_name).' '.h($User->name).' '.hi($User->login)."($str)</h2>";


    if ( $user_id == 1 )
    {
        echo '<h2 class="notice"> '.
            _("Cet utilisateur est administrateur, il a tous les droits").
                '</h2>';
        echo "<p>".
            _("Impossible de modifier cet utilisateur dans cet écran, il faut passer par
		l'écran administration -> utilisateur.").
            "</p>";
		echo $return;
		return;
    }
    //
    // Check if the user can access that folder
    if ( $access == 'X' )
    {
        echo "<H2 class=\"error\">"
        ._("L'utilisateur n'a pas accès à ce dossier")."</H2>";
	echo "<p> ".
                _("Impossible de modifier cet utilisateur dans cet écran, il faut passer par
			l'écran administration -> utilisateur.").
            "</p>";
	echo $return;
        $action="";
        return;
    }
    
    
    //--------------------------------------------------------------------------------
    // Show access for journal
    //--------------------------------------------------------------------------------

    $Res=$cn->exec_sql("select jrn_def_id,jrn_def_name  from jrn_def ".
                               " order by jrn_def_name");
    $sec_User=new User($cn,$user_id);
    $n_dossier_id=Dossier::id();
    $sHref=http_build_query(["act"=>"PDF:sec","user_id"=>$user_id,"gDossier"=>$n_dossier_id]);

    echo dossier::hidden();
    echo HtmlInput::hidden('action','sec');
    echo HtmlInput::hidden('user_id',$user_id);
    $i_profile=new ISelect ('profile');
    $i_profile->id=uniqid("profile");
    $i_profile->value=$cn->make_array("select p_id,p_name from profile
                    order by p_name");
    
    $i_profile->selected=$sec_User->get_profile();
    $ie_profile=new Inplace_Edit($i_profile);
    
    $ie_profile->set_callback("ajax_misc.php");
    $ie_profile->add_json_param("op", "profile");
    $ie_profile->add_json_param("gDossier", $n_dossier_id);
    $ie_profile->add_json_param("user_id", $user_id);
    $ie_profile->add_json_param("profile_id", $i_profile->selected);
    
    echo "<p>";
    echo _("Profil")." ".$ie_profile->input();
    echo "</p>";
    echo '<Fieldset><legend>'._('Journaux').'</legend>';
    echo HtmlInput::button("grant_all", _("Accès à tout"), " onclick=\" grant_ledgers ('W') \"");
    echo HtmlInput::button("grant_readonly", _("Uniquement Lecture"), " onclick=\" grant_ledgers ('R') \"");
    echo HtmlInput::button("revoke_all", _("Aucun accès"), " onclick=\" grant_ledgers ('X') \"");
    //-------------------------------------------------------------------------
    // Enable or not the security on ledger
    //-------------------------------------------------------------------------
    echo "<p>";
    echo _("Sécurité sur les journaux")." ";
    $status_sec_ledger=$sec_User->get_status_security_ledger();
    //--
    // Administrator can always access all the ledgers
    if ( $sec_User->admin==1) {
        echo '<p>';
        echo _("Les administrateurs NOALYSS ont toujours accès à tout");
        $status_sec_ledger=0;
        $sec_User->set_status_security_ledger(0);
    } else {
        $sec_ledger=new Inplace_Switch("sec_ledger", $status_sec_ledger);
        $sec_ledger->set_callback("ajax_misc.php");
        $sec_ledger->add_json_param("gDossier", $n_dossier_id);
        $sec_ledger->add_json_param("user_id", $user_id);
        $sec_ledger->add_json_param("op", "user_sec_ledger");
        $sec_ledger->set_jscript(" if ( $('security_ledger_tbl').visible() ||  {$sec_User->Admin()}==1) { $('security_ledger_tbl').hide();} else { $('security_ledger_tbl').show();}");
        echo $sec_ledger->input();
    }
    echo "</p>";
    //------------------------------------------------------------------------
    // Access by ledgers, needed if the security on ledger is enable
    //------------------------------------------------------------------------
    echo '<div id="security_ledger_tbl">';
    echo '<table>';
    $MaxJrn=Database::num_row($Res);
    $jrn_priv=new ISelect("iledger");
    $array=array(
               array ('value'=>'R','label'=>_('Uniquement lecture')),
               array ('value'=>'W','label'=>_('Lecture et écriture')),
               array ('value'=>'X','label'=>_('Aucun accès'))
           );
    for ( $i =0 ; $i < $MaxJrn; $i++ )
    {
        /* set the widget */
        $l_line=Database::fetch_array($Res,$i);
        $jrn_priv->value=$array;
        $jrn_priv->id="ledas".uniqid();
        $ie_input=new Inplace_Edit($jrn_priv);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("jrn_def_id", $l_line['jrn_def_id']);
        $ie_input->add_json_param("op", "ledger_access");
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("user_id", $user_id);
        $ie_input->set_value($sec_User->get_ledger_access($l_line['jrn_def_id']));
        echo '<TR> ';
        if ( $i == 0 ) echo '<TD class="num"> <B> Journal </B> </TD>';
        else echo "<TD></TD>";
        echo "<TD class=\"num\"> $l_line[jrn_def_name] </TD>";
        echo '<td>';
        echo $ie_input->input();
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '</fieldset>';

    //**********************************************************************
    // Show Priv. for actions
    //**********************************************************************
    echo '<fieldset> <legend>'._('Actions').'</legend>';
    echo HtmlInput::button("grant_all_action", _("Toutes les actions"), " onclick=\" grant_action(1) \"");
    echo HtmlInput::button("revoke_all_action", _("Aucune action"), " onclick=\" grant_action (0) \"");
    //-------------------------------------------------------------------------
    // Enable or not the security on ledger
    //-------------------------------------------------------------------------
    echo "<p>";
    echo _("Sécurité sur les actions")." ";
    // Administrator  always have all action
    if ( $sec_User->admin==1) {
        echo '<p>';
        echo _("Les administrateurs NOALYSS ont toujours accès à tout");
        $status_sec_action=0;
        $sec_User->set_status_security_action(0);
    } else {

        $status_sec_action=$sec_User->get_status_security_action();
        $sec_action=new Inplace_Switch("sec_action", $status_sec_action);
        $sec_action->set_callback("ajax_misc.php");
        $sec_action->add_json_param("gDossier", $n_dossier_id);
        $sec_action->add_json_param("user_id", $user_id);
        $sec_action->add_json_param("op", "user_sec_action");
        $sec_action->set_jscript(" if ( $('security_action_tbl').visible() ) { $('security_action_tbl').hide();} else { $('security_action_tbl').show();}");
        echo $sec_action->input();
    }
    echo "</p>";
    

    include(NOALYSS_TEMPLATE.'/security_list_action.php');
    echo '</fieldset>';
    echo HtmlInput::button('Imprime',_('imprime'),"onclick=\"window.open('export.php?".$sHref."');\"");
	echo $return;
    
    ?>
        <script>
    function grant_ledgers(p_access)  {
        waiting_box();
         var a_select=document.getElementsByTagName('span');
         var i=0;
        var str_id="";
        for (i = 0;i < a_select.length;i++) {
          str_id = new String( a_select[i].id);
           if ( str_id.search(/ledas/) > -1 ) {
              if ( p_access==="W") {
                a_select[i].innerHTML="<?php echo _("Lecture et écriture");?>";
             } else if (p_access === "R") {
                a_select[i].innerHTML="<?php echo _("Uniquement lecture");?>";
            }   else if (p_access === "X") {
                a_select[i].innerHTML="<?php echo _("Aucun accès");?>";
            }
            
           }
        }
        
        new Ajax.Request("ajax_misc.php",{method:"post",
                parameters:{
                            op:"ledger_access_all",
                            gDossier:<?php echo $n_dossier_id?>,
                            method:"get",
                            user_id:<?php echo $user_id;?>,
                            access:p_access
                            }
                });
        remove_waiting_box();
    }
     function grant_action(p_value) {
         var a_select=document.getElementsByTagName('span');
         var i=0;
        var str_id="";
        for (i = 0;i < a_select.length;i++) {
          str_id = new String( a_select[i].id);
           if ( str_id.search(/action/) > -1 ) {
             if ( p_value == 0 ) {
                 a_select[i].setStyle("color:red");
                 a_select[i].innerHTML='&#xf204';
             } else {
                 a_select[i].setStyle("color:green");
                 a_select[i].innerHTML='&#xf205';
             } 
           }
         } // loop
         new Ajax.Request("ajax_misc.php",{method:"get",
                parameters:{
                            op:"action_access_all",
                            gDossier:<?php echo $n_dossier_id?>,
                            method:"get",
                            user_id:<?php echo $user_id;?>,
                            access:p_value
                            }
                });
     }
     function display_security_ledger(p_value) {
        if ( p_value == 1) {
                $('security_ledger_tbl').show();}
            else {
                $('security_ledger_tbl').hide();}
     }
    display_security_ledger(<?=$status_sec_ledger?>);
     function display_security_action(p_value) {
        if ( p_value == 1) {
                $('security_action_tbl').show();}
            else {
                $('security_action_tbl').hide();}
     }
    display_security_action(<?=$status_sec_action?>);
    </script>
<?php
} // end of the form
echo "</DIV>";
html_page_stop();
?>
