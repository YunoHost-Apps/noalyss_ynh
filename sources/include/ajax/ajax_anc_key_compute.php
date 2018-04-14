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


/**
 * @file
 * @brief Compute the amount. This file compute the amount and distribute it
 * following the given distribution key given in parameter.
 * Parameters are :
   - gDossier
   - t the element HTML to use as target
   - amount the amount to distribute
   - key the Distribution key to use
*/ 
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/class/anc_key.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

try
{
    $key=$http->get('key',"number");
    $amount=$http->get('amount',"number");
    $target=$http->get('t');
}
catch (Exception $exc)
{
    echo $exc->getMessage();
    error_log($exc->getTraceAsString());
    return;
}

$compute_key=new Anc_Key($key);
$pos=strrpos($target,"t");
$row=substr($target,$pos+1);

$compute_key->fill_table($target,$amount);
echo <<<EOF
<script>
anc_refresh_remain('$target','$row');
</script>
EOF;
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