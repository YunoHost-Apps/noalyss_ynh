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
 * \brief Logout
 */
require_once '../include/constant.php';
require_once ("lib/ac_common.php");
require_once('lib/database.class.php');
session_unset();

html_page_start("classic");

/* clean Global variable */

if ( isset ($g_user) ) unset ($GLOBAL['g_user']);
if ( isset ($g_pass) ) unset ($GLOBAL['g_pass']);
//
// Clean the possible cookies
//
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        @setcookie($name, '', time()-1000);
        @setcookie($name, '', time()-1000, '/');
    }
}
echo '<h2 class="info">'._('Vous êtes déconnecté').'</h2>';
echo '<META HTTP-EQUIV="REFRESH" content="0;url=index.html">';

html_page_stop();
?>
