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
include_once '../../include/constant.php';
require_once NOALYSS_INCLUDE.'/lib/pdf.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/class/noalyss_parameter_folder.class.php';
bcscale(2);
$_REQUEST['gDossier']=37;
$_GET['gDossier']=37;
$_POST['gDossier']=37;
$cn=Dossier::connect();
$pdf = new PDF($cn);
$pdf->setDossierInfo("  Testing PDF ");
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle("Test long line",true);
$pdf->SetAuthor('Testing');

// Header
$header = array( "Date", "Référence", "Libellé", "Pièce","Let", "Débit", "Crédit", "Solde" );
// Left or Right aligned
$lor    = array( "L"   , "L"        , "L"      , "L"    , "R",   "R"    , "R"     , "R"     );
// Column widths (in mm)
$width  = array( 13    , 20         , 60       , 15     ,  12     , 20     , 20      , 20      );
$detail=array('j_date_fmt'=>'01.07.2015',
            'jr_internal'=>'A000',
            'description'=>'Opération très longue , normalement sur plusieurs lignes, les pages ne doivent pas "sautés"'.
    "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non , vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?",
            'jr_pj_number'=>'ACH',
            'letter'=>123,
            'deb_montant'=>12.002,
            'cred_montant'=>0);
            
for ($j=0;$j<20;$j++)
{

    $pdf->SetFont('DejaVuCond','',10);
    $Libelle=sprintf("%s - %s ",'600001','Charges');
    $pdf->write_cell(0, 7, $Libelle, 1, 1, 'C');

    $pdf->SetFont('DejaVuCond','',9);
    for($i=0;$i<count($header);$i++)
        $pdf->write_cell($width[$i], 4, $header[$i], 0, 0, $lor[$i]);
    
    $pdf->line_new();
    $detail['jr_internal']=sprintf('A000%04d',$j);
    $pdf->SetFont('DejaVuCond','',9);

    $i = 0;
    $solde=bcsub($detail['deb_montant'],$detail['cred_montant']);
    $side=" D";
    $pdf->LongLine($width[$i], 6, shrink_date($detail['j_date_fmt']), 0, $lor[$i]);
    $i++;
    $pdf->LongLine($width[$i], 6, $detail['jr_internal'], 0, $lor[$i] );
    $i++;
    /* limit set to 40 for the substring */
    $triple_point = (mb_strlen($detail['description']) > 40 ) ? '...':'';
    // $pdf->LongLine($width[$i], 6, mb_substr($detail['description'],0,40).$triple_point, 0,$lor[$i]);
    $pdf->LongLine($width[$i], 6,$detail['description'], 0,$lor[$i]);
    $i++;
    $pdf->write_cell($width[$i], 6, $detail['jr_pj_number'], 1, 0, $lor[$i]);
    $i++;
    $pdf->write_cell($width[$i], 6, ($detail['letter']!=-1)?$detail['letter']:'', 1, 0, $lor[$i]);
    $i++;
    $pdf->write_cell($width[$i], 6, ($detail['deb_montant']  > 0 ? nbm( $detail['deb_montant'])  : ''), 0, 0, $lor[$i]);
    $i++;
    $pdf->write_cell($width[$i], 6, ($detail['cred_montant'] > 0 ? nbm( $detail['cred_montant']) : ''), 0, 0, $lor[$i]);
    $i++;
    $pdf->write_cell($width[$i], 6, nbm(abs( $solde)).$side, 0, 0, $lor[$i]);
    $i++;
    $pdf->line_new();

}
//Save PDF to file
$pdf->Output("testing-long-line.pdf", 'D');
exit;
?>
