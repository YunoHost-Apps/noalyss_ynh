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
 * \brief Send a report in CSV format
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_report.class.php';
require_once NOALYSS_INCLUDE.'/lib/impress.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/lib/noalyss_csv.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$gDossier=dossier::id();

/* Admin. Dossier */
$cn=Dossier::connect();

$form_id=$http->get('form_id','number');
$type_periode=$http->get('type_periode',"number");



$Form=new Acc_Report($cn,$form_id);
$Form->get_name();

$export=new Noalyss_Csv('report');
$export->send_header();
// Step ?
//--
$p_step=$http->get('p_step',"string",0);
if (  $step == 0 )
{
    if ( $type_periode == 0 )
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


    if ( count($Form->row ) == 0 )
        exit;

    $title=array(_("Description"),
                _("Montant"));

    $export->write_header($title);

    foreach ( $Form->row as $op )
    {
        $export->add($op['desc']);
        $export->add($op['montant'],"number");
        $export->write();
    }
}
elseif ($step == 1)
{
    // Gather all the data
    //---
    $from_periode=$http->get('from_periode',"number");
    $to_periode=$http->get('to_periode',"number");
    $p_step=$http->get('p_step',"number");
    
    for ($e=$from_periode;$e<=$to_periode;$e+=$p_step)
    {
        $periode=getPeriodeName($cn,$e);
        if ( $periode == null ) continue;
        $array[]=$Form->get_row($e,$e,$type_periode);
        $periode_name[]=$periode;
    }
    // Display column heading
    //--
    $title=array();
    $title[0]=_("Mois");
    $i=1;
    foreach ($array[0] as $e)
    {
        $title[$i]=$e['desc'];
        $i++;
    }
    $export->write_header($title);
    // Display value for each line
    //--
    $a=0;
    foreach ($array as $e )
    {
        $export->add( $periode_name[$a]);
        $a++;
        foreach ($e as $elt)
        {
            $export->add($elt['montant'],"number");
        }
        $export->write();
    }
}
exit;
?>
