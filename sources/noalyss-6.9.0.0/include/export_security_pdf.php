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
// Copyright Stanislas Pinte stanpinte@sauvages.be

/*! \file
 * \brief Print the user security in pdf
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_dossier.php';
$gDossier=dossier::id();
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_pdf.php';
$cn=new Database($gDossier);
//-----------------------------------------------------
// Security

// Check User
$rep=new Database();
require_once  NOALYSS_INCLUDE.'/class_user.php';
$User=new User($rep);

//-----------------------------------------------------
// Get User's info
if ( ! isset($_GET['user_id']) )
    return;

$SecUser=new User($rep,$_GET['user_id']);
$admin=0;
$access=$SecUser->get_folder_access($gDossier);

if ( $access == 'L')
{
    $str='Local Admin';
    $admin=1;
}
elseif ($access=='R')
{
    $str=' Utilisateur normal';
}
elseif ($access=='P')
{
    $str=' Extension uniquement';
}


if ( $SecUser->admin==1 )
{
    $str=' Super Admin';
    $admin=1;
}


//-----------------------------------------------------
// Print result

$pdf=new PDF($cn);
$pdf->setDossierInfo(dossier::name().' Sécurité');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAuthor('NOALYSS');
$pdf->setTitle("Sécurité",true);

$str_user=sprintf("( %d ) %s %s [ %s ] - %s",
                  $SecUser->id,
                  $SecUser->first_name,
                  $SecUser->name,
                  $SecUser->login,
                  $str);

$pdf->SetFont('DejaVu','B',9);
$pdf->Cell(0,7,$str_user,'B',0,'C');
$pdf->Ln();
if ( $SecUser->active==0)
{
    $pdf->SetTextColor(255,0,34);
    $pdf->Cell(0,7,'Bloqué',0,0,'R');
    $pdf->Ln();
}

if ( $SecUser->admin==1)
{
    $pdf->SetTextColor(0,0,0);
    $pdf->setFillColor(239,251,255);
    $pdf->Cell(40,7,'Administrateur',1,1,'R');
    $pdf->Ln();
}
$pdf->SetTextColor(0,0,0);

//-----------------------------------------------------
// Journal
$pdf->Cell(0,7,'Accès journaux',1,0,'C');
$pdf->Ln();
$pdf->SetFont('DejaVu','',6);
$Res=$cn->exec_sql("select jrn_def_id,jrn_def_name  from jrn_def ");
$SecUser->db=$cn;
for ($e=0;$e < Database::num_row($Res);$e++)
{
    $row=Database::fetch_array($Res,$e);
    $pdf->Cell(40,6,$row['jrn_def_name']);
    $priv=$SecUser->check_jrn($row['jrn_def_id']);
    switch($priv)
    {
    case 'X':
            $pdf->SetTextColor(255,0,34);
        $pdf->Cell(30,6,"Pas d'accès");
        break;
    case 'R':
        $pdf->SetTextColor(54,233,0);
        $pdf->Cell(30,6,"Lecture");
        break;
    case 'O':
        /**
         *non implemente
         */
        $pdf->Cell(30,6,"Opérations prédéfinies uniquement");
        break;
    case 'W':
        $pdf->SetTextColor(54,233,0);
        $pdf->Cell(30,6,'Ecriture');
        break;
    }
    $pdf->SetTextColor(0);
    $pdf->Ln();
}

//-----------------------------------------------------
// Follow_Up
$pdf->SetFont('DejaVu','B',9);
$pdf->Cell(0,7,'Accès action',1,0,'C');
$pdf->Ln();
$pdf->SetFont('DejaVu','',6);
$Res=$cn->exec_sql(
         "select ac_id, ac_description from action   order by ac_description ");

$Max=Database::num_row($Res);

for ( $i =0 ; $i < $Max; $i++ )
{
    $l_line=Database::fetch_array($Res,$i);
    $pdf->Cell(90,6,$l_line['ac_description']);
    $right=$SecUser->check_action($l_line['ac_id']);
    switch ($right)
    {
    case 0:
        $pdf->SetTextColor(255,0,34);

        $pdf->Cell(30,6,"Pas d'accès");
        break;
    case 1:
    case 2:
        $pdf->SetTextColor(54,233,0);
        $pdf->Cell(30,6,"Accès");
        break;
    }
    $pdf->SetTextColor(0);

    $pdf->Ln();
}
$fDate=date('dmy-HI');
$pdf->Output('security-'.$fDate.'pdf','D');
?>
