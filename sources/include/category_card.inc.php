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

/*!\file
 * \brief this file will handle all the actions for a specific customer (
 * contact,operation,invoice and financial)
 * include from client.inc.php and concerned only the customer card and
 * the customer category
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_contact.php';

$str_dossier=Dossier::get();
/* $sub_action = sb = detail */
/* $cn database conx */
$root='?ac='.$_REQUEST['ac']."&sb=detail&f_id=".$_REQUEST["f_id"].'&'.$str_dossier;
$ss_action=( isset ($_REQUEST['sc'] ))? $_REQUEST['sc']: '';
switch ($ss_action)
{
case 'dc':
    $def=1;
    break;
case 'sv':			/* all the actions (mail,meeting...) */
    $def=2;
    break;
case 'cn':
    $def=3;
    break;
case 'op':
    $def=4;
    break;
case 'let':
    $def=7;
    break;
case 'bal':
  $def=5;
  break;
case 'balag':
    $def=6;
    break;
default:
    $def=1;
    $ss_action='dc';
}
$f=new Fiche($cn,$_REQUEST['f_id']);

echo '<div class="content">';
echo $f->get_gestion_title();
$menu = array(
                  array('href'=>$root."&sc=dc",'label'=>_('Fiche'),'alt'=>_('Détail de la fiche')),
                  array('href'=>$root.'&sc=sv','label'=>_('Suivi'),'alt'=>_('Suivi Fournisseur, client, banque, devis, bon de commande, courrier')),
                  array('href'=>$root.'&sc=cn','label'=>_('Contact'),'alt'=>_('Liste de contacts')),
                  array('href'=>$root.'&sc=op','label'=>_('Opérations'),'alt'=>_('Toutes les opérations')),
                  array('href'=>$root.'&sc=bal','label'=>_('Balance'),'alt'=>_('Balance du tiers')),
                  array('href'=>$root.'&sc=balag','label'=>_('Balance âgée'),'alt'=>_('Balance âgée du tiers')),
                  array('href'=>$root.'&sc=let','label'=>_('Lettrage'),'alt'=>_('Opérations & Lettrages'))
                  );
echo '<ul class="tabs">';
for ($i=0;$i<count($menu);$i++) {
    $style=($def==($i+1))?"tabs_selected":"tabs";
    echo '<li class="'.$style.'">';
    echo '<a href="'.$menu[$i]['href'].'" title="'.$menu[$i]['alt'].'">';
    echo h($menu[$i]['label']);
    echo '</a>';
    echo '</li>';
}
echo '</ul>';
echo '</div>';
echo '<div>';

echo '<div class="myfieldset">';
//---------------------------------------------------------------------------
// Show Detail of a card and category
//---------------------------------------------------------------------------
if ( $ss_action == 'dc' )
{
    require_once NOALYSS_INCLUDE.'/category_detail.inc.php';
}
//---------------------------------------------------------------------------
// Follow up : mail, bons de commande, livraison, rendez-vous...
//---------------------------------------------------------------------------
if ( $ss_action == 'sv' )
{
    require_once NOALYSS_INCLUDE.'/category_followup.inc.php';
}
/*----------------------------------------------------------------------
 * Operation all the operation of this customer
 *
 * ----------------------------------------------------------------------*/
if ( $ss_action == 'op')
{
    require_once NOALYSS_INCLUDE.'/category_operation.inc.php';
}
/*-------------------------------------------------------------------------
 * Balance of the card
 *-------------------------------------------------------------------------*/
if ( $ss_action=='bal')
  {
    require_once NOALYSS_INCLUDE.'/balance_card.inc.php';
  }
/*-------------------------------------------------------------------------
 * Ageing Balance of the card
 *-------------------------------------------------------------------------*/
if ( $ss_action=='balag')
  {
    require_once NOALYSS_INCLUDE.'/balance_card_ageing.inc.php';
  }
/*----------------------------------------------------------------------
 * All the contact
 *
 *----------------------------------------------------------------------*/
if ( $ss_action == 'cn')
{
    echo '<div class="content">';

	echo dossier::hidden();
	$f = new Fiche($cn, $_REQUEST['f_id']);
	$contact=new Contact($cn);
    $contact->company=$f->get_quick_code();
    echo $contact->summary("");

    $sql=' select fd_id from fiche_def where frd_id='.FICHE_TYPE_CONTACT;
    $filter=$cn->make_list($sql);
    if ( empty ($filter))
    {
        echo '<span class="notice">';
        $url="do.php?".http_build_query(array('gDossier'=>Dossier::id(),'ac'=>'CFGCARD'));
        echo '<a class="line" href="'.$url.'" targer="_blank">';
        echo _("Vous devez aller dans fiche et créer une catégorie pour les contacts");
        echo '</a>';
        echo '</span>';
       return;
    }
    /* Add button */
    $f_add_button=new IButton('add_card');
    $f_add_button->label=_('Créer une nouvelle fiche');

    $f_add_button->set_attribute('filter',$filter);
    $f_add_button->javascript=" select_card_type(this);";

    echo $f_add_button->input();
    echo '</div>';
}
/*----------------------------------------------------------------------------
 * Lettering
 *----------------------------------------------------------------------------*/
if ( $def==7 )
{
    require_once NOALYSS_INCLUDE.'/lettering.gestion.inc.php';
}
echo '</div>';