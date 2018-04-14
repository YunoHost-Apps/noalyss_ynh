<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief Manage the installation of plug
 */
require_once NOALYSS_INCLUDE."/class/package_noalyss.class.php";

/**
 * @class
 * @brief Manage the installation of plug
 */
class Package_Plugin extends Package_Noalyss
{

    public function install()
    {
        $zip=new ZipArchive ();
        // open the file
        if ($zip->open(NOALYSS_HOME."/tmp/".$this->get_file()))
        {
            // try to unzip and overwrite current 
            if (!$zip->extractTo(NOALYSS_PLUGIN))
            {
                throw new Exception(_("Echec installation plugin "), 1);
            }
        }
        else
        {
            throw new Exception(_("Ce n'est pas un fichier valide"), 2);
        }
    }

    /**
     * Check the NOALYSS_PLUGIN is writeable
     */
    public function can_install()
    {
        if (is_writable(NOALYSS_PLUGIN))
        {
            return TRUE; 
        }
        return FALSE;
    }
    

}
