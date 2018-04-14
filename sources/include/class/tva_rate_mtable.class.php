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

/**
 * @file
 * @brief Configure the tva : code , rate, label ...
 */
require_once NOALYSS_INCLUDE."/lib/manage_table_sql.class.php";
require_once NOALYSS_INCLUDE."/lib/icon_action.class.php";
require_once NOALYSS_INCLUDE."/database/v_tva_rate_sql.class.php";
require_once NOALYSS_INCLUDE."/database/tva_rate_sql.class.php";

/**
 * @class
 * @brief Configure the tva : code , rate, label ...
 * When using Manage_Table_SQL
 */
class Tva_Rate_MTable extends Manage_Table_SQL
{

    /**
     * 
     * @param V_Tva_rate_SQL $p_table
     * @example tva_parameter.php
     */
    function __construct(V_Tva_rate_SQL $p_table)
    {
        parent::__construct($p_table);
        $this->set_col_label("tva_id", _("id"));
        $this->set_col_label("tva_label", _("label"));
        $this->set_col_label("tva_rate", _("taux"));
        $this->set_col_label("tva_comment", _("Description"));
        $this->set_col_label("tva_both_side", _("Autoliquidation"));
        $this->set_col_label("tva_sale", _("TVA Vente (C)"));
        $this->set_col_label("tva_purchase", _("TVA Achat (D)"));
        $this->set_col_type("tva_both_side", "select",
                array(
            ["value"=>0, "label"=>_("Normal")],
            ["value"=>1, "label"=>_("Autoliquidation")]
        ));
        $this->set_property_updatable("tva_id", FALSE);
        $this->a_info=["tva_purchase"=>44,"tva_both_side"=>43,"tva_sale"=>45];
    }

