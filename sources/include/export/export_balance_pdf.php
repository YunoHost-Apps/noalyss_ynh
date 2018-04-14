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
include_once("lib/ac_common.php");
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
include_once("class/acc_balance.class.php");
require_once  NOALYSS_INCLUDE.'/header_print.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/lib/pdf.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$gDossier=dossier::id();
bcscale(4);
$cn=Dossier::connect();
$rep=new Database();
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
$g_user->Check();

$bal=new Acc_Balance($cn);
try
{
    $from_periode=$http->request("from_periode");
    $to_periode=$http->request("to_periode");
    $from_poste=$http->request("from_poste");
    $to_poste=$http->request("to_poste");
    $p_filter=$http->request("p_filter","string");
}
catch (Exception $exc)
{
    error_log($exc->getTraceAsString());
    return;
}

// Compute for the summary
$summary_tab=$bal->summary_init();
$summary_prev_tab=$bal->summary_init();
$is_summary=$http->get("summary","string", 0);
  
$bal->jrn=null;
switch( $p_filter)
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

$bal->from_poste=$from_poste;
$bal->to_poste=$to_poste;
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

// If compare with previous exercice ,
// we use the landscape mode
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
$pdf->setTitle(_("Balance comptable"),true);
$pdf->write_cell(30,6,_('poste'));
$pdf->LongLine(60,6,_('Libellé'));
if ($previous == 1 ){ 
    $pdf->write_cell(20,6,'Débit N-1',0,0,'R');
    $pdf->write_cell(20,6,'Crédit N-1',0,0,'R');
    $pdf->write_cell(20,6,'Solde N-1',0,0,'R');
}
$pdf->write_cell(25,6,_('Ouverture'),0,0,'R');
$pdf->write_cell(25,6,_('Total Débit'),0,0,'R');
$pdf->write_cell(25,6,_('Total Crédit'),0,0,'R');
$pdf->write_cell(25,6,_('Solde Débiteur'),0,0,'R');
$pdf->line_new();

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
    $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred','sum_cred_previous','sum_deb_previous','solde_deb_previous','solde_cred_previous','sum_cred_ope','sum_deb_ope');
}
else {
    $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred','sum_cred_ope','sum_deb_ope') ;
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
                    $pdf->write_cell(60,6," ",0,0,'R');
                    $pdf->write_cell(22,6,nbm(${'nlvl'.$ind}['sum_deb_previous']),0,0,'R');
                    $pdf->write_cell(22,6,nbm(${'nlvl'.$ind}['sum_cred_previous']),0,0,'R');
                    $pdf->write_cell(22,6,nbm(abs($delta_previous))." $side_previous",0,0,'R');
                    
                } else {
                     $pdf->write_cell(60,6," ",0,0,'R');
                     
                }
                $solde_lv=bcsub(${'nlvl'.$ind}['sum_deb_ope'],${'nlvl'.$ind}['sum_cred_ope']);
                $side_lv=($solde_lv<0)?" C":" D";
                $side_lv=($solde_lv==0)?" ":$side_lv;
                $pdf->write_cell(25,6,nbm(abs($solde_lv)).$side_lv,0,0,'R');
		$pdf->write_cell(25,6,nbm(bcsub(${'nlvl'.$ind}['sum_deb'],${'nlvl'.$ind}['sum_deb_ope'])),0,0,'R');
		$pdf->write_cell(25,6,nbm(bcsub(${'nlvl'.$ind}['sum_cred'],${'nlvl'.$ind}['sum_cred_ope'])),0,0,'R');
		$solde_lv=bcsub(${'nlvl'.$ind}['solde_deb'],${'nlvl'.$ind}['solde_cred']);
                $side_lv=($solde_lv>0)?"D":"C";
                $side_lv=($solde_lv==0)?"":$side_lv;
                $pdf->write_cell(25,6,nbm(abs($solde_lv))." $side_lv",0,0,'R');
		$pdf->line_new();
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
        $summary_tab=$bal->summary_add($summary_tab,$value['poste'],
                 $value['sum_deb'],
                 $value['sum_cred']);
        if ($previous == 1 ) {
            $pdf->write_cell(22,6,nbm($value['sum_deb_previous']),0,0,'R',$fill);
            $pdf->write_cell(22,6,nbm($value['sum_cred_previous']),0,0,'R',$fill);
            
//            $pdf->write_cell(22,6,nbm($value['solde_deb_previous']),0,0,'R',$fill);
//            $pdf->write_cell(22,6,nbm($value['solde_cred_previous']),0,0,'R',$fill);
            $solde_previous=bcsub($value['solde_cred_previous'],$value['solde_deb_previous']);
            $side_previous=($solde_previous<0)?" D":" C";
            $side_previous=($solde_previous==0)?"":$side_previous;
            
            $pdf->write_cell(22,6,nbm(abs($solde_previous)).$side_previous,0,0,'R',$fill);
            
            $tp_deb_previous=bcadd($tp_deb_previous,$value['sum_deb_previous']);
            $tp_cred_previous=bcadd($tp_cred_previous,$value['sum_cred_previous']);
            $tp_sold_previous=bcadd($tp_sold_previous,$value['solde_deb_previous']);
            $tp_solc_previous=bcadd($tp_solc_previous,$value['solde_cred_previous']);
            $summary_prev_tab=$bal->summary_add($summary_prev_tab,
                                                $value['poste'],
                                                $value['sum_deb_previous'],
                                                $value['sum_cred_previous']);
        }
        $solde_ope=bcsub($value['sum_deb_ope'],$value['sum_cred_ope']);
        $side_ope=($solde_ope>0)?" D":"C";
        $side_ope=($solde_ope==0)?" ":$side_ope;
        
	$pdf->write_cell(25,6,nbm(abs($solde_ope)).$side_ope,0,0,'R',$fill);
	$pdf->write_cell(25,6,nbm(bcsub($value['sum_deb'],$value['sum_deb_ope'])),0,0,'R',$fill);
	$pdf->write_cell(25,6,nbm(bcsub($value['sum_cred'],$value['sum_cred_ope'])),0,0,'R',$fill);
        $solde=bcsub($value['sum_deb'],$value['sum_cred']);
        $side=($solde>0)?"D":"C";
        $side=($solde==0)?"":$side;
	$pdf->write_cell(25,6,nbm(abs($solde)).$side,0,0,'R',$fill);
	$pdf->line_new();
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
	    $pdf->write_cell(30,6,"Totaux ".$ind);
	    $pdf->write_cell(60,6,${'lvl'.$ind.'_old'});
             if ($previous == 1 ) {
                $pdf->write_cell(22,6,nbm(${'nlvl'.$ind}['sum_deb_previous']),0,0,'R');
                $pdf->write_cell(22,6,nbm(${'nlvl'.$ind}['sum_cred_previous']),0,0,'R');
                $pdf->write_cell(22,6,nbm(${'nlvl'.$ind}['solde_deb_previous']),0,0,'R');
                $pdf->write_cell(22,6,nbm(${'nlvl'.$ind}['solde_cred_previous']),0,0,'R');
             }
	    $pdf->write_cell(25,6,nbm(${'nlvl'.$ind}['sum_deb']),0,0,'R');
	    $pdf->write_cell(25,6,nbm(${'nlvl'.$ind}['sum_cred']),0,0,'R');
            $solde_lv=bcsub(${'nlvl'.$ind}['solde_deb'],${'nlvl'.$ind}['solde_cred']);
            $side_lv=($solde_lv>0)?"D":"C";
            $side_lv=($solde_lv==0)?"":$side_lv;
	    $pdf->write_cell(25,6,nbm(abs($solde_lv))." $side_lv",0,0,'R');
	    $pdf->line_new();
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
    $pdf->write_cell(90,6,$r['label']);
     if ($previous == 1 ) {
        $pdf->write_cell(22,6,nbm($tp_deb_previous),'T',0,'R',0);
        $pdf->write_cell(22,6,nbm($tp_cred_previous),'T',0,'R',0);
        $pdf->write_cell(22,6,nbm($tp_sold_previous),'T',0,'R',0);
        $pdf->write_cell(22,6,nbm($tp_solc_previous),'T',0,'R',0);
    }
    $pdf->write_cell(25,6,nbm($tp_deb),'T',0,'R',0);
    $pdf->write_cell(25,6,nbm($tp_cred),'T',0,'R',0);
    $pdf->write_cell(25,6,nbm($tp_sold),'T',0,'R',0);
    $pdf->write_cell(25,6,nbm($tp_solc),'T',0,'R',0);
    $pdf->line_new();
  } /** empty */
 // display the summary
 if ($is_summary==1) {
    if ($previous==1) {
        $pdf->SetFont('DejaVuCond', 'B', 8);
        $pdf->write_cell(50, 8, _("Résumé Exercice précédent"));
        $pdf->line_new();
        $pdf->SetFont('DejaVuCond', '', 7);
        $bal->summary_display_pdf($summary_prev_tab, $pdf);
        $pdf->line_new();
    }
    $pdf->SetFont('DejaVuCond', 'B', 8);
    $pdf->write_cell(50, 8, _("Résumé Exercice courant"));
    $pdf->line_new();
    $pdf->SetFont('DejaVuCond', '', 7);
    $bal->summary_display_pdf($summary_tab, $pdf);
}

$fDate=date('dmy-Hi');
$pdf->Output('balance-'.$fDate.'.pdf','D');



?>
