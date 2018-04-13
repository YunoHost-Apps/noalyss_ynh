<?php

/*
 * Copyright (C) 2017 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


/***
 * @file 
 * @brief class Acc_Plan_MTabme
 * @see Acc_Plan_MTabme
 *
 */
require_once NOALYSS_INCLUDE.'/database/acc_plan_sql.class.php';
require_once NOALYSS_INCLUDE.'/lib/manage_table_sql.class.php';
/**
 * @brief this instance extends Manage_Table_SQL and aims to manage 
 * the Table tmp_pcmn thanks a web interface (add , delete, display...)
 * 
 * @see Acc_Plan_SQL
 */
class Acc_Plan_MTable extends Manage_Table_SQL
{
    function __construct(Acc_Plan_SQL $p_table)
    {
        $this->table = $p_table;
        parent::__construct($p_table);
        //--------------------------------------------------------------
        //Set the table header 
        //--------------------------------------------------------------
        $this->set_col_label("pcm_val", _("Poste Comptable"));
        $this->set_col_label("pcm_type", _("Type"));
        $this->set_col_label("pcm_lib", _("Libellé"));
        $this->set_col_label("parent_accounting", _("Dépend"));
        $this->set_col_label("fiche_qcode", _("Fiche"));
        $this->set_col_label("pcm_direct_use", _("Utilisation directe"));
        //--------------------------------------------------------------
        $this->set_property_visible("id", FALSE);
        $this->set_property_updatable("fiche_qcode", FALSE);
        $this->set_col_type("pcm_type", "select", [
            ["label"=>_("Actif"),"value"=>"ACT"],
            ["label"=>_("Actif inversé"),"value"=>"ACTINV"],
            ["label"=>_("Passif"),"value"=>"PAS"],
            ["label"=>_("Passif Inversé"),"value"=>"PASINV"],
            ["label"=>_("Charge"),"value"=>"CHA"],
            ["label"=>_("Charge inversé"),"value"=>"CHAINV"],
            ["label"=>_("Produit"),"value"=>"PRO"],
            ["label"=>_("Produit inversé"),"value"=>"PROINV"],
            ["label"=>_("Contexte"),"value"=>"CON"]
        ]);
        $this->set_col_type("pcm_direct_use", "select",array(["label"=>_("Oui"),"value"=>"Y"],["label"=>"Non","value"=>"N"]));
        $this->a_order=["pcm_val","pcm_lib","parent_accounting","pcm_direct_use","pcm_type","fiche_qcode"];
        $this->set_icon_mod("first");
    }
    /**
     * Display a row
     * @param type $p_row array of value key column=>value
     */
    function display_row($p_row)
    {
         printf('<tr  id="%s_%s">', 
                 $this->object_name,
                $p_row[$this->table->primary_key])
        ;
        
        
        $nb_order=count($this->a_order);
        for ($i=0; $i<$nb_order; $i++)
        {
            $v=$this->a_order[$i];
            $nb=0;
            $cn=Dossier::connect();
            $nb=$cn->get_value("select count(*) from jrnx where j_poste=$1",[$p_row['pcm_val']]);
            $nb+=$cn->get_value("select count(*) from tmp_pcmn where pcm_val_parent=$1",[$p_row['pcm_val']]);
            if ($v=="pcm_val")
            {
                $js=sprintf("onclick=\"%s.input('%s','%s');\"", $this->object_name,
                        $p_row[$this->table->primary_key], $this->object_name);
                echo sprintf('<td sort_type="text" sort_value="X%s">%s',
                        htmlspecialchars($p_row[$v]),
                        HtmlInput::anchor($p_row[$v], "", $js)).'</td>';
            }
            elseif ($v == "fiche_qcode") {
                $count=$this->table->cn->get_value("select count(*) from fiche_detail where ad_id=5 and ad_value=$1",array($p_row['pcm_val']));
               if ($count ==  0) echo td("");
               elseif ($count == 1 ) { echo td($p_row[$v]) ; }
               elseif ($count > 1) { echo td($p_row[$v] . " ($count) ");} 
            }
            elseif ($v=="pcm_lib")
            {
                
                if ( $nb >0){
                    echo "<td>";
                    echo HtmlInput::history_account($p_row['pcm_val'],h($p_row["pcm_lib"]));
                    echo "</td>";
                } else {
                    echo td($p_row[$v]);
                }

            }
            else
            {
                if ( ! $this->get_property_visible($v)) continue;
                echo td($p_row[$v]);
            }
        }
        if ( $nb == 0 ) $this->display_icon_del($p_row);
        else echo td("&nbsp;");


        echo '</tr>';
    }
    /**
     * Check that the entered data are valid before recording them into 
     * tmp_pcmn, the errors are stored into this->a_error and if someting wrong
     * is found it returns false, if the data can be saved it returns true
     * @return return false if an error is found, 
     */
    function check() 
    {
        $cn=Dossier::connect();
        $count=$cn->get_value("select count(*) from tmp_pcmn where pcm_val = $1 and id <> $2",
                    array($this->table->pcm_val,$this->table->id));
        if ($count > 0 ) {
            $this->set_error("pcm_val", _("Poste comptable est unique"));
        }
        if ( trim($this->table->pcm_val) == "") {
            $this->set_error("pcm_val", _("Poste comptable ne peut être vide"));
        }
        // Check size
         if ( strlen(trim($this->table->pcm_val)) > 40) {
            $this->set_error("pcm_val", _("Poste comptable trop long"));
        }
        if ( trim($this->table->parent_accounting) == "") {
            $this->set_error("parent_accounting", _("Poste comptable dépendant ne peut pas être vide"));
        }
        /**
         * Check that the parent accounting does exist
         */
        $exist_parent=$cn->get_value("select count(*) from tmp_pcmn where pcm_val = $1 ",
                    array($this->table->parent_accounting));
        if ($exist_parent == 0) {
            $this->set_error("parent_accounting", _("Compte parent n'existe pas"));
        }
        if ( count($this->aerror) > 0 ) return false;
        return true;
    }
    
   
    
}
