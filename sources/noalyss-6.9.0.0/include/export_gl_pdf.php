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

/*! \file
 * \brief create GL comptes as PDF
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once('class_acc_account_ledger.php');
include_once('ac_common.php');
require_once NOALYSS_INCLUDE.'/class_database.php';
include_once('class_impress.php');
require_once NOALYSS_INCLUDE.'/class_own.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_pdf.php';
bcscale(2);

$gDossier=dossier::id();

/* Security */
$cn=new Database($gDossier);
$g_user->Check();
$g_user->check_dossier($gDossier);

$sql="select pcm_val from tmp_pcmn ";

extract($_GET);
$cond_poste="";
if ($from_poste != '')
  {
    $cond_poste = '  where ';
    $cond_poste .=' pcm_val >= upper (\''.Database::escape_string($from_poste).'\')';
  }

if ( $to_poste != '')
  {
    if  ( $cond_poste == '')
      {
	$cond_poste =  ' where pcm_val <= upper (\''.Database::escape_string($to_poste).'\')';
      }
    else
      {
	$cond_poste.=' and pcm_val <= upper (\''.Database::escape_string($to_poste).'\')';
      }
  }

$sql=$sql.$cond_poste.'  order by pcm_val::text';
$a_poste=$cn->get_array($sql);

$pdf = new PDF($cn);
$pdf->setDossierInfo("  Periode : ".$from_periode." - ".$to_periode);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle("Grand Livre",true);
$pdf->SetAuthor('NOALYSS');

if ( count($a_poste) == 0 )
{
    $pdf->Output();
    return;
}

// Header
$header = array( "Date", "Référence", "Libellé", "Pièce","Let", "Débit", "Crédit", "Solde" );
// Left or Right aligned
$lor    = array( "L"   , "L"        , "L"      , "L"    , "R",   "R"    , "R"     , "R"     );
// Column widths (in mm)
$width  = array( 13    , 20         , 60       , 15     ,  12     , 20     , 20      , 20      );
$l=(isset($_REQUEST['letter']))?2:0;
$s=(isset($_REQUEST['solded']))?1:0;

