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
 * \brief Send a report in PDF
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once("class/acc_report.class.php");
include_once("lib/ac_common.php");
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
include_once("lib/impress.class.php");
require_once NOALYSS_INCLUDE.'/class/user.class.php';
require_once  NOALYSS_INCLUDE.'/header_print.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_report.class.php';
require_once NOALYSS_INCLUDE.'/lib/pdf.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$form_id=$http->get('form_id','number');
$type_periode=$http->get('type_periode',"number");


$gDossier=dossier::id();

$cn=Dossier::connect();

$ret="";
$Form=new Acc_Report($cn,$form_id);
$Libelle=sprintf("%s ",$Form->get_name());
$pdf= new PDF($cn);
$pdf->setDossierInfo($Libelle);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAuthor('NOALYSS');
$pdf->setTitle("Rapport ".$Libelle,true);

// Step ??
//--
$p_step=$http->get('p_step',"string",0);
if ( $step == 0 )
{
    // No step asked
    //--
    if ( $_GET ['type_periode'] == 0 ) 
    {
        $from_periode=$http->get('from_periode',"number");
        $to_periode=$http->get('to_periode',"number");
        $array=$Form->get_row( $from_periode,$to_periode, $type_periode);
    }
    else
    {
        $from_date=$http->get('from_date',"date");
        $to_date=$http->get('to_date',"date");
        $array=$Form->get_row( $from_date,$to_date, $type_periode);
    }

}
else
{
    // yes with step
    //--
    $from_periode=$http->get('from_periode',"number");
    $to_periode=$http->get('to_periode',"number");
    $p_step=$http->get('p_step',"number");
    
    for ($e=$from_periode;$e<=$to_periode;$e+=$p_step)
    {
        $periode=getPeriodeName($cn,$e);
        if ( $periode == null ) continue;
        $array[]=$Form->get_row($e,$e,'periode');
        $periode_name[]=$periode;
    }

}


$pdf->SetFont('DejaVuCond','',8);

// without step
if ( $step == 0 )
{
    if ( $type_periode == 0 )
    {
        $q=getPeriodeName($cn,$from_periode);
        if ( $from_periode != $to_periode)
        {
            $periode=sprintf(_("Période de %s à %s"),$q,getPeriodeName($cn,$to_periode));
        }
        else
        {
            $periode=sprintf(_("Période %s"),$q);
        }
    }
    else
    {
        $periode=sprintf(_("Date %s jusque %s"),$from_date,$to_date);
    }
    $pdf->write_cell(0,7,$periode,'B');
    $pdf->line_new();
    for ($i=0;$i<count($array);$i++)
    {
        $pdf->write_cell(160,6,$array[$i]['desc']);
        $pdf->write_cell(30,6,sprintf('% 12.2f',$array[$i]['montant']),0,0,'R');
        $pdf->line_new();
    }
}
else
{ // With Step
    $a=0;
    foreach ($array as $e)
    {
        $pdf->write_cell(0,7,$periode_name[$a],'B');
        $pdf->line_new();
        $a++;
        for ($i=0;$i<count($e);$i++)
        {
            $pdf->write_cell(160,6,$e[$i]['desc']);
            $pdf->write_cell(30,6,sprintf('% 12.2f',$e[$i]['montant']),0,0,'R');
            $pdf->line_new();
        }
    }
}

$fDate=date('dmy-Hi');
$pdf->Output('rapport-'.$fDate.'.pdf','D');

?>
