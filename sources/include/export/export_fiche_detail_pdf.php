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
 * \brief send the account list in PDF
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once("class/acc_account_ledger.class.php");
include_once("lib/ac_common.php");
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
include_once("lib/impress.class.php");
require_once NOALYSS_INCLUDE.'/class/fiche.class.php';
require_once  NOALYSS_INCLUDE.'/header_print.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_operation.class.php';
require_once NOALYSS_INCLUDE.'/lib/pdf.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$f_id=$http->request("f_id", "number");
$from_periode=$http->get("from_periode","date");
$to_periode=$http->get("to_periode","date");
$ople=$http->get("ople");

$gDossier=dossier::id();

$cn=Dossier::connect();

$ret="";
$pdf= new PDF($cn);
$pdf->setDossierInfo("  Periode : ".$from_periode." - ".$to_periode);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAuthor('NOALYSS');
$pdf->setTitle(_("Détail fiche"),true);


$Fiche=new Fiche($cn,$f_id);
$operation=new Acc_Operation($cn);

list($array,$tot_deb,$tot_cred)=$Fiche->get_row_date($from_periode,$to_periode,$_GET['ople']);
// don't print empty account
if ( count($array) == 0 )
{
    exit;
}
$size=array(13,25,20,60,12,20,20,20);
$align=array('L','C','C','L','R','R','R','R');

$Libelle=sprintf("(%s) %s [ %s ]",$Fiche->id,$Fiche->getName(),$Fiche->get_quick_code());
$pdf->SetFont('DejaVu','',10);
$pdf->write_cell(0,8,$Libelle,1,0,'C');
$pdf->line_new();


