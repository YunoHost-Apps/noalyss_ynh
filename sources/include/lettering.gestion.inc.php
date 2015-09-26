<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_lettering.php';
global $g_user;
echo '<div class="content">';

echo '<div id="search">';
echo '<FORM METHOD="GET">';
echo dossier::hidden();
echo HtmlInput::hidden('ac',$_REQUEST['ac']);
echo HtmlInput::hidden('sb',$_REQUEST['sb']);
echo HtmlInput::hidden('sc',$_REQUEST['sc']);
echo HtmlInput::hidden('f_id',$_REQUEST['f_id']);

echo '<table width="50%">';

// limit of the year
$exercice=$g_user->get_exercice();
$periode=new Periode($cn);
list($first_per,$last_per)=$periode->get_limit($exercice);

$start=new IDate('start');
$start->value=(isset($_GET['start']))?$_GET['start']:$first_per->first_day();
$r=td(_('Date début'));
$r.=td($start->input());
echo tr($r);

$end=new IDate('end');
$end->value=(isset($_GET['end']))?$_GET['end']:$last_per->last_day();
$r=td(_('Date fin'));
$r.=td($end->input());
echo tr($r);

// type of lettering : all, lettered, not lettered
$sel=new ISelect('type_let');
$sel->value=array(
                array('value'=>0,'label'=>_('Toutes opérations')),
                array('value'=>1,'label'=>_('Opérations lettrées')),
                array('value'=>2,'label'=>_('Opérations NON lettrées'))
            );
if (isset($_GET['type_let'])) $sel->selected=$_GET['type_let'];
else $sel->selected=1;

$r= td("Filtre ").
    td($sel->input());

echo tr($r);
echo '</table>';
echo '<br>';
echo HtmlInput::submit("seek",_('Recherche'));
echo '</FORM>';
echo '</div>';
//if (! isset($_REQUEST['seek'])) exit;
echo '<hr>';
//--------------------------------------------------------------------------------
// record the data
//--------------------------------------------------------------------------------
if ( isset($_POST['record']))
{
    $letter=new Lettering_Account($cn);
    $letter->save($_POST);
}
//--------------------------------------------------------------------------------
// Show the result
//--------------------------------------------------------------------------------
if ( isset($_GET['start']) && isset($_GET['end']))
  {
    if ( isDate($_GET['start']) == null || isDate($_GET['end']) == null )
      {
	echo alert(_('Date malformée, désolé'));
	return;
      }
  }
echo '<div id="list">';
$fiche=new Fiche($cn,$_REQUEST['f_id']);
$quick_code=$fiche->get_quick_code();
$letter=new Lettering_Card($cn);
$letter->set_parameter('quick_code',$quick_code);
$letter->set_parameter('start',$start->value);
$letter->set_parameter('end',$end->value);

if ( $sel->selected == 0 )
    echo $letter->show_list('all');
if ( $sel->selected == 1 )
    echo $letter->show_list('letter');
if ( $sel->selected == 2 )
    echo $letter->show_list('unletter');

echo '</div>';
echo '<div id="detail" style="display:none">';
echo 'Un instant...';
echo '<IMG SRC=image/loading.gif>';
echo '</div>';
echo '</div>';
?>
