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
/*! \file
 * \brief Search module
 */
require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
include_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';

html_page_start($_SESSION['g_theme']);

load_all_script();


$gDossier=dossier::id();

require_once NOALYSS_INCLUDE.'/class_database.php';
/* Admin. Dossier */

$cn=new Database($gDossier);
include_once NOALYSS_INCLUDE.'/class_user.php';

global $g_user;
$g_user=new User($cn);
$g_user->Check();
$act=$g_user->check_dossier($gDossier);
// AC CODE = SEARCH
if ($act =='P')
{
    redirect("extension.php?".dossier::get(),0);
    exit();
}
if ( $act=='X')
  {
     alert(_('Accès interdit'));
     exit();
  }
// display a search box

$ledger=new Acc_Ledger($cn,0);
$ledger->type='ALL';
$search_box=$ledger->search_form('ALL',1);
echo '<div class="content">';

echo '<form method="GET">';
echo $search_box;
echo HtmlInput::submit("viewsearch",_("Recherche"));
?>
<input type="button" class="smallbutton" onclick="window.close()" value="<?php echo _('Fermer')?>">

<?php
echo '</form>';

//-----------------------------------------------------
// Display search result
//-----------------------------------------------------
if ( isset ($_GET['viewsearch']))
{

    // Navigation bar
    $step=$_SESSION['g_pagesize'];
    $page=(isset($_GET['offset']))?$_GET['page']:1;
    $offset=(isset($_GET['offset']))?$_GET['offset']:0;
    if (count ($_GET) == 0)
        $array=null;
    else
        $array=$_GET;
    $array['p_action']='ALL';
    list($sql,$where)=$ledger->build_search_sql($array);
    // Count nb of line
    $max_line=$cn->count_sql($sql);

    list($count,$a)=$ledger->list_operation($sql,$offset,0);
    $bar=navigation_bar($offset,$max_line,$step,$page);

    echo $bar;
    echo $a;
    echo $bar;
    /*
     * Export to csv
     */
    $r=HtmlInput::get_to_hidden(array('l','date_start','date_end','desc','amount_min','amount_max','qcode','accounting','unpaid','gDossier','ledger_type'));
    if (isset($_GET['r_jrn'])) {
      foreach ($_GET['r_jrn'] as $k=>$v)
	$r.=HtmlInput::hidden('r_jrn['.$k.']',$v);
    }
    echo '<form action="export.php" method="get">';
    echo $r;
    echo HtmlInput::hidden('act','CSV:histo');
    echo HtmlInput::submit('viewsearch',_('Export vers CSV'));
    echo HtmlInput::hidden('p_action','ALL');
    ?>
    <input type="button" class="smallbutton" onclick="window.close()" value="<?php echo _('Fermer')?>">

<?php
    echo '</form>';
}
echo '</div>';
?>
