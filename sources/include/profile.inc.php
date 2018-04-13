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
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/database/profile_sql.class.php';
global $cn,$http;

//**********************************************
// Save avail. profiles
//**********************************************
if (isset($_POST['change_profile']))
{
    extract($_POST, EXTR_SKIP);
    try
    {
        for ($e=0; $e<count($right); $e++)
        {
            if ($right[$e]=='X'&&$ua_id[$e]=='')
                continue;
            if ($right[$e]=='X'&&$ua_id[$e]!='')
            {
                $cn->exec_sql("delete from user_sec_action_profile where p_id=$1 and p_granted=$2",
                        array($p_id, $ap_id[$e]));
                continue;
            }
            if ($ua_id[$e]=="")
            {
                $cn->exec_sql("insert into user_sec_action_profile (p_id,p_granted,ua_right) values($1,$2,$3)",
                        array($p_id, $ap_id[$e], $right[$e]));
                continue;
            }
            if ($ua_id[$e]!='')
            {
                $cn->exec_sql("update user_sec_action_profile set ua_right=$3 where  p_id=$1 and p_granted=$2 ",
                        array($p_id, $ap_id[$e], $right[$e]));
                continue;
            }
        }
    }
    catch (Exception $exc)
    {
        echo $exc->getMessage();
        record_log( $exc->getTraceAsString());
        throw $exc;
    }
}
//**********************************************
// Save avail. profiles
//**********************************************
if (isset($_POST['change_stock']))
{
    extract($_POST, EXTR_SKIP);
    try
    {
        for ($e=0; $e<count($right); $e++)
        {
            if ($right[$e]=='X'&&$ur_id[$e]=='')
                continue;
            if ($right[$e]=='X'&&$ur_id[$e]!='')
            {
                $cn->exec_sql("delete from profile_sec_repository where p_id=$1 and r_id=$2",
                        array($p_id, $ar_id[$e]));
                continue;
            }
            if ($ur_id[$e]=="")
            {
                $cn->exec_sql("insert into profile_sec_repository (p_id,r_id,ur_right) values($1,$2,$3)",
                        array($p_id, $ar_id[$e], $right[$e]));
                continue;
            }
            if ($ur_id[$e]!='')
            {
                $cn->exec_sql("update profile_sec_repository set ur_right=$3 where  p_id=$1 and r_id=$2 ",
                        array($p_id, $ar_id[$e], $right[$e]));
                continue;
            }
        }
    }
    catch (Exception $exc)
    {
        echo $exc->getMessage();
        record_log($exc->getTraceAsString());
        throw $exc;
    }
}
//**********************************************
// Save_name
// *********************************************

