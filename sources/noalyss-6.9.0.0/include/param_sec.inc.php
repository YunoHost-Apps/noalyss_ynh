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
require_once  NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once  NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_sort_table.php';

$gDossier=dossier::id();
$str_dossier=dossier::get();

/* Admin. Dossier */
$cn=new Database($gDossier);
global $g_user;
$g_user->Check();
$g_user->check_dossier($gDossier);

require_once  NOALYSS_INCLUDE.'/user_menu.php';

/////////////////////////////////////////////////////////////////////////
// List users
/////////////////////////////////////////////////////////////////////////
if ( ! isset($_REQUEST['action']))
{
	$base_url=$_SERVER['PHP_SELF']."?ac=".$_REQUEST['ac']."&".dossier::get();

    echo '<DIV class="content" >';
	$header=new Sort_Table();
	$header->add('Login',$base_url,"order by use_login asc","order by use_login desc",'la','ld');
	$header->add('Nom',$base_url,"order by use_name asc,use_first_name asc","order by use_name desc,use_first_name desc",'na','nd');
	$header->add('Type d\'utilisateur',$base_url,"order by use_admin asc,use_login asc","order by use_admin desc,use_login desc",'ta','td');


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
					where use_login != 'phpcompta' and use_active=1
					and (dos_id=$1  or (dos_id is null and use_admin=1))" . $ord_sql, array($gDossier));

    $MaxUser = Database::num_row($user_sql);


    echo '<TABLE class="result" style="width:80%;margin-left:10%">';
	echo "<tr>";
	echo '<th>'.$header->get_header(0).'</th>';
	echo '<th>'.$header->get_header(1).'</th>';
	echo th('prénom');
	echo th('profil');
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
		echo td($str);

		echo "</TR>";
    }
    echo '</TABLE>';
}
$action="";

if ( isset ($_GET["action"] ))
{
    $action=$_GET["action"];

}
//----------------------------------------------------------------------
// Action = save
//----------------------------------------------------------------------
if ( isset($_POST['ok']))
{
	try
	{
	$cn->start();
    $sec_User=new User($cn,$_POST['user_id']);

	// save profile
	$sec_User->save_profile($_POST['profile']);

	/* Save first the ledger */
    $a=$cn->get_array('select jrn_def_id from jrn_def');

	foreach ($a as $key)
    {
        $id=$key['jrn_def_id'];
        $priv=sprintf("jrn_act%d",$id);
        $count=$cn->get_value('select count(*) from user_sec_jrn where uj_login=$1 '.
                                      ' and uj_jrn_id=$2',array($sec_User->login,$id));
        if ( $count == 0 )
        {
            $cn->exec_sql('insert into user_sec_jrn (uj_login,uj_jrn_id,uj_priv)'.
                                  ' values ($1,$2,$3)',
                                  array($sec_User->login,$id,$_POST[$priv]));

        }
        else
        {
            $cn->exec_sql('update user_sec_jrn set uj_priv=$1 where uj_login=$2 and uj_jrn_id=$3',
                                  array($_POST[$priv],$sec_User->login,$id));
        }
    }
    /* now save all the actions */
    $a=$cn->get_array('select ac_id from action');

    foreach ($a as $key)
    {
        $id=$key['ac_id'];
        $priv=sprintf("action%d",$id);
		if ( ! isset ($_POST[$priv]))
		{
			$cn->exec_sql("delete from user_sec_act where ua_act_id=$1",array($id));
			continue;
		}
        $count=$cn->get_value('select count(*) from user_sec_act where ua_login=$1 '.
                                      ' and ua_act_id=$2',array($sec_User->login,$id));
        if ( $_POST[$priv] == 1 && $count == 0)
        {
            $cn->exec_sql('insert into user_sec_act (ua_login,ua_act_id)'.
                                  ' values ($1,$2)',
                                  array($sec_User->login,$id));

        }
        if ($_POST[$priv] == 0 )
        {
            $cn->exec_sql('delete from user_sec_act  where ua_login=$1 and ua_act_id=$2',
                                  array($sec_User->login,$id));
        }
	 }
	 $cn->commit();
	} // end try
	catch (Exception $e)
	{
		echo_warning ($e->getTraceAsString());
		$cn->rollback();
	}

}




