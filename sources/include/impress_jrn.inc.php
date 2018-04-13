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
/** \file
 * \brief ask for Printing the ledger (pdf,html)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/icheckbox.class.php';
require_once NOALYSS_INCLUDE.'/class/exercice.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger_history.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
$gDossier = dossier::id();
global $g_user,$http;

/**
 * Get exercice
 */
$user_exercice=$g_user->get_exercice();
$exercice =$http->get("exercice","string",$user_exercice);


//-----------------------------------------------------
// Show the ledger and date
//-----------------------------------------------------
if ($g_user->Admin() == 0 && $g_user->is_local_admin() == 0  && $g_user->get_status_security_ledger()==1)
{
	$sql = "select jrn_def_id,jrn_def_name
         from jrn_def join jrn_type on jrn_def_type=jrn_type_id
         join user_sec_jrn on uj_jrn_id=jrn_def_id
         where
         uj_login=$1
         and uj_priv in ('R','W')
         and ( jrn_enable=1 
                or 
                exists (select 1 from jrn where jr_tech_per in (select p_id from parm_periode where p_exercice=$2)))
		 order by jrn_def_name
         ";
	$ret = $cn->make_array($sql,0,array($g_user->login,$exercice));
}
else
{
	$ret = $cn->make_array("select jrn_def_id,jrn_def_name
                         from jrn_def join jrn_type on jrn_def_type=jrn_type_id
                         where
                         jrn_enable=1 or exists(select 1 from jrn where jr_tech_per in (select p_id from parm_periode where p_exercice=$1))
						 order by jrn_def_name
						 ",0,[$exercice]);
}

/*
 * Show all the available ledgers
 */
$a = count($ret);
if (count($ret) < 1) 	NoAccess();

$all = array('value' => 0, 'label' => _('Tous les journaux disponibles'));
$ret[$a] = $all;

// Get the from_periode and to_periode
$from_periode=$http->get("from_periode","number","");
$to_periode=$http->get("to_periode","number","");

// if from_periode empty, then set to first and last 
// periode of the exercice (from preference)

if ($from_periode=="" || $to_periode=="")
{
    $t_periode=new Periode($cn);
    list($per_min,$per_max)=$t_periode->get_limit($exercice);
    $from_periode=$per_min->p_id;
    $to_periode=$per_max->p_id;
}

//-----------------------------------------------------
// Form
//-----------------------------------------------------
echo '<div class="content">';
/*
 * Let you change the exercice
 */
echo '<form method="GET">';
echo '<fieldset><legend>' . _('Exercice') . '</legend>';
;
echo _('Choisissez un autre exercice').' :';
$ex = new Exercice($cn);
$wex = $ex->select('exercice', $exercice, ' onchange="submit(this)"');
echo $wex->input();
echo dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac', 'type'));
echo '</fieldset>';
echo '</form>';
?>
<?php

echo '<FORM METHOD="GET">' . dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac', 'type'));
echo HtmlInput::hidden('type', 'jrn');
echo HtmlInput::get_to_hidden(array('exercice'));
echo '<TABLE  ><TR>';
$w = new ISelect();
$w->table = 1;
$label = _("Choisissez le journal");
$w->selected = $http->get('jrn_id',"number",0);
print td($label) . $w->input("jrn_id", $ret);
print '</TR>';
print '<TR>';
// filter on the current year
$filter_year = " where p_exercice='" . sql_string($exercice) . "'";
$periode_start = $cn->make_array("select p_id,to_char(p_start,'DD-MM-YYYY') from parm_periode $filter_year order by p_start,p_end");
$w->selected =  $from_periode ;

print td('Depuis') . $w->input('from_periode', $periode_start);
print '</TR>';
print '<TR>';

$periode_end = $cn->make_array("select p_id,to_char(p_end,'DD-MM-YYYY') from parm_periode $filter_year order by p_start,p_end");
$w->selected =  $to_periode ;

// By default , show last day of exercice
if ($w->selected== '' ){
        $w->selected=$per_max->p_id;
}
print td('Jusque ') . $w->input('to_periode', $periode_end);
print "</TR><TR>";
$a = array(
	array('value' => 'L', 'label' => _('Liste opérations')),
	array('value' => 'E', 'label' => _('Liste détaillées opérations ')),
	array('value' => 'A', 'label' => _('Ecriture comptable')),
	array('value' => 'D', 'label' => _('Détails TVA'))
);
$w->selected = 1;
print '</TR>';
print '<TR>';
$simple=$http->get("p_simple","string","L");
$w->selected = $simple;
echo '<td>Style d\'impression '.Icon_Action::infobulle(32).'</td>' . $w->input('p_simple', $a);
print "</TR>";

echo '</TABLE>';
print HtmlInput::submit('bt_html', _('Visualisation'));

echo '</FORM>';
echo '<hr>';

 
//-----------------------------------------------------
// If print is asked
// First time in html
// after in pdf or cvs
//-----------------------------------------------------
if (isset($_REQUEST['bt_html']))
{
    // Type of report : listing=1 , Accounting writing=0, detail =2
    $hid=new IHidden();
    $jrn_id=$http->get("jrn_id","number");
    echo '<table>';
    echo '<td>';
    echo '<form method="GET" ACTION="export.php">' . dossier::hidden() .
        HtmlInput::submit('bt_pdf', "Export PDF") .
        HtmlInput::hidden('act', 'PDF:ledger') .
        $hid->input("type", "jrn") .
        $hid->input("jrn_id", $jrn_id) .
        $hid->input("from_periode", $from_periode) .
        $hid->input("to_periode", $to_periode);
        echo $hid->input("p_simple", $simple);
        echo HtmlInput::get_to_hidden(array('ac', 'type'));
        echo "</form>";
    echo '</td>';

    echo '<TD><form method="GET" ACTION="export.php">' . dossier::hidden() .
        HtmlInput::submit('bt_csv', "Export CSV") .
        HtmlInput::hidden('act', 'CSV:ledger') .
        $hid->input("type", "jrn") .
        $hid->input("jrn_id", $jrn_id) .
        $hid->input("from_periode", $from_periode) .
        $hid->input("to_periode", $to_periode);
        echo $hid->input("p_simple", $simple);
        echo HtmlInput::get_to_hidden(array('ac', 'type'));
        echo "</form></TD>";

    echo '<td style="vertical-align:top">';
        echo HtmlInput::print_window();
    echo '</td>';
    
    echo "</TR>";

    echo "</table>";

    /*
     * Compute an array with all the usable ledger
     */
    $a_ledger=[];
    if ( $jrn_id == 0) {
        $nb_ret=count($ret);
        for ($i=0;$i<$nb_ret;$i++) {
            if ($ret[$i]['value']!=0) 
                $a_ledger[$i]=$ret[$i]['value'];
        }
    } else {
        $a_ledger=[$jrn_id];
    }
    
    $ledger_history=Acc_Ledger_History::factory($cn,$a_ledger,$from_periode,$to_periode,$simple);
    
    $ledger_history->export_html();
    

}

echo '</div>';
?>