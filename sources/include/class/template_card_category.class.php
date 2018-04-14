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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

require_once NOALYSS_INCLUDE.'/lib/manage_table_sql.class.php';
require_once NOALYSS_INCLUDE.'/database/fiche_def_ref_sql.class.php';

/**
 * @file
 * @brief  Manage the template of card category 
 */

/**
 * @class
 * @brief Manage the template of card category
 */
class Template_Card_Category extends Manage_Table_SQL
{

    function __construct(Fiche_def_ref_SQL $p_table)
    {
        $this->table=$p_table;
        parent::__construct($p_table);
        // Label of the columns
        $this->set_col_label("frd_text", _("Nom"));
        $this->set_col_label("frd_class_base", _("Poste comptable de base"));
        $this->set_col_label("frd_id", _("ID"));
        // Cannot update frd_id
        $this->set_property_updatable("frd_id", FALSE);
        $this->a_order=["frd_id", "frd_text", "frd_class_base"];
    }

    function delete()
    {
        $cn=Dossier::connect();

        if ($cn->get_value("select count(*) from fiche_def where frd_id=$1",
                        [$this->table->frd_id])>0)
        {
            throw new Exception(_("Effacement impossible : catégorie utilisée"));
        }
        $cn->exec_sql("delete from attr_min where frd_id=$1",[$this->table->frd_id]);
        $this->table->delete();
    }

    /**
     * Check before inserting or updating, return TRUE if ok otherwise FALSE.
     * @return boolean
     */
    function check()
    {
        $cn=Dossier::connect();
        $error=0;
        if (trim($this->table->frd_text)=="")
        {
            $this->set_error("frd_text", _("Le nom ne peut pas être vide"));
            $error++;
        }
        if (trim($this->table->frd_class_base)!="")
        {
            $cnt=$cn->get_value("select count(*) from tmp_pcmn where pcm_val=$1"
                    , [$this->table->frd_class_base]);
            if ($cnt==0)
            {
                $this->set_error("frd_class_base",
                        _("Poste comptable n'existe pas"));
                $error++;
            }
        }

        if ($error!=0)
        {
            return false;
        }
        return true;
    }

    /**
     * @brief display into a dialog box the datarow in order 
     * to be appended or modified. Can be override if you need
     * a more complex form
     */
    function input()
    {
        echo "<br><font color=\"red\"> ";
        echo _("Attention, ne pas changer la signification de ce poste.");
        echo hi(_("par exemple ne pas changer Client par fournisseur"))."<br>";
        echo _("sinon le programme fonctionnera mal, ".
                "utiliser uniquement des chiffres pour la classe de base ou rien")."</font>";
        parent::input();

        /**
         * Add / Remove attribut Minimum
         */
        if ($this->table->frd_id!=-1)
        {
            echo h2(_("Attribut minimum pour les catégories de fiches"));
            $cn=Dossier::connect();
            $dossier_id=Dossier::id();
            $objname=$this->get_object_name();
            $a_attribut=$cn->get_array("select ad_id,ad_text,ad_type from attr_min join attr_def using (ad_id) where frd_id=$1 order by 2",
                    [$this->table->frd_id]);
            $nb_attribut=count($a_attribut);
            printf('<ul id="%s_list"> ', $objname);
            $used=$cn->get_value("select count(*) from jnt_fic_attr join fiche_def using (fd_id) where frd_id=$1",
                    [$this->table->frd_id]);
            if ($used!=0)
            {
                echo _("Catégorie utilisée, les attributs ne peuvent pas être modifiés");
            }
            for ($i=0; $i<$nb_attribut; $i++)
            {
                printf('<li id="%s_elt%d">', $objname
                        , $a_attribut[$i]['ad_id']);
                echo $a_attribut[$i]['ad_text'];
                // cannot delete NAME and QUICKCODE + attribute used in a
                if (!in_array($a_attribut[$i]['ad_id'], [ATTR_DEF_NAME, ATTR_DEF_QUICKCODE])&&$used==0)
                {
                    // allow to remove attribute
                    $js=sprintf("onclick=\"category_card.remove_attribut('%s','%s','%s',%d)\"",
                            Dossier::id(), $this->table->frd_id, $objname, $a_attribut[$i]['ad_id']);
                    echo HtmlInput::anchor(SMALLX, "", $js,
                            ' class="smallbutton" style="padding:0px;display:inline" ');
                }
                echo '</li>';
            }
            echo '</ul>';
            // Add some attribute if not used
            if ($used==0)
            {
                $sel_attribut=new ISelect("sel".$this->get_object_name());
                $sel_attribut->value=$cn->make_array("select ad_id,ad_text 
                        from attr_def 
                        where 
                        not exists (select 1 
                                    from 
                                    attr_min
                                    where 
                                    frd_id=$1 and ad_id=attr_def.ad_id)", NULL,
                        [$this->table->frd_id]);
                echo _("Attribut à ajouter");
                echo $sel_attribut->input();
                $js_script=sprintf("category_card.add_attribut('%s','%s','%s')",
                        $dossier_id, $this->table->frd_id, $objname);
                echo HtmlInput::button_image($js_script, uniqid(),
                        'class="smallbutton image_search"',
                        "image/bouton-plus.png");
            }
        }
    }

    /**
     * When adding a template of category  of card, the minimum is the name 
     * and the quickcode, which must be added into attr_min
     */
    function add_mandatory_attr()
    {
        $cn=Dossier::connect();
        $frd_id=$this->table->frd_id;
        $cn->exec_sql("insert into attr_min (frd_id,ad_id) values ($1,$2)",
                [$frd_id, ATTR_DEF_NAME]);
        $cn->exec_sql("insert into attr_min (frd_id,ad_id) values ($1,$2)",
                [$frd_id, ATTR_DEF_QUICKCODE]);
    }

}
