<?php
/*
  Copyright (C) 2014 danydb@aevalys.eu

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 */

/*
 * all the pa_id and analytic plan
 */
?>
<form method="post" action="do.php" style="display:inline">
    <?php
    echo HtmlInput::request_to_hidden(array('gDossier', 'ac'));
    echo HtmlInput::hidden('op','consult');
    echo HtmlInput::hidden('key', $this->key->getp('id'));
    $name=HtmlInput::default_value_post("name_key",$this->key->getp('name'));
    $description_text=HtmlInput::default_value_post("description_key",$this->key->getp('description'));
    ?>
    <div class="content">
        <div style="width:30%;display:inline-block;min-height: 75px">
                    <?php
                        $name=new IText('name_key',$name);
                        echo $name->input();
                    ?>
        </div>
        <div style="width: 65%;display:inline-block;min-height: 75px">
                <?php
                        $description=new IText('description_key',$description_text);
                        $description->css_size='70%';
                        echo $description->input();
                    ?>
        </div>
        <h2>
            <?php echo _('Répartition'); ?>
        </h2>
        <table id="key_account_tb" class="result" style="margin-left: 8%;width:84%;margin-right:8%">
            <tr>
                <th><?php echo _('n°'); ?></th>
                <?php
                // Show all the possible analytic plan
                for ($i=0; $i<count($plan); $i++):
                    ?>
                    <th>
                        <?php echo $plan[$i]['pa_name']; ?>
                    </th>
                    <?php
                endfor;
                ?>
                    <th style="text-align: right">
                    <?php echo _('Pourcentage'); ?>
                    <?php echo HtmlInput::infobulle(41); ?>
                </th>
            </tr>
            <?php
            $count_row=count($a_row);
            if ($count_row == 0 ) {
                $a_row [0]['ke_row']=1;
                $a_row [0]['ke_percent']=0;
                $a_row [0]['ke_id']=-1;
                
            }
            $tot_key=0;
            for ($j=0; $j<count($a_row); $j++):
                ?>
                <tr>
                    <td>
                    <?php echo $a_row[$j]['ke_row']; ?>
                        <?php echo HtmlInput::hidden('row[]', $a_row[$j]['ke_id']); ?>
                    </td>
                        <?php
                         $percent=$a_row[$j]['ke_percent'];
                         $tot_key=bcadd($tot_key,$percent);
                        // For each plan
                        for ($i=0; $i<count($plan); $i++):
                            if ( $j == 0 ) {
                                echo HtmlInput::hidden('pa[]',$plan[$i]['pa_id']);
                            }
                            $a_poste=$cn->make_array("select po_id,po_name from poste_analytique where pa_id=$1", 1, array($plan[$i]['pa_id']));
                            $select=new ISelect('po_id['.$j.'][]');
                            $select->value=$a_poste;
                            $value=$cn->get_array('select po_id,ke_percent 
                                from key_distribution_activity as ka
                                join key_distribution_detail using (ke_id) 
                                join key_distribution using (kd_id) 
                                left join poste_analytique using(po_id)
                                
                        where ke_id=$1 and ka.pa_id=$2 ', array($a_row[$j]['ke_id'],$plan[$i]['pa_id']));
                            $selected=-1;
                            if (sizeof($value)==1)
                            {
                                $selected=$value[0]['po_id'];
                               
                            }
                            if (isset($_POST['po_id']))
                            {
                                $a_po_id=HtmlInput::default_value_post('po_id', array());
                                $selected=$a_po_id[$j][$i];
                                $a_percent=HtmlInput::default_value_post('percent', array());
                                $percent=$a_percent[$j];
                            }
                            $select->selected=$selected;
                            ?>
                        <td>
                            <?php
                            echo $select->input();
                            ?>                
                        </td>
                        <?php
                    endfor;
                    ?>
                        <td class="num">
                        <?php
                        $inum_percent=new INum('percent[]');
                        $inum_percent->javascript=' onchange="format_number(this,2);anc_key_compute_table();"';
                        $inum_percent->value=sprintf("%.2f",$percent);
                        echo $inum_percent->input();
                        ?>
                    </td>
                </tr>
                <?php
            endfor;
            ?>
                <tfoot style="font-weight: bolder">
                    <tr>
                        <td style="width: auto" colspan="<?php echo count($plan)+1;?>">
                            <?php echo _('Total')?>
                        </td>
                        <td  class="num">
                            <span id="total_key"><?php echo nb($tot_key);?></span>%
                        </td>
                    </tr>
                </tfoot>
        </table>
<input type="button" class="smallbutton" value="<?php echo _('Ajout ligne')?>" onclick="add_row_key('key_account_tb');">

        <div>
            <div>
                <h2>
                    <?php echo _("Disponible dans les journaux "); ?>
                </h2>
            </div>
            <div style="margin-left: 8%;width:84%;margin-right:8%">

                <?php 
                if ( $this->key->getp("id") == -1 )
                {
                    // for a new key
                        $jrn=$cn->get_array('select null as kl_id,jrn_def_id,jrn_def_name,jrn_def_description 
                                            from jrn_def 
                                            order by jrn_def_name ');
                }else {
                    // for an existing one
                        $jrn=$cn->get_array('select kl_id,jrn_def_id,jrn_def_name,jrn_def_description 
                                            from jrn_def 
                                            left join key_distribution_ledger using (jrn_def_id)
                                            where kd_id=$1 or kd_id is null
                                            order by jrn_def_name ', array($this->key->getp('id')));
                }
                $post_jrn=HtmlInput::default_value_post("jrn",-1);
                ?>
                <table id="jrn_def_tb" class="result">
                    <?php for ($i=0; $i<count($jrn); $i++): ?>
                        <tr>
                            <td>
                                <?php $checkbox=new ICheckBox("jrn[]"); ?>
                                <?php $checkbox->value=$jrn[$i]['jrn_def_id']; ?>
                                <?php
                                $checkbox->selected=false;
                                if ( ($post_jrn == -1 && $jrn[$i]['kl_id']<>"" ) || (is_array($post_jrn) && in_array($jrn[$i]['jrn_def_id'], $post_jrn)))
                                {
                                    $checkbox->selected=true;
                                }
                                ?>
                                <?php echo $checkbox->input(); ?>
                            </td>
                            <td>
                                <?php echo h($jrn[$i]['jrn_def_name']); ?>
                            </td>
                            <td>
                                <?php echo h($jrn[$i]['jrn_def_description']); ?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>
        </div>

        <!-- end -->
    </div>
    <?php echo HtmlInput::submit('save_key', _('Sauver')); ?>
</form>
<form style="display:inline" action="do.php" id="anc_key_input_frm" method="post">
    <?php
    echo HtmlInput::request_to_hidden(array('gDossier', 'ac'));
    echo HtmlInput::hidden('op','delete_key');
    echo HtmlInput::hidden('key', $this->key->getp('id'));
   if ($this->key->getp('id') != -1) echo HtmlInput::submit('delete_key', _('Effacer'),'onclick="return confirm_box(\'anc_key_input_frm\',\''._('Confirmer effacement?').'\')"'); ?>
</form>