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
 * \brief Send a ledger in a pdf format
 *
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
$gDossier = dossier::id();
require_once NOALYSS_INCLUDE.'/lib/pdf.class.php';
require_once NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/impress.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger.class.php';
require_once NOALYSS_INCLUDE.'/class/noalyss_parameter_folder.class.php';
require_once NOALYSS_INCLUDE.'/class/periode.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';

$http=new HttpInput();
$cn = Dossier::connect();
$periode = new Periode($cn);
try
{
    $jrn_id=$http->get('jrn_id',"number");
    $p_simple=$http->get('p_simple',"string");

    
}
catch (Exception $exc)
{
    echo $exc->getMessage();
    error_log($exc->getTraceAsString());
    throw $exc;
}
$l_type = "JRN";
$own = new Noalyss_Parameter_Folder($cn);

$Jrn = new Acc_Ledger($cn, $jrn_id);

$Jrn->get_name();
$g_user->Check();
$g_user->check_dossier($gDossier);

// Security
if ($jrn_id != 0 && $g_user->check_jrn($jrn_id) == 'X') {
    /* Cannot Access */
    NoAccess();
}

$ret = "";

$jrn_type = $Jrn->get_type();

$pdf = Print_Ledger::factory($cn, $p_simple, "PDF", $Jrn);

$pdf->setDossierInfo($Jrn->name);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAuthor('NOALYSS');
$pdf->setTitle(_("Journal"), true);

$pdf->export();

$fDate = date('dmy-Hi');
$pdf->Output('journal-' . $fDate . '.pdf', 'D');
exit(0);


?>