if (isset($_POST['save_name']))
{

    extract($_POST, EXTR_SKIP);
    try
    {
        if (strlen(trim($p_name))==0)
            throw new Exception("Nom ne peut être vide");
        if (isNumber($p_id)==0)
            throw new Exception("profile Invalide");
        $wc=(isset($with_calc))?1:0;
        $wd=(isset($with_direct_form))?1:0;
        $p_desc=(strlen(trim($p_desc))==0)?null:trim($p_desc);
        if ($p_id!=-1)
        {
            $cn->exec_sql("update profile set p_name=$1,p_desc=$2,
					with_calc=$3, with_direct_form=$4 where p_id=$5",
                    array($p_name,
                $p_desc, $wc, $wd, $p_id));
        }
        else
        {
            $p_id=$cn->get_value("insert into profile (p_name,
				p_desc,with_calc,with_direct_form) values
				($1,$2,$3,$4) returning p_id",
                    array(
                $p_name, $p_desc, $wc, $wd
            ));
        }
    }
    catch (Exception $e)
    {
        alert($e->getMessage());
    }
}
//************************************
// Clone
//************************************
if (isset($_POST['clone']))
{
    try
    {
        $p_id = $http->post("p_id","number", 0);
        $cn->start();
        $new_id=$cn->get_value("insert into profile(p_name,p_desc,with_calc,
			with_direct_form)
			select 'copie de '||p_name,p_desc,with_calc,
			with_direct_form from profile where p_id=$1 returning p_id", array($p_id));
        $cn->exec_sql("
                        insert into profile_menu (p_id,me_code,me_code_dep,p_order,p_type_display,pm_default)
                        select $1,me_code,me_code_dep,p_order,p_type_display,pm_default from profile_menu
                        where p_id=$2
			", array($new_id, $p_id));
        $cn->exec_sql("select menu_complete_dependency($1)",array($new_id));
        $cn->exec_sql("update profile_menu 
            set pm_id_dep=(select distinct higher_dep 
                            from v_menu_dependency as a 
                            where
                            a.pm_id= profile_menu.pm_id) 
                        where pm_id_dep is null and p_id=$1",array($new_id));
        $cn->commit();
        $p_id=$new_id;
        $_POST['p_id'] = $new_id;
        $_GET['p_id'] = $new_id;
        $_REQUEST['p_id'] = $new_id;
        $_POST['tab']="profile_gen_div";
    }
    catch (Exception $exc)
    {
        echo alert($exc->getMessage());
        $cn->rollback();
    }

}
//************************************
// Delete
//************************************
if (isset($_POST['delete_profil']))
{
    extract($_POST, EXTR_SKIP);
    try
    {
        $cn->start();
        if ($p_id==1)
        {
            throw new Exception('On ne peut pas effacer le profil par défaut');
        }
        $new_id=$cn->get_value("delete from profile
			where p_id=$1 ", array($p_id));
        $cn->commit();
    }
    catch (Exception $exc)
    {
        echo alert($exc->getMessage());
        $cn->rollback();
    }
}
//************************************
// Modify the menu 
//************************************
if (isset($_POST['mod']))
{
    try
    {
        // pm_id of the menu to modify
        $pm_id=$http->post("pm_id", "number");
        // profile id
        $p_id=$http->post("p_id", "number");
        // display order 
        $p_order=$http->post("p_order", "number");
        // code to add
        $me_code=$http->post("me_code");
        // tab
        $tab=$http->post("tab");
        // set Default
        $pm_default=$http->post('pm_default', "string", 0);
        /**
         * Printing cannot be a menu and do not depend of anything
         */
        $menu_type=$cn->get_value("select me_type from menu_ref
			where me_code=$1", array($me_code));

        if ($menu_type=='PR')
        {
            $p_type='P';
            $me_code_dep=-1;
        }
        $cn->start();
        $p_order=(strlen(trim($p_order))==0)?"0":$p_order;
        if ($pm_default==1)
        {
            // reset all default
            $cn->exec_sql("update profile_menu set pm_default=0
				where pm_id_dep=(select pm_id_dep from profile_menu
								where
								pm_id=$1)", array($pm_id));
        }
        $cn->exec_sql("update profile_menu set me_code=$1,p_order=$2,pm_default=$3
			where pm_id=$4", array($me_code, $p_order, $pm_default, $pm_id));
        $cn->commit();
    }
    catch (Exception $e)
    {
        $cn->rollback();
        alert($e->getMessage());
    }
}

//****************************************************
// Add a menu, module, submenu,plugin...
//****************************************************
if (isset($_POST['add_menu'])||isset($_POST['add_impress']))
{
    try
    {
        // type of menu me or pr
        $p_type=$http->post("type","string",null);
        // level
        $p_level=$http->post("p_level","string",null);
        // pm_id of menu parent
        $p_dep=$http->post("dep","number",null);
        // profile id
        $p_id=$http->post("p_id", "number");
        // display order 
        $p_order=$http->post("p_order");
        // code to add
        $me_code=$http->post("me_code");
        // tab
        $tab=$http->post("tab");
        $cn->start();


        /**
         * Printing cannot be a menu and do not depend of anything
         */
        $menu_type=$cn->get_value("select me_type from menu_ref
                where me_code=$1", array($me_code));

        if ($menu_type=='PR')
        {
            $p_type='P';
            $me_code_dep=null;
            $pm_id_dep=null;
        }

        // Module never depends of anything
        if ($p_type=='me')
        {
            if ($p_level==0)
            {
                $me_code_dep=null;
                $pm_id_dep=null;
                $p_type='M';
            }
            else
            {
                $me_code_dep=$cn->get_value('select me_code from profile_menu'
                        .' where pm_id = $1 and p_id=$2', array($p_dep, $p_id));
                $pm_id_dep=$p_dep;
                $p_type='E';
            }
        }
        /**
         * Check for infinite loop
         */
        $inf=$cn->get_value("select count(*) from profile_menu
                where p_id=$1 and me_code_dep=$2 and me_code=$3",
                array($p_id, $me_code, $me_code_dep));
        if ($inf>0)
            throw new Exception(_("Boucle infinie"));
        /**
         * Check if we don't add a menu depending on itself
         */
        if ($me_code==$me_code_dep)
            throw new Exception(_("Un menu ne peut pas dépendre de lui-même"));


        /**
         * if me_code_dep == -1, it means it is null
         */
        $me_code_dep=($me_code_dep==-1)?null:$me_code_dep;
        
        /*
         * Do not insert twice the same menu 
         */
        $duplicate = $cn->get_value(" select count(*) from profile_menu where "
                . " pm_id_dep = $1 and me_code = $2",array($pm_id_dep,$me_code));
        if ( $duplicate > 0 ) {
            throw new Exception(_('Doublon'));
        }
        $pm_default=(isset($pm_default))?1:0;
        $cn->exec_sql("
                        insert into profile_menu (me_code,me_code_dep,p_id,p_order,pm_default,p_type_display,pm_id_dep)
                        values ($1,$2,$3,$4,$5,$6,$7)
                        ",
                array($me_code, $me_code_dep, $p_id, $p_order, $pm_default, $p_type,
            $pm_id_dep));

        $cn->commit();
    }
    catch (Exception $exc)
    {
        alert($exc->getMessage());
        $cn->rollback;
    }
}

echo '<div id="list_profile" class="content">';
$table=new Sort_Table();
$url=$_SERVER['REQUEST_URI'];

$table->add(_('Nom'), $url, "order by p_name asc", "order by p_name desc", "na",
        "nd");
$table->add(_('Description'), $url, "order by p_desc asc",
        "order by p_desc desc", "da", "dd");
$table->add(_('Calculatrice visible'), $url, "order by with_calc asc",
        "order by with_calc desc", "ca", "cd");
$table->add(_('Accès Direct visible'), $url, "order by with_direct_form asc",
        "order by with_direct_form desc", "fa", "fd");

$ord=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'na';

$order=$table->get_sql_order($ord);

$menu=new Profile_sql($cn);
$ret=$menu->seek("where p_id > 0 ".$order);
echo '<table class="result">';
echo '<tr>';
echo '<th>'.$table->get_header(0).'</th>';
echo '<th>'.$table->get_header(1).'</th>';
echo '<th>'.$table->get_header(2).'</th>';
echo '<th>'.$table->get_header(3).'</th>';
echo '</tr>';
$gDossier=Dossier::id();
for ($i=0; $i<Database::num_row($ret); $i++)
{
    $row=$menu->get_object($ret, $i);

    $js=sprintf('<a href="javascript:void(0)" style="text-decoration:underline" onclick="get_profile_detail(\'%s\',\'%s\')">',
            $gDossier, $row->p_id);
    echo '<tr>';
    echo "<td>".$js.$row->p_name.'</a>'.'</td>';
    echo td($row->p_desc);
    echo td($row->with_calc);
    echo td($row->with_direct_form);
    echo '</tr>';
}
$js=sprintf('<a href="javascript:void(0)"  class="button" onclick="get_profile_detail(\'%s\',\'%s\')">',
        $gDossier, -1);
echo '<tr>';
echo "<td>".$js._("Ajouter un profil")." </td>";
echo '</tr>';
echo '</table>';
echo '</div>';


//*******************************************************
// Show details of the selected profile
//*******************************************************
echo '<div id="detail_profile" class="content">';
if (isset($_POST['p_id']))
{
    require_once NOALYSS_INCLUDE.'/ajax/ajax_get_profile.php';
    ?>
    <script>
        $('list_profile').hide()
    </script>
    <?php

}
echo '</div>';
if (isset($_POST['delete_profil']))
{
    echo create_script(" $('detail_profile').hide()");
    ?>
    <script>
        $('list_profile').show()
    </script>
    <?php

}
$dep=$http->post("dep","string","");
?>
<script>
    var selected_menu="<?php echo $dep;?>";
    function menu_select(rowid) {
        $('sub'+rowid).addClassName("selectedmenu");
        if ( selected_menu != "0" && rowid != selected_menu ) {
            if ( $('sub'+selected_menu) ) {
                $('sub'+selected_menu).removeClassName("selectedmenu");
            }
        }
        selected_menu=rowid;
    }
    
</script>  