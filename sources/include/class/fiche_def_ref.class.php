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
// Copyright Author Dany De Bontridder danydb@aevalys.eu
/*! \file
 * \brief fiche_def_ref, a fiche is owned by fiche_def which is owned by 
 *        fiche_def_ref
 */
/*!
 * \brief fiche_def_ref, a fiche is owned by fiche_def which is owned by 
 *        fiche_def_ref
 */

class Fiche_Def_Ref
{
    var $frd_id;           /*!< $frd_id fiche_def_ref.frd_id */
    var $frd_text;         /*!< $frd_text fiche_def_ref.frd_tex */
    var $frd_class_base;   /*!< fiche_def_ref.frd_class_base */
    var $attribut;         /*!< array which containing list of attr */
    /* it is used with dynamic variables */

    function __construct($p_cn,$p_frd_id=-1)
    {
        $this->db=$p_cn;
        $this->frd_id=$p_frd_id;
        $this->attribut=array('frd_id','frd_text','frd_class_base');
    }
   
    /**
    * get category of cards by model
    * @param $p_modele if the FRD_ID
    * @return array of category (fd_id)
    */
    function get_by_modele($p_modele)
    {
        $array = array();
        $result = $this->db->get_array('select fd_id from fiche_def where frd_id=$1', array($p_modele));
        for ($i = 0; $i < count($result); $i++)
        {
            $array[$i] = $result[$i]['fd_id'];
        }
        return $array;
    }

}
?>
