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

// Copyright Author Dany De Bontridder dany@alchimerys.be
//@description: Developpement for ANC

require_once NOALYSS_INCLUDE . "/class/anc_operation.class.php";

$anc = new Anc_Operation($cn);

$a_jrnxId = $cn->get_array(
        "select j_id from jrnx join jrn on (jr_grpt_id=j_grpt)
            where
            jr_id=$1
            ", array(48));
$nb = count($a_jrnxId);

for ($i = 0; $i<$nb; $i++) {
// First row 
    $a = $anc->get_by_jid($a_jrnxId[$i]['j_id']);
    if (! empty($a) ) {
        $complete=$anc->to_request($a, 1);
        var_dump($complete);
    }
}
