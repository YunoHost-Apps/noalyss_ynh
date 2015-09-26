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
/*! \file
 * \brief Print the balance in pdf format
 * \param received parameters
 * \param e_date element 01.01.2003
 * \param e_client element 3
 * \param nb_item element 2
 * \param e_march0 element 11
 * \param e_quant0 element 1
 * \param e_march1 element 6
 * \param e_quant1 element 2
 * \param e_comment  invoice number
 */
// Copyright Author Dany De Bontridder danydb@aevalys.eu
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once("ac_common.php");
require_once NOALYSS_INCLUDE.'/class_database.php';
include_once("class_acc_balance.php");
require_once  NOALYSS_INCLUDE.'/header_print.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_pdf.php';
$gDossier=dossier::id();
bcscale(4);
$cn=new Database($gDossier);
$rep=new Database();
require_once  NOALYSS_INCLUDE.'/class_user.php';
$g_user->Check();

$bal=new Acc_Balance($cn);

extract ($_GET);
$bal->jrn=null;
switch( $_GET['p_filter'])
{
case 0:
        $bal->jrn=null;
    break;
case 1:
    if (  isset($_GET['r_jrn']))
    {
        $selected=$_GET['r_jrn'];
        $array_ledger=$g_user->get_ledger('ALL',3);
        $array=get_array_column($array_ledger,'jrn_def_id');
        for ($e=0;$e<count($selected);$e++)
        {
            if (isset ($selected[$e]) && in_array ($selected[$e],$array))
                $bal->jrn[]=$selected[$e];
        }
    }
    break;
case 2:
    if ( isset($_GET['r_cat']))   $bal->filter_cat($_GET['r_cat']);
    break;
}

$bal->from_poste=$_GET['from_poste'];
$bal->to_poste=$_GET['to_poste'];
if (isset($_GET['unsold'])) $bal->unsold=true;
$previous=(isset($_GET['previous_exc']))?1:0;
  
$array=$bal->get_row($from_periode,$to_periode,$previous);

$previous= (isset ($array[0]['sum_cred_previous']))?1:0;


if ( sizeof($array)  == 0 )
{
    exit();

}

$pPeriode=new Periode($cn);
$a=$pPeriode->get_date_limit($from_periode);
$b=$pPeriode->get_date_limit($to_periode);
$per_text="  du ".$a['p_start']." au ".$b['p_end'];
if ($previous == 1 ) {
    $pdf=new PDFLand($cn);
} else {
    $pdf= new PDF($cn);
}
$pdf->setDossierInfo(" Balance  ".$per_text);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAuthor('NOALYSS');
$pdf->SetFont('DejaVuCond','',7);
$pdf->setTitle("Balance comptable",true);
$pdf->Cell(30,6,'poste');
$pdf->LongLine(60,6,'Libellé');
if ($previous == 1 ){ 
    $pdf->Cell(20,6,'Débit N-1',0,0,'R');
    $pdf->Cell(20,6,'Crédit N-1',0,0,'R');
    $pdf->Cell(20,6,'Débiteur N-1',0,0,'R');
    $pdf->Cell(20,6,'Créditeur N-1',0,0,'R');
}
$pdf->Cell(25,6,'Total Débit',0,0,'R');
$pdf->Cell(25,6,'Total Crédit',0,0,'R');
$pdf->Cell(25,6,'Solde Débiteur',0,0,'R');
$pdf->Cell(25,6,'Solde Créditeur',0,0,'R');
$pdf->Ln();

$pdf->SetFont('DejaVuCond','',8);
$tp_deb=0;
$tp_cred=0;
$tp_sold=0;
$tp_solc=0;
$tp_deb_previous=0;
$tp_cred_previous=0;
$tp_sold_previous=0;
$tp_solc_previous=0;
if ( $previous == 1) {
    $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred','sum_cred_previous','sum_deb_previous','solde_deb_previous','solde_cred_previous');
}
else {
    $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred') ;
}
foreach($a_sum as $a)
  {
    $nlvl1[$a]=0;
    $nlvl2[$a]=0;
    $nlvl3[$a]=0;
  }
