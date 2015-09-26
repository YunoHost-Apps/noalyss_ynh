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
require_once '../include/constant.php';

include_once NOALYSS_INCLUDE.'/ac_common.php';

/*! \file
 * \brief Login page
 */

require_once NOALYSS_INCLUDE.'/class_database.php';
// Verif if User and Pass match DB
    // if no, then redirect to the login page
$rep=new Database();

if (defined('MULTI') && MULTI == 0)
		$version = $rep->get_value('select val from repo_version');
	else
		$version = $rep->get_value('select val from version');

if (  isset ($_POST["p_user"] ) )
{
    $g_user=sql_string($_POST["p_user"]);
    $g_pass=$_POST["p_pass"];
    $_SESSION['g_user']=$g_user;
    $_SESSION['g_pass']=$g_pass;


    /*
     * Check repository version
     */

	if ($version != DBVERSIONREPO)
	{
		echo alert(_('Version de base de données incorrectes, vous devez mettre à jour'));
		echo "<META HTTP-EQUIV=\"REFRESH\" content=\"0;url=admin/setup.php\">";
		exit();
	}
    include_once NOALYSS_INCLUDE."/class_user.php";
    $User=new User($rep);
    $User->Check(false,'LOGIN');
    if ($g_captcha == true)
      {
	include("securimage/securimage.php");
	$img = new Securimage();
	$valid = $img->check($_POST['captcha_code']);
	if ( $valid == false )
	  {
	    echo alert(_('Code invalide'));
	    echo "<META HTTP-EQUIV=\"REFRESH\" content=\"0;url=index.php\">";
	    exit();
	  }
      }
      // force the nocache
      $backurl='user_login.php?v='.microtime(true);
      if ( isset ($_POST['backurl'])) {
          $backurl=urldecode($_POST['backurl']);
      }
    echo "<META HTTP-EQUIV=\"REFRESH\" content=\"0;url={$backurl}\">";
    exit();
}
else
{
    $rep=new Database();

    /*
     * Check repository version
     */

    if ( $version != DBVERSIONREPO)
      {
	echo alert(_('Version de base de données incorrectes, vous devez mettre à jour'));
	echo "<META HTTP-EQUIV=\"REFRESH\" content=\"1;url=admin/setup.php\">";
	exit();

      }

    include_once ("class_user.php");

    $User=new User($rep);
    $User->Check();

    echo "<META HTTP-EQUIV=\"REFRESH\" content=\"0;url=user_login.php?v=".microtime(true)."\">";
}
html_page_stop();
?>
