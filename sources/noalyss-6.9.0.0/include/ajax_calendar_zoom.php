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
if ( !defined ('ALLOWED') )  die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_calendar.php';
ob_start();
if ( $notitle== 0 ) echo HtmlInput::title_box(_("Calendrier"), "calendar_zoom_div");
$cal=new Calendar();
$in=HtmlInput::default_value_get('in',"");
$notitle=HtmlInput::default_value_get('notitle',"0");
if ( $in == "") {
    $in=$cal->get_preference();
}
$cal->set_periode($in);
echo $cal->zoom($distype,$notitle);
$response=  ob_get_clean();

$html=escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<html>$html</html>
</data>
EOF;
exit();


?>
