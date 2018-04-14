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
  /**
   *@file
   *@brief zoom the sheduler
   */
if ( !defined ('ALLOWED') )  die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/calendar.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

ob_start();
if ($notitle==0)
{
    echo HtmlInput::title_box(_("Calendrier"), "calendar_zoom_div", "close", "", "y");
}
$cal=new Calendar();
$in=$http->get('in',"string","");
$notitle=$http->get('notitle',"string","0");
if ( $in == "") {
    $in=$cal->get_preference();
}
$cal->set_periode($in);
echo $cal->zoom($distype,$notitle);
if ( $notitle== 0 ) {
    echo '<p style="text-align:center">';
    echo HtmlInput::button_close("calendar_zoom_div");
    echo '</p>';
}
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
