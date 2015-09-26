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
 * \brief print the all the operation reconciled or not, with or without the same amount
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once  NOALYSS_INCLUDE.'/class_acc_reconciliation.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
global $g_user;

/**
 *@file
 */
$aledger=$g_user->get_ledger('ALL',3);
echo '<div class="noprint">';
echo '<div class="content">';
$rjrn='';
$radio=new IRadio('choice');
$choice=(isset($_GET['choice']))?$_GET['choice']:0;
$r_jrn=(isset($_GET['r_jrn']))?$_GET['r_jrn']:'';
echo '<form method="GET">';
echo dossier::hidden().HtmlInput::hidden('ac',$_GET['ac']).HtmlInput::hidden('type','rec');
echo _('Filtre par journal');
HtmlInput::button_choice_ledger(array('div'=>'','type'=>'ALL','all_type'=>1));
echo '<br/>';
/*
 * Limit by date, default current exercice
 */
list($start,$end)=$g_user->get_limit_current_exercice();
$dstart=new IDate('p_start');
$dstart->value=(isset($_REQUEST['p_start']))?$_REQUEST['p_start']:$start;

$dend=new IDate('p_end');
$dend->value=(isset($_REQUEST['p_end']))?$_REQUEST['p_end']:$end;

echo "Opérations entre ".$dstart->input()." jusque ".$dend->input();
echo '<ol style="list-style-type:none;">';

$radio->selected=($choice==0)?true:false;
$radio->value=0;
echo '<li>'.$radio->input()._('Opérations rapprochées').'</li>';

$radio->selected=($choice==1)?true:false;
$radio->value=1;
echo '<li>'.$radio->input()._('Opérations rapprochées avec des montants différents').'</li>';

$radio->selected=($choice==2)?true:false;
$radio->value=2;
echo '<li>'.$radio->input()._('Opérations rapprochées avec des montants identiques').'</li>';

$radio->selected=($choice==3)?true:false;
$radio->value=3;
echo '<li>'.$radio->input()._('Opérations non rapprochées').'</li>';

echo '</ol>';




echo HtmlInput::submit('vis',_('Visualisation'));
echo '</form>';
echo '<hr>';
echo '</div>';
echo '</div>';
echo '<div class="content">';
if ( ! isset($_GET['vis'])) return;
$acc_reconciliation=new Acc_Reconciliation($cn);
$acc_reconciliation->a_jrn=$r_jrn;
$acc_reconciliation->start_day=$dstart->value;
$acc_reconciliation->end_day=$dend->value;

$array=$acc_reconciliation->get_data($choice);

$gDossier=Dossier::id();
?>
<form method="get" action="export.php">
    <?php echo HtmlInput::get_to_hidden(array('ac','gDossier','p_end','p_start','choice','r_jrn'));
    echo HtmlInput::hidden('act','CSV:Reconciliation');
    echo HtmlInput::submit("csv_bt", "Export CSV");
    ?>
</form>
<?php
require_once NOALYSS_INCLUDE.'/template/impress_reconciliation.php';
return;