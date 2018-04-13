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
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/idate.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_report.class.php';
require_once NOALYSS_INCLUDE.'/class/exercice.class.php';
global $g_user,$http;
//-----------------------------------------------------
// If print is asked
// First time in html
// after in pdf or cvs
//-----------------------------------------------------
if ( isset( $_GET['bt_html'] ) )
{
    
    // step asked ?
    //--
    try
    {
        $Form=new Acc_Report($cn,$http->get('form_id',"number"));
        $Form->get_name();
        $type_periode=$http->get("type_periode", "number", -1);
        
        if ($type_periode==1)
        {
            $from_date=$http->get("from_date", "date");
            $to_date=$http->get("to_date", "date");
            $array=$Form->get_row(
                    $from_date,
                    $to_date,
                    $type_periode);
        }
        // Printing asked by range of date
        if ($type_periode==0)
        {
            $from_periode=$http->get("from_periode");
            $to_periode=$http->get("to_periode");
            $p_step=$http->get('p_step');
            if ( $http->get("p_step","number")==1) {
                // step are asked
                //--
                for ($e=$from_periode; $e<=$to_periode;
                            $e+=$p_step)
                {

                    $periode=getPeriodeName($cn, $e);
                    if ($periode==null)
                        continue;
                    $array[]=$Form->get_row($e, $e, $type_periode);
                    $periode_name[]=$periode;
                }
            } else {
                 $array=$Form->get_row(
                    $http->get('from_periode',"number"), 
                    $http->get('to_periode',"number"),
                    $type_periode);
            }
        }
    }
    catch (Exception $ex)
    {
        alert($ex->getMessage());;
        
    }



    $rep="";

    $hid=new IHidden();
    echo '<div class="content">';
    if ($type_periode == 0)
    {
        $t=($from_periode==$to_periode)?"":" -> ".getPeriodeName($cn,$to_periode,'p_end');
        echo '<h2 class="info">'.$Form->id." ".$Form->name.
        " - ".getPeriodeName($cn,$from_periode,'p_start').
        " ".$t.
        '</h2>';
    }
    else
    {
        echo '<h2 class="info">'.$Form->id." ".$Form->name.
        ' Date :'.
        $from_date.
        " au ".
        $to_date.
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
    if ( isset($from_periode)) echo $hid->input("from_periode",$from_periode);
    if ( isset($to_periode)) echo $hid->input("to_periode",$to_periode);
    if (isset($p_step)) echo $hid->input("p_step",$p_step);
    if ( isset($from_date)) echo $hid->input("from_date",$from_date);
    if ( isset($to_date)) echo $hid->input("to_date",$to_date);
    echo $hid->input("type_periode",$type_periode);




    echo "</form></TD>";
    echo '<TD><form method="GET" ACTION="export.php">'.
      HtmlInput::hidden('act','CSV:report').
    HtmlInput::submit('bt_csv',"Export CSV").
    dossier::hidden().
    $hid->input("type","form").
    $hid->input("ac",$_GET['ac']).
    $hid->input("form_id",$Form->id);
    if ( isset($from_periode)) echo $hid->input("from_periode",$from_periode);
    if ( isset($to_periode)) echo $hid->input("to_periode",$to_periode);
    if (isset($p_step)) echo $hid->input("p_step",$p_step);
    if ( isset($from_date)) echo $hid->input("from_date",$from_date);
    if ( isset($to_date)) echo $hid->input("to_date",$to_date);
    echo	$hid->input("type_periode",$_GET['type_periode']);


    echo "</form></TD>";

    echo "</TR>";

    echo "</table>";
    if ( count($Form->row ) == 0 )
        exit;
    if ( $type_periode== 0 )
    {
        if ( $p_step == 0)
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
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
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
