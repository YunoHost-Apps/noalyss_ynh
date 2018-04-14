<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Display the history of an analytic account.
 * Receives the parameters GET: 
        - gDossier	integer
        - act            history_anc_account
        - po_id          integer poste_analytic.po_id
        - div           DOM ID of the box 
        - op            history
        - exercice	integer
 */
require_once NOALYSS_INCLUDE."/class/anc_grandlivre.class.php";

try {
    $po_id=$http->get("po_id","number");
    $exercice=$http->get("exercice","number");
    $div=$http->get("div");
} catch (Exception $ex) {
    echo $ex->getTraceAsString();
    throw $ex;
}
$poste_analytic=new Poste_analytique_SQL($cn, $po_id);

$anc_grandlivre=new Anc_GrandLivre($cn);

$anc_grandlivre->from_poste=$poste_analytic->po_name;
$anc_grandlivre->to_poste=$poste_analytic->po_name;

// Find the first and last periode
$periode=new Periode($cn);
$a_periode_limit=$periode->limit_year($exercice);

// Find the first day
$first_day=$periode->first_day($a_periode_limit['start']);

// find the last day
$last_day=$periode->last_day($a_periode_limit['end']);

$anc_grandlivre->from=$first_day;
$anc_grandlivre->to=$last_day;

echo HtmlInput::title_box($poste_analytic->getp("po_name"), $div, "close", "", "n");

echo $anc_grandlivre->display_html(0);
$anc_grandlivre->pa_id=$poste_analytic->pa_id;
echo $anc_grandlivre->show_button();