foreach ($a_poste as $poste)
{

  $Poste=new Acc_Account_Ledger($cn,$poste['pcm_val']);


  $array1=$Poste->get_row_date($from_periode,$to_periode,$l,$s);
  // don't print empty account
  if ( count($array1) == 0 )
    {
        continue;
    }
  $array=$array1[0];
  $tot_deb=$array1[1];
  $tot_cred=$array1[2];

    $pdf->SetFont('DejaVuCond','',10);
    $Libelle=sprintf("%s - %s ",$Poste->id,$Poste->get_name());
    $pdf->Cell(0, 7, $Libelle, 1, 1, 'C');

    $pdf->SetFont('DejaVuCond','',6);
    for($i=0;$i<count($header);$i++)
        $pdf->Cell($width[$i], 4, $header[$i], 0, 0, $lor[$i]);
    $pdf->Ln();

    $pdf->SetFont('DejaVuCond','',7);


    $solde = 0.0;
    $solde_d = 0.0;
    $solde_c = 0.0;
    $current_exercice="";
    foreach ($Poste->row as $detail)
    {

        /*
               [0] => 1 [jr_id] => 1
               [1] => 01.02.2009 [j_date_fmt] => 01.02.2009
               [2] => 2009-02-01 [j_date] => 2009-02-01
               [3] => 0 [deb_montant] => 0
               [4] => 12211.9100 [cred_montant] => 12211.9100
               [5] => Ecriture douverture [description] => Ecriture douverture
               [6] => Opération Diverses [jrn_name] => Opération Diverses
               [7] => f [j_debit] => f
               [8] => 17OD-01-1 [jr_internal] => 17OD-01-1
               [9] => ODS1 [jr_pj_number] => ODS1 ) 1
         */
         /*
             * separation per exercice
             */
            if ( $current_exercice == "") $current_exercice=$detail['p_exercice'];
            
            if ( $current_exercice != $detail['p_exercice']) {
                
                $pdf->SetFont('DejaVuCond','B',8);
                $i=0;
                $pdf->Cell($width[$i], 6, $current_exercice, 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell($width[$i], 6, '', 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell($width[$i], 6, '', 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell($width[$i], 6, '', 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell($width[$i], 6, 'Total du compte '.$Poste->id, 0, 0, 'R');
                $i++;
                $pdf->Cell($width[$i], 6, ($solde_d  > 0 ? nbm($solde_d)  : ''), 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell($width[$i], 6, ($solde_c  > 0 ? nbm( $solde_c)  : ''), 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell($width[$i], 6, nbm(abs($solde_c-$solde_d)), 0, 0, $lor[$i]);
                $i++;
                $pdf->Cell(5, 6, ($solde_c > $solde_d ? 'C' : 'D'), 0, 0, 'L');
                /*
                * reset total and current_exercice
                */
                $current_exercice=$detail['p_exercice'];
                $solde = 0.0;
                $solde_d = 0.0;
                $solde_c = 0.0;
                $pdf->Ln();
                $pdf->SetFont('DejaVuCond','',7);

            }

        if ($detail['cred_montant'] > 0)
        {
            $solde   = bcsub ($solde,$detail['cred_montant']);
            $solde_c = bcadd($solde_c,$detail['cred_montant']);
        }
        if ($detail['deb_montant'] > 0)
        {
            $solde   = bcadd($solde,$detail['deb_montant']);
            $solde_d = bcadd($solde_d,$detail['deb_montant']);
        }

        $i = 0;
		$side=" ".$Poste->get_amount_side($solde);
        $pdf->LongLine($width[$i], 6, shrink_date($detail['j_date_fmt']), 0, $lor[$i]);
        $i++;
        $pdf->LongLine($width[$i], 6, $detail['jr_internal'], 0, $lor[$i] );
        $i++;
        /* limit set to 40 for the substring */
        $triple_point = (mb_strlen($detail['description']) > 40 ) ? '...':'';
        $pdf->LongLine($width[$i], 6, mb_substr($detail['description'],0,40).$triple_point, 0,$lor[$i]);
        $i++;
        $pdf->Cell($width[$i], 6, $detail['jr_pj_number'], 0, 0, $lor[$i]);
        $i++;
        $pdf->Cell($width[$i], 6, ($detail['letter']!=-1)?$detail['letter']:'', 0, 0, $lor[$i]);
        $i++;
        $pdf->Cell($width[$i], 6, ($detail['deb_montant']  > 0 ? nbm( $detail['deb_montant'])  : ''), 0, 0, $lor[$i]);
        $i++;
        $pdf->Cell($width[$i], 6, ($detail['cred_montant'] > 0 ? nbm( $detail['cred_montant']) : ''), 0, 0, $lor[$i]);
        $i++;
        $pdf->Cell($width[$i], 6, nbm(abs( $solde)).$side, 0, 0, $lor[$i]);
        $i++;
        $pdf->Ln();

    }


    $pdf->SetFont('DejaVuCond','B',8);

    $i = 0;
    $pdf->Cell($width[$i], 6, $current_exercice, 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell($width[$i], 6, '', 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell($width[$i], 6, '', 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell($width[$i], 6, '', 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell($width[$i], 6, 'Total du compte '.$Poste->id, 0, 0, 'R');
    $i++;
    $pdf->Cell($width[$i], 6, ($solde_d  > 0 ? nbm($solde_d)  : ''), 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell($width[$i], 6, ($solde_c  > 0 ? nbm( $solde_c)  : ''), 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell($width[$i], 6, nbm(abs($solde_c-$solde_d)), 0, 0, $lor[$i]);
    $i++;
    $pdf->Cell(5, 6, ($solde_c > $solde_d ? 'C' : 'D'), 0, 0, 'L');

    $pdf->Ln();
    $pdf->Ln();

}
//Save PDF to file
$pdf->Output("gl_comptes.pdf", 'D');
exit;
?>
