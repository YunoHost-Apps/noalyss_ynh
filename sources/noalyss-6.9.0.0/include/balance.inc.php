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
// Copyright(2004) Dany De Bontridder danydb@aevalys.eu
/*! \file
 * \brief Show the balance and let you print it or export to PDF
 *        file included by user_impress
 *
 * some variable are already defined ($cn, $g_user ...)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once  NOALYSS_INCLUDE.'/ac_common.php';
include_once NOALYSS_INCLUDE.'/class_acc_balance.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';
require_once NOALYSS_INCLUDE.'/class_exercice.php';
global $g_user;
$gDossier=dossier::id();
$exercice=(isset($_GET['exercice']))?$_GET['exercice']:$g_user->get_exercice();


echo '<div class="content">';
/*
 * Let you change the exercice
 */
echo '<fieldset  class="noprint"><legend>'._('Exercice').'</legend>';;
echo '<form method="GET">';
echo _('Choisissez un autre exercice')." : ";
$ex=new Exercice($cn);
$wex=$ex->select('exercice',$exercice,' onchange="submit(this)"');
echo $wex->input();
echo dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac','type'));
echo '</form>';
echo '</fieldset>';


// Show the form for period
echo '<FORM  method="get">';
echo HtmlInput::get_to_hidden(array('ac'));
echo HtmlInput::hidden('type','bal');
echo HtmlInput::get_to_hidden(array('exercice'));
echo dossier::hidden();



// filter on the current year
$from=HtmlInput::default_value_get("from_periode", "");
$input_from=new IPeriod("from_periode",$from,$exercice);
$input_from->show_end_date=false;
$input_from->type=ALL;
$input_from->cn=$cn;
$input_from->filter_year=true;
$input_from->user=$g_user;

echo _('Depuis').' :'.$input_from->input();
// filter on the current year
$to=HtmlInput::default_value_get("to_periode", "");
$input_to=new IPeriod("to_periode",$to,$exercice);
$input_to->show_start_date=false;
$input_to->filter_year=true;
$input_to->type=ALL;
$input_to->cn=$cn;
$input_to->user=$g_user;
echo "  "._('jusque').' :'.$input_to->input();
echo '<br>';
echo HtmlInput::button_action(_('Avancé'), " if (\$('balance_advanced_div').style.display=='none') { \$('balance_advanced_div').show();} else { \$('balance_advanced_div').hide();}");
//-------------------------------------------------
echo '<div id="balance_advanced_div" style="display:none">';

/*  add a all ledger choice */
echo _('Filtre')." ";
$rad=new IRadio();
$array_ledger=$g_user->get_ledger('ALL',3);
$array=get_array_column($array_ledger,'jrn_def_id');
$selected=(isset($_GET['r_jrn']))?$_GET['r_jrn']:null;
$select_cat=(isset($_GET['r_cat']))?$_GET['r_cat']:null;
$array_cat=Acc_Ledger::array_cat();

echo '<ul style="list-style-type:none">';
if ( ! isset($_GET['p_filter']) || $_GET['p_filter']==0) $rad->selected='t';
else $rad->selected=false;
echo '<li>'.$rad->input('p_filter',0)._('Aucun filtre, tous les journaux').'</li>';
if (  isset($_GET['p_filter']) && $_GET['p_filter']==1) $rad->selected='t';
else $rad->selected=false;
echo '<li>'.$rad->input('p_filter',1)._('Filtré par journal');
echo HtmlInput::button_choice_ledger(array('div'=>'','type'=>'ALL','all_type'=>1));
echo '</li>';
if (  isset($_GET['p_filter']) && $_GET['p_filter']==2) $rad->selected='t';
else $rad->selected=false;
echo '<li>'.$rad->input('p_filter',2)._('Filtré par catégorie').HtmlInput::select_cat($array_cat).'</li>';
echo '</ul>';
echo _('Totaux par sous-niveaux');
$ck_lev1=new ICheckBox('lvl1');
$ck_lev2=new ICheckBox('lvl2');
$ck_lev3=new ICheckBox('lvl3');
$ck_lev1->value=1;
$ck_lev2->value=1;
$ck_lev3->value=1;


