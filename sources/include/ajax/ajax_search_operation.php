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
/* ! \file
 * \brief Search module
 */
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger_search.class.php';

global $http;
$gDossier=dossier::id();

require_once NOALYSS_INCLUDE.'/lib/database.class.php';
/* Admin. Dossier */

$cn=Dossier::connect();
require_once NOALYSS_INCLUDE.'/class/user.class.php';
// display a search box


$base=basename($_SERVER['SCRIPT_NAME']);
$inside=false;
$tiers=$http->get("tiers", "string", "");
// With the amount id, we find the amount in a html elt
if (isset($_GET['amount_id']))
{
    put_global(array(
        array("key"=>'amount_min', 'value'=>$_GET['amount_id']),
        array("key"=>'amount_max', 'value'=>$_GET['amount_id'])
    ));
}
$target=$http->get("target", "string", "");
$ledger=new Acc_Ledger_Search('ALL', 1, $target);

if ($base=='recherche.php'||$base=='do.php')
{
    echo '<div class="content" >';
    echo '<form method="GET">';
}
else
{
    $action="";
    $callback="";
    echo HtmlInput::title_box(_('Recherche'), $target);
    echo '<form name="search_form_ajx" id="search_form_ajx" onsubmit="search_operation(this);return false">';
    echo HtmlInput::get_to_hidden(array('ctlc', 'ledger'));
    echo HtmlInput::hidden("target", $target);
    $inside=true;
}
echo HtmlInput::hidden("tiers", $tiers);
echo $ledger->search_form();
echo HtmlInput::submit("viewsearch", _("Recherche"));
echo HtmlInput::button_close($target);
echo '</form>';

if (isset($_GET['amount_min'])&&isset($_GET['amount_max'])&&($_GET['amount_max']!=0||$_GET['amount_min']!=0 ))
{
    $_GET['viewsearch']=1;
    put_global(
            array
                (
                array('key'=>'ledger_type', 'value'=>'ALL')
            )
    );
}
//-----------------------------------------------------
// Display search result
//-----------------------------------------------------
if (isset($_GET['viewsearch']))
{

    // Navigation bar
    $step=MAX_RECONCILE;
    $page=(isset($_GET['offset']))?$_GET['page']:1;
    $offset=(isset($_GET['offset']))?$_GET['offset']:0;
    if (count($_GET)==0)
        $array=null;
    else
        $array=$_GET;
    $array['p_action']='ALL';
    if (!isset($array['date_start'])||!isset($array['date_end']))
    {
        // get first date of current exercice
        list($array['date_start'], $array['date_end'])=$g_user->get_limit_current_exercice();
    }

    list($sql, $where)=$ledger->build_search_sql($array);
    // Count nb of line
    $max_line=$cn->count_sql($sql);
    $target=$http->get("target");
    list($count, $content)=$ledger->list_operation_to_reconcile($sql, $target);
    $bar=navigation_bar($offset, $max_line, $step, $page);

    if (!$inside)
    {
        echo $bar;
    }
    else
    {
        if ($step<$max_line)
        {
            echo '<h2 class="notice">'._('Liste limitée à ').$step._(' enregistrements. Le nombre d\'enregistrements trouvés est de ').$max_line.'</h2>';
        }
    }
    echo '<form method="get" onsubmit="set_reconcile(this);return false">';
    echo HtmlInput::hidden("tiers", $tiers);
    echo HtmlInput::submit("upd_rec", _("Mettre à jour"));
    echo HtmlInput::get_to_hidden(array('ctlc', 'amount_id', 'ledger'));
    echo HtmlInput::get_to_hidden(array('l', 'date_start', 'date_end', 'desc', 'amount_min', 'amount_max', 'qcodesearch_op',
        'accounting', 'unpaid', 'gDossier', 'ledger_type'));
    echo $content;
    echo HtmlInput::submit("upd_rec", _("Mettre à jour"));
    if (!$inside)
    {
        echo $bar;
    }

    if (isset($_GET[$op.'r_jrn']))
    {
        foreach ($_GET[$op.'r_jrn'] as $k=> $v) echo HtmlInput::hidden($op.'r_jrn['.$k.']', $v);
    }
    echo '</form>';
}
echo '</div>';
?>
