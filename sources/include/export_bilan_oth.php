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
 * \brief send a Bilan in RTF format
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once("ac_common.php");
include_once("class_impress.php");
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/header_print.php';
require_once  NOALYSS_INCLUDE.'/class_acc_bilan.php';

require_once   NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
$gDossier=dossier::id();

/* Admin. Dossier */
$cn=new Database($gDossier);

$bilan=new Acc_Bilan($cn);
$bilan->get_request_get();
$bilan->load();

if ( $bilan->b_type=='odt')
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: must-revalidate");
    header('Content-type: application/vnd.oasis.opendocument.text');
    header('Content-Disposition: attachment;filename="'.$bilan->b_name.'.odt"',FALSE);
    header("Accept-Ranges: bytes");

}
if ( $bilan->b_type=='ods')
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: must-revalidate");
    header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
    header('Content-Disposition: attachment;filename="'.$bilan->b_name.'.ods"',FALSE);
    header("Accept-Ranges: bytes");

}

$bilan->generate();
?>
