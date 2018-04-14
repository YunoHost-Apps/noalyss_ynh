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

/**
 * \file
 *
 *
 * \brief
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_acc_ledger_purchase.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_fin.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_sold.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
global $g_user,$cn;
$p_array = $_GET;
$ledger_type=HtmlInput::default_value_get("ledger_type", 'ALL');
switch($ledger_type)
{
        case 'ACH':
                $Ledger = new Acc_Ledger_Purchase($cn, 0);
                $ask_pay=1;
                break;
        case 'ODS':
                $Ledger=new Acc_Ledger($cn,0);
                $ask_pay=0;
                $p_array['ledger_type']='ODS';
                $Ledger->type='ODS';
                break;
        case 'ALL':
                $Ledger=new Acc_Ledger($cn,0);
                $ask_pay=0;
                $p_array['ledger_type']='ALL';
                $Ledger->type='ALL';
                break;
        case 'VEN':
                $Ledger=new Acc_Ledger_Sold($cn,0);
                $ask_pay=1;
                break;
        case 'FIN':
                $Ledger=new Acc_Ledger_Fin($cn,0);
                $ask_pay=0;
                break;

}
echo '<div class="content">';
// Check privilege
$p_jrn=HtmlInput::default_value_request("p_jrn", -1);
if (isset($_REQUEST['p_jrn']) &&
		$g_user->check_jrn($_REQUEST['p_jrn']) == 'X')
{

	NoAccess();
	exit - 1;
}

$Ledger->id = $p_jrn;
echo $Ledger->display_search_form();
//------------------------------
// UPdate the payment
//------------------------------
if (isset($_GET ['paid']))
{
	$Ledger->update_paid($_GET);
}


$msg="";
/* by default we should use the default period */
if (!isset($p_array['date_start']))
{
	$period = $g_user->get_periode();
	$per = new Periode($cn, $period);
	list($date_start, $date_end) = $per->get_date_limit();
	$p_array['date_start'] = $date_start;
	$p_array['date_end'] = $date_end;
	$msg='<h2 class="info2">'._("Période ").$date_start._(" au ").$date_end.'</h2>';
}
else
{
	$msg='<h2 class="info2">'._("Période ").$_GET['date_start']._(" au ").$_GET['date_end'].'</h2>';

}
/*  compute the sql stmt */
list($sql, $where) = $Ledger->build_search_sql($p_array);

$max_line = $cn->count_sql($sql);

$step = $_SESSION['g_pagesize'];
$page = (isset($_GET['offset'])) ? $_GET['page'] : 1;
$offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$bar = navigation_bar($offset, $max_line, $step, $page);

echo $msg;
echo '<form method="GET" id="fpaida" class="print">';
echo HtmlInput::hidden("ac", $_REQUEST['ac']);
echo HtmlInput::hidden('ledger_type',$ledger_type);
echo dossier::hidden();
echo $bar;

list($count, $html) = $Ledger->list_operation($sql, $offset, $ask_pay);
echo $html;
echo $bar;
$r = HtmlInput::get_to_hidden(array('l', 'date_start', 'date_end', 'desc', 'amount_min', 'amount_max', 'qcode', 'accounting', 'unpaid', 'gDossier', 'ledger_type', 'p_action'));
if (isset($_GET['r_jrn']))
{
	foreach ($_GET['r_jrn'] as $k => $v)
		$r.=HtmlInput::hidden('r_jrn[' . $k . ']', $v);
}
echo $r;

if ($ask_pay)
	echo '<p>' . HtmlInput::submit('paid', _('Mise à jour paiement')) . IButton::select_checkbox('fpaida') . IButton::unselect_checkbox('fpaida') . '</p>';

echo '</form>';
/*
 * Export to csv
 */
$r = HtmlInput::get_to_hidden(array('l', 'date_paid_start','date_paid_end',
    'date_start', 'date_end', 'desc', 'amount_min', 'amount_max', 'qcode', 
    'accounting', 'unpaid', 'gDossier', 'ledger_type', 'p_action'));
if (isset($_GET['r_jrn']))
{
	foreach ($_GET['r_jrn'] as $k => $v)
		$r.=HtmlInput::hidden('r_jrn[' . $k . ']', $v);
}
echo '<form action="export.php" method="get">';
echo $r;
echo HtmlInput::hidden('act', 'CSV:histo');
echo HtmlInput::submit('viewsearch', _('Export vers CSV'));

echo '</form>';

echo '</div>';
return;
?>