//--------------------------------------------------------------------------------
// Action == View detail for users
//--------------------------------------------------------------------------------

if ( $action == "view" )
{
    $l_Db=sprintf("dossier%d",$gDossier);
    $return= HtmlInput::button_anchor('Retour à la liste','?&ac='.$_REQUEST['ac'].'&'.dossier::get(),'retour');

    $repo=new Database();
    $User=new User($repo,$_GET['user_id']);
    $admin=0;
    $access=$User->get_folder_access($gDossier);

    $str="Aucun accès";

	if ($access=='R')
    {
        $str=' Utilisateur normal';
    }

    if ( $User->admin==1 )
    {
        $str=' Administrateur';
        $admin=1;
    }

    echo '<h2>'.h($User->first_name).' '.h($User->name).' '.hi($User->login)."($str)</h2>";


    if ( $_GET['user_id'] == 1 )
    {
        echo '<h2 class="notice"> Cet utilisateur est administrateur, il a tous les droits</h2>';
		echo "<p> Impossible de modifier cet utilisateur dans cet écran, il faut passer par
			l'écran administration -> utilisateur.
			</p>";
		echo $return;
		return;
    }
    //
    // Check if the user can access that folder
    if ( $access == 'X' )
    {
        echo "<H2 class=\"error\">L'utilisateur n'a pas accès à ce dossier</H2>";
			echo "<p> Impossible de modifier cet utilisateur dans cet écran, il faut passer par
			l'écran administration -> utilisateur.
			</p>";
		echo $return;
        $action="";
        return;
    }

    //--------------------------------------------------------------------------------
    // Show access for journal
    //--------------------------------------------------------------------------------

    $Res=$cn->exec_sql("select jrn_def_id,jrn_def_name  from jrn_def ".
                               " order by jrn_def_name");
    $sec_User=new User($cn,$_GET['user_id']);

    echo '<form method="post">';
    $sHref=sprintf ('export.php?act=PDF:sec&user_id=%s&'.$str_dossier ,
                    $_GET ['user_id']
                   );

    echo dossier::hidden();
    echo HtmlInput::hidden('action','sec');
    echo HtmlInput::hidden('user_id',$_GET['user_id']);
	$i_profile=new ISelect ('profile');
	$i_profile->value=$cn->make_array("select p_id,p_name from profile
			order by p_name");

	$i_profile->selected=$sec_User->get_profile();

	echo "<p>";
	echo _("Profil")." ".$i_profile->input();
	echo "</p>";
    echo '<Fieldset><legend>Journaux </legend>';
    echo '<table>';
    $MaxJrn=Database::num_row($Res);
    $jrn_priv=new ISelect();
    $array=array(
               array ('value'=>'R','label'=>'Uniquement lecture'),
               array ('value'=>'W','label'=>'Lecture et écriture'),
               array ('value'=>'X','label'=>'Aucun accès')
           );

    for ( $i =0 ; $i < $MaxJrn; $i++ )
    {
        /* set the widget */
        $l_line=Database::fetch_array($Res,$i);

        echo '<TR> ';
        if ( $i == 0 ) echo '<TD class="num"> <B> Journal </B> </TD>';
        else echo "<TD></TD>";
        echo "<TD class=\"num\"> $l_line[jrn_def_name] </TD>";

        $jrn_priv->name='jrn_act'.$l_line['jrn_def_id'];
        $jrn_priv->value=$array;
        if ($admin != 1)
            $jrn_priv->selected=$sec_User->get_ledger_access($l_line['jrn_def_id']);
        else
            $jrn_priv->selected='W';


        echo '<td>';
        echo $jrn_priv->input();
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</fieldset>';

    //**********************************************************************
    // Show Priv. for actions
    //**********************************************************************
    echo '<fieldset> <legend>Actions </legend>';
    include('template/security_list_action.php');
    echo '</fieldset>';
    echo HtmlInput::button('Imprime','imprime',"onclick=\"window.open('".$sHref."');\"");
    echo HtmlInput::submit('ok','Sauve');
    echo HtmlInput::reset('Annule');
	echo $return;
    echo '</form>';
} // end of the form
echo "</DIV>";
html_page_stop();
?>
