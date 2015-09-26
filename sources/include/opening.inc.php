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
/**\file
 * \brief The opening of the exercices. it takes the saldo of the
 * choosen foolder / exercice and import it as a misc operation in the
 * current folder

 *
 *
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';

$p_mesg="";

$sa = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : '';
$g_user->Check();

require_once NOALYSS_INCLUDE.'/user_menu.php';

// Correct (last step)
if (isset($_POST['correct']))
{
	$ledger = new Acc_Ledger($cn, $_REQUEST['p_jrn']);
	require_once NOALYSS_INCLUDE.'/operation_ods_new.inc.php';
	return;
}

// confirm before saving
if ( isset($_POST['summary']))
{
	try {
		$ledger = new Acc_Ledger($cn, $_REQUEST['p_jrn']);
		$ledger->with_concerned=false;
			$ledger->verify($_POST);
			require_once NOALYSS_INCLUDE.'/operation_ods_confirm.inc.php';
	} catch (Exception $e)
	{
		echo alert($e->getMessage());
		require('operation_ods_new.inc.php');

	}
	return;
}

// record
if (isset($_POST['save']))
{
	$array = $_POST;
	$ledger = new Acc_Ledger($cn, $_REQUEST['p_jrn']);
	$ledger->with_concerned=false;
	try
	{
		$ledger->save($array);
		$jr_id = $cn->get_value('select jr_id from jrn where jr_internal=$1', array($ledger->internal));

		echo '<h2> Op&eacute;ration enregistr&eacute;e  Piece ' . h($ledger->pj) . '</h2>';
		if (strcmp($ledger->pj, $_POST['e_pj']) != 0)
		{
			echo '<h3 class="notice">' . _('Attention numéro pièce existante, elle a du être adaptée') . '</h3>';
		}
		printf('<a class="detail" style="display:inline" href="javascript:modifyOperation(%d,%d)">%s</a><hr>', $jr_id, dossier::id(), $ledger->internal);

		// show feedback
		echo '<div id="jrn_name_div">'; echo '<h2 id="jrn_name" style="display:inline">' . $ledger->get_name() . '</h2>'; echo '</div>';
		echo $ledger->confirm($_POST, true);
	}
	catch (Exception $e)
	{
		require('operation_ods_new.inc.php');
		alert($e->getMessage());
	}
	return;
}


/* --------------------------------------------------
 * step 1 if nothing is asked we show the available folders
 */
if ($sa == '')
{
	echo '<div class="content">';
        
	echo '<h1 class="legend"> Etape 1 </h1>';

	echo 'Choisissez le dossier où sont les soldes à importer';
	$avail = $g_user->get_available_folder();

	if (empty($avail))
	{
		echo '*** Aucun dossier ***';
		return;
	}
	echo '<form class="print" method="post">';
	echo HtmlInput::hidden('ac', $_REQUEST['ac']);
	echo HtmlInput::hidden('sa', 'step2');
	echo dossier::hidden();
	$wAvail = new ISelect();
	/* compute select list */
	$array = array();
	$i = 0;
	foreach ($avail as $r)
	{
		$array[$i]['value'] = $r['dos_id'];
		$array[$i]['label'] = $r['dos_name'];
		$i++;
	}

	$wAvail->value = $array;
	echo 'Choix du dossier :' . $wAvail->input('f');
	echo HtmlInput::submit('ok', 'Continuer');

	echo '</form>';
	echo '</div>';
	echo '</div>';
	return;
}
/* --------------------------------------------------
 * Step 2 choose now the exercice of this folder
 */
$back = 'do.php?ac=' . $_REQUEST['ac'] . '&' . dossier::get();
if ($sa == 'step2')
{
	echo '<div class="content">' .
	'<div><h1 class="legend">Etape 2</h1>' .
	'<h2 class="info">' . dossier::name($_REQUEST['f']) . '</h2>' .
	'<form class="print" method="post">' .
	' Choisissez l\'exercice du dossier ';
	echo dossier::hidden();
	echo HtmlInput::hidden('ac', $_REQUEST['ac']);
	echo HtmlInput::hidden('sa', 'step3');
	echo HtmlInput::hidden('f', $_REQUEST['f']);
	$cn = new Database($_REQUEST['f']);
	$periode = $cn->make_array("select distinct p_exercice,p_exercice from parm_periode order by p_exercice");
	$w = new ISelect();
	$w->table = 0;
	$w->label = 'Periode';
	$w->readonly = false;
	$w->value = $periode;
	$w->name = "p_periode";
	echo 'P&eacute;riode : ' . $w->input();
	echo HtmlInput::submit('ok', 'Continuer');
	echo dossier::hidden();
	echo "</form>";
	echo HtmlInput::button_anchor('Retour', $back);
	exit(0);
}
/* --------------------------------------------------
 * select the ledger where we will import the data
 */
