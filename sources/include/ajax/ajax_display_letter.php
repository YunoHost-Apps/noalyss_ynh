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

// Copyright 2015 Author Dany De Bontridder danydb@aevalys.eu

  /**
   *@file
   *@brief show the lettered operation
   */

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/class/lettering.class.php';
$exercice=$g_user->get_exercice();
if ($g_user->check_module("LETCARD")==0 &&  $g_user->check_module("LETACC")==0)
    exit();
$periode=new Periode($cn);
list($first_per, $last_per)=$periode->get_limit($exercice);

$ret=new IButton('return');
$ret->label=_('Retour');
$ret->javascript="$('detail').hide();$('list').show();$('search').show();";

// retrieve info for the given j_id (date, amount,side and comment)
$sql="select j_date,to_char(j_date,'DD.MM.YYYY') as j_date_fmt,J_POSTE,j_qcode,jr_id,
         jr_comment,j_montant, j_debit,jr_internal from jrnx join jrn on (j_grpt=jr_grpt_id)
         where j_id=$1";
$arow=$cn->get_array($sql, array($j_id));
$row=$arow[0];
$r='';
$r.='<fieldset><legend>'._('Lettrage').'</legend>';
$r.=_('Poste')." ".$row['j_poste'].'  '.$row['j_qcode'].'<br>';

$detail="<A class=\"detail\" style=\"display:inline\" HREF=\"javascript:modifyOperation('".$row['jr_id']."',".$gDossier.")\" > ".$row['jr_internal']."</A>";

$r.=_('Date').' : '.$row['j_date_fmt'].' ref :'.$detail.' <br>  ';
$r.=h($row['jr_comment'])." "._("montant")." : ".($row['j_montant'])." ".(($row['j_debit']=='t')?'D':'C');
$r.='</fieldset>';
$r.='<div id="filtre" style="float:left;display:block">';
$r.='<form method="get" id="search_form" onsubmit="search_letter(this);return false">';
$r.='<div style="float:left;">';
// needed hidden var
$r.=dossier::hidden();
if (isset($_REQUEST['ac']))
    $r.=HtmlInput::hidden('ac', $_REQUEST['ac']);
if (isset($_REQUEST['sa']))
    $r.=HtmlInput::hidden('sa', $_REQUEST['sa']);
if (isset($_REQUEST['acc']))
    $r.=HtmlInput::hidden('acc', $_REQUEST['acc']);
$r.=HtmlInput::hidden('j_id', $j_id);
$r.=HtmlInput::hidden('op', $op);
$r.=HtmlInput::hidden('ot', $ot);

$r.='<table>';
//min amount
$line=td(_('Montant min. '));
$min=new INum('min_amount');
$min->value=(isset($min_amount))?$min_amount:$row['j_montant'];
$min_amount=(isset($min_amount))?$min_amount:$row['j_montant'];

$line.=td($min->input());
// max amount
$line.=td(_('Montant max. '));
$max=new INum('max_amount');
$max->value=(isset($max_amount))?$max_amount:$row['j_montant'];
$max_amount=(isset($max_amount))?$max_amount:$row['j_montant'];
$line.=td($max->input());
$r.=tr($line);

$date_error="";
// start date
$start=new IDate('search_start');

/*  check if date are valid */
if (isset($search_start)&&isDate($search_start)==null)
{
    ob_start();
    alert(_('Date malformée'));
    $date_error=ob_get_contents();
    ob_end_clean();
    $search_start=$first_per->first_day();
}
$start->value=(isset($search_start))?$search_start:$first_per->first_day();

$line=td(_('Date Début')).td($start->input());
// end date
$end=new IDate('search_end');
/*  check if date are valid */
if (isset($search_end)&&isDate($search_end)==null)
{
    ob_start();
    alert(_('Date malformée'));
    $date_error=ob_get_contents();
    ob_end_clean();
    $search_end=$last_per->last_day();
}
$end->value=(isset($search_end))?$search_end:$last_per->last_day();
$line.=td(_('Date Fin')).td($end->input());
$r.=tr($line);
// Side
$line=td(_('Debit / Credit'));
$iside=new ISelect('side');
$iside->value=array(
    array('label'=>_('Debit'), 'value'=>0),
    array('label'=>_('Credit'), 'value'=>1),
    array('label'=>_('Les 2'), 'value'=>3)
);
/**
 *
 * if $side is not then
 * - if jl_id exist and is > 0 show by default all the operation (=3)
 * - if jl_id does not exist or is < 0 then show by default the opposite
 *  side
 */
if (!isset($side))
{
    // find the jl_id of the j_id
    $jl_id=$cn->get_value('select comptaproc.get_letter_jnt($1)', array($j_id));
    if ($jl_id==null)
    {
        // get the other side
        $iside->selected=(isset($side))?$side:(($row['j_debit']=='t')?1:0);
        $side=(isset($side))?$side:(($row['j_debit']=='t')?1:0);
    }
    else
    {
        // show everything
        $iside->selected=3;
        $side=3;
    }
}
else
{
    $iside->selected=$side;
}

$r.=tr($line.td($iside->input()));
$r.='</table>';
$r.='</div>';
$r.='<div style="float:left;padding-left:100">';
$r.=HtmlInput::submit('search', 'Rechercher');
$r.='</div>';
$r.='</form>';
$r.='</div>';

$form='<div id="result" style="float:top;clear:both">';

$form.='<FORM id="letter_form" METHOD="post">';
$form.=dossier::hidden();
if (isset($_REQUEST['p_action']))
    $form.=HtmlInput::hidden('p_action', $_REQUEST['p_action']);
if (isset($_REQUEST['sa']))
    $form.=HtmlInput::hidden('sa', $_REQUEST['sa']);
if (isset($_REQUEST['acc']))
    $form.=HtmlInput::hidden('acc', $_REQUEST['acc']);
if (isset($_REQUEST['sc']))
    $form.=HtmlInput::hidden('sc', $_REQUEST['sc']);
if (isset($_REQUEST['sb']))
    $form.=HtmlInput::hidden('sb', $_REQUEST['sb']);
if (isset($_REQUEST['f_id']))
    $form.=HtmlInput::hidden('f_id', $_REQUEST['f_id']);


// display a list of operation from the other side + box button
if ($ot=='account')
{
    $obj=new Lettering_Account($cn, $row['j_poste']);
    if (isset($search_start))
        $obj->start=$search_start;
    if (isset($search_end))
        $obj->end=$search_end;
    if (isset($max_amount))
        $obj->fil_amount_max=$max_amount;
    if (isset($min_amount))
        $obj->fil_amount_min=$min_amount;
    if (isset($side))
        $obj->fil_deb=$side;

    $form.=$obj->show_letter($j_id);
}
else if ($ot=='card')
{
    $obj=new Lettering_Card($cn, $row['j_qcode']);
    if (isset($search_start))
        $obj->start=$search_start;
    if (isset($search_end))
        $obj->end=$search_end;
    if (isset($max_amount))
        $obj->fil_amount_max=$max_amount;
    if (isset($min_amount))
        $obj->fil_amount_min=$min_amount;
    if (isset($side))
        $obj->fil_deb=$side;
    $form.=$obj->show_letter($j_id);
}
else
{
    $form.=_('Mauvais type objet');
}

$form.=HtmlInput::submit('record', _('Sauver')).$ret->input();
$form.='</FORM>';
$form.='</div>';

$html=$r.$form;
$html.=$date_error;
//       echo $html;exit;
$html=escape_xml($html);

header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>detail</code>
<value>$html</value>
</data>
EOF;
?>