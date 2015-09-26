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
 * \brief Manage the account
 */
/*!
 * \brief Manage the account from the table tmp_pcmn
 */
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';

class Acc_Account
{
    var $db;          /*!< $db database connection */
    static private $variable = array("value"=>'pcm_val',
                                     'type'=>'pcm_type',
                                     'parent'=>'pcm_val_parent',
                                     'libelle'=>'pcm_lib');
    private  $pcm_val;
    private  $pcm_type;
    private  $pcm_parent;
    private  $pcm_lib;
    static public $type=array(
                            array('label'=>'Actif','value'=>'ACT'),
                            array('label'=>'Passif','value'=>'PAS'),
                            array('label'=>'Actif c. inverse','value'=>'ACTINV'),
                            array('label'=>'Passif c.inverse','value'=>'PASINV'),
                            array('label'=>'Produit','value'=>'PRO'),
                            array('label'=>'Produit Inverse','value'=>'PROINV'),
                            array('label'=>'Charge','value'=>'CHA'),
                            array('label'=>'Charge Inverse','value'=>'CHAINV'),
                            array('label'=>'Non defini','value'=>'CON')
                        );

    function __construct ($p_cn,$p_id=0)
    {
        $this->db=$p_cn;
        $this->pcm_val=$p_id;
    }
    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }
        else
            throw new Exception (__FILE__.":".__LINE__._('Erreur attribut inexistant'));
    }

    function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            if ($this->check($idx,$p_value) == true )      $this->$idx=$p_value;
        }
        else
            throw new Exception (__FILE__.":".__LINE__._('Erreur attribut inexistant'));


    }
    /*!\brief Return the name of a account
     *        it doesn't change any data member
     * \return string with the pcm_lib
     */
    function get_lib()
    {
        $ret=$this->db->exec_sql(
                 "select pcm_lib from tmp_pcmn where
                 pcm_val=$1",array($this->pcm_val));
        if ( Database::num_row($ret) != 0)
        {
            $r=Database::fetch_array($ret);
            $this->pcm_lib=$r['pcm_lib'];
        }
        else
        {
            $this->pcm_lib=_("Poste inconnu");
        }
        return $this->pcm_lib;
    }
    /*!\brief Check that the value are valid
     *\return true if all value are valid otherwise false
     */
    function check ($p_member='',$p_value='')
    {
        // if there is no argument we check all the member
        if ($p_member == '' && $p_value== '' )
        {
            foreach (self::$variable as $l=>$k)
            {
                $this->check($k,$this->$k);
            }
        }
        else
        {
            // otherwise we check only the value
            if ( strcmp ($p_member,'pcm_val') == 0 )
            {
                    return true;
            }
            else if ( strcmp ($p_member,'pcm_val_parent') == 0 )
            {
                    return true;
            }
            else if ( strcmp ($p_member,'pcm_lib') == 0 )
            {
                return true;
            }
            else if ( strcmp ($p_member,'pcm_type') == 0 )
            {
                foreach (self::$type as $l=>$k)
                {
                    if ( strcmp ($k['value'],$p_value) == 0 ) return true;

                }
                throw new Exception(_('type de compte incorrect ').$p_value);
            }
            throw new Exception (_('Donnee member inconnue ').$p_member);
        }

    }
    /*!\brief Get all the value for this object from the database
     *        the data member are set
     * \return false if this account doesn't exist otherwise true
     */
    function load()
    {
        $ret=$this->db->exec_sql("select pcm_lib,pcm_val_parent,pcm_type from
                                 tmp_pcmn where pcm_val=$1",array($this->pcm_val));
        $r=Database::fetch_all($ret);

        if ( ! $r ) return false;
        $this->pcm_lib=$r[0]['pcm_lib'];
        $this->pcm_val_parent=$r[0]['pcm_val_parent'];
        $this->pcm_type=$r[0]['pcm_type'];
        return true;

    }
    function form($p_table=true)
    {
        $wType=new ISelect();
        $wType->name='p_type';
        $wType->value=self::$type;

        if ( ! $p_table )
        {
            $ret='    <TR>
                 <TD>
                 <INPUT TYPE="TEXT" NAME="p_val" SIZE=7>
                 </TD>
                 <TD>
                 <INPUT TYPE="TEXT" NAME="p_lib" size=50>
                 </TD>
                 <TD>
                 <INPUT TYPE="TEXT" NAME="p_parent" size=5>
                 </TD>
                 <TD>';

            $ret.=$wType->input().'</TD>';
            return $ret;
        }
        else
        {
            $ret='<TABLE><TR>';
            $ret.=sprintf ('<TD>'._('Numéro de classe').' </TD><TD><INPUT TYPE="TEXT" name="p_val" value="%s"></TD>',$this->pcm_val);
            $ret.="</TR><TR>";
            $ret.=sprintf('<TD>'._('Libellé').' </TD><TD><INPUT TYPE="TEXT" size="70" NAME="p_lib" value="%s"></TD>',h($this->pcm_lib));
            $ret.= "</TR><TR>";
            $ret.=sprintf ('<TD>'._('Classe Parent').'</TD><TD><INPUT TYPE="TEXT" name="p_parent" value="%s"></TD>',$this->pcm_val_parent);
            $ret.='</tr><tr>';
            $wType->selected=$this->pcm_type;
            $ret.="<td> Type de poste </td>";
            $ret.= '<td>'.$wType->input().'</td>';
            $ret.="</TR> </TABLE>";
            $ret.=dossier::hidden();

            return $ret;
        }
    }
    function count($p_value)
    {
        $sql="select count(*) from tmp_pcmn where pcm_val=$1";
        return $this->db->get_value($sql,array($p_value));
    }
    /*!\brief for developper only during test */
    static function test_me()
    {
        $cn=new Database(dossier::id());

    }
    /**
     *@brief update an accounting, but you can update pcm_val only if
     * this accounting has never been used before  */
    function update($p_old)
    {
        if (strcmp(trim($p_old), trim($this->pcm_val)) !=0 )
        {
            $count=$this->db->get_value('select count(*) from jrnx where j_poste=$1',
                                        array($p_old)
                                       );
            if ($count != 0)
                throw new Exception(_('Impossible de changer la valeur: poste déjà utilisé'));
        }
        $this->pcm_lib=mb_substr($this->pcm_lib,0,150);
        $this->check();
        $sql="update tmp_pcmn set pcm_val=$1, pcm_lib=$2,pcm_val_parent=$3,pcm_type=$4 where pcm_val=$5";
        $Ret=$this->db->exec_sql($sql,array($this->pcm_val,
                                            $this->pcm_lib,
                                            $this->pcm_val_parent,
                                            $this->pcm_type,
                                            $p_old));
    }
}