echo '<ul style="list-style-type:none">';

if (HtmlInput::default_value('lvl1',false,$_GET) !== false)
  $ck_lev1->selected=true;
if (HtmlInput::default_value('lvl2',false,$_GET) !== false)
  $ck_lev2->selected=true;
if (HtmlInput::default_value('lvl3',false,$_GET) !== false)
  $ck_lev3->selected=true;
echo '<li>'.$ck_lev1->input()._('Niveau 1').'</li>';
echo '<li>'.$ck_lev2->input()._('Niveau 2').'</li>';
echo '<li>'.$ck_lev3->input()._('Niveau 3').'</li>';
echo '</ul>';

$unsold=new ICheckBox('unsold');
if (HtmlInput::default_value('unsold',false,$_GET) !== false)
  $unsold->selected=true;

// previous exercice if checked
$previous_exc=new ICheckBox('previous_exc');
if (HtmlInput::default_value('previous_exc',false,$_GET) !== false)
  $previous_exc->selected=true;


$from_poste=new IPoste();
$from_poste->name="from_poste";
$from_poste->set_attribute('ipopup','ipop_account');
$from_poste->set_attribute('label','from_poste_label');
$from_poste->set_attribute('account','from_poste');

$from_poste->value=(isset($_GET['from_poste']))?$_GET['from_poste']:"";
$from_span=new ISpan("from_poste_label","");

$to_poste=new IPoste();
$to_poste->name="to_poste";
$to_poste->set_attribute('ipopup','ipop_account');
$to_poste->set_attribute('label','to_poste_label');
$to_poste->set_attribute('account','to_poste');

$to_poste->value=(isset($_GET['to_poste']))?$_GET['to_poste']:"";
$to_span=new ISpan("to_poste_label","");

echo "<div>";
echo _("Plage de postes")." :".$from_poste->input();
echo $from_span->input();
echo " "._("jusque")." :".$to_poste->input();
echo $to_span->input();
echo "</div>";
echo '<div>';
echo '<p>';
echo _("Uniquement comptes non soldés")." ".$unsold->input();
echo '</p>';
echo '<p>';
echo _("Avec la balance de l'année précédente")." ".$previous_exc->input();
echo '</p>';
echo '</div>';
echo '</div>';
echo HtmlInput::submit("view",_("Visualisation"));
echo '</form>';
echo '<hr>';
//-----------------------------------------------------
// Form
//-----------------------------------------------------
// Show the export button
if ( isset ($_GET['view']  ) )
{

    $hid=new IHidden();


    echo "<table>";
    echo '<TR>';
    echo '<TD><form method="GET" ACTION="export.php">'.
    dossier::hidden().
    HtmlInput::submit('bt_pdf',"Export PDF").
    HtmlInput::hidden("ac",$_REQUEST['ac']).
    HtmlInput::hidden("act","PDF:balance").

    HtmlInput::hidden("from_periode",$_GET['from_periode']).
    HtmlInput::hidden("to_periode",$_GET['to_periode']);
    echo HtmlInput::hidden('p_filter',$_GET['p_filter']);
    for ($e=0;$e<count($selected);$e++)
        if (isset($selected[$e]) && in_array ($selected[$e],$array))
            echo    HtmlInput::hidden("r_jrn[$e]",$selected[$e]);
    for ($e=0;$e<count($array_cat);$e++)
        if (isset($select_cat[$e]))
            echo    HtmlInput::hidden("r_cat[$e]",$e);

    echo HtmlInput::hidden("from_poste",$_GET['from_poste']).
    HtmlInput::hidden("to_poste",$_GET['to_poste']);
    echo HtmlInput::get_to_hidden(array('lvl1','lvl2','lvl3','unsold','previous_exc'));

    echo "</form></TD>";

    echo '<TD><form method="GET" ACTION="export.php">'.
    HtmlInput::submit('bt_csv',"Export CSV").
    dossier::hidden().
    HtmlInput::hidden("act","CSV:balance").
    HtmlInput::hidden("from_periode",$_GET['from_periode']).
    HtmlInput::hidden("to_periode",$_GET['to_periode']);
    echo HtmlInput::get_to_hidden(array('ac'));
    echo HtmlInput::hidden('p_filter',$_GET['p_filter']);
    for ($e=0;$e<count($selected);$e++){
        if (isset($selected[$e]) && in_array ($selected[$e],$array)){
                echo    HtmlInput::hidden("r_jrn[$e]",$selected[$e]);
            }
    }
    for ($e=0;$e<count($array_cat);$e++)
        if (isset($select_cat[$e]))
            echo    HtmlInput::hidden("r_cat[$e]",$e);

    echo   HtmlInput::hidden("from_poste",$_GET['from_poste']).
    HtmlInput::hidden("to_poste",$_GET['to_poste']);
    echo HtmlInput::get_to_hidden(array('unsold','previous_exc'));

    echo "</form></TD>";
	echo '<td style="vertical-align:top">';
	echo HtmlInput::print_window();
	echo '</td>';
    echo "</TR>";

    echo "</table>";
}


