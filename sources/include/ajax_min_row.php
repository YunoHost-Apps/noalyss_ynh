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
 * @file
 * @brief
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
extract($_GET);
/* check the parameters */
foreach ( array('j','ctl') as $a )
{
    if ( ! isset(${$a}) )
    {
        echo "missing $a";
        return;
    }
}

if ( $g_user->check_jrn($_GET['j'])=='X' ) { echo  '{"row":"0"}';exit();}

$row=$cn->get_value('select jrn_deb_max_line from jrn_def where jrn_def_id=$1',array($_GET['j']));

echo '{"row":"'.$row.'"}';

?>
