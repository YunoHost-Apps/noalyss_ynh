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
 * \brief Page who manage the different actions (meeting, letter)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
global $g_user;
$retour=HtmlInput::button_anchor(_('Retour liste'),
	HtmlInput::request_to_string(array("closed_action","remind_date_end","remind_date","sag_ref","only_internal","state","ac","gDossier","qcode","ag_dest_query","action_query","tdoc","date_start","date_end","hsstate","searchtag")),
        "","","smallbutton");
//-----------------------------------------------------
// Follow_Up
//-----------------------------------------------------
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_ifile.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_follow_up.php';
/*!\brief Show the list of action, this code should be common
 *        to several webpage. But for the moment we keep like that
 *        because it is used only by this file.
 *\param $cn database connection
 * \param $retour button for going back
 * \param $h_url calling url
 */

// We need a sub action (3rd level)
// show a list of already taken action
// propose to add one
// permit also a search
// show detail
$sub_action=(isset($_REQUEST['sa']))?$_REQUEST['sa']:"";
/* if ag_id is set then we give it otherwise we have problem
 * with the generation of document
 */
$ag_id=(isset($_REQUEST['ag_id']))?$_REQUEST['ag_id']:0;
$ac=$_REQUEST['ac'];
$base=HtmlInput::request_to_string(array('ac','gDossier'),"");

require_once NOALYSS_INCLUDE.'/action.common.inc.php';
echo "</div>";

?>