if ($sa == 'step3')
{
	echo '<div class="content">' .
	'<div><h1 class="legend">Etape 3</h1>' .
	'<h2 class="info">' . dossier::name($_REQUEST['f']) . '</h2>' .
	'<form class="print" method="post">' .
	' Choisissez le journal qui contiendra l\'opération d\'ouverture ';
	echo dossier::hidden();
	echo HtmlInput::hidden('p_action', 'ouv');
	echo HtmlInput::hidden('sa', 'step4');
	echo HtmlInput::hidden('f', $_REQUEST['f']);
	echo HtmlInput::hidden('p_periode', $_REQUEST['p_periode']);
	$wLedger = new ISelect();
	$g_user = new User(new Database(dossier::id()));
	$avail = $g_user->get_ledger('ODS');
	/* compute select list */
	$array = array();
	$i = 0;
	foreach ($avail as $r)
	{
		$array[$i]['value'] = $r['jrn_def_id'];
		$array[$i]['label'] = $r['jrn_def_name'];
		$i++;
	}
	$wLedger->value = $array;
	echo $wLedger->input('p_jrn');
	echo HtmlInput::submit('ok', 'Continuer');
	echo HtmlInput::hidden('ac', $_REQUEST['ac']);
	echo dossier::hidden();
	echo "</form>";
	echo HtmlInput::button_anchor('Retour', $back . '&sa=step2&f=' . $_REQUEST['f']);
	exit(0);
}
/* --------------------------------------------------
 * Step 4 we import data from the selected folder and year and
 * transform it into a misc operation
 */
if ($sa == 'step4')
{
	echo '<div class="content">';
	echo '<div><h1 class="legend"> Dernière étape</h1>';
	$cn_target = new Database($_REQUEST['f']);
	$saldo = new Acc_Ledger($cn_target, 0);
	$array = $saldo->get_saldo_exercice($_REQUEST['p_periode']);
	/*  we need to transform the array into a Acc_Ledger array */
	$result = array();
	$result['desc'] = 'Ecriture d\'ouverture';
	$result['nb_item'] = sizeof($array);
	$result['p_jrn'] = $_REQUEST['p_jrn'];
	$idx = 0;

	foreach ($array as $row)
	{
		$qcode = 'qc_' . $idx;
		$poste = 'poste' . $idx;
		$amount = 'amount' . $idx;
		$ck = 'ck' . $idx;
		$result[$qcode] = $row['j_qcode'];
		if (trim($row['j_qcode']) == '')
			$result[$poste] = $row['j_poste'];
		$result[$amount] = abs($row['solde']);
		if ($row['solde'] > 0)
			$result[$ck] = 'on';
		$idx++;
	}
	$cn = new Database(dossier::id());

	$jrn = new Acc_Ledger($cn, $_REQUEST['p_jrn']);

	echo '<form class="print" method="post">';
	echo HtmlInput::hidden('ac', $_REQUEST['ac']);
	echo HtmlInput::hidden('sa', 'step5');
	echo HtmlInput::hidden('f', $_REQUEST['f']);
	echo HtmlInput::hidden('p_periode',$_REQUEST['p_periode']);
	echo dossier::hidden();
	echo HtmlInput::hidden('p_jrn', $_REQUEST['p_jrn']);
	echo $jrn->input($result, 0);
	echo '<hr>';
	echo '<h2 class="notice">Ne corrigez pas encore, cliquez continuer pour passer à l\'étape suivante</h2>';
	echo HtmlInput::submit('correct_it', 'Continuer');
	echo '</form>';
	echo HtmlInput::button_anchor('Retour', $back);

	echo '</div>';
}
// if sa=step4 then record it
//
if ($_REQUEST['sa'] == 'step5')
{
	$ledger = new Acc_Ledger($cn, $_REQUEST['p_jrn']);
	require_once NOALYSS_INCLUDE.'/operation_ods_new.inc.php';
}

