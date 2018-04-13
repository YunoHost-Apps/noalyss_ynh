<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS isfree software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS isdistributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>


/**
 * @file
 * @brief This file is used for the first installation or redirect to 
 * include/admin.inc.php
 */
if (file_exists("../include/config.inc.php") ) {
    /* if the file exists it means that NOALYSS is already 
     * installed
     */
    define ('ALLOWED',1);
    define ('ALLOWED_ADMIN',1);
    require_once '../include/constant.php';
    require_once NOALYSS_INCLUDE.'/admin_repo.inc.php';
} else {
    // Redirect to install file , if this file exists then 
    // we can't connect to anything
    echo '<HTML><head><META HTTP-EQUIV="REFRESH" content="0;url=install.php"></head><body> Connecting... </body></html>';

}
?>
