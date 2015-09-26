<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * Export to CSV the operations asked in impress_rec.inc.php
 * variable set $g_user,$cn
 * @see impress_rec.inc.php
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
$Date=date('Ymd');
$filename="reconcialed_operation-".$Date;

header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="'.$filename.'.csv"',FALSE);

require_once NOALYSS_INCLUDE.'/class_acc_reconciliation.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
// --------------------------
// Check if all mandatory arg are passed
foreach (array('choice','p_end','p_start') as $arg)
{
    if ( ! isset ($_GET[$arg])) {
        die ("argument [".$arg."] is missing");
    }
}
extract($_GET);
$r_jrn=(isset($r_jrn))?$r_jrn:'';
// -------------------------
// Create object and export
$acc_reconciliation=new Acc_Reconciliation($cn);
$acc_reconciliation->a_jrn=$r_jrn;
$acc_reconciliation->start_day=$p_start;
$acc_reconciliation->end_day=$p_end;

$array=$acc_reconciliation->export_csv($choice);