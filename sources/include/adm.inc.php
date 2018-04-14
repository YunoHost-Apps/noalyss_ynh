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
/*!\brief include from adm.inc.php and concerned only the customer card and
 * the customer category
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_customer.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_fiche_def.php';
require_once NOALYSS_INCLUDE.'/class_fiche_def.php';
require_once NOALYSS_INCLUDE.'/class_admin.php';

global $g_user;

$low_action=(isset($_REQUEST['sb']))?$_REQUEST['sb']:"list";
/*! \file
 * \brief Called from the module "Gestion" to manage the customer
 */
$href=basename($_SERVER['PHP_SELF']);

// by default open liste
if ( $low_action  == "" )
    $low_action="list";


//-----------------------------------------------------
// Remove a card
//-----------------------------------------------------
if ( isset($_POST['action_fiche'] ) )
{
    if ( $_POST['action_fiche'] == 'delete_card') 
    {
        if ( $g_user->check_action(FICADD) == 0 )
        {
            alert(_('Vous  ne pouvez pas enlever de fiche'));
            return;
        }

        $f_id=$_REQUEST['f_id'];

        $fiche=new Admin($cn,$f_id);
        $fiche->remove();
        $low_action="list";
    }

}

//-----------------------------------------------------
//    list of customer
//-----------------------------------------------------
if ( $low_action == "list" )
{

    ?>
    <div class="content">
                           <div>
	<form method="get" action="<?php echo $href; ?>">
	<?php
	echo '<h2>' . "Exercice " . $g_user->get_exercice() . '</h2>';
	echo dossier::hidden();
        $a=(isset($_GET['query']))?$_GET['query']:"";
        printf (_('Recherche').' <input class="input_text" type="text" name="query" value="%s">',
                $a);
        echo HtmlInput::request_to_hidden(array('ac'));
        $choice_cat=HtmlInput::default_value_request("choice_cat", 1);
        if ( $choice_cat == 1 )
        {
            $sel_card=new ISelect('cat');
            $sel_card->value=$cn->make_array('select fd_id, fd_label from fiche_def '.
                                             ' where  frd_id=$1 '.
                                             ' order by fd_label ',1,array(FICHE_TYPE_ADM_TAX));
            $sel_card->selected=(isset($_GET['cat']))?$_GET['cat']:-1;
            $sel_card->javascript=' onchange="submit(this);"';
            echo _('Catégorie :').$sel_card->input();
        } else
        {
            $cat=HtmlInput::default_value_request('cat', '');
            echo HtmlInput::hidden("cat",$cat);
            echo HtmlInput::hidden('choice_cat', 0);
        }
            echo Dossier::hidden();

    ?>
    <input type="submit" class="button" name="submit_query" value="<?php echo _('recherche')?>">
	 <input type="hidden" name="p_action" value="adm">
                                                                     </form>
                                                                     </div>
                                                                     <?php
                                                                     $adm=new Admin($cn);
    $search=(isset($_GET['query']))?$_GET['query']:"";
    $sql="";
    if ( isset($_GET['cat']))
	{
        if ( $_GET['cat'] != -1) $sql=sprintf(" and fd_id = %d",$_GET['cat']);
    }

    echo '<div class="content">';
    echo $adm->Summary($search,'adm',$sql);


    echo '<br>';
    echo '<br>';
    echo '<br>';
	if  ($g_user->check_action(FICADD)==1)
	{
		/* Add button */
		$f_add_button=new IButton('add_card');
		$f_add_button->label=_('Créer une nouvelle fiche');
		$f_add_button->set_attribute('ipopup','ipop_newcard');
		$f_add_button->set_attribute('win_refresh','yes');

		$f_add_button->set_attribute('type_cat',FICHE_TYPE_ADM_TAX);
		$f_add_button->javascript=" select_card_type(this);";
		echo $f_add_button->input();

		$f_cat_button=new IButton('add_cat');
		$f_cat_button->set_attribute('ipopup','ipop_cat');
		$f_cat_button->set_attribute('type_cat',FICHE_TYPE_ADM_TAX);
		$f_cat_button->label=_('Ajout d\'une catégorie');
		$f_cat_button->javascript='add_category(this)';
		echo $f_cat_button->input();
	}
    echo '</div>';
    echo '</div>';


}
/*----------------------------------------------------------------------
 * Detail for a card, Suivi, Contact, Operation,... *
 * cc stands for customer card
 *----------------------------------------------------------------------*/
if ( $low_action == 'detail')
{
    /* Menu */
    require_once NOALYSS_INCLUDE.'/category_card.inc.php';
return;
}



html_page_stop();
?>
