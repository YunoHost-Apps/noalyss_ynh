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
 *   Foundation, Inshowc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/*!\file
 * \brief Nearly all the menu are here, some of them returns a HTML string, others echo
 * directly the result.
 */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';




/*!
 * \brief  Show the menu for the card management
 *
 * \param $p_dossier dossier 1
 *
 *
 *
 * \return nothing
 */
function ShowMenuFiche($p_dossier)
{
    $cn=new Database($p_dossier);
    $mod="&ac=".$_REQUEST['ac'];
    $str_dossier=dossier::get().$mod;
    echo '<div class="lmenu">';
    echo '<TABLE>';

    echo '<TR><TD colspan="1" class="mtitle"  style="width:auto" >
    <A class="mtitle" HREF="?p_action=fiche&action=add_modele&fiche=modele&'.$str_dossier.'">'._('Création').'</A></TD>
    <TD><A class="mtitle" HREF="?p_action=fiche&'.$str_dossier.'">'._('Recherche').'</A></TD>
    </TR>';
    $Res=$cn->exec_sql("select fd_id,fd_label from fiche_def order by fd_label");
    $Max=Database::num_row($Res);
    for ( $i=0; $i < $Max;$i++)
    {
        $l_line=Database::fetch_array($Res,$i);
        printf('<TR><TD class="cell">
               <A class="mtitle" HREF="?p_action=fiche&action=modifier&fiche=%d&%s">%s</A></TD>
               <TD class="mshort">
               <A class="mtitle" HREF="?p_action=fiche&action=vue&fiche=%d&%s">Liste</A>
               </TD>
               </TR>',
               $l_line['fd_id'],
               $str_dossier,
               $l_line['fd_label'],
               $l_line['fd_id'],
               $str_dossier

              );
    }
    echo "</TABLE>";
    echo '</div>';
}
/*!   MenuAdmin */
/* \brief show the menu for user/database management
/*
/* \return HTML code with the menu
*/

function MenuAdmin()
{
    $def=-1;
    if (isset($_REQUEST['UID']))
        $def=0;
    if ( isset ($_REQUEST['action']))
    {
        switch ($_REQUEST['action'])
        {
        case 'user_mgt':
            $def=0;
            break;
        case 'dossier_mgt':
            $def=1;
            break;
        case 'modele_mgt':
            $def=2;
            break;
	case 'audit_log':
	  $def=4;
	  break;
        case 'restore';
            $def=3;
            break;
        }
    }
	if (!defined("MULTI")||(defined("MULTI")&&MULTI==1))
	{
		$item=array (array("admin_repo.php?action=user_mgt",_("Utilisateurs"),_('Gestion des utilisateurs'),0),
                 array("admin_repo.php?action=dossier_mgt",_("Dossiers"),_('Gestion des dossiers'),1),
                 array("admin_repo.php?action=modele_mgt",_("Modèles"),_('Gestion des modèles'),2),
                 array("admin_repo.php?action=restore",_("Restaure"),_("Restaure une base de données"),3),
                 array("admin_repo.php?action=audit_log",_("Audit"),_("Utilisateurs qui se sont connectés"),4),
                 array("login.php",_("Accueil"))
                );
	}
	else
	{
		$item=array (array("admin_repo.php?action=user_mgt",_("Utilisateurs"),_('Gestion des utilisateurs'),0),
                 array("admin_repo.php?action=audit_log",_("Audit"),_("Utilisateurs qui se sont connectés"),4),
                 array("login.php",_("Accueil"))
                );

	}
    $menu=ShowItem($item,'H',"mtitle","mtitle",$def,' style="width:80%;margin-left:10%" ');
    return $menu;
}

/*!
 * \brief  Show the menu from the pcmn page
 *
 * \param $p_start class start default=1
 *
 *
 *
 * \return nothing
 *
 *
 */

function menu_acc_plan($p_start=1)
{
    $base="?ac=".$_REQUEST['ac'];
    $str_dossier="&".dossier::get();
    for ($i=0;$i<10;$i++) { $class[$i]="tabs";}
    $class[$p_start]="tabs_selected";
    $idx=0;
    ?>
    <ul class="tabs">
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=0'.$str_dossier; ?>">0 <?php echo _(' Hors Bilan')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=1'.$str_dossier; ?>">1 <?php echo _(' Immobilisé')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=2'.$str_dossier; ?>">2 <?php echo _('Actif a un an au plus')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=3'.$str_dossier; ?>">3 <?php echo _('Stock et commande')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=4'.$str_dossier; ?>">4 <?php echo _('Compte tiers')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=5'.$str_dossier; ?>">5 <?php echo _('Financier')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=6'.$str_dossier; ?>">6 <?php echo _('Charges')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=7'.$str_dossier; ?>">7 <?php echo _('Produits')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=8'.$str_dossier; ?>">8 <?php echo _('Hors Comptabilité')?></A></li>
    <li class="<?php echo $class[$idx];$idx++; ?>"><A HREF="<?php echo $base.'&p_start=9'.$str_dossier; ?>">9 <?php echo _('Hors Comptabilité')?></A></li>
    </ul>
<?php
}

