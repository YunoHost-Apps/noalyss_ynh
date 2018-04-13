<?php

/*
 *   This file is part of NOALYSS.
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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');


require_once NOALYSS_INCLUDE.'/lib/itext.class.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/inum.class.php';
require_once NOALYSS_INCLUDE.'/lib/inplace_edit.class.php';
require_once NOALYSS_INCLUDE.'/lib/inplace_switch.class.php';

/**
 * @file
 * @brief Manage the security of a ledger , from CFGSEC module
 * 
 */
 global $g_user;
if ( $g_user->check_module("CFGSEC") == 0)        
        throw new Exception(_("Non autorisé"));
    
$n_dossier_id=Dossier::id();
//-----------------------------------------------------------------------------
// Manage the user's access to ledgers
//-----------------------------------------------------------------------------
if ($op=="ledger_access")
{
    $input=$http->request("input");
    $action=$http->request("ieaction", "string", "display");
    $user_id=$http->post("user_id", "number");
    $jrn_def_id=$http->post("jrn_def_id", "number");
    if ($action=="display")
    {
        $ie_input=Inplace_Edit::build($input);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("jrn_def_id", $jrn_def_id);
        $ie_input->add_json_param("op", "ledger_access");
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("user_id", $user_id);
        echo $ie_input->ajax_input();
        return;
    }
    if ($action=="ok")
    {
        $value=$http->post("value");
        $ie_input=Inplace_Edit::build($input);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("jrn_def_id", $jrn_def_id);
        $ie_input->add_json_param("op", "ledger_access");
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("user_id", $user_id);
        $ie_input->set_value($value);
        $sec_User=new User($cn, $user_id);
        $count=$cn->get_value('select count(*) from user_sec_jrn where uj_login=$1 '.
                ' and uj_jrn_id=$2', array($sec_User->login, $jrn_def_id));
        if ($count==0)
        {
            $cn->exec_sql('insert into user_sec_jrn (uj_login,uj_jrn_id,uj_priv)'.
                    ' values ($1,$2,$3)',
                    array($sec_User->login, $jrn_def_id, $value));
        }
        else
        {
            $cn->exec_sql('update user_sec_jrn set uj_priv=$1 where uj_login=$2 and uj_jrn_id=$3',
                    array($value, $sec_User->login, $jrn_def_id));
        }
        echo $ie_input->value();
        return;
    }
    if ($action=="cancel")
    {
        $ie_input=Inplace_Edit::build($input);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("jrn_def_id", $jrn_def_id);
        $ie_input->add_json_param("op", "ledger_access");
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("user_id", $user_id);
        echo $ie_input->value();
        return;
    }
}
//-----------------------------------------------------------------------------
// Set the user's profile
//-----------------------------------------------------------------------------
if ($op=="profile")
{
    $input=$http->request("input");
    $action=$http->request("ieaction", "string", "display");
    $user_id=$http->post("user_id", "number");
    $profile_id=$http->post("profile_id");
    if ($action=="display")
    {
        $ie_input=Inplace_Edit::build($input);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("profile_id", $profile_id);
        $ie_input->add_json_param("op", "profile");
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("user_id", $user_id);
        echo $ie_input->ajax_input();
        return;
    }
    if ($action=="ok")
    {
        $value=$http->post("value");
        // save profile
        $sec_User=new User($cn, $user_id);
        $sec_User->save_profile($value);
        $ie_input=Inplace_Edit::build($input);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("op", "profile");
        $ie_input->add_json_param("profile_id", $profile_id);
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("user_id", $user_id);
        $ie_input->set_value($value);

        echo $ie_input->value();
        return;
    }
    if ($action=="cancel")
    {
        $ie_input=Inplace_Edit::build($input);
        $ie_input->set_callback("ajax_misc.php");
        $ie_input->add_json_param("op", "profile");
        $ie_input->add_json_param("gDossier", $n_dossier_id);
        $ie_input->add_json_param("profile_id", $profile_id);
        $ie_input->add_json_param("user_id", $user_id);
        echo $ie_input->value();
        return;
    }
}
//------------------------------------------------------------------------------
// Update in once all the ledger access for an user
//------------------------------------------------------------------------------
if ($op=='ledger_access_all')
{
    // Find the login
    $user_id=$http->post("user_id", "number");
    $access=$http->post("access");
    if ($access!="W"&&$access!="X"&&$access!="R")
        die("Invalid access");
    $sec_User=new User($cn, $user_id);
    // Insert all the existing ledgers to user_sec_jrn 
    $sql="insert into   user_sec_jrn(
			uj_jrn_id,
			uj_login,
			uj_priv
		) select  jrn_def_id,$1,'X'
		from
			jrn_def
		where
                    not exists(select 1
				from
					user_sec_jrn
				where
					uj_jrn_id = jrn_def_id
					and uj_login = $1
			)";
    $cn->exec_sql($sql, array($sec_User->login));
    $cn->exec_sql('update user_sec_jrn set uj_priv=$1 where uj_login=$2',
            array($access, $sec_User->login));
    return;
}
//------------------------------------------------------------------------------
// Set on or off the action
//------------------------------------------------------------------------------
if ($op=="action_access")
{
    $action_id=$http->get("ac_id", "number");
    $user_id=$http->get("user_id","number");
    $sec_User=new User($cn, $user_id);
    
    $right=$sec_User->check_action($action_id);
    $is_switch=new Inplace_Switch("action".$action_id,0);
    if ($right==1)
    {
        $cn->exec_sql("delete from user_sec_act where ua_act_id=$1 and ua_login=$2",
                array($action_id, $sec_User->login));
        echo $is_switch->get_iconoff();
    } else {
       $cn->exec_sql('insert into user_sec_act (ua_login,ua_act_id)'.
                                  ' values ($1,$2)',
                                  array($sec_User->login,$action_id));
        echo $is_switch->get_iconon();
    }
    
    
    
}
//----------------------------------------------------------------------------
// Set all the actions
//----------------------------------------------------------------------------
if ($op=="action_access_all")
{
    $user_id=$http->get("user_id","number");
    $access=$http->get("access","number");
    $sec_User=new User($cn, $user_id);
    if ( $access==0) {
        $cn->exec_sql("delete from user_sec_act where ua_login=$1",array($sec_User->login));
    }
    if ( $access==1) {
        $cn->exec_sql("
        insert into user_sec_act(ua_login,ua_act_id) select $1,ac_id from action where not exists(select 1 from user_sec_act where ua_login=$1 and ua_act_id=ac_id)",
                array($sec_User->login));
    }
    
}
//----------------------------------------------------------------------------
// Enable or disable security on ledger
//----------------------------------------------------------------------------
if ($op=="user_sec_ledger")
{
    $user_id=$http->get("user_id", "number");
    $value=$http->get("value", "number");
    $sec_user=new User($cn, $user_id);
    $status_sec_ledger=$sec_user->get_status_security_ledger();
    $sec_ledger=new Inplace_Switch("sec_ledger", $status_sec_ledger);
    $sec_ledger->set_callback("ajax_misc.php");
    $sec_ledger->add_json_param("gDossier", $n_dossier_id);
    $sec_ledger->add_json_param("user_id", $user_id);
    $sec_ledger->add_json_param("op", "user_sec_ledger");
    if ($sec_user->get_status_security_ledger()==1||$sec_user->Admin()==1)
    {
        $sec_user->set_status_security_ledger(0);
        echo $sec_ledger->get_iconoff();
    }else {
        $sec_user->set_status_security_ledger(1);
        echo $sec_ledger->get_iconon();
        
    }
}
//----------------------------------------------------------------------------
// Enable or disable security on action
//----------------------------------------------------------------------------
if ($op=="user_sec_action")
{
    $user_id=$http->get("user_id", "number");
    $value=$http->get("value", "number");
    $sec_user=new User($cn, $user_id);
    $status_sec_action=$sec_user->get_status_security_action();
    $sec_action=new Inplace_Switch("sec_action", $status_sec_action);
    $sec_action->set_callback("ajax_misc.php");
    $sec_action->add_json_param("gDossier", $n_dossier_id);
    $sec_action->add_json_param("user_id", $user_id);
    $sec_action->add_json_param("op", "user_sec_action");
    if ($sec_user->get_status_security_action()==1||$sec_user->Admin()==1)
    {
        $sec_user->set_status_security_action(0);
        echo $sec_action->get_iconoff();
    }else {
        $sec_user->set_status_security_action(1);
        echo $sec_action->get_iconon();
        
    }
}