$lvl1_old='';
$lvl2_old='';
$lvl3_old='';

bcscale(2);
if (! empty($array))
  {
    $i=0;
    foreach ($array as $key=>$value)
      {
	$i++;
	/*
	 * level x
	 */
	if ( $value['poste']=='') continue;
	foreach (array(3,2,1) as $ind)
	  {
	    $r=$value;
	    if ( ! isset($_GET['lvl'.$ind]))continue;

	    if (${'lvl'.$ind.'_old'} == '')	  ${'lvl'.$ind.'_old'}=substr($r['poste'],0,$ind);
	    if ( ${'lvl'.$ind.'_old'} != substr($r['poste'],0,$ind))
	      {
		$pdf->SetFont('DejaVu','B',7);
		$pdf->LongLine(30,6,${'lvl'.$ind.'_old'});
                $delta=bcsub(${'nlvl'.$ind}['solde_cred'],${'nlvl'.$ind}['solde_deb']);
                $side=($delta< 0) ? "D":"C";
                if ($previous == 1 ) {
                    $delta_previous=bcsub(${'nlvl'.$ind}['solde_cred_previous'],${'nlvl'.$ind}['solde_deb_previous']);
                    $side_previous=($delta_previous < 0) ? "D":"C";
                    $pdf->Cell(30,6,"n-1 : " .nbm($delta_previous)." $side_previous",0,0,'R');
                     $pdf->Cell(30,6," n : ".nbm($delta)." $side",0,0,'R');
                    $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['sum_deb_previous']),0,0,'R');
                    $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['sum_cred_previous']),0,0,'R');
                    $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['solde_deb_previous']),0,0,'R');
                    $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['solde_cred_previous']),0,0,'R');
                } else {
                     $pdf->Cell(60,6,nbm($delta)." $side",0,0,'R');
                }
		$pdf->Cell(25,6,nbm(${'nlvl'.$ind}['sum_deb']),0,0,'R');
		$pdf->Cell(25,6,nbm(${'nlvl'.$ind}['sum_cred']),0,0,'R');
		$pdf->Cell(25,6,nbm(${'nlvl'.$ind}['solde_deb']),0,0,'R');
		$pdf->Cell(25,6,nbm(${'nlvl'.$ind}['solde_cred']),0,0,'R');
		$pdf->Ln();
		$pdf->SetFont('DejaVuCond','',7);
		${'lvl'.$ind.'_old'}=substr($r['poste'],0,$ind);
		foreach($a_sum as $a)
		  {
		    ${'nlvl'.$ind}[$a]=0;
		  }
	      }
	  }
	foreach($a_sum as $a)
	  {
	    $nlvl1[$a]=bcadd($nlvl1[$a],$r[$a]);
	    $nlvl2[$a]=bcadd($nlvl2[$a],$r[$a]);
	    $nlvl3[$a]=bcadd($nlvl3[$a],$r[$a]);
	  }

	if ( $i % 2 == 0 )
	  {
	    $pdf->SetFillColor(220,221,255);
	    $fill=1;
	  }
	else
	  {
	    $pdf->SetFillColor(0,0,0);
	    $fill=0;
	  }

	$pdf->LongLine(30,6,$value['poste'],0,'L',$fill);
	$pdf->LongLine(60,6,$value['label'],0,'L',$fill);
        if ($previous == 1 ) {
            $pdf->Cell(22,6,nbm($value['sum_deb_previous']),0,0,'R',$fill);
            $pdf->Cell(22,6,nbm($value['sum_cred_previous']),0,0,'R',$fill);
            $pdf->Cell(22,6,nbm($value['solde_deb_previous']),0,0,'R',$fill);
            $pdf->Cell(22,6,nbm($value['solde_cred_previous']),0,0,'R',$fill);
            $tp_deb_previous=bcadd($tp_deb_previous,$value['sum_deb_previous']);
            $tp_cred_previous=bcadd($tp_cred_previous,$value['sum_cred_previous']);
            $tp_sold_previous=bcadd($tp_sold_previous,$value['solde_deb_previous']);
            $tp_solc_previous=bcadd($tp_solc_previous,$value['solde_cred_previous']);
        }
	$pdf->Cell(25,6,nbm($value['sum_deb']),0,0,'R',$fill);
	$pdf->Cell(25,6,nbm($value['sum_cred']),0,0,'R',$fill);
	$pdf->Cell(25,6,nbm($value['solde_deb']),0,0,'R',$fill);
	$pdf->Cell(25,6,nbm($value['solde_cred']),0,0,'R',$fill);
	$pdf->Ln();
	$tp_deb=bcadd($tp_deb,$value['sum_deb']);
	$tp_cred=bcadd($tp_cred,$value['sum_cred']);
	$tp_sold=bcadd($tp_sold,$value['solde_deb']);
	$tp_solc=bcadd($tp_solc,$value['solde_cred']);

      }
    foreach (array(3,2,1) as $ind)
      {
	$r=$value;
	if ( ! isset($_GET['lvl'.$ind]))continue;

	if (${'lvl'.$ind.'_old'} == '')	  ${'lvl'.$ind.'_old'}=substr($r['poste'],0,$ind);
	if ( ${'lvl'.$ind.'_old'} != substr($r['poste'],0,$ind))
	  {
	    $pdf->SetFont('DejaVu','B',7);
	    $pdf->Cell(30,6,"Totaux ".$ind);
	    $pdf->Cell(60,6,${'lvl'.$ind.'_old'});
             if ($previous == 1 ) {
                $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['sum_deb_previous']),0,0,'R');
                $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['sum_cred_previous']),0,0,'R');
                $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['solde_deb_previous']),0,0,'R');
                $pdf->Cell(22,6,nbm(${'nlvl'.$ind}['solde_cred_previous']),0,0,'R');
             }
	    $pdf->Cell(25,6,nbm(${'nlvl'.$ind}['sum_deb']),0,0,'R');
	    $pdf->Cell(25,6,nbm(${'nlvl'.$ind}['sum_cred']),0,0,'R');
	    $pdf->Cell(25,6,nbm(${'nlvl'.$ind}['solde_deb']),0,0,'R');
	    $pdf->Cell(25,6,nbm(${'nlvl'.$ind}['solde_cred']),0,0,'R');
	    $pdf->Ln();
	    $pdf->SetFont('DejaVuCond','',7);
	    ${'lvl'.$ind.'_old'}=substr($r['poste'],0,$ind);
	    foreach($a_sum as $a)
	      {
		${'nlvl'.$ind}[$a]=0;
	      }
	  }
      }

    // Totaux
    $pdf->SetFont('DejaVuCond','B',8);
    $pdf->Cell(90,6,'Totaux');
     if ($previous == 1 ) {
        $pdf->Cell(22,6,nbm($tp_deb_previous),'T',0,'R',0);
        $pdf->Cell(22,6,nbm($tp_cred_previous),'T',0,'R',0);
        $pdf->Cell(22,6,nbm($tp_sold_previous),'T',0,'R',0);
        $pdf->Cell(22,6,nbm($tp_solc_previous),'T',0,'R',0);
    }
    $pdf->Cell(25,6,nbm($tp_deb),'T',0,'R',0);
    $pdf->Cell(25,6,nbm($tp_cred),'T',0,'R',0);
    $pdf->Cell(25,6,nbm($tp_sold),'T',0,'R',0);
    $pdf->Cell(25,6,nbm($tp_solc),'T',0,'R',0);
    $pdf->Ln();
  } /** empty */

$fDate=date('dmy-Hi');
$pdf->Output('balance-'.$fDate.'.pdf','D');



?>
