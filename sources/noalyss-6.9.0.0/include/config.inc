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
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*!\file
 * \brief user setting
 */
date_default_timezone_set ('Europe/Brussels');
$_ENV['TMP']='/tmp';
# Complete path to pg_restore
define("PG_PATH","/usr/bin");
define("PG_RESTORE","/usr/bin/pg_restore ");
define("PG_DUMP","/usr/bin/pg_dump ");
define ("PSQL","/usr/bin/psql");
define ("noalyss_user","dany");
define ("noalyss_password","dany");
define ("noalyss_psql_port","5432");
define ("noalyss_psql_host","127.0.0.1");
define ("LOCALE",1);
define ("domaine","");
define ("MULTI",1);
define ("dbname","");
// Uncomment to DEBUG
// define ("DEBUG",TRUE);
// Uncomment to log your input
// define ("LOGINPUT",TRUE);


// Do not change below !!!
// These variable are computed but could be changed in 
// very special configuration
// define ("NOALYSS_HOME","");
// define ("NOALYSS_PLUGIN","");
// define ("NOALYSS_INCLUDE","");
// define ("NOALYSS_TEMPLATE","");


// Uncomment if you don't want 
// to be informed when a new release is 
// published
// define ("SITE_UPDATE","");
// define ("SITE_UPDATE_PLUGIN","");
// Uncomment to disable cache header
// define ("ENABLE_CACHE","NO");
?>
