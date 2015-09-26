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
/*! \file
 * \brief print first the report in html and propose to print it in pdf
 *        file included by user_impress
 *
 * some variable are already defined ($cn, $g_user ...)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_acc_report.php';
require_once NOALYSS_INCLUDE.'/class_exercice.php';
global $g_user;
//-----------------------------------------------------
// If print is asked
// First time in html
// after in pdf or cvs
//-----------------------------------------------------
if ( isset( $_GET['bt_html'] ) )
{
    $Form=new Acc_Report($cn,$_GET['form_id']);
    $Form->get_name();
    // step asked ?
    //--
    $type_periode=HtmlInput::default_value_get("type_periode", -1);
    if ( $type_periode == 1 )
        $array=$Form->get_row( $_GET['from_date'],$_GET['to_date'], $type_periode);

    if ($type_periode == 0   && $_GET['p_step'] == 0)
        $array=$Form->get_row( $_GET['from_periode'],$_GET['to_periode'], $type_periode);


    if ($type_periode  == 0  && $_GET['p_step'] == 1 )
    {
        // step are asked
        //--
        for ($e=$_GET['from_periode'];$e<=$_GET['to_periode'];$e+=$_GET['p_step'])
        {

            $periode=getPeriodeName($cn,$e);
            if ( $periode == null ) continue;
            $array[]=$Form->get_row($e,$e,$_GET['type_periode']);
            $periode_name[]=$periode;
        }
    }



    $rep="";

    $hid=new IHidden();
    echo '<div class="content">';
    if ( $_GET['type_periode'] == 0)
    {
        $t=($_GET['from_periode']==$_GET['to_periode'])?"":" -> ".getPeriodeName($cn,$_GET['to_periode'],'p_end');
        echo '<h2 class="info">'.$Form->id." ".$Form->name.
        " - ".getPeriodeName($cn,$_GET['from_periode'],'p_start').
        " ".$t.
        '</h2>';
    }
    else
    {
        echo '<h2 class="info">'.$Form->id." ".$Form->name.
        ' Date :'.
        $_GET['from_date'].
        " au ".
        $_GET['to_date'].
        '</h2>';
    }
    echo '<table >';
    echo '<TR>';
    echo '<TD><form method="GET" ACTION="?">'.
    dossier::hidden().
    HtmlInput::submit('bt_other',"Autre Rapport").
    $hid->input("type","rapport").$hid->input("ac",$_GET['ac'])."</form></TD>";

    echo '<TD><form method="GET" ACTION="export.php">'.
    HtmlInput::submit('bt_pdf',"Export PDF").
      HtmlInput::hidden('act','PDF:report').
    dossier::hidden().
    $hid->input("type","rapport").
    $hid->input("ac",$_GET['ac']).
    $hid->input("form_id",$Form->id);
    if ( isset($_GET['from_periode'])) echo $hid->input("from_periode",$_GET['from_periode']);
    if ( isset($_GET['to_periode'])) echo $hid->input("to_periode",$_GET['to_periode']);
    if (isset($_GET['p_step'])) echo $hid->input("p_step",$_GET['p_step']);
    if ( isset($_GET['from_date'])) echo $hid->input("from_date",$_GET['from_date']);
    if ( isset($_GET['to_date'])) echo $hid->input("to_date",$_GET['to_date']);
    echo $hid->input("type_periode",$_GET['type_periode']);




    echo "</form></TD>";
    echo '<TD><form method="GET" ACTION="export.php">'.
      HtmlInput::hidden('act','CSV:report').
    HtmlInput::submit('bt_csv',"Export CSV").
    dossier::hidden().
    $hid->input("type","form").
    $hid->input("ac",$_GET['ac']).
    $hid->input("form_id",$Form->id);
    if ( isset($_GET['from_periode'])) echo $hid->input("from_periode",$_GET['from_periode']);
    if ( isset($_GET['to_periode'])) echo $hid->input("to_periode",$_GET['to_periode']);
    if (isset($_GET['p_step'])) echo $hid->input("p_step",$_GET['p_step']);
    if ( isset($_GET['from_date'])) echo $hid->input("from_date",$_GET['from_date']);
    if ( isset($_GET['to_date'])) echo $hid->input("to_date",$_GET['to_date']);
    echo	$hid->input("type_periode",$_GET['type_periode']);


    echo "</form></TD>";

    echo "</TR>";

    echo "</table>";
    if ( count($Form->row ) == 0 )
        exit;
    if ( $_GET['type_periode']== 0 )
    {
        if ( $_GET['p_step'] == 0)
        { // check the step
            // show tables
            ShowReportResult($Form->row);
        }
        else
        {
            $a=0;
            foreach ( $array as $e)
            {
                echo '<h2 class="info">Periode : '.$periode_name[$a]."</h2>";
                $a++;
                ShowReportResult($e);
            }
        }
    }
    else
    {
        ShowReportResult($Form->row);
    }
    echo "</div>";
    exit;
}

//-----------------------------------------------------
// Show the jrn and date
//-----------------------------------------------------
require_once NOALYSS_INCLUDE.'/class_database.php';
$ret=$cn->make_array("select fr_id,fr_label
                     from formdef
                     order by fr_label");
if ( sizeof($ret) == 0 )
{
    echo "Aucun Rapport";
    return;
}
//-----------------------------------------------------
// Form
//-----------------------------------------------------
echo '<div class="content">';
$exercice=(isset($_GET['exercice']))?$_GET['exercice']:$g_user->get_exercice();

/*
 * Let you change the exercice
 */
