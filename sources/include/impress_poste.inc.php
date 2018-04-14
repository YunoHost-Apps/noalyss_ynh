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
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once NOALYSS_INCLUDE.'/class_acc_operation.php';
/*! \file
 * \brief Print account (html or pdf)
 *        file included from user_impress
 *
 * some variable are already defined $cn, $g_user ...
 *
 */
//-----------------------------------------------------
// Show the jrn and date
//-----------------------------------------------------
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_ipopup.php';
global $g_user;

//-----------------------------------------------------
// Form
//-----------------------------------------------------
echo '<div class="content">';

echo '<FORM action="?" METHOD="GET">';
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
echo HtmlInput::hidden('type','poste');
echo dossier::hidden();
echo '<TABLE><TR>';
$span=new ISpan();

$w=new IPoste('poste_id');
$w->set_attribute('ipopup','ipop_account');
$w->set_attribute('label','poste_id_label');
$w->set_attribute('account','poste_id');
$w->table=0;
$w->value=(isset($_REQUEST['poste_id']))?$_REQUEST['poste_id']:"";
$w->label="Choisissez le poste";
print td('Choisissez un poste ').td($w->input());
echo td($span->input('poste_id_label'));
echo '</tr><tr>';

$w_poste=new ICard('f_id');
$w_poste->table=0;
$w_poste->jrn=0;
echo td("Ou Choisissez la fiche");
$w_poste->set_attribute('label','f_id_label');
$w_poste->set_attribute('ipopup','ipop_card');
$w_poste->set_attribute('gDossier',dossier::id());
$w_poste->set_attribute('typecard','all');
$w_poste->set_function('fill_data');
$w_poste->set_dblclick("fill_ipopcard(this);");


$w_poste->value=(isset($_REQUEST['f_id']))?$_REQUEST['f_id']:"";
print td($w_poste->input().$w_poste->search());
echo td($span->input('f_id_label'));
print '</TR>';
print '<TR>';

$date_from=new IDate('from_periode');
$date_to=new IDate('to_periode');
$year=$g_user->get_exercice();
$date_from->value=(isset($_REQUEST['from_periode']))?$_REQUEST['from_periode']:"01.01.".$year;
$date_to->value=(isset($_REQUEST['to_periode']))?$_REQUEST['to_periode']:"31.12.".$year;
echo td(_('Depuis').$date_from->input());
echo td(_('Jusque ').$date_to->input());
//
print "<TR><TD>";
$all=new ICheckBox();
$all->label="Tous les postes qui en dépendent";
$all->disabled=false;
$all->selected=(isset($_REQUEST['poste_fille']))?true:false;
echo $all->input("poste_fille");
echo '</TD></TR><TR><TD>';
$detail=new ICheckBox();
$detail->label="D&eacute;tail des op&eacute;rations";
$detail->disabled=false;
$detail->selected=(isset($_REQUEST['oper_detail']))?true:false;
echo $detail->input("oper_detail");
echo '</td></tr>';
$a_let=array(
           array('value'=>0,'label'=>'Toutes les opérations'),
           array('value'=>1,'label'=>' Opérations lettrées'),
           array('value'=>2,'label'=>' Opérations non lettrées')
       );
echo '</TABLE>';
$salet=new ISelect('ople');
$salet->value=$a_let;
$salet->selected=(isset ($_GET['ople']))?$_GET['ople']:0;

echo $salet->input();

print HtmlInput::submit('bt_html','Visualisation');

echo '</FORM>';
echo '<hr>';
echo '</div>';

