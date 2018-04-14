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
/*!\file
 * \brief this file let you debug and test the different functionnalities, there are 2 important things to do
 * It is only a quick and dirty testing. You should use a tool as PHPUNIT for the unit testing
 * 
 *  - first do not forget to create the authorized_debug file in the html folder
 *  - secund the test must adapted to this page : if you do a post (or get) from a test, you won't get any result
 * if the $_REQUEST[test_select] is not set, so set it . 
 */



include_once  "../include/constant.php";
include_once NOALYSS_INCLUDE."/lib/ac_common.php";
require_once  NOALYSS_INCLUDE."/lib/database.class.php";
require_once  NOALYSS_INCLUDE."/class/dossier.class.php";
require_once  NOALYSS_INCLUDE."/lib/html_input.class.php";
require_once  NOALYSS_INCLUDE."/lib/http_input.class.php";
require_once  NOALYSS_INCLUDE."/lib/function_javascript.php";
require_once  NOALYSS_INCLUDE."/class/user.class.php";
$http=new HttpInput();
$gDossier=$http->request('gDossier', "number",-1);
if ($gDossier==-1)
{
    echo " Vous devez donner le dossier avec paramètre gDossier dans l'url, exemple http://localhost/noalyss/html/test.php?gDossier=25";
    exit();
}
$gDossierLogInput=$gDossier;
global $cn, $g_user, $g_succeed, $g_failed;
$cn=Dossier::connect();

$g_parameter=new Noalyss_Parameter_Folder($cn);
$g_user=new User($cn);

if (!file_exists('authorized_debug'))
{
    echo "Pour pouvoir utiliser ce fichier vous devez creer un fichier nomme authorized_debug
    dans le repertoire html du server";
    exit();
}
define('ALLOWED', 1);
define('AJAX_TEST', 1);

$w=$http->request("TestAjaxFile");

require_once $w;