    /**
     * @brief display into a dialog box the datarow in order 
     * to be appended or modified. Can be override if you need
     * a more complex form
     */
    function input()
    {
        $nb_order=count($this->a_order);
        echo "<table>";
        for ($i=0; $i<$nb_order; $i++)
        {
            echo "<tr>";
            $key=$this->a_order[$i];
            $label=$this->a_label_displaid[$key];
            $value=$this->table->get($key);
            $error=$this->get_error($key);
            $error=($error=="")?"":HtmlInput::errorbulle($error);
            if ($this->get_property_visible($key)===TRUE)
            {
                $info="";
                if ( isset($this->a_info[$key])) {
                    $info=Icon_Action::infobulle($this->a_info[$key]);
                }
                // Label
                echo "<td> {$label} {$info} {$error}</td>";

                if ($this->get_property_updatable($key)==TRUE)
                {
                    echo "<td>";
                    if ($this->a_type[$key]=="select")
                    {
                        $select=new ISelect($key);
                        $select->value=$this->a_select[$key];
                        $select->selected=$value;
                        echo $select->input();
                    }
                    elseif ($key=="tva_rate")
                    {
                        $text=new INum($key);
                        $text->value=$value;
                        $text->prec=4;
                        $min_size=(strlen($value)<10)?10:strlen($value)+1;
                        $text->size=$min_size;
                        echo $text->input();
                    }
                    elseif ($key=='tva_purchase')
                    {
                        $text=new IPoste("tva_purchase");
                        $text->value=$value;
                        $min_size=10;
                        $text->size=$min_size;
                        $text->set_attribute('gDossier', Dossier::id());
                        $text->set_attribute('jrn', 0);
                        $text->set_attribute('account', 'tva_purchase');
                        echo $text->input();
                        //@todo ajout infobulle pour prévenir que compte doit exister 
                    }
                    elseif ($key=='tva_sale')
                    {
                        $text=new IPoste("tva_sale");
                        $text->value=$value;
                        $min_size=10;
                        $text->set_attribute('gDossier', Dossier::id());
                        $text->set_attribute('jrn', 0);
                        $text->set_attribute('account', 'tva_sale');
                        $text->size=$min_size;
                        echo $text->input();
                    }
                    elseif ($this->a_type[$key]=="text")
                    {
                        $text=new IText($key);
                        $text->value=$value;
                        $min_size=(strlen($value)<30)?30:strlen($value)+5;
                        $text->size=$min_size;
                        echo $text->input();
                    }
                    echo "</td>";
                }
                else
                {
                    printf('<td>%s %s</td>', h($value),
                            HtmlInput::hidden($key, $value)
                    );
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    /**
     * @brief save the data in TVA_RATE
     * if tva_both_side is 1 and tva_purchase or tva_sale is empty then
        it is equal to the other value
     * 
     */
    function save()
    {
        $cn=Dossier::connect();
        // if tva_both_side is 1 and tva_purchase or tva_sale is empty then
        // it is equal to the other value
        if ($this->table->tva_both_side==1)
        {
            if ($this->table->tva_purchase=="#"||trim($this->table->tva_purchase)
                    =="#")
            {
                $this->table->tva_purchase=$this->table->tva_sale;
            }
            if ($this->table->tva_sale=="#"||trim($this->table->tva_sale)=="#")
            {
                $this->table->tva_sale=$this->table->tva_purchase;
            }
        }
        $tva_rate=new Tva_rate_SQL($cn, $this->table->tva_id);
        $tva_rate->setp("tva_rate", $this->table->tva_rate);
        $tva_rate->setp("tva_label", $this->table->tva_label);
        $tva_rate->setp("tva_comment", $this->table->tva_comment);
        $tva_rate->setp("tva_both_side", $this->table->tva_both_side);

        // TVA accounting must be joined and separated with a comma
        $tva_purchase=(trim($this->table->tva_purchase)=="")?"#":$this->table->tva_purchase;
        $tva_sale=(trim($this->table->tva_sale)=="")?"#":$this->table->tva_sale;
        $tva_rate->setp("tva_poste", $tva_purchase.",".$tva_sale);
        $tva_rate->save();
        
        // reload the row
        $this->table->set_pk_value($tva_rate->tva_id);
        $this->table->load();
    }
    /**
     * Check data are valid 
     *   1. tva_rate between 0 & 1
     *   2. label is uniq
     *   3. accounting must exist
     * @return boolean
     */
    function check()
    {
        $cn=Dossier::connect();

        // both accounting can not be empty
        if (trim($this->table->tva_purchase)==""&&trim($this->table->tva_sale)=="")
        {
            $this->set_error("tva_purchase",
                    _("Les 2 postes comptables ne peuvent être nuls"));
            $this->set_error("tva_sale",
                    _("Les 2 postes comptables ne peuvent être nuls"));
        }

        // Check the tva rate
        if (trim($this->table->tva_rate)==""||isNumber($this->table->tva_rate)==0||$this->table->tva_rate>1)
        {
            $this->set_error("tva_rate", _("Taux de TVA invalide"));
        }

        //Check the label must be unique
        $count=$cn->get_value("select count(*) from tva_rate where tva_id<>$1 and lower(tva_label)=lower($2)",
                [$this->table->tva_id, $this->table->tva_label]);
        if ($count>0)
        {
            $this->set_error("tva_label", _("Ce nom est déjà utilisé"));
        }

        // Check accounting exists for purchase
        if (trim($this->table->tva_purchase)!=""&&$this->table->tva_purchase!="#")
        {
            $count=$cn->get_value("select count(*) from tmp_pcmn where pcm_val = $1",
                    [$this->table->tva_purchase]);
            if ($count==0)
            {
                $this->set_error("tva_purchase", _("Poste comptable inexistant"));
            }
        }
        // Check accounting exists for sale
        if (trim($this->table->tva_sale)!=""&&$this->table->tva_sale!="#")
        {
            $count=$cn->get_value("select count(*) from tmp_pcmn where pcm_val = $1",
                    [$this->table->tva_sale]);
            if ($count==0)
            {
                $this->set_error("tva_sale", _("Poste comptable inexistant"));
            }
        }

        // check if tva_both_side is valid
        if ($this->table->tva_both_side!=0&&$this->table->tva_both_side!=1)
        {
            $this->set_error("tva_both_side", _("Choix incorrect"));
        }


        if ($this->count_error()!=0)
            return false;
        return true;
    }
    /**
     * delete if not used anywhere
     */
    function delete()
    {
        $cn=Dossier::connect();
        $count_purchase=$cn->get_value("select count(*) from quant_purchase where qp_vat_code = $1",[$this->table->tva_id]);
        $count_sale=$cn->get_value("select count(*) from quant_sold where qs_vat_code = $1",[$this->table->tva_id]);
        if ( $count_purchase > 0 || $count_sale > 0) {
            throw new Exception(_("Effacement interdit : TVA utilisée"));
        }
        
        // Forbid to remove all tva 
        $count=$cn->get_value("select count(*) from tva_rate");
        if ( $count < 2) {
            throw new Exception(_("Vous ne pouvez pas effacer tous les taux. Si votre société n'utilise pas la TVA, changer dans le menu société"));
        }
        
    }

}
