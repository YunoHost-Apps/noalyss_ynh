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
 * \brief Called by impress->category, export in PDF the history of a category or balance
 * of card
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
// Security we check if user does exist and his privilege
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_pdf.php';
require_once NOALYSS_INCLUDE.'/class_lettering.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';

/* Security */
$gDossier=dossier::id();
$cn=new Database($gDossier);
$g_user->Check();
$g_user->check_dossier($gDossier);
$name=$cn->get_value('select fd_label from fiche_def where fd_id=$1',array($_GET['cat']));

$pdf=new PDF($cn);
$pdf->setDossierInfo("  Periode : ".$_GET['start']." - ".$_GET['end']);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetTitle($name,1);
$pdf->SetAuthor('NOALYSS');
/* balance */
$allcard=(isset($_GET['allcard']))?1:0;
/*
 * Balance
 */
if ( $_GET['histo'] == 4 || $_GET['histo']==5)
{
    $fd=new Fiche_Def($cn,$_REQUEST['cat']);
    if ($allcard==1 &&  $fd->hasAttribute(ATTR_DEF_ACCOUNT) == false )
    {
        $pdf->Cell(0,10, "Cette catégorie n'ayant pas de poste comptable n'a pas de balance");
        //Save PDF to file
        $fDate=date('dmy-Hi');
        $pdf->Output("category-$fDate.pdf", 'D');
        exit;
    }
	// all card
	if ($allcard==1)
	{
		$afiche=$cn->get_array("select fd_id from vw_fiche_def where ad_id=".ATTR_DEF_ACCOUNT." order by fd_label asc");
	}
	else
	{
		$afiche[0]=array('fd_id'=>$_REQUEST['cat']);
	}

	if ( $allcard==0 && empty($afiche))
    {
        $pdf->Cell(0,10, "Aucune fiche trouvée");//Save PDF to file
        $fDate=date('dmy-Hi');
        $pdf->Output("category-$fDate.pdf", 'D');
        exit;
    }
	for ($e = 0; $e < count($afiche); $e++)
	{
    $aCard=$cn->get_array("select f_id,ad_value from fiche join fiche_Detail using (f_id)  where ad_id=1 and fd_id=$1 order by 2 ",array($afiche[$e]['fd_id']));
	$name=$cn->get_value('select fd_label from fiche_def where fd_id=$1',array($afiche[$e]['fd_id']));
	$pdf->SetFont('DejaVu','BI',14);
	$pdf->Cell(0,8,$name,0,1,'C');

	$pdf->SetFont('DejaVuCond','',7);
    $pdf->LongLine(30,7,'Quick Code',0,'L',0);
    $pdf->LongLine(80,7,'Libellé',0,'L',0);
    $pdf->Cell(20,7,'Débit',0,0,'R',0);
    $pdf->Cell(20,7,'Crédit',0,0,'R',0);
    $pdf->Cell(20,7,'Solde',0,0,'R',0);
    $pdf->Cell(20,7,'D/C',0,0,'C',0);
    $pdf->Ln();

	if (empty($aCard)) continue;


    $idx=0;$sum_deb=0;$sum_cred=0;bcscale(4);
    for ($i=0;$i < count($aCard);$i++)
    {
        if ( isDate($_REQUEST['start']) == null || isDate ($_REQUEST['end']) == null ) 	 exit;
        $filter= " (j_date >= to_date('".$_REQUEST['start']."','DD.MM.YYYY') ".
                 " and  j_date <= to_date('".$_REQUEST['end']."','DD.MM.YYYY')) ";
        $oCard=new Fiche($cn,$aCard[$i]['f_id']);
        $solde=$oCard->get_solde_detail($filter);
        if ( $solde['debit'] == 0 && $solde['credit']==0) continue;
	/* only not purged card */
	if ($_GET['histo'] == 5 && $solde['debit'] == $solde['credit']) continue;

        if ( $idx % 2 == 0 )
        {
            $pdf->SetFillColor(220,221,255);
            $fill=1;
        }
        else
        {
            $pdf->SetFillColor(0,0,0);
            $fill=0;
        }
        $idx++;
	$side='';
	if(bcsub($solde['credit'],$solde['debit']) < 0) $side='Deb.';
	if(bcsub($solde['credit'],$solde['debit']) > 0) $side='Cred.';

        $sum_cred=bcadd($sum_cred,$solde['credit']);
        $sum_deb=bcadd($sum_deb,$solde['debit']);
        $sum_solde=bcsub($sum_deb,$sum_cred);

        $pdf->Cell(30,7,$oCard->strAttribut(ATTR_DEF_QUICKCODE),0,0,'L',$fill);
        $pdf->LongLine(80,7,$oCard->strAttribut(ATTR_DEF_NAME)." (".$oCard->strAttribut(ATTR_DEF_ACCOUNT).")",0,'L',$fill);
        $pdf->Cell(20,7,nbm($solde['debit']),0,0,'R',$fill);
        $pdf->Cell(20,7,nbm($solde['credit']),0,0,'R',$fill);
        $pdf->Cell(20,7,nbm(abs($solde['solde'])),0,0,'R',$fill);
        $pdf->Cell(20,7,$side,0,0,'C',$fill);
        $pdf->Ln();
        }
		if ( $idx % 2 == 0 )
        {
            $pdf->SetFillColor(220,221,255);
            $fill=1;
        }
        else
        {
            $pdf->SetFillColor(0,0,0);
            $fill=0;
        }
		$idx++;
        // Sum by category
        $pdf->Cell(30,7,"",0,0,'L',$fill);
        $pdf->Cell(80,7,_("Totaux"),0,0,'L',$fill);
        $pdf->Cell(20,7,nbm($sum_deb),0,0,'R',$fill);
        $pdf->Cell(20,7,nbm($sum_cred),0,0,'R',$fill);
        $pdf->Cell(20,7,nbm(abs($sum_solde)),0,0,'R',$fill);
        $side=" = ";
        if ( $sum_solde > 0 )
        {
            $side='Deb.';
        }
        else if ( $sum_solde < 0)
        {
            $side='Cred.';
        }

        $pdf->Cell(20,7,$side,0,0,'C',$fill);
        $pdf->Ln();
	}
}
else
{
	// History
	// all card
	if ($allcard == 1)
	{
		$afiche = $cn->get_array("select fd_id from vw_fiche_def where ad_id=" . ATTR_DEF_ACCOUNT . " order by fd_label asc");
	}
	else
	{
		$afiche[0] = array('fd_id' => $_REQUEST['cat']);
	}
	$fic=new Fiche($cn);
	for ($e = 0; $e < count($afiche); $e++)
	{
		$array = Fiche::get_fiche_def($cn,$afiche[$e]['fd_id'] , 'name_asc');
		/*
		 * You show now the result
		 */
		if ($array == null)
		{
			continue;
		}
		$tab = array(12, 20, 20,68, 20, 20, 10, 20);
		$align = array('L', 'C', 'L', 'L', 'R','R', 'R', 'R');

		foreach ($array as $row_fiche)
		{
			$fic = new Fiche($cn, $row_fiche['f_id']);
			$letter = new Lettering_Card($cn);
			$letter->set_parameter('quick_code', $fic->strAttribut(ATTR_DEF_QUICKCODE));
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
			$pdf->SetFont('DejaVuCond', '', 10);
			$fiche = new Fiche($cn, $row_fiche['f_id']);
			$pdf->Cell(0, 7, $fiche->strAttribut(ATTR_DEF_NAME)." [".$fiche->strAttribut(ATTR_DEF_QUICKCODE).":".$fiche->strAttribut(ATTR_DEF_ACCOUNT)."]", 1, 'C');

			$pdf->SetFont('DejaVuCond', '', 7);
			$pdf->Ln();
			$pdf->Cell($tab[0], 7, 'Date');
			$pdf->Cell($tab[1], 7, 'ref');
			$pdf->Cell($tab[2], 7, 'Internal');
			$pdf->Cell($tab[3], 7, 'Comm');
			$pdf->Cell($tab[4], 7, 'Montant', 0, 0, 'C');
			$pdf->Cell($tab[5], 7, 'Prog.', 0, 0, 'R');
			$pdf->Cell($tab[6], 7, 'Let.', 0, 0, 'R');
			$pdf->Cell($tab[7], 7, 'Diff. Let.', 0, 0, 'R');
			$pdf->ln();

			$amount_deb = 0;
			$amount_cred = 0;
			$prog=0;
			for ($i = 0; $i < count($letter->content); $i++)
			{
				if ($i % 2 == 0)
				{
					$pdf->SetFillColor(220, 221, 255);
					$fill = 1;
				}
				else
				{
					$pdf->SetFillColor(0, 0, 0);
					$fill = 0;
				}
				$pdf->SetFont('DejaVuCond', '', 7);
				$row = $letter->content[$i];
				$str_date = shrink_date($row['j_date_fmt']);

				$pdf->LongLine($tab[0], 4, $str_date, 0, $align[0], $fill);
				$pdf->Cell($tab[1], 4, $row['jr_pj_number'], 0, 0, $align[1], $fill);
				$pdf->LongLine($tab[2], 4, $row['jr_internal'], 0, $align[1], $fill);
				$pdf->LongLine($tab[3], 4, $row['jr_comment'], 0,  $align[2], $fill);
				if ($row['j_debit'] == 't')
				{
					$prog=bcadd($prog,$row['j_montant']);
					$pdf->Cell($tab[4], 4, sprintf('%s D', nbm($row['j_montant'])), 0, 0, $align[4], $fill);
					$amount_deb+=$row['j_montant'];
					$str_prog=sprintf("%s %s",nbm(abs($prog)),$fic->get_amount_side($prog));
					$pdf->Cell($tab[5], 4, $str_prog, 0, 0, $align[5], $fill);
				}
				else
				{
					$prog=bcsub($prog,$row['j_montant']);
					$pdf->Cell($tab[4], 4, sprintf('%s C', nbm($row['j_montant'])), 0, 0, $align[4], $fill);
					$amount_cred+=$row['j_montant'];
					$str_prog=sprintf("%s %s",nbm(abs($prog)),$fic->get_amount_side($prog));
					$pdf->Cell($tab[5], 4, $str_prog, 0, 0, $align[5], $fill);
				}
				if ($row['letter'] != -1)
				{
					$pdf->Cell($tab[6], 4, strtoupper(base_convert($row['letter'],10,36)), 0, 0, $align[6], $fill);
					// get sum for this lettering

					$pdf->Cell($tab[7], 4, sprintf('%s', nbm($row['letter_diff'])), '0', '0', $align[7], $fill);
				}
				else
					$pdf->Cell($tab[6], 4, "", 0, 0, 'R', $fill);
				$pdf->Ln();
			}
			$pdf->SetFillColor(0, 0, 0);
			$pdf->SetFont('DejaVuCond', 'B', 8);
			$debit = sprintf('Debit  : %s', nbm($amount_deb));
			$credit = sprintf('Credit : %s', nbm($amount_cred));
			if ($amount_deb > $amount_cred)
				$s = 'solde débiteur';
			else
				$s = 'solde crediteur';
			$solde = sprintf('%s  : %s', $s, nbm(abs(round($amount_cred - $amount_deb, 2))));

			$pdf->Cell(0, 6, $debit, 0, 0, 'R');
			$pdf->ln(4);
			$pdf->Cell(0, 6, $credit, 0, 0, 'R');
			$pdf->ln(4);
			$pdf->Cell(0, 6, $solde, 0, 0, 'R');
			$pdf->ln(4);

			$pdf->Ln();
		}
	}
}
//Save PDF to file
$fDate=date('dmy-Hi');
$pdf->Output("category-$fDate.pdf", 'D');
exit;
