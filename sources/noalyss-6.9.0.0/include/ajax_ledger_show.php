<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
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

require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
if ( ! isset ($r_jrn)) { $r_jrn=null;}
$ctl='div_jrn'.$div;
ob_start();
echo HtmlInput::select_ledger($type,$r_jrn, $div);

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