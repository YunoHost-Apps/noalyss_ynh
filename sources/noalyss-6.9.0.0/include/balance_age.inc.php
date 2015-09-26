<?php
/*
 *   This file is part of PhpCompta.
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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
$date_start=HtmlInput::default_value_get('p_date_start', '01.01.'.$g_user->get_exercice());
$w_date_start=new IDate('p_date_start',$date_start);
$w_select=new ISelect('p_type');
$w_select->value=array( 
    array('value'=>'C','label'=>_('Client')),
    array('value'=>'F','label'=>_('Fournisseur'))
);
$w_select->selected=HtmlInput::default_value_get('p_type','C');

$w_lettre=new ISelect('p_let');
$w_lettre->value=array( 
    array('value'=>'let','label'=>_('lettrées et non lettrées')),
    array('value'=>'unlet','label'=>_('non lettrées'))
);
$w_lettre->selected=HtmlInput::default_value_get('p_let','unlet');

?>
<form method="GET">
    <?php
        echo HtmlInput::request_to_hidden(array('gDossier','ac'));
    ?>
    <?php printf (_(' Opérations après la date %s qui sont %s '),$w_date_start->input(),$w_lettre->input())?> 
   <?php echo _("Type de tiers")." ".$w_select->input()?>
   <?php echo HtmlInput::submit("view", _('Valider'))?>
</form>

<?php
    if ( ! isset($_GET['view']) ):
    html_page_stop();
    return;
    endif;
?>
<form method="get" action="export.php">
    <?php 
        echo HtmlInput::request_to_hidden(array('gDossier','ac','p_type','p_let','p_date_start'));
        echo HtmlInput::hidden('act','CSV:balance_age');
        echo HtmlInput::submit('csv',_('export CSV'));
?>
</form>
<?php
    require_once 'class_balance_age.php';
    $balance=new Balance_Age($cn);
    $type=HtmlInput::default_value_get('p_type', 'C');
    $let=HtmlInput::default_value_get('p_let', 'unlet');
    $date=HtmlInput::default_value_get('p_date_start', date('d.m.Y'));
    if ( $type == "C") :
        $balance->display_sale($date,$let);
    else:
        $balance->display_purchase($date,$let);
    endif;

?>