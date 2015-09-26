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

/*!\file
 * \brief export definition of a report
 */

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once   NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/user_common.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_acc_report.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
if ( ! isset($_GET['gDossier']) ||
        ! isset($_GET['f']) )
{
    $a='Paramètre manquant';
    header("Content-type: text/html; charset: utf8",true);
    print $a;
    exit();
}

$gDossier=dossier::id();
if ( ! is_dir('tmp') )
{
    mkdir ('tmp');
}

$cn=new Database($gDossier);
$rap=new Acc_Report($cn,$_GET['f']);

$file= fopen('php://output',"a+");
header('Pragma: public');
header('Content-type: application/bin');
header('Content-Disposition: attachment;filename="export.bin"',FALSE);
$rap->export_csv($file);
?>
