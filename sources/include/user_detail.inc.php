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
/** \file
 * \brief Users Security
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/user_menu.php';
require_once  NOALYSS_INCLUDE.'/class_user.php';

$rep = new Database();

if (!isset($_REQUEST['use_id']))
{
    html_page_stop();
    return;
}
$uid = $_REQUEST['use_id'];
$UserChange = new User($rep, $uid);

if ($UserChange->id == false)
{
    // Message d'erreur
    html_page_stop();
}

/*  
 * Update user changes 
 */
$sbaction=HtmlInput::default_value_post('sbaction', "");
if ($sbaction == "save")
{
    $uid = $_POST['UID'];

    // Update User
    $cn = new Database();
    $UserChange = new User($cn, $uid);
    
    if ($UserChange->load() == -1)
    {
        alert(_("Cet utilisateur n'existe pas"));
    }
    else
    {
        $UserChange->first_name =HtmlInput::default_value_post('fname',null);
        $UserChange->last_name = HtmlInput::default_value_post('lname',null);
        $UserChange->active = HtmlInput::default_value_post('Actif',-1);
        $UserChange->admin = HtmlInput::default_value_post('Admin',-1);
        $UserChange->email = HtmlInput::default_value_post('email',null);
        if ($UserChange->active ==-1 || $UserChange->admin ==-1)
        {
            die ('Missing data');
        }
        else if (  trim($_POST['password'])<>'')
        {
            $UserChange->pass = md5($_POST['password']);
            $UserChange->save();
        }
        else
	{
            $UserChange->pass=$UserChange->password;
            $UserChange->save();
	}

    }
}
else if ($sbaction == "delete")
{
//
// Delete the user
//
    $cn = new Database();
    $Res = $cn->exec_sql("delete from jnt_use_dos where use_id=$1", array($uid));
    $Res = $cn->exec_sql("delete from ac_users where use_id=$1", array($uid));

    echo "<center><H2 class=\"info\"> Utilisateur " . h($_POST['fname']) . " " . h($_POST['lname']) . " est effacé</H2></CENTER>";
    require_once NOALYSS_INCLUDE.'/class_iselect.php';
    require_once NOALYSS_INCLUDE.'/user.inc.php';
    return;
}
$UserChange->load();
$it_pass=new IText('password');
$it_pass->value="";
?>
<FORM  id="user_detail_frm" METHOD="POST">

<?php echo HtmlInput::hidden('UID',$uid)?>
    <TABLE BORDER=0>
        <TR>

<?php printf('<td>login</td><td> %s</td>', $UserChange->login); ?>
            </TD>
        </tr>
        <TR>
            <TD>
            <?php printf('Nom de famille </TD><td><INPUT class="input_text"  type="text" NAME="lname" value="%s"> ', $UserChange->name); ?>
            </TD>
        </TR>
        <TR>
          <?php printf('<td>prénom</td><td>
             <INPUT class="input_text" type="text" NAME="fname" value="%s"> ', $UserChange->first_name);
                ?>
        </TD>
        </TR>
        <tr>
            <td>
                <?php 
                echo _('email');
                ?>
            </td>
            <td>
                <INPUT class="input_text" type="text" NAME="email" value="<?php echo $UserChange->email;?>">
            </td>
        </tr>
        <tr>
            <td>
                Mot de passe :<span class="info">Laisser à VIDE pour ne PAS le changer</span>
            </td>
            <td>
                <?php echo $it_pass->input();?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('Actif');?>
            </td>
            <td>
                <?php
                $select_actif=new ISelect('Actif');
                $select_actif->value=array(
                    array('value'=>0,'label'=>_('Non')),
                    array('value'=>1,'label'=>_('Oui'))
                );
                $select_actif->selected=$UserChange->active;
                echo $select_actif->input();
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('Type');?>
            </td>
            <td>
                <?php
                $select_admin=new ISelect('Admin');
                $select_admin->value=array(
                    array('value'=>0,'label'=>_('Utilisateur normal')),
                    array('value'=>1,'label'=>_('Administrateur'))
                );
                $select_admin->selected=$UserChange->admin;
                echo $select_admin->input();
                ?>
            </td>
        </tr>
    </table>
    <input type="hidden" name="sbaction" id="sbaction" value="">
        <input type="Submit" class="button" NAME="SAVE" VALUE="Sauver les changements" onclick="$('sbaction').value='save';return confirm_box('user_detail_frm','Confirmer changement ?');">

        <input type="Submit"  class="button" NAME="DELETE" VALUE="Effacer" onclick="$('sbaction').value='delete';return confirm_box('user_detail_frm','Confirmer effacement ?');" >

</FORM>
<?php
if  ($UserChange->admin == 0 ) :
?>
        <!-- Show all database and rights -->
        <H2 class="info"> Accès aux dossiers</H2>
        <p class="notice">
            Les autres droits doivent être réglés dans les dossiers (paramètre->sécurité), le fait de changer un utilisateur d'administrateur à utilisateur
			normal ne change pas le profil administrateur dans les dossiers.
			Il faut aller dans CFGSECURITY pour diminuer ses privilèges.
        </p>
     
<?php
$array = array(
    array('value' => 'X', 'label' => 'Aucun Accès'),
    array('value' => 'R', 'label' => 'Utilisateur normal')
);
$repo = new Dossier(0);
if ( $repo->count() == 0) 
{
    echo hb('* Aucun Dossier *');
    echo '</div>';
    return;
}

$Dossier = $repo->show_dossier('R',$UserChange->login);

$mod_user = new User(new Database(), $uid);
?>
           <TABLE id="database_list" class="result">
<?php 
//
// Display all the granted folders
//
$i=0;
foreach ($Dossier as $rDossier):
    $i++;
$class=($i%2==0)?' even ':'odd ';
?>
            <tr id="row<?php echo $rDossier['dos_id']?>" class="<?php echo $class;?>">
                <td>
                    <?php echo h($rDossier['dos_name']); ?>
                </td>
                <td>
                    <?php echo h($rDossier['dos_description']); ?>
                </td>
                <td>
                    <?php echo HtmlInput::anchor(_('Enleve'),"",
                            " onclick=\"folder_remove({$mod_user->id},{$rDossier['dos_id']});\"");?>
                </td>
                
            </tr>
<?php 	
endforeach;
?>
        </TABLE>
        <?php 
               echo HtmlInput::button("database_add_button",_('Ajout'),
                            " onclick=\"folder_display({$mod_user->id});\"");
        ?>
        <?php
        // If UserChange->admin==1 it means he can access all databases
        //
        else :
        ?>
        
<?php
    endif;
?>

</DIV>

<?php
html_page_stop();
?>


