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
global $g_user,$cn,$g_parameter;
require_once NOALYSS_INCLUDE.'/class_stock.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';

$stock=new Stock($cn);
$array=$_GET;
if ( ! isset ($array['wdate_start']) || ! isset ($array['wdate_end']))
{
	// Date start / end
		$exercice = $g_user->get_exercice();
		$periode = new Periode($cn);
		list($periode_start, $periode_end) = $periode->get_limit($exercice);

		$array['wdate_start'] = $periode_start->first_day();
		$array['wdate_end'] =$periode_end->last_day();
}

$stock->history($array);

?>
