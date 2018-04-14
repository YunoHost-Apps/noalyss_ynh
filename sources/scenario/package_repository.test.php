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
// Copyright (2018) Author Dany De Bontridder <danydb@noalyss.eu>
//@description:Test class Package_Repository
$_GET=array ();
$_POST=array ();
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
 
 require_once NOALYSS_INCLUDE.'/class/package_repository.class.php';
 
 $package_repository=new Package_Repository();
 $package_repository->display_noalyss_info();
 
 echo $package_repository->can_download();
 
 $xml=$package_repository->getContent();
 
 $a_plugin=$xml->xpath('//plugins/plugin');
 $nb_plugin=count($a_plugin);
 $a_coprop="";
 echo "<ol>";
 for ( $i=0;$i < $nb_plugin;$i++)
 {
     echo "<li>";
     echo $a_plugin[$i]->name;
     echo "- - - - - - ";
     echo $a_plugin[$i]->path;
     echo "- - - - - - ";
     echo $a_plugin[$i]->author;
     echo "- - - - - - ";
     echo $a_plugin[$i]->code;
     echo "</li>";
     if (trim($a_plugin[$i]->code)=="AMORTIS")
    {
        $a_coprop=$a_plugin[$i];
        var_dump($a_coprop);
    }
 }
 echo "</ol>";
 
 // find a specific plugin
$spec_plugin=$xml->xpath("//plugins/plugin[code='AMORTIS']");
var_dump($spec_plugin);
 


 $a_noalyss=$xml->xpath("//core");
 
echo "description core ".$a_noalyss[0]->description;
 
// Download fake core 
echo h1(_("Download core"));
$core=$package_repository->make_object("core", "");
$core->download();

//download fake plugin
echo h1("Plugin : download and install ");
$plugin=$package_repository->make_object("plugin","COPRO");
$plugin->download();
echo h2("Install in noalyss/include/ext/copro-fake");
$plugin->install();


echo h1("Available template");
 $a_template=$xml->xpath('//database_template/dbtemplate');
 $nb_template=count($a_template);
 echo "<ol>";
 for ( $i=0;$i < $nb_template;$i++)
 {
     echo "<li>";
     echo $a_template[$i]->name;
     echo "- - - - - - ";
     echo $a_template[$i]->description;
     echo "- - - - - - ";
     echo $a_template[$i]->path;
     echo "</li>";
 }
 echo "</ol>";

 echo h1("Create new database");
$template=$package_repository->make_object("template", "mod1");
$template->download();
$template->install();
echo h2( $template->get_name()." est installé");