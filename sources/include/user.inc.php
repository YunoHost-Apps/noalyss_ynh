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
if ( !defined ('ALLOWED')) die('Forbidden');
/*!\file
 *
 *
 * \brief user managemnt, included from admin_repo,
 * action=user_mgt
 *
 */
require_once NOALYSS_INCLUDE.'/class_sort_table.php';
echo '<div class="content" style="width:80%;margin-left:10%">';
/******************************************************/
// Add user
/******************************************************/
if ( isset ($_POST["ADD"]) )
{
    $cn=new Database();
    $pass5=md5($_POST['PASS']);
    $new_user=new User($cn,0);
    $new_user->first_name=HtmlInput::default_value_post('FNAME','');
    $new_user->last_name=HtmlInput::default_value_post('LNAME','');
    $login=HtmlInput::default_value_post('LOGIN','');
    $login=str_replace("'","",$login);
    $login=str_replace('"',"",$login);
    $login=str_replace(" ","",$login);
    $login=strtolower($login);
    $new_user->login=$login;
    $new_user->pass=$pass5;
    $new_user->email=HtmlInput::default_value_post('EMAIL','');
	if ( trim($login)=="")
	{
		alert(_("Le login ne peut pas être vide"));
	}
	else
	{
            $new_user->insert();
            $new_user->load();
            $_REQUEST['use_id']=$new_user->id;
            require_once NOALYSS_INCLUDE.'/user_detail.inc.php';
            return;

	}
} //SET login

// View user detail
if ( isset($_REQUEST['det']))
{
    require_once NOALYSS_INCLUDE.'/user_detail.inc.php';

    return;
}
?>

<div id="create_user" style="display:none;width:30%;margin-right: 20%" class="inner_box">
<?php echo HtmlInput::title_box(_('Ajout Utilisateur'),"create_user","hide");?>
    <form action="admin_repo.php?action=user_mgt" method="POST" onsubmit="return check_form()">
    <div style="text-align: center">
<TABLE class="result" >            
       <TR><TD style="text-align: right"> <?php echo _('login')?></TD><TD><INPUT id="input_login" class="input_text"  TYPE="TEXT" NAME="LOGIN"></TD></tr>
        <TR><TD style="text-align: right"> <?php echo _('Prénom')?></TD><TD><INPUT class="input_text" TYPE="TEXT" NAME="FNAME"></TD></tr>
       <TR><TD style="text-align: right"> <?php echo _('Nom')?></TD><TD><INPUT class="input_text"  TYPE="TEXT" NAME="LNAME"></TD></TR>
       <TR><TD style="text-align: right"> <?php echo _('Mot de passe')?></TD><TD> <INPUT id="input_password" class="input_text" TYPE="TEXT" NAME="PASS"></TD></TR>
       <TR><TD style="text-align: right"> <?php echo _('Email')?></TD><TD> <INPUT class="input_text" TYPE="TEXT" NAME="EMAIL"></TD></TR>
</TABLE>
<?php
echo HtmlInput::submit("ADD",_('Créer Utilisateur'));
echo HtmlInput::button_action(_("Fermer"), "$('create_user').style.display='none';");

?>
</div>
</FORM>
    <script>
        function check_form() {
            if ($F('input_login') == "") { 
                    alert('<?php echo _('Le login ne peut être vide') ?>');
                    $('input_login').setStyle({border:"red solid 2px"});
                    return false;
                }
            if ($F('input_password') == "") { 
                alert('<?php echo _('Le mot de passe ne peut être vide') ?>');
                $('input_password').setStyle({border:"red solid 2px"});
                return false;
            }
            return true;
        }
    </script>
</div>

<?php
echo '<p>';
echo HtmlInput::button_action(_("Ajout utilisateur"), "$('create_user').show();","cu");
echo '</p>';
// Show all the existing user on 7 columns
$repo=new Dossier(0);
/******************************************************/
// Detail of a user
/******************************************************/



$compteur=0;
$header=new Sort_Table();
$url=basename($_SERVER['PHP_SELF'])."?action=".$_REQUEST['action'];
$header->add(_("Login"), $url," order by use_login asc", "order by use_login desc","la", "ld");
$header->add(_("Nom"), $url," order by use_name asc,use_first_name asc", "order by use_name desc,use_first_name desc","na", "nd");
$header->add(_('Dossier'),$url,' order by ag_dossier asc','order by ag_dossier desc',
        'da','dd');
$header->add(_("Actif"), $url," order by use_active asc", "order by  use_active desc","aa", "ad");
$ord=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'la';
$sql=$header->get_sql_order($ord);

$a_user=$repo->get_user_folder($sql);

if ( !empty ($a_user) )
{
	echo '<span style="display:block">';
	echo _('Filtre').HtmlInput::infobulle(22);
	echo HtmlInput::filter_table("user", "0,1,2,5","1");
	echo '</span>';
    echo '<table id="user" class="result">';
    echo '<tr>';
    echo '<th>'.$header->get_header(0).'</th>';
    echo '<th>'.$header->get_header(1).'</th>';
    echo th(_("Prénom"));
    echo '<th>'.$header->get_header(3).'</th>';
	echo "<th>"._('Type')."</th>";
    echo '<th>'.$header->get_header(2).'</th>';
    echo '</tr>';

    foreach ( $a_user as $r_user)
    {
        $compteur++;
        $class=($compteur%2==0)?"odd":"even";

        echo "<tr class=\"$class\">";
        if ( $r_user['use_active'] == 0 )
        {
            $Active=$g_failed;
        }
        else
        {
            $Active=$g_succeed;
        }
        $det_url=$url."&det&use_id=".$r_user['use_id'];
        echo "<td>";
        echo HtmlInput::anchor($r_user['use_login'],$det_url);
        echo "</td>";

        echo td($r_user['use_name']);
        echo td($r_user['use_first_name']);
        echo td($Active);
		$type=($r_user['use_admin']==1)?_("Administrateur"):_("Utilisateur");
		echo "<td>".$type."</td>";
		echo td($r_user['ag_dossier']);
        echo '</tr>';
    }// foreach
    echo '</table>';
} // $cn != null
?>

</div>