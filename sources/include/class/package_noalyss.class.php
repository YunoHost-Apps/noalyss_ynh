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
 * @brief package noalyss is the mother class of the class to install and download package
 */
abstract class Package_Noalyss
{

    private $file;
    private $path;
    private $name;
    private $description;

    function __construct($name, $description, $full_path)
    {
        $this->file=basename(trim($full_path));
        $this->path=dirname(trim($full_path));

        $this->description=$description;
        $this->name=$name;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function set_path($path)
    {
        $this->path=$path;
        return $this;
    }

    public function get_file()
    {
        return $this->file;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function set_file($file)
    {
        $this->file=$file;
        return $this;
    }

    public function set_name($name)
    {
        $this->name=$name;
        return $this;
    }

    public function set_description($description)
    {
        $this->description=$description;
        return $this;
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

    function download()
    {
        // If install is writable then download 
        if ( $this->can_download() )
        {
            $full=$this->get_path()."/".$this->get_file();
            $file = file_get_contents(NOALYSS_PACKAGE_REPOSITORY."/".$full);
            $fh_file=fopen(NOALYSS_HOME."/tmp/".$this->get_file(),"w+");
            
            fwrite($fh_file, $file);
             fclose($fh_file);
        }
    }

    abstract function install();
    
    abstract function can_install();
}
