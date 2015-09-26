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
 *
 *
 * \brief reconcile operation
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
global $g_failed,$g_succeed;
require_once NOALYSS_INCLUDE.'/class_acc_ledger_fin.php';
bcscale(2);
echo '<div class="content">';
$Ledger = new Acc_Ledger_Fin($cn, 0);
if (!isset($_REQUEST['p_jrn']))
{
	$a = $Ledger->get_first('fin');
	$Ledger->id = $a['jrn_def_id'];
}
else
	$Ledger->id = $_REQUEST['p_jrn'];
$jrn_priv = $g_user->get_ledger_access($Ledger->id);
if (isset($_GET["p_jrn"]) && $jrn_priv == "X")
{
	NoAccess();
	return;
}
//-------------------------
// save
//-------------------------
if (isset($_POST['save']))
{
	if (trim($_POST['ext']) != '' && isset($_POST['op']))
	{
		$array = $_POST['op'];
		$tot = 0;
		$cn->start();
		for ($i = 0; $i < count($array); $i++)
		{
			$cn->exec_sql('update jrn set jr_pj_number=$1 where jr_id=$2', array($_POST['ext'], $array[$i]));
			$tot = bcadd($tot, $cn->get_value('select qf_amount from quant_fin where jr_id=$1', array($array[$i])));
		}
		$diff = bcsub($_POST['end_extrait'], $_POST['start_extrait']);
		if ($diff != 0 && $diff != $tot)
		{
			$remain=bcsub($tot,$diff);
			$cn->rollback();
			alert("D'après l'extrait il y aurait du avoir un montant de $diff à rapprocher alors qu'il y a $tot rapprochés, mise à jour annulée, la différence est de $remain");
			echo '<div class="error">';
			echo '<p>'.$g_failed._("D'après l'extrait il y aurait du avoir un montant de $diff à rapprocher alors qu'il y a $tot rapprochés, la différence est de $remain <br>mise à jour annulée").'</p>';
			echo '</div>';
		}
		else
		  {
		    echo '<div class="content">'.$g_succeed.' Mise à jour extrait '.$_POST['ext'].'</div>';
		  }

		$cn->commit();
	}
}
//-------------------------
// show the operation of this ledger
// without receipt number
//-------------------------
echo '<div class="content">';
echo '<form method="get">';
echo HtmlInput::get_to_hidden(array('gDossier', 'ledger_type', 'ac', 'sa'));
$wLedger = $Ledger->select_ledger('FIN', 3);
if ($wLedger == null)
	exit('Pas de journal disponible');
echo '<div id="jrn_name_div">';
echo '<h2 id="jrn_name" style="display:inline">' . $Ledger->get_name() . '</h2>';
echo '</div>';
$wLedger->javascript = "onchange='this.form.submit()';";
echo $wLedger->input();
echo HtmlInput::submit('ref', 'Rafraîchir');
echo '</form>';
echo '<span id="bkname" style="display:block">' . hb(h($Ledger->get_bank_name())) . '</span>';

echo '<form method="post" id="rec1">';

echo dossier::hidden();
echo HtmlInput::get_to_hidden(array('sa', 'p_action', 'p_jrn'));

