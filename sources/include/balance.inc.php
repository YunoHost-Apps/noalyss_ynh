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
include_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
include_once NOALYSS_INCLUDE.'/class/acc_balance.class.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/ispan.class.php';
require_once NOALYSS_INCLUDE.'/lib/icheckbox.class.php';
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger.class.php';
require_once NOALYSS_INCLUDE.'/class/periode.class.php';
require_once NOALYSS_INCLUDE.'/class/exercice.class.php';
global $g_user, $http;
$gDossier=dossier::id();
// Get the exercice
$exercice=$http->request("exercice","number",0);
if ($exercice == 0 ){
    $exercice=$g_user->get_exercice();
}

bcscale(2);

echo '<div class="content">';
/*
 * Let you change the exercice
 */
echo '<form method="GET">';
echo _('Choisissez un autre exercice')." : ";
$ex=new Exercice($cn);
$js=sprintf("updatePeriode(%d,'%s','%s','%s',1)",Dossier::id(),'exercice','from_periode','to_periode');
$wex=$ex->select('exercice',$exercice,' onchange="'.$js.'"');
echo $wex->input();
echo dossier::hidden();
echo HtmlInput::get_to_hidden(array('ac','type'));


// Show the form for period
echo HtmlInput::get_to_hidden(array('ac'));
echo HtmlInput::hidden('type','bal');
echo dossier::hidden();



// filter on the current year
$from=$http->get("from_periode", "number",0);
$input_from=new IPeriod("from_periode",$from,$exercice);
$input_from->id="from_periode";
$input_from->show_end_date=false;
$input_from->type=ALL;
$input_from->cn=$cn;
$input_from->filter_year=true;
$input_from->user=$g_user;

echo _('Depuis').' :'.$input_from->input();
// filter on the current year
$to=$http->get("to_periode", "number",0);


if( $to == 0) {
     $t_periode=new Periode($cn);
     list($per_max,$per_min)=$t_periode->get_limit($exercice);
     $to=$per_min->p_id;
}
$input_to=new IPeriod("to_periode",$to,$exercice);
$input_to->id="to_periode";
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

if ($http->get('lvl1',"string",false) !== false)
  $ck_lev1->selected=true;
if ($http->get('lvl2',"string",false) !== false)
  $ck_lev2->selected=true;
if ($http->get('lvl3',"string",false) !== false)
  $ck_lev3->selected=true;
echo '<li>'.$ck_lev1->input()._('Niveau 1').'</li>';
echo '<li>'.$ck_lev2->input()._('Niveau 2').'</li>';
echo '<li>'.$ck_lev3->input()._('Niveau 3').'</li>';
echo '</ul>';

$unsold=new ICheckBox('unsold');
if ($http->get('unsold',"string",false) !== false)
  $unsold->selected=true;

// previous exercice if checked
$previous_exc=new ICheckBox('previous_exc');
if ($http->get('previous_exc',"string",false) !== false)
  $previous_exc->selected=true;


$from_poste=new IPoste();
$from_poste->name="from_poste";
$from_poste->set_attribute('ipopup','ipop_account');
$from_poste->set_attribute('label','from_poste_label');
$from_poste->set_attribute('account','from_poste');

$from_poste->value=$http->get('from_poste',"string",''); 
$from_span=new ISpan("from_poste_label","");

$to_poste=new IPoste();
$to_poste->name="to_poste";
$to_poste->set_attribute('ipopup','ipop_account');
$to_poste->set_attribute('label','to_poste_label');
$to_poste->set_attribute('account','to_poste');

$to_poste->value=$http->get('to_poste',"string",''); 
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
?>
<div>
    <?php echo _("Récapitulatif par classe")?>
    <?php 
        $summary=new ICheckBox("summary");
        $summary->value=1;
        $is_summary=$http->get("summary","string", 0);
        $summary->set_check($is_summary);
        echo $summary->input();
    ?>
