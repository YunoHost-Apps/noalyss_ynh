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
/**
 * @file
 * @brief show the available distribution keys for analytic activities. Expected
 * parameter are 
 *  - t for the table id
 *  - amount is the amount to distributed
 *
 */
// Copyright (2014) Author Dany De Bontridder danydb@aevalys.eu
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
$amount=HtmlInput::default_value_get("amount", 0);
$table_id=HtmlInput::default_value_get("t", "");
$ledger=HtmlInput::default_value_get('led',0);

if ($table_id == "" || isNumber($amount) == 0 || isNumber($ledger) == 0) die ('Invalid Parameter');

require_once 'class_anc_key.php';

ob_start();
echo HtmlInput::title_box(_("Choix d'une clef"), 'div_anc_key_choice');

Anc_Key::display_choice($amount,$table_id,$ledger);

echo HtmlInput::button_close('div_anc_key_choice');
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