$pdf->SetFont('DejaVuCond','',8);
$l=0;
$pdf->write_cell($size[$l],6,'Date',0,0,'L');
$l++;
$pdf->write_cell($size[$l],6,'Ref',0,0,'C');
$l++;
$pdf->write_cell($size[$l],6,'Journal',0,0,'C');
$l++;
$pdf->write_cell($size[$l],6,'Libellé',0,0,'L');
$l++;
$pdf->write_cell($size[$l],6,'Let',0,0,'R');
$l++;
$pdf->write_cell($size[$l],6,'Debit',0,0,'R');
$l++;
$pdf->write_cell($size[$l],6,'Credit',0,0,'R');
$l++;
$pdf->write_cell($size[$l],6,'Prog',0,0,'R');
$l++;
$pdf->line_new();
$tot_deb=0;
$tot_cred=0;
$progress=0;
$current_exercice="";
bcscale(2);
for ($e=0;$e<count($array);$e++)
{
    $row=$array[$e];
    /*
     * separation per exercice
     */
    if ( $current_exercice == "") $current_exercice=$row['p_exercice'];

    if ( $current_exercice != $row['p_exercice']) {
            $str_debit=sprintf("% 12.2f €",$tot_deb);
            $str_credit=sprintf("% 12.2f €",$tot_cred);
            $diff_solde=bcsub($tot_deb,$tot_cred);
            if ( $diff_solde < 0 )
            {
                $solde=" créditeur ";
                $diff_solde=bcmul($diff_solde,-1);
            }
            else
            {
                 $solde=" débiteur ";
            }
            $str_diff_solde=sprintf("%12.2f €",$diff_solde);

            $pdf->SetFont('DejaVu','B',8);
            $pdf->LongLine(15,6,_('totaux'),0,'L');
            $pdf->write_cell(15,6,$current_exercice,0,0,'L');
            $pdf->LongLine(40,6,$solde,0,'L');
            $pdf->write_cell(40,6,$str_debit,0,0,'R');
            $pdf->write_cell(40,6,$str_credit,0,0,'R');
            $pdf->write_cell(40,6,$str_diff_solde,0,0,'R');
            $pdf->line_new();
            /*
            * reset total and current_exercice
            */
            $prog=0;
            $current_exercice=$row['p_exercice'];
            $tot_deb=0;$tot_cred=0;    
            $pdf->SetFont('DejaVuCond','',8);
    }
    $l=0;
    $delta=bcsub($row['deb_montant'],$row['cred_montant']);
    $progress=bcadd($progress,$delta);

    $date=shrink_date($row['j_date_fmt']);
    $pdf->write_cell($size[$l],6,$date,0,0,$align[$l]);
    $l++;
    if ( $row['jr_pj_number'] == '')
      $pdf->write_cell($size[$l],6,"",0,0,$align[$l]);
    else
      $pdf->write_cell($size[$l],6,$row['jr_pj_number'],0,0,$align[$l]);

    $l++;
    $pdf->write_cell($size[$l],6,mb_substr($row['jrn_def_code'],0,14),0,0,$align[$l]);
    $l++;
    $tiers=$operation->find_tiers($row['jr_id'], $row['j_id'], $row['j_qcode']);
    $description=($tiers=="")?$row["description"]:"[".$tiers."]".$row['description'];

    $pdf->LongLine($size[$l],6,($description.'('.$row['jr_internal'].")"),0,$align[$l]);

    $l++;
    $pdf->LongLine($size[$l],6,(($row['letter']!=-1)?strtoupper(base_convert($row['letter'],10,36)):''),0,$align[$l]);
    $l++;
    $pdf->LongLine($size[$l],6,(sprintf('% 12.2f',$row['deb_montant'])),0,$align[$l]);
    $l++;
    $pdf->LongLine($size[$l],6,(sprintf('% 12.2f',$row['cred_montant'])),0,$align[$l]);
    $l++;
    $pdf->LongLine($size[$l],6,(sprintf('% 12.2f',abs($progress))),0,$align[$l]);
    $l++;
    $pdf->line_new();
    $tot_deb=bcadd($tot_deb,$row['deb_montant']);
    $tot_cred=bcadd($tot_cred,$row['cred_montant']);
    /* -------------------------------------- */
    /* if details are asked we show them here */
    /* -------------------------------------- */
    if ( isset($_GET['oper_detail']))
    {
        $detail=new Acc_Operation($cn);
        $detail->jr_id=$row['jr_id'];
        $a_detail=$detail->get_jrnx_detail();
        for ($f=0;$f<count($a_detail);$f++)
        {
            $l=0;
            $pdf->write_cell($size[$l],6,'',0,0,$align[$l]);
            $l++;
            $pdf->write_cell($size[$l],6,$a_detail[$f]['j_qcode'],0,0,'L');
            $l++;
            $pdf->write_cell($size[$l],6,$a_detail[$f]['j_poste'],0,0,'R');
            $l++;
            if ( $a_detail[$f]['j_qcode']=='')
                $lib=$a_detail[$f]['pcm_lib'];
            else
            {
                $f_id=$cn->get_value('select f_id from vw_poste_qcode where j_qcode=$1',array($a_detail[$f]['j_qcode'])) ;
                $lib=$cn->get_value('select ad_value from fiche_detail where ad_id=$1 and f_id=$2',
                                    array(ATTR_DEF_NAME,$f_id));
            }
            $pdf->write_cell($size[$l],6,$lib,0,0,$align[$l]);
            $l++;
            $pdf->write_cell($size[$l],6,(($a_detail[$f]['letter']!=-1)?$a_detail[$f]['letter']:''),0,0,$align[$l]);
            $l++;

            $deb=($a_detail[$f]['debit']=='D')?$a_detail[$f]['j_montant']:'';
            $cred=($a_detail[$f]['debit']=='C')?$a_detail[$f]['j_montant']:'';

            $pdf->write_cell($size[$l],6,(sprintf('% 12.2f',$deb)),0,0,$align[$l]);
            $l++;
            $pdf->write_cell($size[$l],6,(sprintf('% 12.2f',$cred)),0,0,$align[$l]);
            $l++;
            $pdf->line_new();
        }
    }

}
$str_debit=sprintf("% 12.2f €",$tot_deb);
$str_credit=sprintf("% 12.2f €",$tot_cred);
$diff_solde=$tot_deb-$tot_cred;
if ( $diff_solde < 0 )
{
    $solde=" créditeur ";
    $diff_solde*=-1;
}
else
{
    $solde=" débiteur ";
}
$str_diff_solde=sprintf("%12.2f €",$diff_solde);

$pdf->SetFont('DejaVu','B',8);

$pdf->write_cell(160,5,'Débit',0,0,'R');
$pdf->write_cell(30,5,$str_debit,0,0,'R');
$pdf->line_new();
$pdf->write_cell(160,5,'Crédit',0,0,'R');
$pdf->write_cell(30,5,$str_credit,0,0,'R');
$pdf->line_new();
$pdf->write_cell(160,5,'Solde '.$solde,0,0,'R');
$pdf->write_cell(30,5,$str_diff_solde,0,0,'R');
$pdf->line_new();

$fDate=date('dmy-Hi');
$pdf->Output('fiche-'.$fDate.'.pdf','D');


?>