</div>
<?php
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
            HtmlInput::hidden("summary", $is_summary).
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
    $from_periode=$http->get("from_periode","number");
    $to_periode=$http->get("to_periode","number");
    $row=$bal->get_row($from_periode,$to_periode,$previous);
    $previous= (isset ($row[0]['sum_cred_previous']))?1:0;

    $periode=new Periode($cn);
    $a=$periode->get_date_limit($_GET['from_periode']);
    $b=$periode->get_date_limit($_GET['to_periode']);
    echo "<h2 class=\"info\"> période du ".$a['p_start']." au ".$b['p_end']."</h2>";
	echo '<span style="display:block">';
	echo _('Cherche').Icon_Action::infobulle(24);
	echo HtmlInput::filter_table("t_balance", "0,1","1");
	echo '</span>';
    echo '<table id="t_balance" width="100%">';
    echo '<th>'._("Poste Comptable").'</th>';
    echo '<th>'._("Libellé").'</th>';
    if ( $previous == 1 ){
        echo '<th>'._("Débit N-1").'</th>';
        echo '<th>'._('Crédit N-1').'</th>';
        echo '<th>'._('Solde N-1').'</th>';
            
    }
    echo '<th>'._('Ouverture').'</th>';
    echo '<th>'._('Débit').'</th>';
    echo '<th>'._('Crédit').'</th>';
    echo '<th>'._('Solde').'</th>';

    $i=0;
    if ( $previous == 1) {
        $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred','sum_deb_ope','sum_cred_ope','sum_cred_previous','sum_deb_previous','solde_previous');
    }
    else {
              $a_sum=array('sum_cred','sum_deb','solde_deb','solde_cred','sum_deb_ope','sum_cred_ope') ;
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
    $nb_row = count($row);
    
    // Compute for the summary
    $summary_tab=$bal->summary_init();
    $summary_prev_tab=$bal->summary_init();
    foreach ($row as $r)
    {
        $i++;
        if ( $i%2 == 0 )
            $tr="even";
        else
            $tr="odd";
        $view_history=HtmlInput::history_account($r['poste'], $r['poste'], "",$exercice);
        if ($previous == 1 ) {
            $r['solde_previous']=bcsub($r['solde_deb_previous'],$r['solde_cred_previous']);
        }
	/*
	 * level x
	 */
	foreach (array(3,2,1) as $ind)
	  {
	    if ( ! isset($_GET['lvl'.$ind]))continue;
	    if (${'lvl'.$ind.'_old'} == '')	  ${'lvl'.$ind.'_old'}=mb_substr($r['poste'],0,$ind);
	    if ( ${'lvl'.$ind.'_old'} != mb_substr($r['poste'],0,$ind))
	      {

		echo '<tr class="highlight">';
		echo td(${'lvl'.$ind.'_old'},'style="font-weight:bold;"');
		echo td(${'lvl'.$ind.'_old'}." "._("Total niveau")." ".$ind,'style="font-weight:bold;"');
                
                // compare with previous exercice
                if ($previous==1) {
                    echo td(nbm(${'lvl'.$ind}['sum_deb_previous']),'class="previous_year" style="font-weight:bold;"');
                    echo td(nbm(${'lvl'.$ind}['sum_cred_previous']),' class="previous_year" style="font-weight:bold;" ');
                    $delta_previous=${'lvl'.$ind}['solde_previous'];
                    $side_previous=($delta_previous > 0 ) ? "D":"C";
                    echo td(nbm(abs($delta_previous))." $side_previous",'class="previous_year"  style="text-align:right;font-weight:bold;"  ');
                    
                }
                
                // Ouverture
                $solde3=bcsub(${'lvl'.$ind}['sum_deb_ope'],${'lvl'.$ind}['sum_cred_ope']);
                $side3=($solde3<0)?" C":" D";
                $side3=($solde3==0)?" ":$side3;
                echo td(nbm(abs($solde3)).$side3,'style="text-align:right;font-weight:bold;"');
                
                // Saldo debit
                $solde_deb=bcsub(${'lvl'.$ind}['sum_deb'],${'lvl'.$ind}['sum_deb_ope']);
		echo td(nbm($solde_deb),'style="text-align:right;font-weight:bold;"');
                
                // Saldo cred
                $solde_cred=bcsub(${'lvl'.$ind}['sum_cred'],${'lvl'.$ind}['sum_cred_ope']);
		echo td(nbm($solde_cred),'style="text-align:right;font-weight:bold;"');
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
       // For the Total row , there is no accounting
        if ( $r['poste'] == "") {
            $tr="highlight";
        }

        $summary_tab=$bal->summary_add($summary_tab,$r['poste'],$r['sum_deb'],$r['sum_cred']);
        
        echo '<TR class="'.$tr.'">';
        echo td($view_history);
        echo td(h($r['label']));
      
        if ($previous == 1 ) {
            echo td(nbm($r['sum_deb_previous']),' class="previous_year"');
            echo td(nbm($r['sum_cred_previous']),' class="previous_year" ');
            $solde_previous=bcsub($r['solde_deb_previous'],$r['solde_cred_previous']);
            $side=($solde_previous<0)?"D":"C";
            $side=($solde_previous==0)?"":$side;
            $r['solde_previous']=$solde_previous;
            echo td(nbm(abs($solde_previous))." ".$side,' class="previous_year"');
            
             $summary_prev_tab=$bal->summary_add($summary_prev_tab,
                                                $r['poste'],
                                                $r['sum_deb_previous'],
                                                $r['sum_cred_previous']);

        }
        $solde=bcsub($r['sum_deb_ope'],$r['sum_cred_ope']);
        $side=($solde < 0)?" C":" D";
        $side=($solde==0)?"":$side;
        echo td(nbm(abs($solde)).$side,'style="text-align:right;"');
        echo td(nbm(bcsub($r['sum_deb'],$r['sum_deb_ope'])),'style="text-align:right;"');
	echo td(nbm(bcsub($r['sum_cred'],$r['sum_cred_ope'])),'style="text-align:right;"');
        
        $solde2=bcsub($r['sum_deb'],$r['sum_cred']);
        $side=($solde2 < 0)?" C":" D";
        $side=($solde2==0)?"":$side;
	
        echo td(nbm(abs($solde2)).$side,'style="text-align:right;"');
	
        if ( isset($_GET['lvl1']) || isset($_GET['lvl2']) || isset($_GET['lvl3']))             echo '<td></td>';
        echo '</TR>';
        
    }
    echo '</table>';
    // display the summary
    if ($is_summary==1) {
        if ( $previous == 1) {
            echo '<div style="float:left;margin-right:50px">';
            echo '<h2>';
            echo _("Résumé Exercice précédent");
            echo '</h2>';
            $bal->summary_display($summary_prev_tab);
            echo "</div>";
        }
        echo '<div style="float:left">';
        echo '<h2>';
        echo _("Résumé Exercice courant");
        echo '</h2>';
        $bal->summary_display($summary_tab);
        echo "</div>";
    }
}// end submit
echo "</div>";
?>