echo '<fieldset><legend>'._('Exercice').'</legend>';;
echo '<form method="GET">';
echo 'Choisissez un autre exercice :';
$ex=new Exercice($cn);
$wex=$ex->select('exercice',$exercice,' onchange="submit(this)"');
echo $wex->input();
echo dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac','type'));
echo '</form>';
echo '</fieldset>';


echo '<FORM METHOD="GET">';
$hidden=new IHidden();
echo $hidden->input("ac",$_GET['ac']);
echo $hidden->input("type","rapport");
echo 	dossier::hidden();

echo '<TABLE><TR>';
$w=new ISelect();
$w->table=1;
print td(_("Choisissez le rapport"));
print $w->input("form_id",$ret);
print '</TR>';
//-- calendrier ou periode comptable
$aCal=array(
          array('value'=>0,'label'=>_('P&eacute;riode comptable')),
          array('value'=>1,'label'=>_('Calendrier'))
      );

$w->javascript=' onchange=enable_type_periode();';
$w->id='type_periode';
echo '<tr>';
print td('Type de date : ');
echo $w->input('type_periode',$aCal);
echo '</Tr>';
$w->javascript='';
print '<TR>';
// filter on the current year
$filter_year=" where p_exercice='".sql_string($exercice)."'";
$periode_start_select=new ISelect();
$periode_start_select->table=1;
$periode_end_select=new ISelect();
$periode_end_select->table=1;
$periode_start=$cn->make_array("select p_id,to_char(p_start,'DD-MM-YYYY') from parm_periode $filter_year order by p_start,p_end");
print td("P&eacute;riode comptable : Depuis");
echo $periode_start_select->input('from_periode',$periode_start);
print td(" jusqu'à ");
$periode_end=$cn->make_array("select p_id,to_char(p_end,'DD-MM-YYYY') from parm_periode  $filter_year order by p_start,p_end");
print $periode_end_select->input('to_periode',$periode_end);
print "</TR>";
echo '<tr>';
//--- by date
$date_from=new IDate('from_date');
$date_from->id='from_date';
$date_to=new IDate('to_date');
$date_to->id='to_date';

echo td(_("Calendrier depuis :"));
echo td($date_from->input('from_date'));
echo td(_("jusque"));
echo td($date_to->input('to_date'));
echo '</tr>';

$aStep=array(
           array('value'=>0,'label'=>_('Pas d\'étape')),
           array('value'=>1,'label'=>_('1 mois'))
       );
echo '<tr>';
echo td(_('Par étape de'));
$w->id='p_step';
echo $w->input('p_step',$aStep);
echo '</TR>';

echo '</TABLE>';
echo '<span class="notice"> '._('Attention : vous ne pouvez pas utiliser les &eacute;tapes avec les dates calendriers.').'</span>';
echo '<br>';
echo '<span class="notice">'._('Les clauses FROM sont ignorés avec les dates calendriers').'</span>';
echo '<br>';
print HtmlInput::submit('bt_html',_('Visualisation'));

echo '</FORM>';
echo '<script>enable_type_periode()</script>';
echo '<hr>';
echo '</div>';
//-----------------------------------------------------
// Function
//-----------------------------------------------------
function ShowReportResult($p_array)
{

    echo '<TABLE class="result">';
    echo "<TR>".
    "<TH> Description </TH>".
    "<TH> montant </TH>".
    "</TR>";
    $i=0;
    foreach ( $p_array as $op )
    {
        $i++;
        $class= ( $i % 2 == 0 )?' class="odd"':' class="even"';

        echo "<TR $class>".
        "<TD>".h($op['desc'])."</TD>".
        "<TD align=\"right\">".nbm($op['montant'])."</TD>".
        "</TR>";
    }
    echo "</table>";

}

?>
