<?php

/*
 *   This file is part of PhpCompta.
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
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require 'class_balance_age.php';
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="balance_age.csv"',FALSE);
/**
 * @file
 * @brief Export an ageing balance
 * @param p_date_start considered writing after this date
 * @param p_let lettered or not value (all): let or  only unlettered value:unlet
 * @param p_type
 *  - C customer
 *  - F supplier
 *  - U only a card 
 *  - X a category 
 * @param cat only if p_type = X it that case contains the category id (fiche_def.fd_id)
 * @param fiche only if p_type = U in that case contains the card id (fiche.f_id)
 * @param all Ony with p_type = X all the customer / supplier cards
 */
/*
 * Retrieve card
 */
$bal=new Balance_Age($cn);
$p_type = HtmlInput::default_value_get('p_type', "-");
$p_date= HtmlInput::default_value_get('p_date_start', "-");
$p_let= HtmlInput::default_value_get('p_let', "let");
$cat= HtmlInput::default_value_get('cat', "");
$fiche= HtmlInput::default_value_get('fiche', "0");
$all= HtmlInput::default_value_get('all', "0");
switch ($p_type)
{
    case 'C':
        $bal->get_array_card('C');
        $bal->export_csv($p_date, $p_let);
        break;
    case 'F':
        $bal->get_array_card('F');
        $bal->export_csv($p_date, $p_let);
        break;
    case 'U':
        $bal->get_array_card('U', $fiche);
        $bal->export_csv($p_date, $p_let);
        break;
    case 'X':
        $all=HtmlInput::default_value_get('all', 0);
        if ($all==0)
        {
            $bal->get_array_card('X', $_GET['cat']);
            $bal->export_csv($p_date, $p_let);
        }
        else
        {
            $a_cat=$cn->get_array("select fd_id from vw_fiche_def where ad_id=".ATTR_DEF_ACCOUNT." order by fd_label asc");
            $nb_cat=count($a_cat);
            for ($i=0; $i<$nb_cat; $i++)
            {
                $bal->get_array_card('X', $a_cat[$i]['fd_id']);
                $bal->export_csv($p_date, $p_let);
            }
        }
        break;

    default:
        break;
}
?>
