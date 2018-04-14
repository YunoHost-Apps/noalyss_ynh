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
 * @brief contains the class Package_Repository
 */
require_once NOALYSS_INCLUDE.'/class/package_core.class.php';
require_once NOALYSS_INCLUDE.'/class/package_plugin.class.php';
require_once NOALYSS_INCLUDE.'/class/package_template.class.php';
require_once NOALYSS_INCLUDE.'/class/package_contrib.class.php';

/**
 * @brief connect to NOALYSS_PACKAGE and fetch the file web.xml , it displays
 * content of this file , build the appropriate object for installing
 */
class Package_Repository
{

    private $content;

    /**
     * @see package_repository.test.php
     */
    function __construct()
    {
        $content=file_get_contents(NOALYSS_PACKAGE_REPOSITORY."/web.xml");

        $this->content=simplexml_load_string($content);
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * check that NOALYSS_HOME exists and is writable
     */
    function can_download()
    {
        $download_dir=NOALYSS_HOME."/tmp";
        if (is_dir($download_dir)&&is_writable($download_dir))
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get info for Noalyss code : version #, announce, path to the last
     * version
     */
    function display_noalyss_info()
    {
        global $g_user;
        switch ($g_user->lang)
        {
            case 'fr_FR.utf8':
                    echo "<pre>";
                    echo $this->content->core->description;
                    echo "</pre>";
                
                break;
            case 'en_US.utf8':
                break;
            case 'nl_NL.utf8':
                break;
        }
    }

    /**
     * return a SimpleXMLElement of the plugin thanks its code, it returns NULL if no plugin is found
     * @param string $p_code  code of the plugin
     * @return SimpleXMLElement or NULL if not found
     */
    function find_plugin($p_code)
    {
        $a_plugin=$this->content->xpath('//plugins/plugin');
        $nb_plugin=count($a_plugin);
        for ($i=0; $i<$nb_plugin; $i++)
        {
            if (trim($a_plugin[$i]->code)==$p_code)
            {
                return $a_plugin[$i];
            }
        }
        return NULL;
    }
    

    /**
     * return a SimpleXMLElement of the db template thanks its code, it returns NULL if no template is found
     * @param string $p_code  code of the template
     * @return SimpleXMLElement or NULL if not found
     */
    function find_template($p_code)
    {
        $a_template=$this->content->xpath('//database_template/dbtemplate');
        $nb_template=count($a_template);
        for ($i=0; $i<$nb_template; $i++)
        {
            if (trim($a_template[$i]->code)==$p_code)
            {
                return $a_template[$i];
            }
        }
        return NULL;
    }
    
    function make_object($p_type, $p_id)
    {
        switch ($p_type)
        {
            case "core":
                // Create an object to download & install the core
                $obj=new Package_Core("Noalyss", "Core", $this->content->core->path);
                return $obj;
                break;
            case 'template':
            // create an object to download & install the template
                $db=$this->find_template($p_id);
                if ($db == NULL ) {
                    throw new Exception(_("Modèle non trouvé"),1002);
                }
                $obj=new Package_Template($db->name,$db->description,$db->path);
                return $obj;
            case 'plugin':
                // create an object to download & install a plugin
                $plugin = $this->find_plugin($p_id);
                if ($plugin==NULL)
                {
                    throw new Exception(_("Extension non trouvée"), 1001);
                }
                $obj=new Package_Plugin($plugin->name,$plugin->description,$plugin->path);
                return $obj;
                break;
            case 'contrib':
            //create an object to download & install a contrib
            default:
                break;
        }
    }
    /**
     * Read xml file from the package
     * @param string $p_file
     * @return SimpleXMLElement
     */
    public function read_package_xml($p_file)
    {
        $dom=new DomDocument('1.0');
        $dom->load($p_file);
        $xml=simplexml_import_dom($dom);
        return $xml;
    }

}
