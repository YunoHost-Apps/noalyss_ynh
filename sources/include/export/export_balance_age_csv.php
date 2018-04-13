<?php

/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS isfree software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS isdistributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
require 'class/balance_age.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

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
$p_type = $http->get('p_type',"string", "-");
$p_date= $http->get('p_date_start',"string", "-");
$p_let= $http->get('p_let',"string", "let");
$cat= $http->get('cat',"string", "");
$fiche= $http->get('fiche',"string", "0");
$all= $http->get('all',"string", "0");
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
        $all=$http->get('all', "string",0);
        if ($all==0)
        {
            $bal->get_array_card('X', $http->get('cat'));
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
