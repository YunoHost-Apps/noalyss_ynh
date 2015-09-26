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
 * \brief printing of category of card  : balance, historic
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/class_lettering.php';

$gDossier = dossier::id();
$cn = new Database($gDossier);
global $g_user, $g_failed;
;

/**
 * Show first the form
 */
/* category */
$categorie = new ISelect('cat');
$categorie->value = $cn->make_array('select fd_id,fd_label from fiche_def order by fd_label');
$categorie->selected = (isset($_GET['cat'])) ? $_GET['cat'] : 0;
$str_categorie = $categorie->input();

$icall = new ICheckBox("allcard", 1);
$icall->selected = (isset($_GET['allcard'])) ? 1 : 0;
$str_icall = $icall->input();
/* periode */
$exercice = $g_user->get_exercice();
$iperiode = new Periode($cn);
list ($first, $last) = $iperiode->get_limit($exercice);

$periode_start = new IDate('start');
$periode_end = new IDate('end');

$periode_start->value = (isset($_GET['start'])) ? $_GET['start'] : $first->first_day();
$periode_end->value = (isset($_GET['end'])) ? $_GET['end'] : $last->last_day();

$str_start = $periode_start->input();
$str_end = $periode_end->input();

/* histo ou summary */
$histo = new ISelect('histo');
$histo->value = array(
	array('value' => -1, 'label' => _('Liste')),
	array('value' => 0, 'label' => _('Historique')),
	array('value' => 1, 'label' => _('Historique Lettré')),
	array('value' => 6, 'label' => _('Historique Lettré et montants différents')),
	array('value' => 2, 'label' => _('Historique non Lettré')),
	array('value' => 3, 'label' => _('Résumé')),
	array('value' => 4, 'label' => _('Balance')),
	array('value' => 6, 'label' => _('Balance âgée')),
	array('value' => 7, 'label' => _('Balance âgée en-cours')),
	array('value' => 5, 'label' => _('Balance non soldée'))
);
$histo->javascript = 'onchange="if (this.value==3 || this.value==-1) {
                   g(&quot;trstart&quot;).style.display=&quot;none&quot;;g(&quot;trend&quot;).style.display=&quot;none&quot;;g(&quot;allcard&quot;).style.display=&quot;none&quot;;}
                   else  {g(&quot;trstart&quot;).style.display=&quot;&quot;;g(&quot;trend&quot;).style.display=&quot;&quot;;g(&quot;allcard&quot;).style.display=&quot;&quot;;}"';

$histo->selected = (isset($_GET['histo'])) ? $_GET['histo'] : -1;
$str_histo = $histo->input();
echo '<div class="content">';
echo '<FORM method="GET">';
echo dossier::hidden();
echo HtmlInput::hidden('ac', $_GET['ac']);
require_once NOALYSS_INCLUDE.'/template/impress_cat_card.php';
echo HtmlInput::submit('cat_display', _('Recherche'));
echo '</FORM>';
$search_card=new IText('card_search');
$search_card_js=sprintf('onclick="boxsearch_card(\'%d\')"',dossier::id());
?>
<div id="box_search_card">

		<?php echo _('Recherche de fiche')?> <?php echo HtmlInput::infobulle(18)?> :<?php echo $search_card->input()?>
		<?php echo HtmlInput::button_anchor(_("Chercher"),"javascript:void(0)","",$search_card_js,'smallbutton')?>
</div>
<?php
echo '</div>';
$str = "if (g('histo').value==3 || g('histo').value== -1 ) {
     g('trstart').style.display='none';g('trend').style.display='none';g('allcard').style.display='none';}
     else  {g('trstart').style.display='';g('trend').style.display='';g('allcard').style.display='';}
	 if (  g('histo').value== -1 ) { g('allcard').style.display='';}

	";
echo create_script($str);
echo '<hr>';

//-----------------------------------------------------
if (!isset($_GET['cat_display']))
	return;

$fd_id = $_GET['cat'];

