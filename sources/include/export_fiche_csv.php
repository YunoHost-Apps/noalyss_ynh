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
 * \brief Send a CSV file with card
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="fiche.csv"',FALSE);
include_once ("ac_common.php");
include_once('class_fiche.php');
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
$gDossier=dossier::id();

$cn=new Database($gDossier);

require_once  NOALYSS_INCLUDE.'/class_user.php';



if  ( isset ($_GET['fd_id']))
{
    $fiche_def=new Fiche_Def($cn,$_GET ['fd_id']);
    $fiche=new Fiche($cn);
    $e=$fiche_def->get_by_type();
    $o=0;
    //  Heading
    $fiche_def->GetAttribut();
    foreach ($fiche_def->attribut as $attribut)
    {
        if ( $o == 0 )
        {
            printf("%s",$attribut->ad_text);
            $o=1;
        }
        else
        {
            printf(";%s",$attribut->ad_text);
        }
    }
    printf("\n");
    $o=0;
    // Details

    foreach ($e as $fiche)
      {
	$detail=new Fiche($cn,$fiche['f_id']);

	$detail->getAttribut();

        foreach ( $detail->attribut as $dattribut )
        {
            if ( $o == 0 )
            {
                printf("%s",$dattribut->av_text);
                $o=1;
            }
            else
            {
                printf (";%s",$dattribut->av_text);

            }
        }
        printf("\n");
        $o=0;
    }


}
exit;
?>
