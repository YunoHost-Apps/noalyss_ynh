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

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief display Financial operation , simple and detailled
 */
?>
<table class="result">
    <tr>
        <th><?= _('Banque') ?></th>
        <th><?= _("Tiers") ?></th>
        <th><?= _("Libellé") ?></th>
        <th><?= _("Montant") ?></th>
        <th><?= _("Opérations rapprochées") ?></th>
    </tr>
    <?php
    $nb_data=count($this->data);
    $tot_amount=0;
    for ($i=0; $i<$nb_data; $i++):
        $class=($i%2==0)?' class="even" ':' class="odd" ';
        ?>
        <tr <?= $class ?> >
            <td>
                <?= $this->data[$i]['bk_qcode']; ?>
            </td>
            <td>
                <?php
                echo HtmlInput::history_card($this->data[$i]['tiers_f_id'],
                        h($this->data[$i]['tiers_first_name'].$this->data[$i]['tiers_name']."[{$this->data[$i]['tiers_qcode']}]"));
                ?>
            </td>
            <td>
                <?= h($this->data[$i]['jr_comment']) ?>
            </td>
            <td>
                <?= nbm($this->data[$i]['qf_amount']) ?>
            </td>
            <td>
                <?php
                $ret_reconcile=$this->db->execute('reconcile_date',
                        array($this->data[$i]['jr_id']));
                $max=Database::num_row($ret_reconcile);
                if ($max>0)
                {
                    $sep="";
                    for ($e=0; $e<$max; $e++)
                    {
                        $row=Database::fetch_array($ret_reconcile, $e);
                        echo $sep.HtmlInput::detail_op($row['jr_id'],
                                $row['jr_date'].' '.$row['jr_internal']);
                        $sep=' ,';
                    }
                }
                ?>
            </td>

    <?php
endfor;
?>
</table>