$array = Fiche::get_fiche_def($cn, $_GET['cat'], 'name_asc');

$h_add_card_b = new IButton('add_card');
$h_add_card_b->label = _('Créer une nouvelle fiche');
$h_add_card_b->javascript = "dis_blank_card({gDossier:$gDossier,fd_id:$fd_id,ref:2})";
$str_add_card = ($g_user->check_action(FICADD) == 1) ? $h_add_card_b->input() : "";

/*
 * You show now the result
 */
if ($array == null)
{
        echo '<div class="content">';
	echo '<h2 class="info2"> '._('Aucune fiche trouvée').'</h2>';
	echo $str_add_card;
        echo '</div>';
	return;
}

$allcard = (isset($_GET['allcard'])) ? 1 : 0;
if ( $allcard == 0 ){
	$fiche_def=new Fiche_Def($cn,$_GET['cat']);
	$fiche_def->get();
	echo h1($fiche_def->label,"");
	echo h2($fiche_def->fd_description,"");
}
echo '<div class="content">';
/* * *********************************************************************************************************************************
 * Liste
 *
 * ******************************************************************************************************************************** */
if ($_GET['histo'] == -1)
{
	$write = $g_user->check_action(FICADD);
	/**
	 * If ask for move or delete
	 */
	if (isset($_POST['action']))
	{
		if ($write == 1)
		{
			$ack = $_POST['f_id'];
			/**
			 * Move
			 */
			if (isset($_POST['move'])&& $_POST['move'] == 1)
			{
				for ($i = 0; $i < count($ack); $i++)
				{
					$fiche = new Fiche($cn, $ack[$i]);
					$fiche->move_to($_POST['move_to']);
				}
			}
			/**
			 * Delete
			 */
			if (isset($_POST['delete'])&& $_POST['delete']==1)
			{
				$msg="";
				for ($i = 0; $i < count($ack); $i++)
				{
					$fiche = new Fiche($cn, $ack[$i]);
					if ( $fiche->remove(true) == 1 )
					{
						$msg.="\n ".$fiche->strAttribut(ATTR_DEF_QUICKCODE);
					}
				}
				if ($msg != "")
				{
                                        echo '<div class="content">';   
					echo h2(_("Fiche non effacées"), ' class="error"  ');
					echo '<p class="error">'._(" Ces fiches n'ont pas été effacées  ").$msg;
                                        echo '</div>';
				}
			}
		}
		else
		{
			echo NoAccess();
		}
	}
	$sql = "select f_id from fiche ";
	if ($allcard == 1)
	{
		$cond = "";
	}
	else
	{
		$cond = " where f.fd_id = " . sql_string($_GET['cat']);
	}
	// Create nav bar
	$max = $cn->get_value("select count(*) from fiche as f " . $cond);

	$step = $_SESSION['g_pagesize'];
	$page = (isset($_GET['offset'])) ? $_GET['page'] : 1;
	$offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;
	$bar = navigation_bar($offset, $max, $step, $page);
	$limit = ($step == -1 ) ? "" : " limit " . $step;
	$res = $cn->exec_sql("
		select f_id,
			(select ad_value from fiche_detail as fd1 where ad_id=1 and fd1.f_id=f.f_id) as name,
			(select ad_value from fiche_detail as fd1 where ad_id=23 and fd1.f_id=f.f_id) as qcode,
			fd_label,
			(select ad_value from fiche_detail as fd1 where ad_id=5 and fd1.f_id=f.f_id) as poste
		from fiche as f join fiche_def as fd on (fd.fd_id=f.fd_id)
		$cond   order by 2,4 offset $offset $limit
	");
	$nb_line = Database::num_row($res);
	if ($write != 1 || $allcard != 0 )  $str_add_card="";
	require_once NOALYSS_INCLUDE.'/template/fiche_list.php';
	echo '<hr>'.$bar;
	return;
}
/* * *********************************************************************************************************************************
 * Summary
 *
 * ******************************************************************************************************************************** */
if ($_GET['histo'] == 3)
{
	$cat_card = new Fiche_Def($cn);
	$cat_card->id = $_GET['cat'];
	$aHeading = $cat_card->getAttribut();
	if ( $allcard == 0) echo $str_add_card;
	require_once NOALYSS_INCLUDE.'/template/result_cat_card_summary.php';

	$hid = new IHidden();
	echo '<form method="GET" ACTION="export.php">' . dossier::hidden() .
	HtmlInput::submit('bt_csv', "Export CSV") .
	HtmlInput::hidden('act', "CSV:fiche") .
	$hid->input("type", "fiche") .
	$hid->input("ac", $_REQUEST['ac']) .
	$hid->input("fd_id", $_REQUEST['cat']);
	echo "</form>";

	return;
}
$export_pdf = '<FORM METHOD="get" ACTION="export.php" style="display:inline">';
$export_pdf.=HtmlInput::hidden('cat', $_GET['cat']);
$export_pdf.=HtmlInput::hidden('act', "PDF:fiche_balance") .
		$export_pdf.=HtmlInput::hidden('start', $_GET['start']);
$export_pdf.=HtmlInput::hidden('end', $_GET['end']);
$export_pdf.=HtmlInput::hidden('histo', $_GET['histo']);
$export_pdf.=HtmlInput::request_to_hidden(array('allcard'));
$export_pdf.=dossier::hidden();
$export_pdf.=HtmlInput::submit('pdf', 'Export en PDF');
$export_pdf.='</FORM>';

$export_print = HtmlInput::print_window();

$export_csv = '<FORM METHOD="get" ACTION="export.php" style="display:inline">';
$export_csv.=HtmlInput::hidden('cat', $_GET['cat']);
$export_csv.=HtmlInput::hidden('act', 'CSV:fiche_balance');
$export_csv.=HtmlInput::hidden('start', $_GET['start']);
$export_csv.=HtmlInput::hidden('end', $_GET['end']);
$export_csv.=HtmlInput::hidden('histo', $_GET['histo']);
$export_csv.=HtmlInput::request_to_hidden(array('allcard'));
$export_csv.=dossier::hidden();
$export_csv.=HtmlInput::submit('CSV', 'Export en CSV');
$export_csv.='</FORM>';
/*
 * Date is important is requested balance
 */
if (isDate($_REQUEST['start']) == null || isDate($_REQUEST['end']) == null)
{
	echo h2('Date invalide !', 'class="error"');
	alert('Date invalide !');
	return;
}
/*************************************************************************************************************************
 * Balance agée tous
/*************************************************************************************************************************/
if ( $_GET['histo'] == 6)
{
    require_once NOALYSS_INCLUDE.'/class_balance_age.php';
    $bal=new Balance_Age($cn);
    $export_csv = '<FORM METHOD="get" ACTION="export.php" style="display:inline">';
    $export_csv .=HtmlInput::request_to_hidden(array('gDossier','ac','p_let','p_date_start'));
    $export_csv.=HtmlInput::hidden('p_date_start', $_GET['start']);
    $export_csv .= HtmlInput::hidden('act','CSV:balance_age');
    $export_csv .= HtmlInput::hidden('p_let','let');
    $export_csv .= HtmlInput::hidden('p_type','X');
    $export_csv .= HtmlInput::hidden('cat',$_GET['cat']);
    $export_csv .= HtmlInput::hidden('all',$allcard);
    $export_csv .= HtmlInput::submit('csv',_('Export CSV'));
    $export_csv.='</FORM><p></p>';
    if ( $allcard == 0 )
    {
        echo $export_csv;
        $bal->display_category($_GET['start'],$_GET['cat'],'let');
        echo $export_csv;
    }    
    else
    {
        echo $export_csv;
        $a_cat = $cn->get_array("select fd_id from vw_fiche_def where ad_id=" . ATTR_DEF_ACCOUNT . " order by fd_label asc");
        $nb_cat=count($a_cat);
        for ($i=0;$i < $nb_cat;$i++)
        {
             $bal->display_category($_GET['start'],$a_cat[$i]['fd_id'],'let');
        }
        echo $export_csv;
    }
    return;
}
/*************************************************************************************************************************
 * Balance en-cours
/*************************************************************************************************************************/
if ( $_GET['histo'] == 7)
{
    require_once NOALYSS_INCLUDE.'/class_balance_age.php';
    $bal=new Balance_Age($cn);
       $export_csv = '<FORM METHOD="get" ACTION="export.php" style="display:inline">';
    $export_csv .=HtmlInput::request_to_hidden(array('gDossier','ac','p_let','p_date_start'));
    $export_csv.=HtmlInput::hidden('p_date_start', $_GET['start']);
    $export_csv .= HtmlInput::hidden('act','CSV:balance_age');
    $export_csv .= HtmlInput::hidden('p_let','unlet');
    $export_csv .= HtmlInput::hidden('p_type','X');
    $export_csv .= HtmlInput::hidden('cat',$_GET['cat']);
    $export_csv .= HtmlInput::hidden('all',$allcard);
    $export_csv .= HtmlInput::submit('csv',_('Export CSV'));
    $export_csv.='</FORM><p></p>';
    if ( $allcard == 0 )
    {
        echo $export_csv;
        $bal->display_category($_GET['start'],$_GET['cat'],'unlet');
        echo $export_csv;
    }
      else
    {
        echo $export_csv;
        $a_cat = $cn->get_array("select fd_id from vw_fiche_def where ad_id=" . ATTR_DEF_ACCOUNT . " order by fd_label asc");
        $nb_cat=count($a_cat);
        for ($i=0;$i < $nb_cat;$i++)
        {
             $bal->display_category($_GET['start'],$a_cat[$i]['fd_id'],'unlet');
        }
        echo $export_csv;
    }
    return;
}
/********************************************************************************************************************************
 * Balance
 *
 **********************************************************************************************************************************/
if ($_GET['histo'] == 4 || $_GET['histo'] == 5)
{
	if ( $allcard == 0 ) echo $str_add_card;
	echo $export_pdf;
	echo $export_csv;
	echo $export_print;

	$fd = new Fiche_Def($cn, $_REQUEST['cat']);
	if ($allcard == 0 && $fd->hasAttribute(ATTR_DEF_ACCOUNT) == false)
	{
		echo alert(_("Cette catégorie n'ayant pas de poste comptable n'a pas de balance"));
		return;
	}
	// all card
	if ($allcard == 1)
	{
		$afiche = $cn->get_array("select fd_id from vw_fiche_def where ad_id=" . ATTR_DEF_ACCOUNT . " order by fd_label asc");
	}
	else
	{
		$afiche[0] = array('fd_id' => $_REQUEST['cat']);
	}

	for ($e = 0; $e < count($afiche); $e++)
	{
		$ret = $cn->exec_sql("select f_id,ad_value from fiche join fiche_detail using(f_id) where fd_id=$1 and ad_id=1 order by 2 ", array($afiche[$e]['fd_id']));
		if ($cn->count() == 0)
		{
			if ($allcard == 0)
			{
				echo _("Aucune fiche trouvée");
				return;
			} else
				continue;
		}
		echo '<h2>' . $cn->get_value("select fd_label from fiche_def where fd_id=$1", array($afiche[$e]['fd_id'])) . '</h2>';
                $id="table_".$afiche[$e]['fd_id']."_id";
                echo _('Filtre rapide:').HtmlInput::filter_table($id, '0,1,2', '1'); 
		echo '<table class="sortable" id="'.$id.'" class="result" >';
		echo tr(
				th('Quick Code') .
				th('Libellé') .
				'<th>Poste'.HtmlInput::infobulle(27).'</th>'.
				th('Débit', 'style="text-align:right"') .
				th('Crédit', 'style="text-align:right"') .
				th('Solde', 'style="text-align:right"') .
				th('D/C', 'style="text-align:right"')
		);
		$idx = 0;$sum_deb=0;$sum_cred=0;$sum_solde=0;bcscale(4);
		for ($i = 0; $i < Database::num_row($ret); $i++)
		{
			$filter = " (j_date >= to_date('" . $_REQUEST['start'] . "','DD.MM.YYYY') " .
					" and  j_date <= to_date('" . $_REQUEST['end'] . "','DD.MM.YYYY')) ";
			$aCard = Database::fetch_array($ret, $i);
			$oCard = new Fiche($cn, $aCard['f_id']);
			$solde = $oCard->get_solde_detail($filter);
			if ($solde['debit'] == 0 && $solde['credit'] == 0)
				continue;
			/* only not purged card */
			if ($_GET['histo'] == 5 && $solde['debit'] == $solde['credit'])
				continue;
			$class =($idx % 2 == 0) ?  'class="odd"':'class="even"';
			$idx++;
                        $sum_cred=bcadd($sum_cred,$solde['credit']);
                        $sum_deb=bcadd($sum_deb,$solde['debit']);
                        $sum_solde=bcsub($sum_deb,$sum_cred);
			echo tr(
					td(HtmlInput::history_card($oCard->id, $oCard->strAttribut(ATTR_DEF_QUICKCODE))) .
					td($oCard->strAttribut(ATTR_DEF_NAME)) .
					td(HtmlInput::history_account($oCard->strAttribut(ATTR_DEF_ACCOUNT),$oCard->strAttribut(ATTR_DEF_ACCOUNT))).
					td(nbm($solde['debit']), 'class="sorttable_numeric" sorttable_customkey="'.$solde['debit'].'" style="text-align:right"') .
					td(nbm($solde['credit']), 'class="sorttable_numeric" sorttable_customkey="'.$solde['debit'].'" style="text-align:right"') .
					td(nbm(abs($solde['solde'])), 'class="sorttable_numeric" sorttable_customkey="'.$solde['solde'].'" style="text-align:right"') .
					td((($solde['debit'] < $solde['credit']) ? 'C' : 'D'), 'style="text-align:right"'), $class
			);



		}
                echo '<tfoot>';
                echo tr(
                                td('').
                                td(_('Totaux')).
                                td('').
                                td(nbm($sum_deb), 'style="text-align:right"').
                                td(nbm($sum_cred), 'style="text-align:right"').
                                td(nbm(abs($sum_solde)), 'style="text-align:right"').
                                td((($sum_deb < $sum_cred) ? 'C' : 'D'), 'style="text-align:right"'),' class="highlight"');
                echo '</tfoot>';
		echo '</table>';
	}
	if ( $allcard == 0 ) echo $str_add_card;
	echo $export_pdf;
	echo $export_csv;
	echo $export_print;

	return;
}

/***********************************************************************************************************************************
 * Lettering
 *
 **********************************************************************************************************************************/
// all card
if ($allcard == 1)
{
	$afiche = $cn->get_array("select fd_id from vw_fiche_def where ad_id=" . ATTR_DEF_ACCOUNT . " order by fd_label asc");
}
else
{
	$afiche[0] = array('fd_id' => $_REQUEST['cat']);
}
if ( $allcard == 0) echo $str_add_card;
echo $export_csv;
echo $export_pdf;
echo $export_print;
$fiche = new Fiche($cn);
for ($e = 0; $e < count($afiche); $e++)
{
	$array = Fiche::get_fiche_def($cn, $afiche[$e]['fd_id'], 'name_asc');

	foreach ($array as $card)
	{
		$row = new Fiche($cn, $card['f_id']);
		$letter = new Lettering_Card($cn);
		$letter->set_parameter('quick_code', $row->strAttribut(ATTR_DEF_QUICKCODE));
		$letter->set_parameter('start', $_GET['start']);
		$letter->set_parameter('end', $_GET['end']);
		// all
		if ($_GET['histo'] == 0)
		{
			$letter->get_all();
		}

		// lettered
		if ($_GET['histo'] == 1)
		{
			$letter->get_letter();
		}
		// unlettered
		if ($_GET['histo'] == 2)
		{
			$letter->get_unletter();
		}
		if ($_GET['histo'] == 6)
		{
			$letter->get_letter_diff();
		}
		/* skip if nothing to display */
		if (count($letter->content) == 0)
			continue;
		$detail_card = HtmlInput::card_detail($row->strAttribut(ATTR_DEF_QUICKCODE), $row->strAttribut(ATTR_DEF_NAME));

		echo '<h2>' . $detail_card ;
                echo "poste "
                        . ":".HtmlInput::history_account($row->strAttribut(ATTR_DEF_ACCOUNT),$row->strAttribut(ATTR_DEF_ACCOUNT),'display:inline').HtmlInput::infobulle(27).'</h2>';

		echo '<table class="result">';
		echo '<tr>';
		echo th(_('Date'));
		echo th(_('ref'));
		echo th(_('Interne'));
		echo th(_('Comm'));
		echo th(_('Montant'), 'style="width:auto" colspan="2"');
		echo th(_('Prog.'));
		echo th(_('Let.'));
		echo '</tr>';
		$amount_deb = 0;
		$amount_cred = 0;
		$prog = 0;
		bcscale(2);
		for ($i = 0; $i < count($letter->content); $i++)
		{
                    $row = $letter->content[$i];
                     $html_letter="";
                        if ($row['letter']!=-1) {
                            $html_letter=strtoupper(base_convert($row['letter'],10,36));
                        }
			if ($i % 2 == 0)
				echo '<tr class="even" name="tr_'.$html_letter.'_">';
			else
				echo '<tr class="odd" name="tr_'.$html_letter.'_">';
			
			echo td($row['j_date_fmt']);
			echo td(h($row['jr_pj_number']));
			echo td(HtmlInput::detail_op($row['jr_id'], $row['jr_internal']));
			echo td(h($row['jr_comment']));
			if ($row['j_debit'] == 't')
			{
				echo td(nbm($row['j_montant']), ' style="text-align:right"');
				$amount_deb=bcadd($amount_deb,$row['j_montant']);
				$prog = bcadd($prog, $row['j_montant']);
				echo td("");
			}
			else
			{
				echo td("");
				echo td(nbm($row['j_montant']), ' style="text-align:right"');
				$amount_cred=bcadd($amount_cred,$row['j_montant']);
				$prog = bcsub($prog, $row['j_montant']);
			}
			$side = "&nbsp;" . $fiche->get_amount_side($prog);
			echo td(nbm($prog) . $side, 'style="text-align:right"');
                        $html_let="";
                        if ($row['letter']!=-1) {
                            $span_error = "";
				if ($row['letter_diff'] != 0)
					$span_error = $g_failed;
                                echo '<td>'.HtmlInput::show_reconcile("", $html_letter,$span_error).'</td>';
                        }
			else
				echo td('');
			echo '</tr>';
		}
		echo '</table>';
		echo '<table>';
		echo '<tr>';
		echo td(_('Debit'));
		echo td(nbm($amount_deb), ' style="font-weight:bold;text-align:right"');
		echo '</tr>';
		echo '<tr>';
		echo td(_('Credit'));
		echo td(nbm($amount_cred), ' style="font-weight:bold;text-align:right"');
		echo '</tr>';
		echo '<tr class="highlight">';
                $solde=abs(round($amount_cred - $amount_deb, 2));
                if ( $solde == 0)
                {
                    $s='solde';
                }
                else if ($amount_deb > $amount_cred)
			$s = 'solde débiteur';
		else
			$s = 'solde crediteur';
		echo td($s);
		echo td(nbm($solde), ' style="font-weight:bold;text-align:right"');
		echo '</tr>';
		echo '</table>';
	}
}
if ( $allcard == 0) echo $str_add_card;
echo $export_csv;
echo $export_pdf;
echo $export_print;
?>

