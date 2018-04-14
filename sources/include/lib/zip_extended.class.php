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
 * \brief extends the Zip object
 */

class Zip_Extended extends ZipArchive
{
  /**
   * Function to recursively add a directory,
   * sub-directories and files to a zip archive
   *@note 
   * ODS format expect unix / instead of DIRECTORY_SEPARATOR
   * otherwise, new file can not be read by OpenOffice
   * see PHP Bug #48763 	ZipArchive produces corrupt OpenOffice.org files
  */
  function add_recurse_folder($dir,$zipdir='')
  {
    if (is_dir($dir)) 
      {
	if ($dh = opendir($dir)) 
	  {
	    // Loop through all the files
	    $filenct = 0;
	    while (($file = readdir($dh)) !== false) 
	      {
		//If it's a folder, run the function again!
		if(!is_file($dir . $file))
		  {
		    // Skip parent and root directories
		    if( ($file !== ".") && ($file !== ".."))
		      {
			$this->add_recurse_folder($dir . $file . '/',  $zipdir . $file . '/');
		      }
		  }
		else
		  {
		    // Add the files
		    $this->addFile($dir . $file, $zipdir . $file);
		    $filenct +=1;
		  }
	      }
	    //Add the directory when folder was empty
	    if( (!empty($zipdir)) && ($filenct==0)) 
	      {
		// remove directory separator before addEmptyDir      
		// otherwhisen create double folder in zip
		$this->addEmptyDir(substr($zipdir, 0, -1));
	      }	
	  }
      }
  }

}