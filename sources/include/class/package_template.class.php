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
 * @brief 
 */
require_once NOALYSS_INCLUDE."/class/package_noalyss.class.php";

/**
 * @brief
 * 
 */
class Package_Template extends Package_Noalyss
{

    public function can_install()
    {
        return TRUE;
    }
    
    /**
     * Install the template
     */
    public function install()
    {
        // make temp dir
        $tmp=NOALYSS_HOME."/tmp/";
        $tmpdir=$tmp."db-".microtime(TRUE);
        mkdir($tmpdir);

        // unzip Archive file 
        $db=new ZipArchive;
        $db->open($tmp."/".$this->get_file());
        $db->extractTo($tmpdir);


        // create database
        $cn=new Database();
        $seq=$cn->get_value("select nextval('s_modid')");

        $sql=sprintf(" create database %smod%d encoding='utf8'", domaine, $seq);
        $cn->exec_sql($sql);
        
        $newdb=new Database($seq, 'mod');

        // Execute SQL Script
        $newdb->execute_script($tmpdir.'/schema.sql');
        $newdb->execute_script($tmpdir.'/data.sql');
        $newdb->execute_script($tmpdir.'/constraint.sql');
        
        // Register into account_repository, we add the seq number for avoiding duplicate
        $description = sprintf(_("Installé le %s"),date("d-m-Y h:i:s"));
        $cn->exec_sql(" insert into modeledef (mod_id,mod_name,mod_desc) values ($1,$2,$3)",
                [$seq,$seq."-".$this->get_name(),$this->get_description()." ".$description]);
    }

}
