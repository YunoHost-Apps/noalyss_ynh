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

// Copyright 2014 Author Dany De Bontridder danydb@aevalys.eu
// @brief Compute the amount. This file compute the amount and distribute it
// following the given distribution key given in parameter.
// Parameters are :
//   - gDossier
//   - t the element HTML to use as target
//   - amount the amount to distribute
//   - key the Distribution key to use
// 
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/class_anc_key.php';
ob_start();
/////
$key=HtmlInput::default_value_get('key',0);
$amount=HtmlInput::default_value_get('amount',0);
$target=HtmlInput::default_value_get('t','');

if (        isNumber($key)== 0
        ||  isNumber($amount) ==0
        || $target==''
    ) 
{
    die ('Invalid parameter');
}

$compute_key=new Anc_Key($key);

$compute_key->fill_table($target,$amount);

////
$response = ob_get_clean();
$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
</data>
EOF;
?>        