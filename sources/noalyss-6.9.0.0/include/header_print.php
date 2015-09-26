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
 * \brief contains several function to replace the header in generated document
 *
 */

require_once  NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/class_own.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';


date_default_timezone_set ('Europe/Brussels');

function header_txt($p_cn)
{
    $own=new own($p_cn);
    $soc=$own->MY_NAME;

    $date=date('d / m / Y H:i ');
    $dossier=utf8_decode(" Dossier : ".dossier::name());
    return $dossier." ".$soc." ".$date;
}

?>