//-----------------------------------------------------
// If print is asked
// First time in html
// after in pdf or cvs
//-----------------------------------------------------
if ( isset( $_REQUEST['bt_html'] ) )
{
    if ( isDate($_REQUEST['from_periode'])==null || isDate($_REQUEST['to_periode'])==null)
    {
        echo alert(_('Date malformée, désolée'));
        return;
    }
    require_once NOALYSS_INCLUDE.'/class_acc_account_ledger.php';
    $go=0;
// we ask a poste_id
    if ( isset($_GET['poste_id']) && strlen(trim($_GET['poste_id'])) != 0 )
    {
        if ( isset ($_GET['poste_fille']) )
        {
            $parent=$_GET['poste_id'];
            $a_poste=$cn->get_array("select pcm_val from tmp_pcmn where pcm_val::text like '$parent%' order by pcm_val::text");
            $go=3;
        }
        // Check if the post is numeric and exists
        elseif (  $cn->count_sql('select * from tmp_pcmn where pcm_val=$1',array($_GET['poste_id'])) != 0 )
        {
            $Poste=new Acc_Account_Ledger($cn,$_GET['poste_id']);
            $go=1;
        }
    }
    if ( strlen(trim($_GET['f_id'])) != 0 )
    {
        require_once NOALYSS_INCLUDE.'/class_fiche.php';
        // thanks the qcode we found the poste account
        $fiche=new Fiche($cn);
        $qcode=$fiche->get_by_qcode($_GET['f_id']);
        $p=$fiche->strAttribut(ATTR_DEF_ACCOUNT);
        if ( $p != NOTFOUND)
        {
            $go=2;
        }
    }

    // A account  is given
    if ( $go == 1)
    {
        echo '<div class="content">';
        if ( ! isset($_REQUEST['oper_detail']) )
        {
            Acc_Account_Ledger::HtmlTableHeader();
	    echo '<div class="content">';
            $Poste->HtmlTable(null,$_GET['ople']);
	    echo '</div>';
            echo Acc_Account_Ledger::HtmlTableHeader();
        }
        else
        {
            //----------------------------------------------------------------------
            // Detail
            //----------------------------------------------------------------------
            Acc_Account_Ledger::HtmlTableHeader();

            $Poste->get_row_date( $_GET['from_periode'], $_GET['to_periode'],$_GET['ople']);
            if ( empty($Poste->row)) return;
            $Poste->load();

            echo '<table class="result" >';
            echo '<tr><td  class="mtitle" style="width:auto" colspan="6"><h2 class="info">'. $_GET['poste_id'].' '.h($Poste->label).'</h2></td></tr>';
            /* avoid duplicates */
            $old=array();
            foreach ($Poste->row as $detail)
            {
                if ( in_array($detail['jr_id'],$old) == TRUE ) continue;
                $old[]=$detail['jr_id'];
                echo '<tr><td style="text-align:center;background-color:lightgrey" colspan="6">'.$detail['j_date'].' '.$detail['jr_internal'].h($detail['description']).'</td></tr>';

                $op=new Acc_Operation($cn);
                $op->jr_id=$detail['jr_id'];
                $op->poste=$_GET['poste_id'];
                echo $op->display_jrnx_detail(1);
            }
            echo '</table>';

            echo Acc_Account_Ledger::HtmlTableHeader();
        }
        echo "</div>";
        exit;
    }

    // A QuickCode  is given
    if ( $go == 2)
    {
        if ( ! isset($_REQUEST['oper_detail']) )
        {
            echo '<div class="content">';
            echo '<h2 class="info"> ' .
                '(' . $fiche->id . ')' .
                $fiche->getName() . ' ' .
                ' [ ' . $fiche->get_quick_code() . ' ] ' .
                '</h2>';
            $fiche->HtmlTableHeader();
            $fiche->HtmlTable(null, $_GET['ople']);
            $fiche->HtmlTableHeader();
            echo "</div>";
        }
        else
        {
            // Detail //
            echo '<div class="content">';
            echo '<h2 class="info"> ' .
                '(' . $fiche->id . ')' .
                $fiche->getName() . ' ' .
                ' [ ' . $fiche->get_quick_code() . ' ] ' .
                '<h2>';

            $fiche->HtmlTableHeader();
            $fiche->HtmlTableDetail();
            $fiche->HtmlTableHeader();
            echo "</div>";
        }
        exit;
    }

    // All the children account
    if ( $go == 3 )
    {

        if ( sizeof($a_poste) == 0 )
            exit;
        echo '<div class="content">';


        if ( ! isset ($_REQUEST['oper_detail']))
        {
            $Poste=new Acc_Account_Ledger($cn,$_GET['poste_id']);
            echo Acc_Account_Ledger::HtmlTableHeader();

            foreach ($a_poste as $poste_id )
            {
                $Poste=new Acc_Account_Ledger ($cn,$poste_id['pcm_val']);
                $Poste->HtmlTable(null,$_GET['ople']);
            }
            echo Acc_Account_Ledger::HtmlTableHeader();
            echo "</div>";
        }
        else
        {
            //----------------------------------------------------------------------
            // Detail
            //----------------------------------------------------------------------
            echo Acc_Account_Ledger::HtmlTableHeader();
            echo '<table  style="width:100%;margin-left:0%">';
            foreach ($a_poste as $poste_id )
            {
                $Poste=new Acc_Account_Ledger ($cn,$poste_id['pcm_val']);
                $Poste->load();
                $Poste->get_row_date( $_GET['from_periode'], $_GET['to_periode'],$_GET['ople']);
                if ( empty($Poste->row)) continue;
                echo '<tr><td  class="mtitle" style="width:auto" colspan="6"><h2 class="title">'. $poste_id['pcm_val'].' '.h($Poste->label).'</h2></td></tr>';

                $detail=$Poste->row[0];

                $old=array();

                foreach ($Poste->row as $detail)
                {
                    /* avoid duplicates */
                    if ( in_array($detail['jr_id'],$old) == TRUE ) continue;
                    $old[]=$detail['jr_id'];
                    echo tr(td("Journal :".$detail['jrn_def_name'],''),'style="width:auto" colspan="6"');
                    echo '<tr><td class="mtitle" style="width:auto" colspan="6">'. $detail['j_date'].' '.$detail['jr_internal'].' '.hb($detail['description']).' '.hi($detail['jr_pj_number']).'</td></tr>';

                    $op=new Acc_Operation($cn);
                    $op->poste=$poste_id['pcm_val'];

                    $op->jr_id=$detail['jr_id'];
                    echo $op->display_jrnx_detail(1);
                }
            }
            echo '</table>';
            echo Acc_Account_Ledger::HtmlTableHeader();
        }

        exit;
    }
}
?>
