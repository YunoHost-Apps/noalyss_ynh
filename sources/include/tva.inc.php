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
/** \file
 * \brief included file for customizing with the vat (account,rate...)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/noalyss_parameter_folder.class.php';
require_once NOALYSS_INCLUDE.'/lib/html_input.class.php';
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/lib/itextarea.class.php';
require_once NOALYSS_INCLUDE."/class/tva_rate_mtable.class.php";

$cn=Dossier::connect();
$own=new Noalyss_Parameter_Folder($cn);

echo '<div class="content">';
if ($own->MY_TVA_USE == 'N')
{
    echo '<h2 class="error">'._("Vous n'êtes pas assujetti à la TVA").'</h2>';
    return;
}

$tva_rate=new V_Tva_Rate_SQL($cn);

$manage_table=new Tva_Rate_MTable($tva_rate);

$manage_table->set_callback("ajax_misc.php");
$manage_table->add_json_param("op", "tva_parameter");
$manage_table->create_js_script();
$manage_table->display_table();
echo '</div>';
?>
