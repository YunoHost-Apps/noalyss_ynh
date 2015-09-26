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

require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';


html_page_start($_SESSION['g_theme']);
echo '<div style="float:left;">';
global $g_user;
if ( basename($_GET['ajax']) == 'ajax_history.php' )
  {
    $href=dossier::get();
    $cn=new Database(dossier::id());
    /* current year  */
    $g_user=new User($cn);
    $exercice=$g_user->get_exercice();

    /* get date limit */
    $periode=new Periode($cn);
    $limit=$periode->get_limit($exercice);

    $from_periode='from_periode='.format_date($limit[0]->p_start);
    $to_periode='to_periode='.format_date($limit[1]->p_end);
    if (isset($_GET['ex']))
      {
	if ( $exercice > $_GET['ex'])
	  {
	    $limit_periode=$periode->get_limit($_GET['ex']);
	    $from_periode='from_periode='.format_date($limit_periode[0]->p_start);
	  }
	else
	  {
	    $limit_periode=$periode->get_limit($_GET['ex']);
	    $to_periode='to_periode='.format_date($limit_periode[1]->p_end);

	  }
      }

    if (isset($_GET['pcm_val']) )
      {
	$href_csv="export.php?".$href.'&poste_id='.$_GET['pcm_val'].'&ople=0&type=poste&'.$from_periode.'&'.$to_periode."&act=CSV:postedetail";
	$href_pdf="export.php?".$href.'&poste_id='.$_GET['pcm_val'].'&ople=0&type=poste&'.$from_periode.'&'.$to_periode."&act=PDF:postedetail";;
      }
    else
      {
	$href_csv="export.php?".$href.'&f_id='.$_GET['f_id'].'&ople=0&type=poste&'.$from_periode.'&'.$to_periode."&act=CSV:fichedetail";
	$href_pdf="export.php?".$href.'&f_id='.$_GET['f_id'].'&ople=0&type=poste&'.$from_periode.'&'.$to_periode."&act=PDF:fichedetail";
      }
    echo HtmlInput::print_window();
    echo '<a class="smallbutton"  href="'.$href_csv.'">'._("Export CSV").'</a>';
    echo '<a class="smallbutton"  href="'.$href_pdf.'">'._("Export PDF").'</a>';
  }
  else {
      echo HtmlInput::print_window();
  }
echo '</div>';
echo HtmlInput::hidden('inpopup',1);
load_all_script();

$str=$_SERVER['QUERY_STRING']."&div=popup";
$script="
        var obj={id:'popup',fixed:1,cssclass:'content',style:'width:auto',html:loading(),qs:'$str',js_success:'success_box',js_error:null,callback:'".$_GET['ajax']."'};
        show_box(obj);
        ";
echo create_script($script);
?>
