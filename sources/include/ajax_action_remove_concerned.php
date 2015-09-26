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

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
$ag_id=HtmlInput::default_value_get("ag_id", "0");
$f_id=HtmlInput::default_value_get("f_id", "0");
if ( $ag_id == 0 || $f_id == 0 ) throw new Exception ("Invalid values", 0);
require_once 'class_follow_up.php';
$follow=new Follow_Up($cn,$ag_id);

ob_start();
$follow->remove_linked_card($f_id);
echo $follow->display_linked();
HtmlInput::button_action_add_concerned_card( $follow->ag_id);

$response = ob_get_clean();

$html = escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>unused</ctl>
<code>$html</code>
</data>
EOF;
?>        