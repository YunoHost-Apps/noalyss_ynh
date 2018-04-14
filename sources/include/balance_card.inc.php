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

/*!\file
 * \brief show the status of a card
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_exercice.php';

global $g_user;
echo '<div class="content" style="width:90%;margin-left:5%">';
$exercice=new Exercice($cn);
$old='';
$fiche=new Fiche($cn,$_GET['f_id']);
$year=$g_user->get_exercice();
if ( $year == 0 )
  {
    $html=_("erreur aucune période par défaut, allez dans préférence pour en choisir une");
  }
else
  {
    $per=new Periode($cn);
    $limit_periode=$per->get_limit($year);
    $array['from_periode']=$limit_periode[0]->first_day();
    $array['to_periode']=$limit_periode[1]->last_day();
    if (isset($_GET['ex']))
      {
	$limit_periode=$per->get_limit($_GET['ex']);
	$array['from_periode']=$limit_periode[0]->first_day();
      }

    /*
     * Add button to select another year
     */
    if ($exercice->count() > 1 )
      {
	$default=(isset($_GET['ex']))?$_GET['ex']:$year;
	$dossier=dossier::id();

	    $old='<form method="get" action="do.php">';
	    $is=$exercice->select('ex',$default,'onchange = "submit(this)"');
	    $old.="Autre exercice ".$is->input();
	    $old.=HtmlInput::hidden('f_id',$_GET['f_id']);
	    $old.=HtmlInput::hidden('ac',$_GET['ac']);
	    $old.=HtmlInput::hidden('sb',$_GET['sb']);
	    $old.=HtmlInput::hidden('sc',$_GET['sc']);
	    $old.=dossier::hidden();
	    $old.='</form>';
      }

    if (   $fiche->HtmlTable($array,0,0)==-1){
      echo h2(_("Aucune opération pour l'exercice courant"),'class="error"');
    }
    echo $old;

  }

echo '</div>';
