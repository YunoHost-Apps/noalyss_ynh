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

require_once NOALYSS_INCLUDE.'/lib/itext.class.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/icheckbox.class.php';

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
	$multi="N";
	$cdbname="";
        $chost="localhost";
        $cadmin='admin';

    }
    else extract ($p_array, EXTR_SKIP);

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
    $ichost=new IText("chost",$chost);
    
    $icadmin=new IText('cadmin',$cadmin);
    /*
     * For version MONO
     */
    $smulti=new ICheckBox('multi');
    $smulti->javascript=' onchange="show_dbname(this)" ';
    $smulti->value = 'Y';
    if ( isset($multi) && $multi == 'Y') {
        $smulti->selected=true;

    }
    $icdbname=new IText('cdbname');
    $icdbname->value=$cdbname;
    require NOALYSS_TEMPLATE.'/template_config_form.php';
}
/**
 * Display the  content of the config.inc.php with variables
 * @param type $p_array
 * @param type $from_setup
 * @param type $p_os
 */
function display_file_config($p_array,$from_setup=1,$p_os=1) 
{
    extract($p_array, EXTR_SKIP);
    print ('<?php ');
    print ("\r\n");
    print ( 'date_default_timezone_set (\'Europe/Brussels\');');
    print ("\r\n");
    print ( "\$_ENV['TMP']='".$ctmp."';");
    print ("\r\n");
    print ( 'define("PG_PATH","'.$cpath.'");');
    print ("\r\n");
    if ( $p_os == 1 )
    {
        print ( 'define("PG_RESTORE","'.$cpath.DIRECTORY_SEPARATOR.'pg_restore ");');
        print ("\r\n");
        print ( 'define("PG_DUMP","'.$cpath.DIRECTORY_SEPARATOR.'pg_dump ");');
        print ("\r\n");
        print ( 'define ("PSQL","'.$cpath.DIRECTORY_SEPARATOR.'psql");');
    }
    else
    {
        print ( 'define("PG_RESTORE","pg_restore.exe");');
        print ("\r\n");
        print ( 'define("PG_DUMP","pg_dump.exe");');
        print ("\r\n");
        print ( 'define ("PSQL","psql.exe");');
    }
    print ("\r\n");
    print ( 'define ("noalyss_user","'.$cuser.'");');
    print ("\r\n");
    print ( 'define ("noalyss_password","'.$cpasswd.'");');
    print ("\r\n");
    print ( 'define ("noalyss_psql_port","'.$cport.'");');
    print ("\r\n");
    print ( 'define ("noalyss_psql_host","'.$chost.'");');
    print ("\r\n");
    print ("\r\n");
    print ("// If you change the NOALYSS_ADMINISTRATOR , you will need to rerun http://..../noalyss/html/install.php");
    print ("\r\n");
    print ("// But it doesn't change the password");
    print ("\r\n");
    print ( 'define ("NOALYSS_ADMINISTRATOR","'.$cadmin.'");');
    print ("\r\n");
    
    print ( 'define ("LOCALE",'.$clocale.');');
    print ("\r\n");

    print ( 'define ("domaine","");');
    print ("\r\n");
    if (isset($multi))
    {
        print ( 'define ("MULTI",0);');
    }
    if (!isset($multi))
    {
        print ( 'define ("MULTI",1);');
    }
    print ("\r\n");
    print ( 'define ("dbname","'.$cdbname.'");');
    print ("\r\n");
    
    print (' // Uncomment to DEBUG');
    print ("\r\n");
    print ( '// define ("DEBUG",TRUE);');
    print ("\r\n");
    print (' // Uncomment to log your input');
    print ("\r\n");   
    print ( '// define ("LOGINPUT",TRUE);');
    print ("\r\n");
    print ("\r\n");
    print ("\r\n");
    print (' // Do not change below !!!');
    print ("\r\n");
    print (' // These variable are computed but could be changed in ');
    print ("\r\n");
    print (' // very special configuration');
    print ("\r\n");
    print ( '// define ("NOALYSS_HOME","");');
    print ("\r\n");
    print ( '// define ("NOALYSS_PLUGIN","");');
    print ("\r\n");
    print ( '// define ("NOALYSS_INCLUDE","");');
    print ("\r\n");
    print ( '// define ("NOALYSS_TEMPLATE","");');
    print ("\r\n");
    print ( '// define ("NOALYSS_INCLUDE","");');
    print ("\r\n");
    print ( '// define ("NOALYSS_TEMPLATE","");');
    print ("\r\n");
    print ( "// Fix an issue with PDF when exporting receipt in PDF in ANCGL"."\r\n");
    print ( '// define ("FIX_BROKEN_PDF","NO");');
    print ("\r\n");
    print ("// Uncomment if you want to convert to PDF");
    print ("\r\n");
    print ("// With the unoconv tool");
    print ("\r\n");
    print ( "//define ('OFFICE','HOME=/tmp unoconv ');");
    print ("\r\n");
    print ("//define ('GENERATE_PDF','YES');");
    print ("\r\n");
    print ( "// Uncomment if you don't want "."\r\n");
    print ( "// to be informed when a new release is "."\r\n");
    print ( "// published"."\r\n");
    print ( '// define ("SITE_UPDATE","");'."\r\n");
    print ( '// define ("SITE_UPDATE_PLUGIN","");'."\r\n");
    print ( '// To allow to access the Info system'."\r\n");
    print ( '// define ("SYSINFO_DISPLAY",true);'."\r\n");
    print ( '// For developpement'."\r\n");
    print ( '// define ("NOALYSS VERSION",9999);'."\r\n");
    print (' // If you want to override the parameters you have to define OVERRIDE_PARAM'."\r\n");
    print ('// and give your own parameters for max_execution_time and memory_limit'."\r\n");
    print ("// define ('OVERRIDE_PARAM',1);\r\n");
    print ("// ini_set ('max_execution_time',240);\r\n");
    print ("// ini_set ('memory_limit','256M');\r\n");
// 

}
/*!\brief create the config file
 */
function config_file_create($p_array,$from_setup,$p_os=1)
{
    extract ($p_array, EXTR_SKIP);
    $hFile=  fopen(NOALYSS_INCLUDE.'/config.inc.php','w');
    ob_start();
    display_file_config($p_array,$from_setup,$p_os);
    $r=ob_get_clean();
    fputs($hFile, $r);
    fclose($hFile);
}
