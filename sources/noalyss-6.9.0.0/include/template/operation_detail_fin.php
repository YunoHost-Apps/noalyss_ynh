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
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief show detail of a operation of fin
 *
 */
echo '<table class="result">';
echo '<tr>';
echo th(_('Compte en banque'));
echo th(_('Tiers'));
echo th(_('Libellé'));
echo th(_('Montant'));
echo '</tr>';

echo '<tr>';
$bk = new Fiche($cn, $obj->det->array[0]['qf_bank']);
$view_card_detail = HtmlInput::card_detail($bk->get_quick_code(), h($bk->getName()), ' class="line" ');
echo  td($view_card_detail);
$other = new Fiche($cn, $obj->det->array[0]['qf_other']);
$view_card_detail = HtmlInput::card_detail($other->get_quick_code(), h($other->getName()), ' class="line" ');
echo  td($view_card_detail);
$comment = strip_tags($obj->det->jr_comment);
echo  td($comment);
echo td(nbm($obj->det->array[0]['qf_amount']), ' class="inum"');
echo '</tr>';
echo '</table>';
?>

