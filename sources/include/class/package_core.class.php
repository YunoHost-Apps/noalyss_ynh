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
require_once NOALYSS_INCLUDE.'/class/package_noalyss.class.php';

/**
 * @file
 * @brief Class Package Core to install the core , if possible
 */
class Package_Core extends Package_Noalyss
{

    /**
     * @brief check if it is possible to install
     *  Are the folder html and include writeable ?
     */
    public function can_install()
    {
        if (! is_writable(NOALYSS_HOME)) {
            return 0;
        }
        if ( !is_writable(NOALYSS_INCLUDE)) {
            return 0;
        }
        return 1;
    }

    /**
     * Unzip the file and overwrite current implementation, the databases and modele are not upgraded.
     *It must be done after.
     *  In this package the install is given and must be delete manually 
     * @throws Exception 1 : cannot extract content of the zip , 2: cannot open zip file 
     */
    public function install()
    {
        if ( $this->can_install() == 0 )
        {
            throw new Exception(sprintf(_("Permission incorrecte : ne peut écrire dans %s ou %s"),NOALYSS_HOME,NOALYSS_INCLUDE),3);
        }
        $zip=new ZipArchive ();
        // open the file
        if ($zip->open(NOALYSS_HOME."/tmp/".$this->get_file()))
        {
            // try to unzip and overwrite current 
            if (!$zip->extractTo(NOALYSS_HOME."/../"))
            {
                throw new Exception(_("Echec mise à jour"), 1);
            }
        }
        else
        {
            throw new Exception(_("Ce n'est pas un fichier valide"), 2);
        }
        
    }

}
