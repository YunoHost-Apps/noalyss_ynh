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
 * @brief  file to add to a message
 *
 * @see Sendmail
 * @author dany
 */
class FileToSend
{
    /** 
     * @brief name of the file without path
     */
    var $filename;
    /**
     *@brief mimetype of the file
     */
    var $type;
    /**
     * path
     */
    var $path;
    /**
     * Path to filename + filename
     */
    var $full_name;
    function __construct($p_filename,$p_type="")
    {
        $this->full_name=$p_filename;
        if (strpos($p_filename,'/') != false)
        {
            $this->path=dirname($p_filename);
        }
        $this->filename=basename ($p_filename);
        if ( $p_type=="")
        {
            $this->guess_type();
            
        }
    }
    /**
     * set the $this->type to the mimetype, called from __construct
     */
    private function guess_type()
    {
        $ext_pos=  strrpos($this->filename,'.');
        if ( $ext_pos == false ) {
            $this->type="application/octect";
            return;
        }
        $ext=  substr($this->filename, $ext_pos+1, 3);
        switch ($ext)
        {
            case 'odt':
                $this->type='application/vnd.oasis.opendocument.text';
                break;
            case 'ods':
                $this->type='application/vnd.oasis.opendocument.spreadsheet';
                break;
            case 'pdf':
                $this->type="application/pdf";
                break;
            case 'zip':
                $this->type="application/zip";
                break;
            default:
                $this->type="application/octet";
        }
        
    }
    /**
     * Compute properly the filename
     */
    function compute_name($p_filename)
    {
        /**
         * @todo compute a filename
         */
    }
  
}
