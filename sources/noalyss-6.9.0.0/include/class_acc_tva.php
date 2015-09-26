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

/*!\file
 * \brief this class is used for the table tva_rate
 */
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_database.php';

/*!\brief Acc_Tva is used for to map the table tva_rate
 * parameter are
- private static $cn;	database connection
- private static $variable=array("id"=>"tva_id",
		 "label"=>"tva_label",
		 "rate"=>"tva_rate",
		 "comment"=>"tva_comment",
		 "account"=>"tva_poste");

*/
class Acc_Tva
{
    private  $cn;		/*!< $cn database connection */
    private static $variable=array("id"=>"tva_id",
                                   "label"=>"tva_label",
                                   "rate"=>"tva_rate",
                                   "comment"=>"tva_comment",
                                   "account"=>"tva_poste",
                                    "both_side"=>'tva_both_side');

    function __construct ($p_init,$p_tva_id=0)
    {
        $this->cn=$p_init;
        $this->tva_id=$p_tva_id;
        $this->poste="";
    }
    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }

        echo  (__FILE__.":".__LINE__.'Erreur attribut inexistant');
    }
    public function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            $this->$idx=$p_value;
        }
        else
            throw new Exception("Attribut inexistant $p_string");


    }
    public function get_info()
    {
        return var_export(self::$variable,true);
    }

    public function verify()
    {
        // Verify that the elt we want to add is correct
    }
    public function save()
    {

        if (  $this->tva_id == 0 )
            $this->insert();
        else
            $this->update();
    }

    public function insert()
    {
        if ( $this->verify() != 0 ) return;
        $sql="select tva_insert($1,$2,$3,$4,$5)";

        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->tva_label,
                       $this->tva_rate,
                       $this->tva_comment,
                       $this->tva_poste,
                        $this->tva_both_side)
             );
        $this->tva_id=$this->cn->get_current_seq('s_tva');
        $err=Database::fetch_result($res);
    }

    public function update()
    {
        if ( $this->verify() != 0 ) return;
        $sql="update tva_rate set tva_label=$1,tva_rate=$2,tva_comment=$3,tva_poste=$4,tva_both_side=$5 ".
             " where tva_id = $6";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->tva_label,
                       $this->tva_rate,
                       $this->tva_comment,
                       $this->tva_poste,
                       $this->tva_both_side,
                       $this->tva_id)
             );

    }
    /**
     *Load the VAT,
     *@note if the label is not found then we get an message error, so the best is probably
     *to initialize the VAT object with default value
     */
    public function load()
    {
        $sql="select tva_id,tva_label,tva_rate, tva_comment,tva_poste,tva_both_side from tva_rate where tva_id=$1";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->tva_id)
             );

        if ( $this->cn->size() == 0 ) return -1;

        $row=Database::fetch_array($res,0);
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }
        return 0;
    }
    /*!\brief get the account of the side (debit or credit)
     *\param $p_side is d or C
     *\return the account to use
     *\note call first load if tva_poste is empty
     */
    public function get_side($p_side)
    {
        if ( strlen($this->tva_poste) == 0 ) $this->load();
        list($deb,$cred)=explode(",",$this->tva_poste);
        switch ($p_side)
        {
        case 'd':
                return $deb;
            break;
        case 'c':
            return $cred;
            break;
        default:
            throw (new Exception (__FILE__.':'.__LINE__." param est d ou c, on a recu [ $p_side ]"));
        }
    }
    public function delete()
    {
        $sql="delete from tva_rate where tva_id=$1";
        $res=$this->cn->exec_sql($sql,array($this->tva_id));
    }
    /*!\brief
     * Test function
     */
    static function test_me()
    {
        $cn=new Database(dossier::id());
        $a=new Acc_Tva($cn);
        echo $a->get_info();
        $a->set_parameter("id",1);
        $a->load();
        $a->set_parameter("id",0);
        $a->set_parameter("rate","0.2222");
        $a->set_parameter("label","test");
        $a->save();
        $a->load();
        print_r($a);

        $a->set_parameter("comment","un cht'it test");
        $a->save();
        $a->load();
        print_r($a);

        $a->delete();
    }

}

/* test::test_me(); */
