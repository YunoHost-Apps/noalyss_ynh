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
 *   \brief class acc_reconciliation, this class is new and the code
 *   must use it
 *
 */
require_once NOALYSS_INCLUDE.'/class_iconcerned.php';
require_once  NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_lettering.php';

/*! \brief new class for managing the reconciliation it must be used
 * instead of the function InsertRapt, ...
 *
 */
class Acc_Reconciliation
{
    var $db;			/*!< database connection */
    var $jr_id;			/*!< jr_id */

    function   __construct($cn)
    {
        $this->db=$cn;
        $this->jr_id=0;
    }

    function set_jr_id($jr_id)
    {
        $this->jr_id=$jr_id;
    }
    /*! \brief return a widget of type js_concerned
     */
    function widget()
    {
        $wConcerned=new IConcerned();
        $wConcerned->extra=0; // with 0 javascript search from e_amount... field (see javascript)

        return $wConcerned;

    }
    /*!
     *\brief   Insert into jrn_rapt the concerned operations
     *
     * \param $jr_id2 (jrn.jr_id) => jrn_rapt.jra_concerned or a string
     * like "jr_id2,jr_id3,jr_id4..."
     *
     * \return none
     *
     */
    function insert($jr_id2)
    {
        if ( trim($jr_id2) == "" )
            return;
        if ( strpos($jr_id2,',') !== 0 )
        {
            $aRapt=explode(',',$jr_id2);
            foreach ($aRapt as $rRapt)
            {
                if ( isNumber($rRapt) == 1 )
                {
                    $this->insert_rapt($rRapt);
                }
            }
        }
        else
            if ( isNumber($jr_id2) == 1 )
            {
                $this->insert_rapt($jr_id2);
            }
    }

    /*!
     *\brief   Insert into jrn_rapt the concerned operations
     * should not  be called directly, use insert instead
     *
     * \param $jr_id2 (jrn.jr_id) => jrn_rapt.jra_concerned
     *
     * \return none
     *
     */
    function insert_rapt($jr_id2)
    {
        if ( isNumber($this->jr_id)  == 0 ||  isNumber($jr_id2) == 0 )
        {
            return false;
        }
        if ( $this->jr_id==$jr_id2)
            return true;

		if ( $this->db->count_sql("select jr_id from jrn where jr_id=".$this->jr_id)==0 )
				return false;
		if ( $this->db->count_sql("select jr_id from jrn where jr_id=".$jr_id2)==0 )
				return false;

        // verify if exists
        if ( $this->db->count_sql(
                    "select jra_id from jrn_rapt where jra_concerned=".$this->jr_id.
                    " and jr_id=$jr_id2
                    union
                    select jra_id from jrn_rapt where jr_id=".$this->jr_id.
                    " and jra_concerned=$jr_id2 ")
                ==0)
        {
            // Ok we can insert
            $Res=$this->db->exec_sql("insert into jrn_rapt(jr_id,jra_concerned) values ".
                                    "(".$this->jr_id.",$jr_id2)");
            // try to letter automatically same account from both operation
            $this->auto_letter($jr_id2);
            
            // update date of paiement -----------------------------------------------------------------------
            $source_type=$this->db->get_value("select substr(jr_internal,1,1) from jrn where jr_id=$1",array($this->jr_id));
            $dest_type=$this->db->get_value("select substr(jr_internal,1,1) from jrn where jr_id=$1",array($jr_id2));
            if (($source_type =='A' || $source_type=='V') && ($dest_type != 'A' && $dest_type != 'V'))
            {
                // set the date on source
                $date=$this->db->get_value('select jr_date from jrn where jr_id=$1',array($jr_id2));
                if ( trim ($date) == '') $date=null;
                $this->db->exec_sql('update jrn set jr_date_paid=$1 where jr_id=$2 and jr_date_paid is null ',array($date,$this->jr_id));
            }
            if (($source_type !='A' && $source_type !='V') && ($dest_type == 'A' || $dest_type == 'V'))
            {
                // set the date on dest
                $date=$this->db->get_value('select jr_date from jrn where jr_id=$1',array($this->jr_id));
                if (trim($date) == '') $date=null;
                $this->db->exec_sql('update jrn set jr_date_paid=$1 where jr_id=$2 and jr_date_paid is null ',array($date,$jr_id2));
            }
        }
        return true;
    }
	/**
	 * @brief try to letter same card between $p_jrid and $this->jr_id
	 * @param jrn.jr_id $p_jrid  the operation to reconcile
	 */
	function auto_letter($p_jrid)
	{
		// Try to find same card from both operation
		$sql="select j1.f_id as fiche ,coalesce(j1.j_id,-1) as jrnx_id1,coalesce(j2.j_id,-1) as jrnx_id2,
j1.j_poste as poste
				from jrnx as j1
					join jrn as jr1 on (j1.j_grpt=jr1.jr_grpt_id)
					join jrnx as j2 on (coalesce(j1.f_id,-1)=coalesce(j2.f_id,-1) and j1.j_poste=j2.j_poste)
					join jrn as jr2 on (j2.j_grpt=jr2.jr_grpt_id)
				where
					jr1.jr_id=$1
					and
					jr2.jr_id= $2";
		$result=$this->db->get_array($sql,array($this->jr_id,$p_jrid));
		if ( count($result) == 0)
		{
			return;
		}
		for ($i=0;$i<count($result);$i++)
		{
			if ( $result[$i]['fiche'] != -1)
			{
				$letter = new Lettering_Card($this->db);
				$letter->insert_couple($result[$i]['jrnx_id1'],$result[$i]['jrnx_id2']);
			}
			else
			{
				$letter = new Lettering_Account($this->db);
				$letter->insert_couple($result[$i]['jrnx_id1'],$result[$i]['jrnx_id2']);
			}
		}

	}