//-----------------------------------------------------
// Display result
//-----------------------------------------------------
if ( isset($_GET['view'] ) )
{
    $bal=new Acc_Balance($cn);
    if ( $_GET['p_filter']==1)
    {
        for ($e=0;$e<count($selected);$e++)
            if (isset($selected[$e]) && in_array ($selected[$e],$array))
                $bal->jrn[]=$selected[$e];
    }
    if ( $_GET['p_filter'] == 0 )
    {
        $bal->jrn=null;
    }
    if ( $_GET['p_filter'] == 2 && isset ($_GET['r_cat']))
    {
        $bal->filter_cat($_GET['r_cat']);
    }
    $bal->from_poste=$_GET['from_poste'];
    $bal->to_poste=$_GET['to_poste'];
    if ( isset($_GET['unsold']))  $bal->unsold=true;
    $previous=(isset($_GET['previous_exc']))?1:0;
    
    $row=$bal->get_row($_GET['from_periode'],
                       $_GET['to_periode'],
            $previous);
    $previous= (isset ($row[0]['sum_cred_previous']))?1:0;

    $periode=new Periode($cn);
    $a=$periode->get_date_limit($_GET['from_periode']);
    $b=$periode->get_date_limit($_GET['to_periode']);
    echo "<h2 class=\"info\"> période du ".$a['p_start']." au ".$b['p_end']."</h2>";
	echo '<span style="display:block">';
	echo _('Filtre').HtmlInput::infobulle(24);
	echo HtmlInput::filter_table("t_balance", "0,1","1");
	echo '</span>';
    echo '<table id="t_balance" width="100%">';
    echo '<th>Poste Comptable</th>';
    echo '<th>Libell&eacute;</th>';
    if ( $previous == 1 ){
        echo '<th>D&eacute;bit N-1</th>';
        echo '<th>Cr&eacute;dit N-1</th>';
        echo '<th>Solde D&eacute;biteur N-1</th>';
        echo '<th>Solde Cr&eacute;diteur N-1</th>';
        if ( isset($_GET['lvl1']) || isset($_GET['lvl2']) || isset($_GET['lvl3'])) 
            echo '<th>Solde  N-1</th>';
            
    }
    echo '<th>D&eacute;bit</th>';
    echo '<th>Cr&eacute;dit</th>';
    echo '<th>Solde D&eacute;biteur </th>';
    echo '<th>Solde Cr&eacute;diteur</th>';
    if ( isset($_GET['lvl1']) || isset($_GET['lvl2']) || isset($_GET['lvl3'])) 
        echo '<th>Solde</th>';
    $i=0;
    if ( $previous == 1) {
        $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred','sum_cred_previous','sum_deb_previous','solde_deb_previous','solde_cred_previous');
    }
    else {
              $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred') ;
    }
    foreach($a_sum as $a)
      {
	$lvl1[$a]=0;
	$lvl2[$a]=0;
	$lvl3[$a]=0;
      }
    $lvl1_old='';
    $lvl2_old='';
    $lvl3_old='';

    bcscale(2);
    foreach ($row as $r)
    {
        $i++;
        if ( $i%2 == 0 )
            $tr="even";
        else
            $tr="odd";
        $view_history= sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_account(\'%s\',\'%s\')" >%s</A>',
                               $r['poste'], $gDossier, $r['poste']);

	/*
	 * level x
	 */
	foreach (array(3,2,1) as $ind)
	  {
	    if ( ! isset($_GET['lvl'.$ind]))continue;
	    if (${'lvl'.$ind.'_old'} == '')	  ${'lvl'.$ind.'_old'}=mb_substr($r['poste'],0,$ind);
	    if ( ${'lvl'.$ind.'_old'} != mb_substr($r['poste'],0,$ind))
	      {

		echo '<tr >';
		echo td(${'lvl'.$ind.'_old'},'style="font-weight:bold;"');
		echo td(${'lvl'.$ind.'_old'}." "._("Total niveau")." ".$ind,'style="font-weight:bold;"');
                if ($previous==1) {
                    echo td(nbm(${'lvl'.$ind}['sum_deb_previous']),'class="previous_year" style="font-weight:bold;"');
                    echo td(nbm(${'lvl'.$ind}['sum_cred_previous']),' class="previous_year" style="font-weight:bold;" ');
                    echo td(nbm(${'lvl'.$ind}['solde_deb_previous']),'class="previous_year" style="font-weight:bold;"');
                    echo td(nbm(${'lvl'.$ind}['solde_cred_previous']),'class="previous_year" style="font-weight:bold;"');
                    $delta_previous=bcsub(${'lvl'.$ind}['solde_cred_previous'],${'lvl'.$ind}['solde_deb_previous']);
                    $side_previous=($delta_previous > 0 ) ? "C":"D";
                    echo td(nbm(abs($delta_previous))." $side_previous",'class="previous_year"  style="text-align:right;font-weight:bold;"  ');
                    
                }
		echo td(nbm(${'lvl'.$ind}['sum_deb']),'style="text-align:right;font-weight:bold;"  ');
		echo td(nbm(${'lvl'.$ind}['sum_cred']),'style="text-align:right;font-weight:bold;"');
		echo td(nbm(${'lvl'.$ind}['solde_deb']),'style="text-align:right;font-weight:bold;"');
		echo td(nbm(${'lvl'.$ind}['solde_cred']),'style="text-align:right;font-weight:bold;"');
                $delta=bcsub(${'lvl'.$ind}['solde_cred'],${'lvl'.$ind}['solde_deb']);
                $side=($delta > 0 ) ? "C":"D";
                echo td(nbm(abs($delta))." $side",'style="text-align:right;font-weight:bold;"  ');

		echo '</tr>';
		${'lvl'.$ind.'_old'}=mb_substr($r['poste'],0,$ind);
		foreach($a_sum as $a)
		  {
		    ${'lvl'.$ind}[$a]=0;
		  }
	      }
	  }
          
	  foreach($a_sum as $a)
	    {
	      $lvl1[$a]=bcadd($lvl1[$a],$r[$a]);
	      $lvl2[$a]=bcadd($lvl2[$a],$r[$a]);
	      $lvl3[$a]=bcadd($lvl3[$a],$r[$a]);
	    }
        echo '<TR class="'.$tr.'">';
        echo td($view_history);
        echo td(h($r['label']));
        if ($previous == 1 ) {
            echo td(nbm($r['sum_deb_previous']),' class="previous_year"');
            echo td(nbm($r['sum_cred_previous']),' class="previous_year" ');
            echo td(nbm($r['solde_deb_previous']),' class="previous_year"');
            echo td(nbm($r['solde_cred_previous']),'class="previous_year" ');
            if ( isset($_GET['lvl1']) || isset($_GET['lvl2']) || isset($_GET['lvl3']))             echo '<td></td>';
        }
        echo td(nbm($r['sum_deb']),'style="text-align:right;"');
	echo td(nbm($r['sum_cred']),'style="text-align:right;"');
	echo td(nbm($r['solde_deb']),'style="text-align:right;"');
	echo td(nbm($r['solde_cred']),'style="text-align:right;"');
        if ( isset($_GET['lvl1']) || isset($_GET['lvl2']) || isset($_GET['lvl3']))             echo '<td></td>';
        echo '</TR>';

    }
    echo '</table>';

}// end submit
echo "</div>";
?>
