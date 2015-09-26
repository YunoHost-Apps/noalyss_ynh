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
/* !\file
 * \brief this file let you debug and test the different functionnalities, there are 2 important things to do
 * It is only a quick and dirty testing. You should use a tool as PHPUNIT for the unit testing
 * 
 *  - first do not forget to create the authorized_debug file in the html folder
 *  - secund the test must adapted to this page : if you do a post (or get) from a test, you won't get any result
 * if the $_REQUEST[test_select] is not set, so set it . 
 */



include_once("../include/constant.php");
include_once("ac_common.php");
require_once('class_database.php');
require_once ('class_dossier.php');
require_once('class_html_input.php');
require_once ('function_javascript.php');
require_once 'class_user.php';
load_all_script();
$gDossier=HtmlInput::default_value_get('gDossier', -1);
if ($gDossier==-1)
{
    echo " Vous devez donner le dossier avec paramètre gDossier dans l'url, exemple http://localhost/noalyss/html/test.php?gDossier=25";
    exit();
}
$gDossierLogInput=$gDossier;
global $cn, $g_user, $g_succeed, $g_failed;
$cn=new Database($_GET['gDossier']);

$g_parameter=new Own($cn);
$g_user=new User($cn);

if (!file_exists('authorized_debug'))
{
    echo "Pour pouvoir utiliser ce fichier vous devez creer un fichier nomme authorized_debug
    dans le repertoire html du server";
    exit();
}
define('ALLOWED', 1);
html_page_start();

/*
 * Loading of all scenario
 */
$scan=scandir('../scenario/');
$maxscan=count($scan);
$cnt_scenario=0;$scenario=array();

for ($e_scan=0; $e_scan<$maxscan; $e_scan++)
    {
        if (is_file('../scenario/'.$scan[$e_scan])&&strpos($scan[$e_scan], '.php')==true)
        {
            $description="";
            $a_description=file('../scenario/'.$scan[$e_scan]);
            $max_description=count($a_description);
            for ($w=0; $w<$max_description; $w++)
            {
                if (strpos($a_description[$w], '@description:')==true)
                {
                    $description=$a_description[$w];
                    $description=str_replace('//@description:', '', $description);
                }
            }
            $scenario[$cnt_scenario]['file']=$scan[$e_scan];
            $scenario[$cnt_scenario]['desc']=$description;
            $cnt_scenario++;
            
            
        }
    }
$script=HtmlInput::default_value_get('script', '');
if ($script=="")
{
    echo "<h1>Test NOALYSS</h1>";
    /*
     * cherche pour fichier a include, s'il y en a alors les affiche
     * avec une description
     */
    

    echo '<table>';
    $get='test.php?'.http_build_query(array('script'=>"all", 'gDossier'=>$gDossierLogInput, 'description'=>"Tous les scripts"));
    echo '<tr>';
    echo '<td>';
    echo '<a href="'.$get.'" target="_blank">';
    echo "Tous ";
    echo '</a>';
    echo '</td>';
    echo '<td>Tous les scripts</td>';
    echo '</tr>';

    for ($e=0; $e<$cnt_scenario; $e++)
    {

            $get='test.php?'.http_build_query(array('script'=>$scenario[$e]['file'], 'gDossier'=>$gDossierLogInput, 'description'=>$scenario[$e]['desc']));
            echo '<tr>';
            echo '<td>';
            echo $e;
            echo '</td>';
            echo '<td>';
            echo '<a href="'.$get.'" target="_blank">';
            echo $scenario[$e]['file'];
            echo '</a>';
            echo '</td>';
            echo '<td>'.$scenario[$e]['desc'].'</td>';
            echo '</tr>';
        
    }
    echo '</table>';
}
else if ($script=='all')
{
    $nb=HtmlInput::default_value_get('nb_script', 0);
    
            $start_mem=memory_get_usage();
            $start_time=microtime(true);
            $script=str_replace('../', '', $script);
    
            echo '<h1>'.$nb." ".$scenario[$nb]['file']."</h1>";
            echo '<h2> description = '.$scenario[$nb]["desc"].'</h2>';
            include '../scenario/'.$scenario[$nb]['file'];
            echo '</div>';
            echo '</div>';
            $end_mem=memory_get_usage();
            $end_time=microtime(true);

            echo "<p>start mem : ".$start_mem;
            echo '</p>';
            echo "<p>end mem : ".$end_mem;
            echo '</p>';
            echo "<p>Diff = ".($end_mem-$start_mem)." bytes ";
            echo "<p>Diff = ".(round(($end_mem-$start_mem)/1024, 2))." kbytes ";
            echo "<p>Diff = ".(round(($end_mem-$start_mem)/1024/1024, 2))." Mbytes ";
            echo '</p>';
            echo "<p>Execution script ".$script." time = ".(round(($end_time-$start_time), 4))." secondes</p>";
            $nb++;
            if      ( $nb == $maxscan ) {
                echo "Dernier test";
            } else {
            $get='test.php?'.http_build_query(array('script'=>"all", 'gDossier'=>$gDossierLogInput, 'nb_script'=>$nb));
             echo '<a href="'.$get.'" target="_blank">';
            echo $scenario[$nb]['file'];
            }
}
else
{
    $start_mem=memory_get_usage();
    $start_time=microtime(true);
    $script=str_replace('../', '', $script);
    $description=HtmlInput::default_value_get("description", "aucune description");
    echo '<h1>'.$script."</h1>";
    echo '<p> description = '.$description.'<p>';
    include '../scenario/'.$script;

    $end_mem=memory_get_usage();
    $end_time=microtime(true);

    echo "<p>start mem : ".$start_mem;
    echo '</p>';
    echo "<p>end mem : ".$end_mem;
    echo '</p>';
    echo "<p>Diff = ".($end_mem-$start_mem)." bytes ";
    echo "<p>Diff = ".(round(($end_mem-$start_mem)/1024, 2))." kbytes ";
    echo "<p>Diff = ".(round(($end_mem-$start_mem)/1024/1024, 2))." Mbytes ";
    echo '</p>';
    echo "<p>Execution script ".$script." time = ".(round(($end_time-$start_time), 4))." secondes</p>";
}    