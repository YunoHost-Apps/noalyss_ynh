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
 * \brief class for the group of the analytic account
 *
 */
require_once  NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/constant.php';
require_once  NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_anc_print.php';

/*! \brief class for the group of the analytic account
 *
 */
class Anc_Group extends Anc_Print
{
    var $db;
    var $ga_id;
    var $ga_description;
    var $pa_id;

    function __construct ( $p_cn )
    {
        $this->db=$p_cn;
        $this->ga_id=null;
        $this->ga_description=null;
        $this->pa_id=null;
    }
    /*!
     * \brief insert into the database  an object
     * \return message with error otherwise an empty string
     */

    function insert()
    {
        if (strlen ($this->ga_id) > 10 )            return '<span class="notice">'.
                _('Taille de la code trop long maximum 10 caractères').'</span>';
        $sql=" insert into groupe_analytique (ga_id,ga_description,pa_id) values ('%s','%s',%d)";
        $sql=sprintf($sql,Database::escape_string($this->ga_id),
                     Database::escape_string($this->ga_description),
                     $this->pa_id);
        try
        {
            $this->db->exec_sql($sql);
        }
        catch (Exception $a)
        {
            return '<span class="notice">Doublon !!</span>';
        }
        return "";
    }
    /*!
     * \brief remove from the database
     */

    function remove()
    {
        $this->ga_id=str_replace(' ','',$this->ga_id);
        $this->ga_id=strtoupper($this->ga_id);
        $sql=" delete from groupe_analytique where ga_id='".Database::escape_string($this->ga_id)."'";

        $this->db->exec_sql($sql);
    }

    /*!
     * \brief load from the database and make an object
     */
    function load()
    {
        $sql="select ga_id, ga_description,pa_id from groupe_analytique where".
             " ga_id = ".$this->ga_id;
        $res=$this->db->exec_sql($sql);
        $array=Database::fetch_all($res);
        if ( ! empty($array) )
        {
            $this->ga_id=$array['ga_id'];
            $this->ga_description=$array['ga_description'];
            $this->pa_id=$array['pa_id'];
        }
    }

    /*!
     * \brief fill the object thanks an array
     * \param array
     */
    function get_from_array($p_array)
    {
        $this->ga_id=$p_array['ga_id'];
        $this->pa_id=$p_array['pa_id'];
        $this->ga_description=$p_array['ga_description'];
    }
    function myList()
    {
        $sql=" select ga_id,groupe_analytique.pa_id,pa_name,ga_description ".
             " from groupe_analytique ".
             " join plan_analytique using (pa_id)";
        $r=$this->db->exec_sql($sql);
        $array=Database::fetch_all($r);
        $res=array();
        if ( ! empty($array))
        {
            foreach ($array as $m )
            {
                $obj= new Anc_Group($this->db);
                $obj->get_from_array($m);
                $obj->pa_name=$m['pa_name'];
                $res[]=clone $obj;
            }
        }
        return $res;
    }

    function set_sql_filter()
    {
        $sql="";
        $and="and ";
        if ( $this->from != "" )
        {
            $sql.=" $and  oa_date >= to_date('".$this->from."','DD.MM.YYYY')";
            $and=" and ";
        }
        if ( $this->to != "" )
        {
            $sql.=" $and oa_date <= to_date('".$this->to."','DD.MM.YYYY')";
            $and=" and ";
        }
        if ( $this->from_poste != "" )
        {
            $sql.=" $and upper(po_name)>= upper('".$this->from_poste."')";
            $and=" and ";
        }
        if ( $this->to_poste != "" )
        {
            $sql.=" $and upper(po_name)<= upper('".$this->to_poste."')";
            $and=" and ";
        }
        return $sql;

    }

    function get_result()
    {
      $filter_date=$this->set_sql_filter();

      $sql="with m as (select po_id,
	po_name,
	ga_id,
	case when  oa_debit = 't' then oa_amount
	else 0
	end  as amount_deb,
	case when oa_debit = 'f' then oa_amount
	else 0
	end as amount_cred,
	oa_date
	from operation_analytique
join poste_analytique using (po_id)
where pa_id=$1 $filter_date )
select sum(amount_cred) as sum_cred, sum(amount_deb)as sum_deb,po_name,ga_id,ga_description
from m left join groupe_analytique using (ga_id)
group by ga_id,po_name,ga_description
order by ga_description,po_name";
      $ret=$this->db->get_array($sql,array($this->pa_id));

      return $ret;
    }

    function display_html()
    {
      if ( $this->check()  != 0)
	{
	  alert('Désolé mais une des dates données n\'est pas valide');
	  return;
	}

      $array=$this->get_result();
      if ( empty ($array) ) return "";
      require_once NOALYSS_INCLUDE.'/template/anc_balance_group.php';


    }
  /**
   *@brief display the button export CSV
   *@param $p_hidden is a string containing hidden items
   *@return html string
   */
  function show_button($p_hidden="")
  {
    $r="";
    $r.= '<form method="GET" action="export.php"  style="display:inline">';
    $r.= HtmlInput::hidden("act","CSV:AncBalGroup");
    $r.= HtmlInput::hidden("to",$this->to);
    $r.= HtmlInput::hidden("from",$this->from);
    $r.= HtmlInput::hidden("pa_id",$this->pa_id);
    $r.= HtmlInput::hidden("from_poste",$this->from_poste);
    $r.= HtmlInput::hidden("to_poste",$this->to_poste);
    $r.= $p_hidden;
    $r.= dossier::hidden();
    $r.=HtmlInput::submit('bt_csv',"Export en CSV");
    $r.= '</form>';
    return $r;
  }
  function export_csv()
  {
    $array=$this->get_result();
    printf('"groupe";"activité";"débit";"credit";"solde"');
    printf("\r\n");
    bcscale(2);
    for ($i=0;$i<count($array);$i++)
      {
	printf('"%s";"%s";%s;%s;%s',
	       $array[$i]['ga_id'],
	       $array[$i]['po_name'],
	       nb($array[$i]['sum_deb']),
	       nb($array[$i]['sum_cred']),
	       nb(bcsub($array[$i]['sum_cred'],$array[$i]['sum_deb']))
	       );
	printf("\r\n");
      }
  }
    static function test_me()
    {

        $cn=new Database(dossier::id());
        print_r($cn);
        $o=new Anc_Group($cn);
        $r=$o->myList();
        print_r($r);
        echo '<hr>';
        print_r($o);
        $o->ga_id="DD' dd dDD";
        $o->ga_description="Test 1";
        $o->remove();
        //    $o->insert();
        $o->ga_id="DD";
        $o->ga_description="Test 1";
        $o->remove();

        $r=$o->myList();
        print_r($r);
    }
}
