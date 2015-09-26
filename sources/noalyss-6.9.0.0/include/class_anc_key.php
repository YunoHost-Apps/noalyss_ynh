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

// Copyright (2014) Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief Class to manage distribution keys for Analytic accountancy
 * 
 */
require_once NOALYSS_INCLUDE.'/class_anc_key_sql.php';

class Anc_Key
{

    private $key; /* !  the distribution key */
    /**
     * Return the number of keys available.
     *  Return the number of keys available for the ledger given in parameter
     * 
     * @global $cn database connection
     * @param $p_jrn number of the ledger (jrn_def.jrn_def_id
     * @return number of available keys
     */
    static function key_avaiable($p_jrn)
    {
        global $cn;
        $count=$cn->get_value (' select count(*) 
            from key_distribution_ledger 
            join key_distribution using (kd_id)
            where
            jrn_def_id=$1', array($p_jrn));
        return $count;
    }
    function __construct($p_id=-1)
    {
        global $cn;
        $this->key=new Anc_Key_SQL($cn, $p_id);
        $this->a_ledger=null;
        $this->a_activity=null;
        $this->a_row=null;
    }

    /**
     * @brief display list of available keys
     * @param $p_amount   amount to distribute
     * @param $p_target   target to update
     * @param $p_ledger   is the jrn_def_id
     */
    static function display_choice($p_amount, $p_target,$p_ledger)
    {
        global $cn;
        $a_key=$cn->get_array(' select kd_id,
                kd_name,
                kd_description
                from
                key_distribution
                join key_distribution_ledger using (kd_id)
                where
                jrn_def_id=$1',
                array(
                    $p_ledger
                ));
        if (empty($a_key))
        {
            echo _('Aucune clef disponible');
            echo _('Allez dans ANCKEY pour en ajouter pour ce journal');
        }
        include 'template/anc_key_display_choice.php';
    }

    /**
     * @brief display a  list of keys, choose one to modify it
     * 
     */
    static function display_list()
    {
        global $cn;
        $a_key=$cn->get_array('select b.kd_id,b.kd_name,b.kd_description,
                (select sum(ke_percent) from key_distribution_detail as a where a.kd_id=b.kd_id) as distrib 
                from key_distribution as b order by b.kd_name');
        if (empty($a_key))
        {
            echo _('Aucune clef disponible');
        }
        include 'template/anc_key_display_list.php';
    }

    /**
     * @brief Show the detail for a key distribution and let you change it
     * for adding or update
     */
    function input()
    {
        global $cn;

        $plan=$cn->get_array('
     select 
     pa_id,
     pa_name , 
     pa_description 
     from 
       plan_analytique 
     order by pa_name');
        $count_max=count($plan);

        $a_row=$cn->get_array('select ke_id,ke_row,ke_percent from key_distribution_detail 
         where
         kd_id=$1 order by ke_row', array($this->key->getp('id')));

        require_once NOALYSS_INCLUDE.'/template/anc_key_input.php';
    }

    /**
     * @brief verify that data are ok
     * @param type $p_array
     */
    function verify($p_array)
    {
        $a_percent=$p_array['percent'];
        if (count($a_percent)==0)
        {
            throw new Exception(_('Aucune répartition'));
        }
        $tot_percent=0;
        bcscale(4);
        for ($i=0; $i<count($a_percent); $i++)
        {
            $tot_percent=bcadd($tot_percent, $a_percent[$i]);
        }
        if ($tot_percent >100)
        {
            throw new Exception(_('Le total ne vaut pas 100, total calculé = ').$tot_percent);
        }
        if ($p_array['name_key']=='') {
            throw new Exception (_('Le nom ne peut être vide'));
        }
    }

    /**
     * @brief save the data of a repartition key.
     * @param received an array
     * index :
     *   - key_id : key_distribution.kd_id
     *   - row : array of key_distribution.ke_id (row
     *   - pa : array of plan_analytic.pa_id (column)
     *   - po_id : double array, 
     *              first index is the row
     *              second  index is the first plan, the second the second plan...(column)
     *   - percent array, one per row
     *   - jrn : array of available ledgers
     * @note  if po_id == -1 then it is replaced by null, this why the pa_id is needed : to identify
     *  the column
     * @verbatim
     
        'key_id' => string '1' (length=1)
        'row' => 
          array
            0 => string '1' (length=1)
            1 => string '2' (length=1)
            2 => string '3' (length=1)
        'pa' => 
          array
            0 => string '1' (length=1)
            1 => string '2' (length=1)
        'po_id' => 
          array
            0 => 
              array
                0 => string '1' (length=1)
                1 => string '8' (length=1)
            1 => 
              array
                0 => string '2' (length=1)
                1 => string '-1' (length=2)
            2 => 
              array
                0 => string '3' (length=1)
                1 => string '8' (length=1)
        'percent' => 
          array
            0 => string '50.0000' (length=7)
            1 => string '20.0000' (length=7)
            2 => string '30.0000' (length=7)
        'jrn' => 
          array
            0 => string '3' (length=1)
            1 => string '2' (length=1)
      @endverbatim
     * 
     */
    function save($p_array)
    {
        global $cn;
        $this->verify($p_array);
        $cn->start();
        // for each row
        $a_row=$p_array['row'];
        $a_ledger=HtmlInput::default_value("jrn",array(),$p_array);
        $a_percent=$p_array['percent'];
        $a_po_id=$p_array['po_id'];
        $a_plan=$p_array['pa'];
        try
        {
            $this->key->setp('name',$p_array['name_key']);
            $this->key->setp('description',$p_array['description_key']);
            $this->key->save();
            for ($i=0; $i<count($a_row); $i++)
            {
                //save key_distribution_row
                $key_row=new Anc_Key_Detail_SQL($cn);
                $key_row->setp('id', $a_row[$i]);
                $key_row->setp('key', $this->key->getp('id'));
                $key_row->setp('row', $i+1);
                $key_row->setp('percent', $a_percent[$i]);
                $key_row->save();
                //
                // Save each activity + percent
                $cn->exec_sql('delete from key_distribution_activity where ke_id=$1', array($key_row->getp('id')));

                // Don't save row with 0 %
                if ($a_percent[$i]==0)
                {
                    $key_row->delete();
                    continue;
                }
                for ($j=0; $j<count($a_po_id[$i]); $j++)
                {
                    $activity=new Anc_Key_Activity_SQL($cn);
                    $activity->setp('detail', $key_row->ke_id);
                    $value=($a_po_id[$i][$j]==-1)?null:$a_po_id[$i][$j];
                    $activity->setp('activity', $value);
                    $activity->setp('plan',$a_plan[$j]);
                    $activity->save();
                }
            }
            // delete all from key_distribution_ledger
            $cn->exec_sql('delete from key_distribution_ledger where kd_id=$1', array($this->key->getp('id')));
            for ($k=0; $k<count($a_ledger); $k++)
            {
                $ledger=new Anc_Key_Ledger_SQL($cn);
                $ledger->kd_id=$this->key->getp('id');
                $ledger->jrn_def_id=$a_ledger[$k];
                $ledger->save();
            }
            
            $cn->commit();
        }
        catch (Exception $e)
        {
            if ( DEBUG ) { echo $e->getTraceAsString(); var_dump($_POST);} else { echo _('erreur');}
            $cn->rollback();
        }
    }
    /**
     * @brief Call the Anc_Operation::display_form_plan with the right amounts.
     * This function compute the array and amount to pass to the Anc_Operation::display_form_plan
     * and replace the current table of activity with the value computed from the key.
     * 
     * @global $cn database connection
     * @param $p_target Table to be replaced
     * @param $p_amount amount to distribute among activities
     */
    function fill_table($p_target,$p_amount)
    {
        global $cn;
        /* number is the index of the plan, he's computed from p_target */
        $number=preg_replace('/det[0-9]/', '', $p_target);
        $number=str_replace('t', '', $number);
        $number=str_replace('popup', '', $number);
        
        $op[$number]=$p_amount;
        $array['op']=$op;
        $a_plan=$cn->get_array('select pa_id from plan_analytique order by pa_id');
        for ($i=0;$i < count($a_plan);$i++)
        {
            $array['pa_id'][$i]=$a_plan[$i]['pa_id'];
        }
        
        $a_poste=$cn->get_array('select po_id,ke_percent,pa_id,ke_row
                 from key_distribution_activity 
                 join key_distribution_detail using (ke_id)  
                 where
                 kd_id=$1
                 order by ke_row,pa_id',
                 array($this->key->getp('id')));

        for ($i=0;$i< count($a_poste);$i++)
        {
            $hplan[$number][$i]=($a_poste[$i]['po_id']==null)?-1:$a_poste[$i]['po_id'];
        }
        $array['hplan']=$hplan;
        
         $a_amount=$cn->get_array("select distinct ke_row,ke_percent 
                 from key_distribution_activity 
                 join key_distribution_detail using (ke_id) 
                 where
                    kd_id=$1
                    and pa_id=$2
                 order by ke_row",
                 array($this->key->getp('id'),$a_plan[0]['pa_id']));
         bcscale(2);
        for ($i=0;$i< count($a_amount);$i++)
        {
            $val[$number][$i]=bcmul($p_amount,$a_amount[$i]['ke_percent'])/100;
        }
        $array['val']=$val;
               
        $anc_operation=new Anc_Operation($cn);
        echo $anc_operation->display_form_plan($array, 1, 1, $number, $p_amount,'',false);
        
    }
    /**
     *@brief show a form for adding a key + button to display it
     * 
     */
    static function key_add()
    {
        $key=new Anc_Key();
        $key->key->setp('name',_('Nouvelle clef'));
        $key->key->setp('description',_('Description de la nouvelle clef'));
        ?>
<input type="button" class="smallbutton" value="<?php echo  _('Ajout')?>" onclick="$('key_add_div_id').show()">
<div id="key_add_div_id" style="display: none">
<?php
        $key->input();
        echo '</div>';
        
    }
    /**
     *@brief delete the distribution key 
     */
    function delete ()
    {
        $this->key->delete();
    }
}
