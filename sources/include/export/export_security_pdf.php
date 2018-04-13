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
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
$gDossier=dossier::id();
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/pdf.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$cn=Dossier::connect();
try
{
    $user_id=$http->get("user_id");
}
catch (Exception $exc)
{
    error_log($exc->getTraceAsString());
    return;
}

//-----------------------------------------------------
// Security

// Check User
$rep=new Database();
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
$User=new User($rep);

//-----------------------------------------------------
// Get User's info

$SecUser=new User($rep,$user_id);
$admin=0;
$access=$SecUser->get_folder_access($gDossier);

if ( $access == 'L')
{
    $str=_('Local Admin');
    $admin=1;
}
elseif ($access=='R')
{
    $str=_('Utilisateur normal');
}
elseif ($access=='P')
{
    $str=_('Extension uniquement');
}


if ( $SecUser->admin==1 )
{
    $str=_(' Super Admin');
    $admin=1;
}


//-----------------------------------------------------
// Print result

$pdf=new PDF($cn);
$pdf->setDossierInfo(dossier::name()._(' Sécurité'));
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAuthor('NOALYSS');
$pdf->setTitle(_("Sécurité"),true);

$str_user=sprintf("( %d ) %s %s [ %s ] - %s",
                  $SecUser->id,
                  $SecUser->first_name,
                  $SecUser->name,
                  $SecUser->login,
                  $str);

$pdf->SetFont('DejaVu','B',9);
$pdf->write_cell(0,7,$str_user,'B',0,'C');
$pdf->line_new();
if ( $SecUser->active==0)
{
    $pdf->SetTextColor(255,0,34);
    $pdf->write_cell(0,7,_('Bloqué'),0,0,'R');
    $pdf->line_new();
}

if ( $SecUser->admin==1)
{
    $pdf->SetTextColor(0,0,0);
    $pdf->setFillColor(239,251,255);
    $pdf->write_cell(40,7,_('Administrateur'),1,1,'R');
    $pdf->line_new();
}
$pdf->SetTextColor(0,0,0);

//-----------------------------------------------------
// Journal
$pdf->write_cell(0,7,_('Accès journaux'),1,0,'C');
$pdf->line_new();
$pdf->SetFont('DejaVu','',6);
$Res=$cn->exec_sql("select jrn_def_id,jrn_def_name  from jrn_def ");
$SecUser->db=$cn;
for ($e=0;$e < Database::num_row($Res);$e++)
{
    $row=Database::fetch_array($Res,$e);
    $pdf->write_cell(40,6,$row['jrn_def_name']);
    $priv=$SecUser->check_jrn($row['jrn_def_id']);
    switch($priv)
    {
    case 'X':
            $pdf->SetTextColor(255,0,34);
        $pdf->write_cell(30,6,_("Pas d'accès"));
        break;
    case 'R':
        $pdf->SetTextColor(54,233,0);
        $pdf->write_cell(30,6,_("Lecture"));
        break;
    case 'O':
        /**
         *non implemented
         */
        $pdf->write_cell(30,6,_("Opérations prédéfinies uniquement"));
        break;
    case 'W':
        $pdf->SetTextColor(54,233,0);
        $pdf->write_cell(30,6,_('Ecriture'));
        break;
    }
    $pdf->SetTextColor(0);
    $pdf->line_new();
}

//-----------------------------------------------------
// Follow_Up
$pdf->SetFont('DejaVu','B',9);
$pdf->write_cell(0,7,_('Accès action'),1,0,'C');
$pdf->line_new();
$pdf->SetFont('DejaVu','',6);
$Res=$cn->exec_sql(
         "select ac_id, ac_description from action   order by ac_description ");

$Max=Database::num_row($Res);

for ( $i =0 ; $i < $Max; $i++ )
{
    $l_line=Database::fetch_array($Res,$i);
    $pdf->write_cell(90,6,$l_line['ac_description']);
    $right=$SecUser->check_action($l_line['ac_id']);
    switch ($right)
    {
    case 0:
        $pdf->SetTextColor(255,0,34);

        $pdf->write_cell(30,6,_("Pas d'accès"));
        break;
    case 1:
    case 2:
        $pdf->SetTextColor(54,233,0);
        $pdf->write_cell(30,6,_("Accès"));
        break;
    }
    $pdf->SetTextColor(0);

    $pdf->line_new();
}
$fDate=date('dmy-HI');
$pdf->Output('security-'.$fDate.'.pdf','D');
?>