$operation = $cn->get_array("select jr_id,jr_internal,
								jr_comment,
								to_char(jr_date,'DD.MM.YYYY') as fmt_date,
								jr_montant,
								to_char(jr_date,'YYYYMMDD') as raw_date
                              from jrn where jr_def_id=$1 and (jr_pj_number is null or jr_pj_number='') order by jr_date", array($Ledger->id));

echo '<p>';
$iextrait = new IText('ext');
if ( isset ($_POST['ext'])) $iextrait->value=$_POST['ext']; else $iextrait->value = $Ledger->guess_pj();
$nstart_extrait = new INum('start_extrait');
if( isset($_POST['start_extrait'])) $nstart_extrait->value=$_POST['start_extrait'];
$nend_extrait = new INum('end_extrait');
if( isset($_POST['end_extrait'])) $nend_extrait->value=$_POST['end_extrait'];

echo "Extrait / relevé :" . $iextrait->input();
echo 'solde Début' . $nstart_extrait->input();
echo 'solde Fin' . $nend_extrait->input();
echo IButton::tooggle_checkbox('rec1');
echo '</p>';
echo '<p>';
echo _('Filtre').HtmlInput::infobulle(25);
echo HtmlInput::filter_table("t_rec_bk", "0,1,2,3","1");
echo '</p>';
echo HtmlInput::submit('save', 'Mettre à jour le n° de relevé bancaire');
echo '<span style="display:block">';


	echo '</span>';
echo '<table id="t_rec_bk" class="sortable" style="width:90%;margin-left:5%">';
$r ='<th class=" sorttable_sorted_reverse">'.'Date '.HtmlInput::infobulle(17).'<span id="sorttable_sortrevind">&nbsp;&blacktriangle;</span>'.'</th>';
$r.=th('Libellé');
$r.=th('N° interne');
$r.=th('Montant', ' style="text-align:right"');
$r.=th('Selection', ' style="text-align:center" ');
echo tr($r);
$iradio = new ICheckBox('op[]');
$tot_not_reconcilied = 0;
$diff = 0;
for ($i = 0; $i < count($operation); $i++)
{
	$row = $operation[$i];
	$r = '';
	$js = HtmlInput::detail_op($row['jr_id'], $row['jr_internal']);
	$r.='<td sorttable_customkey="'.$row['raw_date'].'">'.$row['fmt_date'].'</td>';
	$r.=td($row['jr_comment']);
	$r.=td($js);
	$amount=$cn->get_value('select qf_amount from quant_fin where jr_id=$1', array($row['jr_id']));
	$r.='<td class="num" class="sorttable_numeric" sorttable_customkey="'.$amount.'" style="text-align:right">'.nbm ($amount).'</td>';

	$diff=bcadd($diff,$amount);
	$tot_not_reconcilied+=$row['jr_montant'];
	$iradio->value = $row['jr_id'];
	$iradio->selected=false;
	if (isset($_POST['op']))
	{
		for ($x=0;$x<count($_POST['op']);$x++)
		{
			if ($row['jr_id']==$_POST['op'][$x])
			{
				$iradio->selected=true;
				break;
			}
		}
	}
	$r.=td(HtmlInput::hidden('jrid['.$i.']', $row['jr_id']) . $iradio->input(), ' style="text-align:center" ');
	if ($i % 2 == 0)
		echo tr($r, ' class="odd" ');
	else
		echo tr($r,' class="even" ');
}
echo '</table>';
$bk_card = new Fiche($cn);
$bk_card->id = $Ledger->get_bank();
$filter_year = "  j_tech_per in (select p_id from parm_periode where  p_exercice='" . $g_user->get_exercice() . "')";

/*  get saldo for not reconcilied operations  */
$saldo_not_reconcilied = $bk_card->get_solde_detail($filter_year . " and j_grpt in (select jr_grpt_id from jrn where trim(jr_pj_number) ='' or jr_pj_number is null)");

/*  get saldo for reconcilied operation  */
$saldo_reconcilied = $bk_card->get_solde_detail($filter_year . " and j_grpt in (select jr_grpt_id from jrn where trim(jr_pj_number) != '' and jr_pj_number is not null)");

/* solde compte */
$saldo = $bk_card->get_solde_detail($filter_year);

echo '<table>';
echo '<tr>';
echo td("Solde compte  ");
echo td(nbm(bcsub($saldo['debit'] , $saldo['credit'])), ' style="text-align:right"');
echo '</tr>';

echo '<tr>';
echo td("Solde non rapproché ");
echo td(nbm (bcsub($saldo_not_reconcilied['debit'], $saldo_not_reconcilied['credit'])), ' style="text-align:right"');
echo '</tr>';

echo '<tr>';
echo td("Solde  rapproché ");
echo td(nbm(bcsub($saldo_reconcilied['debit'] , $saldo_reconcilied['credit'])), ' style="text-align:right"');
echo '</tr>';


echo '<tr>';
echo td("Total montant ");
echo td(nbm ($tot_not_reconcilied), ' style="text-align:right"');
echo '</tr>';

echo '</table>';

echo HtmlInput::submit('save', 'Mettre à jour le n° de relevé bancaire');
echo '</form>';
echo '</div>';
return;
?>