	/*!
     *\brief   Insert into jrn_rapt the concerned operations
     *
     * \param $this->jr_id (jrn.jr_id) => jrn_rapt.jr_id
     * \param $jr_id2 (jrn.jr_id) => jrn_rapt.jra_concerned
     *
     * \return none
     */
    function remove($jr_id2)
    {
        if ( isNumber($this->jr_id)  == 0 or
                isNumber($jr_id2) == 0 )
        {
            return;
        }
        // verify if exists
        if ( $this->db->count_sql("select jra_id from jrn_rapt where ".
                                  " jra_concerned=".$this->jr_id."  and jr_id=$jr_id2
                                  union
                                  select jra_id from jrn_rapt where jra_concerned=$jr_id2 ".
                                  " and jr_id=".$this->jr_id) !=0)
        {
			/**
			 * remove also lettering between both operation
			 */
			$sql = " delete from
					jnt_letter
					where jl_id in ( select jl_id from jnt_letter
										join letter_cred as lc using(jl_id)
										join letter_deb as ld using (jl_id)
									where
										lc.j_id in (select j_id
													from jrnx join jrn on (j_grpt=jr_grpt_id)
													where jr_id in ($1,$2))
										or
										ld.j_id in (select j_id
													from jrnx join jrn on (j_grpt=jr_grpt_id)
													where jr_id in ($1,$2))



							)";
			$this->db->exec_sql($sql, array($jr_id2, $this->jr_id));
			// Ok we can delete
			$Res=$this->db->exec_sql("delete from jrn_rapt where ".
                                     "(jra_concerned=$jr_id2 and jr_id=".$this->jr_id.") or
                                     (jra_concerned=".$this->jr_id." and jr_id=$jr_id2) ");
        }
    }

    /*!
     *\brief   Return an array of the concerned operation
     *
     *
     *\param database connection
     * \return array if something is found or null
     */
    function get ( )
    {
        $sql=" select jr_id as cn from jrn_rapt where jra_concerned=".$this->jr_id.
             " union ".
             " select jra_concerned as cn from jrn_rapt where jr_id=".$this->jr_id;
        $Res=$this->db->exec_sql($sql);

        // If nothing is found return null
        $n=Database::num_row($Res);

        if ($n ==0 ) return null;

        // put everything in an array
        for ($i=0;$i<$n;$i++)
        {
            $l=Database::fetch_array($Res,$i);
            $r[$i]=$l['cn'];
        }
        return $r;
    }
    function fill_info()
    {
        $sql="select jr_id,jr_date,jr_comment,jr_internal,jr_montant,jr_pj_number,jr_def_id,jrn_def_name,jrn_def_type
             from jrn join jrn_def on (jrn_def_id=jr_def_id)
             where jr_id=$1";
        $a=$this->db->get_array($sql,array($this->jr_id));
        return $a[0];
    }
    /**
     *@brief return array of not-reconciled operation
    * Prepare and put in memory the SQL detail_quant
    */
    function get_not_reconciled()
    {
      $filter_date=$this->filter_date();
      /* create ledger filter */
      $sql_jrn=$this->ledger_filter();

        $array=$this->db->get_array("select distinct jr_id,jr_date from jrn where $filter_date and $sql_jrn and jr_id not in (select jr_id from jrn_rapt union select jra_concerned from jrn_rapt) order by jr_date");
        $ret=array();
        for ($i=0;$i<count($array);$i++)
        {
            $this->jr_id=$array[$i]['jr_id'];
            $ret[$i]['first']=$this->fill_info();
        }
        $this->db->prepare('detail_quant','select * from v_quant_detail where jr_id=$1');
        return $ret;
    }
    /**
     *Create a sql condition to filter by security and by asked ledger
     * based on $this->a_jrn
     *@return a valid sql stmt to include
     *@see get_not_reconciled get_reconciled
     */
    function ledger_filter ()
    {
        global $g_user;
        /* get the available ledgers for current user */
        $sql=$g_user->get_ledger_sql('ALL',3);
        $sql=str_replace('jrn_def_id','jr_def_id',$sql);
        $r='';
        /* filter by this->r_jrn */
        if ($this->a_jrn != null )
        {
            $sep='';
            $r='and jr_def_id in (';
            foreach( $this->a_jrn as $key=>$value)
            {
                $r.=$sep.$value;
                $sep=',';
            }
            $r.=')';
        }
        return $sql.'  '.$r;
    }
    /**
     *@brief return array of reconciled operation
     * Prepare and put in memory the SQL detail_quant
     *@return
     *@note
     *@see
     @code

     @endcode
    */
    function get_reconciled()
    {
      $filter_date=$this->filter_date();


        /* create ledger filter */
        $sql_jrn=$this->ledger_filter();

        $array=$this->db->get_array("select distinct jr_id,jr_date from jrn where $filter_date and $sql_jrn and jr_id  in (select jr_id from jrn_rapt union select jra_concerned from jrn_rapt) order by jr_date");
        $ret=array();
        for ($i=0;$i<count($array);$i++)
        {
            $this->jr_id=$array[$i]['jr_id'];
            $ret[$i]['first']=$this->fill_info();
            $atmp=$this->get();
            for ( $e=0;$e<count($atmp);$e++)
            {
                $this->jr_id=$atmp[$e];
                $ret[$i]['depend'][$e]=$this->fill_info();
            }
        }
        $this->db->prepare('detail_quant','select * from v_quant_detail where jr_id=$1');
        return $ret;
    }
    /**
     *@brief
     * Prepare and put in memory the SQL detail_quant
     *@param
     *@return
     *@note
     *@see
    @code

    @endcode
     */
    function get_reconciled_amount($p_equal=false)
    {
        $array=$this->get_reconciled();
        $ret=array();
        bcscale(2);
        for ($i=0;$i<count($array);$i++)
        {
            $first_amount=$array[$i]['first']['jr_montant'];
            $second_amount=0;
            for ($e=0;$e<count($array[$i]['depend']);$e++)
            {
                $second_amount=bcadd($second_amount,$array[$i]['depend'][$e]['jr_montant']);
            }
            if ( $p_equal &&  $first_amount==$second_amount)
            {
                $ret[]=$array[$i];
            }
            if ( ! $p_equal &&  $first_amount != $second_amount)
            {
                $ret[]=$array[$i];
            }
        }
        return $ret;
    }
  /**
   *@brief create a string to filter thanks the date
   *@return a sql string like jr_date > ... and jr_date < ....
   *@note use the data member start_day and end_day
   *@see get_reconciled get_not_reconciled
   */
    function filter_date()
    {
      global $g_user;
      list($start,$end)=$g_user->get_limit_current_exercice();

      if (isDate($this->start_day) ==null)
	{
	  $this->start_day=$start;
	}
      if ( isDate($this->end_day) == null)
	{
	  $this->end_day=$end;
	}
      $sql=" (jr_date >= to_date('".$this->start_day."','DD.MM.YYYY')
		and jr_date <= to_date('".$this->end_day."','DD.MM.YYYY'))";
      return $sql;

    }
    function show_detail($p_ret)
    {
        if (Database::num_row($p_ret)> 0)
        {
            echo '<tr class="odd">';
            echo '<td></td>';
            echo '<td colspan="5" style="border:1px solid black;width:auto">';
            include 'template/impress_reconciliation_detail.php';
            echo '</td>';
            echo '</tr>';
        }
    }
    /**
     * Export to CSV
     * @param type $p_choice 
     * 
     * @note must be set before calling
     *    - $this->a_jrn       array of ledger
     *    - $this->start_day start date
     *    - $this->end_day end date
     * @see Acc_Reconciliation::get_data
     */
    function export_csv($p_choice)
    {
        $array = $this->get_data($p_choice);
        for ($i = 0; $i < count($array); $i++)
        {
            // ---------------------------------------
            // first index has 2 arrays : first & depend[]
            // ---------------------------------------

            $first = $array[$i]['first'];
            $a_depend = array();
            if (isset($array[$i]['depend']))
            {
                $a_depend = $array[$i]['depend'];
                //----- HEADER ----
                if ($i == 0)
                {
                    printf('"n°";"Date";"internal";"libellé";"n° pièce";"nom journal";"type journal";"montant"');
                    printf(';"<->";"Date";"internal";"libellé";"n° pièce";"nom journal";"type journal";"montant"' . "\n\r");
                }
            }
            else
            {
                //----- HEADER ----
                if ($i == 0)
                {
                    printf('"n°";"Date";"internal";"libellé";"n° pièce";"nom journal";"type journal";"montant"' . "\n\r");
                }
            }
            // --------------------------
            // Print First
            // --------------------------
            printf('%d;"%s";"%s";"%s";"%s";"%s";"%s";%f',$i, $first['jr_date'], $first['jr_internal'], $first['jr_comment'], $first['jr_pj_number'], $first['jrn_def_name'], $first['jrn_def_type'], $first['jr_montant']);
            if (count($a_depend) > 0)
            {
                // --------------------------------------
                // Print first depending operation
                // --------------------------------------
                $depend = $a_depend[0];
                printf(';"<->";"%s";"%s";"%s";"%s";"%s";"%s";%f' . "\n\r", $depend['jr_date'], $depend['jr_internal'], $depend['jr_comment'], $depend['jr_pj_number'], $depend['jrn_def_name'], $depend['jrn_def_type'], $depend['jr_montant']);

                // --------------------------------------
                // print other depending operation if any
                // --------------------------------------
                for ($e = 1; $e < count($a_depend); $e++)
                {
                    $depend = $a_depend[$e];
                    printf(';;;;;;;"<->";');
                    printf('"%s";"%s";"%s";"%s";"%s";"%s";%f' . "\n\r", $depend['jr_date'], $depend['jr_internal'], $depend['jr_comment'], $depend['jr_pj_number'], $depend['jrn_def_name'], $depend['jrn_def_type'], $depend['jr_montant']);
                }
            }
            else
            {
                printf("\n\r");
            }
        }
    }

    /**
     * 
     * @param type $p_choice
     *       - 0 : operation reconcilied
     *       - 1 : reconcilied with different amount
     *       - 2 : reconcilied with same amount
     *       - 3 : not reconcilied 
     * @return $array
     */
    function get_data($p_choice)
    {
        switch ($p_choice)
        {
            case 0:
                $array = $this->get_reconciled();
                break;
            case 1:
                $array = $this->get_reconciled_amount(false);
                break;
            case 2:
                $array = $this->get_reconciled_amount(true);
                break;
            case 3:
                $array = $this->get_not_reconciled();
                break;
            default:
                echo "Choix invalid";
                throw new Exception("invalide");
        }
        return $array;
    }

    static function test_me()
    {
        $cn=new Database(dossier::id());
        $rap=new Acc_Reconciliation($cn);
        var_dump($rap->get_reconciled_amount('',false));
    }

}
