<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 *@file
 * @brief
 * Show a div for selecting ledger
 * return a html code for creating a window
 * parameter 
 *   - type 
 *   - div
 *   - nbjrn
 *   - r_jrn[]
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/class/acc_ledger_search.class.php';
require_once NOALYSS_INCLUDE.'/lib/html_input.class.php';
if ( ! isset ($r_jrn)) { $r_jrn=null;}
$ctl='div_jrn'.$div;
ob_start();
$ledger=new Acc_Ledger_Search($type,1,$ctl);
echo $ledger->select_ledger($r_jrn,$div);

$response = ob_get_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$html</code>
</data>
EOF;
exit();
?>    