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

require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_user.php';

$cn=new Database($_GET['gDossier']);
global $g_user;
$g_user=new User($cn);
$g_user->Check();
$g_user->check_dossier($_GET['gDossier']);
$res=$cn->exec_sql("select distinct code,description from get_profile_menu($1) where code ~* $2 or description ~* $3 order by code limit 5  ",array($g_user->get_profile(),$_POST['acs'],$_POST['acs']));
$nb=Database::num_row($res);
	echo "<ul>";
for ($i = 0;$i< $nb;$i++)
{
	$row=Database::fetch_array($res,$i);
	echo "<li>";
	echo $row['code'];
	echo '<span class="informal"> '.$row['description'].'</span></li>';
}
	echo "</ul>";
if ( $nb == 0 ) {
    echo _('Aucune correspondance');
}        
?>
