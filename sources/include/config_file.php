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
 * \brief functions concerning the config file config.inc.php. The domain is not set into the form for security issues
 */

require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';

function is_unix()
{
    $inc_path=get_include_path();

    if ( strpos($inc_path,";") != 0 )
    {
        $os=0;			/* $os is 0 for windoz */
    }
    else
    {
        $os=1;			/* $os is 1 for unix */
    }
    return $os;
}


/*!\brief
 *\param array with the index
 *  - ctmp temporary folder
 *  - cpath path to postgresql
 *  - cuser postgresql user
 *  - cpasswd password of cuser
 *  - cport port for postgres
 *\return string with html code
 */
function config_file_form($p_array=null)
{
	$os=is_unix();
    if ( $p_array == null )
    {

        /* default value */
        $ctmp=($os==1)?'/tmp':'c:/tmp';
        $cpath=($os==1)?'/usr/bin':'c:/noalyss/postgresql/bin';
        $cuser='noalyss_sql';
        $cpasswd='dany';
        $cport=5432;
        $cdomain='';
        $clocale=1;
		$multi=1;
		$cdbname="database_noalyss";

    }
    else extract ($p_array);

    $ictmp=new IText('ctmp',$ctmp);
    $ictmp->size=25;

    $iclocale=new ISelect('clocale');
	$iclocale->value=array(
		array("value"=>1,"label"=>"Activé"),
		array("value"=>0,"label"=>"Désactivé")
	);
	$iclocale->selected=1;

	$icpath=new IText("cpath",$cpath);
	$icpath->size=30;

	$icuser=new IText('cuser',$cuser);
	$icpasswd=new IText('cpasswd',$cpasswd);
	$icport=new IText("cport",$cport);
	/*
	 * For version MONO
	 */
	$smulti=new ICheckBox('multi');
	$smulti->javascript=' onchange="show_dbname(this)" ';

	$icdbname=new IText('cdbname');

	require 'template_config_form.php';
}
/*!\brief create the config file
 */
function config_file_create($p_array,$from_setup=1,$p_os=1)
{
    extract ($p_array);
    $add=($from_setup==1)?'..'.DIRECTORY_SEPARATOR:'';
    $hFile=  fopen($add.'..'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'config.inc.php','w');
    fputs($hFile,'<?php ');
    fputs($hFile,"\r\n");
    fputs($hFile, 'date_default_timezone_set (\'Europe/Brussels\');');
    fputs($hFile,"\r\n");
    fputs($hFile, "\$_ENV['TMP']='".$ctmp."';");
    fputs($hFile,"\r\n");
    fputs($hFile, 'define("PG_PATH","'.$cpath.'");');
    fputs($hFile,"\r\n");
    if ( $p_os == 1 )
    {
        fputs($hFile, 'define("PG_RESTORE","'.$cpath.DIRECTORY_SEPARATOR.'pg_restore ");');
        fputs($hFile,"\r\n");
        fputs($hFile, 'define("PG_DUMP","'.$cpath.DIRECTORY_SEPARATOR.'pg_dump ");');
        fputs($hFile,"\r\n");
        fputs($hFile, 'define ("PSQL","'.$cpath.DIRECTORY_SEPARATOR.'psql");');
    }
    else
    {
        fputs($hFile, 'define("PG_RESTORE","pg_restore.exe");');
        fputs($hFile,"\r\n");
        fputs($hFile, 'define("PG_DUMP","pg_dump.exe");');
        fputs($hFile,"\r\n");
        fputs($hFile, 'define ("PSQL","psql.exe");');
    }
    fputs($hFile,"\r\n");
    fputs($hFile, 'define ("noalyss_user","'.$cuser.'");');
    fputs($hFile,"\r\n");
    fputs($hFile, 'define ("noalyss_password","'.$cpasswd.'");');
    fputs($hFile,"\r\n");
    fputs($hFile, 'define ("noalyss_psql_port","'.$cport.'");');
    fputs($hFile,"\r\n");
    fputs($hFile, 'define ("noalyss_psql_host","127.0.0.1");');
    fputs($hFile,"\r\n");

    fputs($hFile, 'define ("LOCALE",'.$clocale.');');
    fputs($hFile,"\r\n");

    fputs($hFile, 'define ("domaine","");');
    fputs($hFile,"\r\n");
    if (isset($multi))
    {
        fputs($hFile, 'define ("MULTI",0);');
    }
    if (!isset($multi))
    {
        fputs($hFile, 'define ("MULTI",1);');
    }
    fputs($hFile,"\r\n");
    fputs($hFile, 'define ("dbname","'.$cdbname.'");');
    fputs($hFile,"\r\n");
    
    fputs($hFile,' // Uncomment to DEBUG');
    fputs($hFile,"\r\n");
    fputs($hFile, '// define ("DEBUG",TRUE);');
    fputs($hFile,"\r\n");
    fputs($hFile,' // Uncomment to log your input');
    fputs($hFile,"\r\n");   
    fputs($hFile, '// define ("LOGINPUT",TRUE);');
    fputs($hFile,"\r\n");
    fputs($hFile,"\r\n");
    fputs($hFile,"\r\n");
    fputs($hFile,' // Do not change below !!!');
    fputs($hFile,"\r\n");
    fputs($hFile,' // These variable are computed but could be changed in ');
    fputs($hFile,"\r\n");
    fputs($hFile,' // very special configuration');
    fputs($hFile,"\r\n");
    fputs($hFile, '// define ("NOALYSS_HOME","");');
    fputs($hFile,"\r\n");
    fputs($hFile, '// define ("NOALYSS_PLUGIN","");');
    fputs($hFile,"\r\n");
    fputs($hFile, '// define ("NOALYSS_INCLUDE","");');
    fputs($hFile,"\r\n");
    fputs($hFile, '// define ("NOALYSS_TEMPLATE","");');
    fputs($hFile,"\r\n");
    fputs($hFile,"\r\n");
    fputs($hFile,"\r\n");
    fputs($hFile, "// Uncomment if you don't want "."\r\n");
    fputs($hFile, "// to be informed when a new release is "."\r\n");
    fputs($hFile, "// published"."\r\n");
    fputs($hFile, '// define ("SITE_UPDATE","");'."\r\n");
    fputs($hFile, '// define ("SITE_UPDATE_PLUGIN","");'."\r\n");
    fputs($hFile,'?>');
    fclose($hFile);
}
