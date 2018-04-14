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
 * \brief ask for Printing the ledger (pdf,html)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once NOALYSS_INCLUDE.'/class_exercice.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
load_all_script();
$gDossier = dossier::id();
global $g_user;
//-----------------------------------------------------
// Show the jrn and date
//-----------------------------------------------------
require_once NOALYSS_INCLUDE.'/class_database.php';

if ($g_user->Admin() == 0 && $g_user->is_local_admin() == 0)
{
	$sql = "select jrn_def_id,jrn_def_name
         from jrn_def join jrn_type on jrn_def_type=jrn_type_id
         join user_sec_jrn on uj_jrn_id=jrn_def_id
         where
         uj_login='$g_user->login'
         and uj_priv in ('R','W')
		 order by jrn_def_name
         ";
	$ret = $cn->make_array($sql);
}
else
{
	$ret = $cn->make_array("select jrn_def_id,jrn_def_name
                         from jrn_def join jrn_type on jrn_def_type=jrn_type_id
						 order by jrn_def_name
						 ");
}
// Count the forbidden journaux
$NoPriv = $cn->count_sql("select jrn_def_id,jrn_def_name,jrn_def_class_deb,jrn_def_class_cred,jrn_type_id,jrn_desc,uj_priv,
                       jrn_deb_max_line,jrn_cred_max_line
                       from jrn_def join jrn_type on jrn_def_type=jrn_type_id
                       join  user_sec_jrn on uj_jrn_id=jrn_def_id
                       where
                       uj_login='$g_user->id'
                       and uj_priv ='X'
                       ");
/*
 * Show all the available ledgers
 */
$a = count($ret);
$all = array('value' => 0, 'label' => 'Tous les journaux disponibles');
$ret[$a] = $all;
if (count($ret) < 1)
	NoAccess();
$exercice = (isset($_GET['exercice'])) ? $_GET['exercice'] : $g_user->get_exercice();

//-----------------------------------------------------
// Form
//-----------------------------------------------------
echo '<div class="content">';
/*
 * Let you change the exercice
 */
echo '<form method="GET">';
echo '<fieldset><legend>' . _('Exercice') . '</legend>';
;
echo _('Choisissez un autre exercice').' :';
$ex = new Exercice($cn);
$wex = $ex->select('exercice', $exercice, ' onchange="submit(this)"');
echo $wex->input();
echo dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac', 'type'));
echo '</fieldset>';
echo '</form>';
?>
<?php


echo '<FORM METHOD="GET">' . dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac', 'type'));
echo HtmlInput::hidden('type', 'jrn');
echo HtmlInput::get_to_hidden(array('exercice'));
echo '<TABLE  ><TR>';
$w = new ISelect();
$w->table = 1;
$label = "Choisissez le journal";
$w->selected = (isset($_GET['jrn_id'])) ? $_GET['jrn_id'] : '';
print td($label) . $w->input("jrn_id", $ret);
print '</TR>';
print '<TR>';
// filter on the current year
$filter_year = " where p_exercice='" . sql_string($exercice) . "'";

$periode_start = $cn->make_array("select p_id,to_char(p_start,'DD-MM-YYYY') from parm_periode $filter_year order by p_start,p_end");
$w->selected = (isset($_GET['from_periode'])) ? $_GET['from_periode'] : '';
print td('Depuis') . $w->input('from_periode', $periode_start);
print '</TR>';
print '<TR>';

$periode_end = $cn->make_array("select p_id,to_char(p_end,'DD-MM-YYYY') from parm_periode $filter_year order by p_start,p_end");
$w->selected = (isset($_GET['to_periode'])) ? $_GET['to_periode'] : '';
print td('Jusque ') . $w->input('to_periode', $periode_end);
print "</TR><TR>";
$a = array(
	array('value' => 0, 'label' => 'Ecriture comptable'),
	array('value' => 1, 'label' => 'Liste opérations'),
	array('value' => 2, 'label' => 'Avec Détails opérations ')
);
$w->selected = 1;
print '</TR>';
print '<TR>';
$w->selected = (isset($_GET['p_simple'])) ? $_GET['p_simple'] : '';
echo '<td>Style d\'impression '.HtmlInput::infobulle(32).'</td>' . $w->input('p_simple', $a);
print "</TR>";
echo '</TABLE>';
print HtmlInput::submit('bt_html', 'Visualisation');

echo '</FORM>';
echo '<hr>';


//-----------------------------------------------------
// If print is asked
// First time in html
// after in pdf or cvs
//-----------------------------------------------------
if (isset($_REQUEST['bt_html']))
{
	require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';

	$d = var_export($_GET, true);
	$Jrn = new Acc_Ledger($cn, $_GET['jrn_id']);
	$Jrn->get_name();
	switch ($_GET['p_simple'])
	{
		case "0":
			$Row = $Jrn->get_row($_GET['from_periode'], $_GET['to_periode']);
			break;
		case "1":
			$Row = $Jrn->get_rowSimple($_GET['from_periode'], $_GET['to_periode']);
			break;
		case "2":
			$Row = $Jrn->get_rowSimple($_GET['from_periode'], $_GET['to_periode']);
			break;
		default:
			var_dump($_GET['p_simple']);
			die(__FILE__ . ":" . __LINE__ . " error unknown style ");
	}
	$rep = "";
	$hid = new IHidden();
	echo '<div class="content">';
	echo '<h2 class="info">' . h($Jrn->name) . '</h2>';
	echo "<table>";
	echo '<TR>';
        echo '<TD><form method="GET" ACTION="?">' . dossier::hidden() .
        $hid->input("type", "jrn") . $hid->input('p_action', 'impress') . "</form></TD>";

        echo '<TD><form method="GET" ACTION="export.php">' . dossier::hidden() .
        HtmlInput::submit('bt_pdf', "Export PDF") .
        HtmlInput::hidden('act', 'PDF:ledger') .
        $hid->input("type", "jrn") .
        $hid->input("jrn_id", $Jrn->id) .
        $hid->input("from_periode", $_GET['from_periode']) .
        $hid->input("to_periode", $_GET['to_periode']);
        echo $hid->input("p_simple", $_GET['p_simple']);
        echo HtmlInput::get_to_hidden(array('ac', 'type'));
        echo "</form></TD>";

        echo '<TD><form method="GET" ACTION="export.php">' . dossier::hidden() .
        HtmlInput::submit('bt_csv', "Export CSV") .
        HtmlInput::hidden('act', 'CSV:ledger') .
        $hid->input("type", "jrn") .
        $hid->input("jrn_id", $Jrn->id) .
        $hid->input("from_periode", $_GET['from_periode']) .
        $hid->input("to_periode", $_GET['to_periode']);
        echo $hid->input("p_simple", $_GET['p_simple']);
        echo HtmlInput::get_to_hidden(array('ac', 'type'));
        echo "</form></TD>";

	echo '<td style="vertical-align:top">';
	echo HtmlInput::print_window();
	echo '</td>';
	echo "</TR>";

	echo "</table>";
	if (count($Jrn->row) == 0
			&& $Row == null)
		exit;


	/////////////////////////////////////////////////////////////////////////////////////
	// Ecriture comptable
	/////////////////////////////////////////////////////////////////////////////////////
	if ($_GET['p_simple'] == 0)
	{
		echo '<TABLE class="result">';
		// detailled printing
		//---
		foreach ($Jrn->row as $op)
		{
			$class = "";
			if ($op['j_date'] != '')
			{
				$class = "odd";
			}

			echo "<TR  class=\"$class\">";

			echo "<TD>" . $op['j_date'] . "</TD>";
			echo "<TD >" . $op['jr_pj_number'] . "</TD>";


			if ($op['internal'] != '')
				echo "<TD>" . HtmlInput::detail_op($op['jr_id'], $op['internal']) . "</TD>";
			else
				echo td();

			echo "<TD >" . $op['poste'] . "</TD>" .
			"<TD  >" . $op['description'] . "</TD>" .
			"<TD   style=\"text-align:right\">" . nbm($op['deb_montant']) . "</TD>" .
			"<TD style=\"text-align:right\">" . nbm($op['cred_montant']) . "</TD>" .
			"</TR>";
		}// end loop
		echo "</table>";
		// show the saldo

		$solde = $Jrn->get_solde($_GET['from_periode'], $_GET['to_periode']);
		echo "solde d&eacute;biteur:" . $solde[0] . "<br>";
		echo "solde cr&eacute;diteur:" . $solde[1];
	} // if
	/////////////////////////////////////////////////////////////////////////////////////
	// Liste opérations
	/////////////////////////////////////////////////////////////////////////////////////
	elseif ($_GET['p_simple'] == 1)
	{
            if ( $Jrn->get_type() != 'ACH' && $Jrn->get_type() != 'VEN')
            {
		// Simple printing
		//---
		echo '<TABLE class="result">';
		echo "<TR>" .
		"<th> operation </td>" .
		"<th>Date</th>" .
		"<th> n° de pièce </th>" .
		"<th>internal</th>" .
		th('Tiers') .
		"<th>Commentaire</th>" .
		"<th>Total opération</th>" .
		"</TR>";
		// set a filter for the FIN
		$i = 0;$tot_amount=0;
                bcscale(2);
		foreach ($Row as $line)
		{
			$i++;
			$class = ($i % 2 == 0) ? ' class="even" ' : ' class="odd" ';
			echo "<tr $class>";
			echo "<TD>" . $line['num'] . "</TD>";
			echo "<TD>" . $line['date'] . "</TD>";
			echo "<TD>" . h($line['jr_pj_number']) . "</TD>";
			echo "<TD>" . HtmlInput::detail_op($line['jr_id'], $line['jr_internal']) . "</TD>";
			$tiers = $Jrn->get_tiers($line['jrn_def_type'], $line['jr_id']);
			echo td($tiers);
			echo "<TD>" . h($line['comment']) . "</TD>";


			//	  echo "<TD>".$line['pj']."</TD>";
			// If the ledger is financial :
			// the credit must be negative and written in red
			// Get the jrn type
			if ($line['jrn_def_type'] == 'FIN')
			{
				$positive = $cn->get_value("select qf_amount from quant_fin where jr_id=$1", array($line['jr_id']));
				if ($cn->count() == 0)
					$positive = 1;
				else
					$positive = ($positive > 0) ? 1 : 0;

				echo "<TD align=\"right\">";
				echo ( $positive == 0 ) ? "<font color=\"red\">  - " . nbm($line['montant']) . "</font>" : nbm($line['montant']);
				echo "</TD>";
                                if ( $positive == 1 ) {
                                    $tot_amount=bcadd($tot_amount,$line['montant']);
                                } else {
                                    $tot_amount=bcsub($tot_amount,$line['montant']);
                                }
			}
			else
			{
				echo "<TD align=\"right\">" . nbm($line['montant']) . "</TD>";
                                $tot_amount=bcadd($tot_amount,$line['montant']);
			}

			echo "</tr>";
		}
                echo '<tr class="highlight">';
                echo '<td>'._('Totaux').'</td>';
                echo td().td().td().td().td();
                echo '<td class="num">'.nbm($tot_amount).'</td>';
                echo '</tr>';
		echo "</table>";
            } else {
                /*
                 * Ledger ACH or VEN
                 */
                $own=new Own($cn);
                require_once NOALYSS_INCLUDE.'/template/print_ledger_simple.php';
                
            }
	}
	/////////////////////////////////////////////////////////////////////////////////////
	// Détaillé
	/////////////////////////////////////////////////////////////////////////////////////
	elseif ($_GET['p_simple'] == 2)
	{
		foreach ($Row as $line)
		{
			echo '<div style="margin-top:2px;margin-bottom:10px;border:solid 1px black">';
			$class = ' class="odd" style="font-stretch: expanded;font-size:1em;"';
			echo '<table class="result" style="font-weight: bolder;font-variant: small-caps;width:100%;">';
			echo "<tr $class>";
			echo '<TD style="width:5%">' . $line['date'] . "</TD>";
			echo '<TD style="width:10%">' . h($line['jr_pj_number']) . "</TD>";
			echo '<TD style="width:5%">' . HtmlInput::detail_op($line['jr_id'], $line['jr_internal']) . "</TD>";
			$tiers = $Jrn->get_tiers($line['jrn_def_type'], $line['jr_id']);
			$ledger_name = $cn->get_value("select jrn_def_name from jrn_def where jrn_def_id=$1", array($line['jr_def_id']));
			echo '<TD style="width:20%">' . h($ledger_name) . ' </td>';
			echo '<TD style="width:20%">' . h($tiers) . ' </td>';
			echo '<TD style="width:30%">' . h($line['comment']) . "</TD>";
			echo '<TD style="text-align:right">';
			if ($line['jrn_def_type'] == 'FIN')
			{
				$positive = $cn->get_value("select qf_amount from quant_fin where jr_id=$1", array($line['jr_id']));
				if ($cn->count() == 0)
					$positive = 1;
				else
					$positive = ($positive > 0) ? 1 : 0;

				echo ( $positive == 0 ) ? "<font color=\"red\">  - " . nbm($line['montant']) . "</font>" : nbm($line['montant']);
			}
			else
			{
				if ( isset ($line['TVAC'])) {
                                    echo  ( nbm($line['TVAC'])  < 0 ) ? "<font color=\"red\">  - " . nbm($line['TVAC']) . "</font>" : nbm($line['TVAC']);
                                } else
                                {
                                    echo  nbm($line['montant']) ;
                                }
			}
			echo  "</TD>";
			echo "</tr>";
			echo '</table>';
			//////////////////////////////////////////////////////////////////////////////////////////////////////
			// Add detail for each operation
			//////////////////////////////////////////////////////////////////////////////////////////////////////
			$op = new Acc_Operation($cn);
			$op->jr_id = $line['jr_id'];
			$op->get();
			$obj = $op->get_quant();
			switch ($obj->signature)
			{
				case 'FIN':
					require 'template/operation_detail_fin.php';
					break;
				case 'ACH':
					require 'template/operation_detail_ach.php';
					break;
				case 'VEN':
					require 'template/operation_detail_ven.php';
					break;
				case 'ODS':
					require 'template/operation_detail_misc.php';
					break;
				default:
					die("unknown type of ledger");
					break;
			}
			echo '</div>';
			//echo '<div style="display:block;height:15px"></div>';
		} // end loop
	}

	echo "</div>";
	exit;
}

echo '</div>';
?>
