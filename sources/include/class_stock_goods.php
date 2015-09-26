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

/**
 * @file
 * @brief Manage the goods
 *
 */
require_once NOALYSS_INCLUDE.'/class_stock_goods_sql.php';

class Stock_Goods extends Stock_Goods_Sql
{
/**
 * if an array if receive the keys are
 *  p_date
 *  p_depot
 *  f_idX f_id
 *  sg_codeX
 *  sg_type0
 *
 * @global $cn database connx
 * @param $p_array
 * if an array if receive the keys are
 *  p_date
 *  p_depot
 *  f_idX f_id
 *  sg_codeX
 *  sg_type0
 * @param $p_readonly true or false
 */
	function input($p_array = null, $p_readonly = false)
	{
		global $cn;

		if ($p_array != null)
		{
			extract($p_array);
		}
		else
		{
			$p_date = '';
			$p_motif = '';
			$p_depot = 0;
		}
		$date = new IDate('p_date', $p_date);
		$date->setReadOnly($p_readonly);
		$motif = new IText('p_motif', $p_motif);
		$motif->setReadOnly($p_readonly);
		$motif->size = 80;
		$idepo = HtmlInput::select_stock($cn, "p_depot", "W");
		$idepo->setReadOnly($p_readonly);
		if (count($idepo->value) == 0)
		{
			NoAccess();
			die();
		}
		$idepo->selected = $p_depot;
                if ($p_readonly ) {
                    $nb=$row;
                } else {
                    if (isset ($row ) )
                    {
                        $nb=($row > MAX_ARTICLE_STOCK)?$row:MAX_ARTICLE_STOCK;
                    }else {
                        $nb=MAX_ARTICLE_STOCK;
                    }
                }
		for ($e = 0; $e < $nb; $e++)
		{//ATTR_DEF_STOCKfiche_
			$sg_code[$e] = new ICard('sg_code' . $e);
			$sg_code[$e]->extra = "[sql]  fd_id = 500000";
			$sg_code[$e]->set_attribute("typecard", $sg_code[$e]->extra);
			$sg_code[$e]->set_attribute("label", "label" . $e);
			$sg_code[$e]->value = (isset(${'sg_code' . $e})) ? ${'sg_code' . $e} : '';
			$sg_quantity[$e] = new INum('sg_quantity' . $e);
			$sg_quantity[$e]->value = (isset(${'sg_quantity' . $e})) ? ${'sg_quantity' . $e} : '';
			$label[$e] = new ISpan("label$e");
			if (trim($sg_code[$e]->value) != '')
			{
				$label[$e]->value = $cn->get_value("select vw_name from vw_fiche_attr where quick_code=$1", array($sg_code[$e]->value));
			}
			$sg_code[$e]->setReadOnly($p_readonly);
			$sg_quantity[$e]->setReadOnly($p_readonly);
			if ( isset (${'sg_type'.$e})) {
				$sg_type[$e]=(${'sg_type'.$e}=='c')?'OUT':'IN';
			}
			if ( isset (${'f_id'.$e})) {
				$fiche[$e]=new Fiche($this->cn,${'f_id'.$e});
			}
		}
                $select_exercice=new ISelect('p_exercice');
                $select_exercice->value=$cn->make_array('select distinct p_exercice,p_exercice from parm_periode order by 1 desc');
                
                require_once NOALYSS_INCLUDE.'/template/stock_inv.php';
	}

	function record_save($p_array)
	{
		global $cn;
		try
		{
			if (isDate($p_array['p_date']) == null)
				throw new Exception('Date invalide');
			$cn->start();
			$ch = new Stock_Change_Sql($cn);
			$ch->setp("c_comment", $p_array['p_motif']);
			$ch->setp("r_id", $p_array['p_depot']);
			$ch->setp("c_date", $p_array['p_date']);
			$ch->setp('tech_user', $_SESSION['g_user']);
			$ch->insert();
			$per = new Periode($cn);
			$periode = $per->find_periode($p_array['p_date']);
			$exercice = $per->get_exercice($periode);
                        $nb=$p_array['row'];
			for ($i = 0; $i < $nb; $i++)
			{
				$a = new Stock_Goods_Sql($cn);
				if ($p_array['sg_quantity' . $i] != 0 &&
						trim($p_array['sg_code' . $i]) != '')
				{
                                        $stock=  strtoupper(trim($p_array['sg_code' . $i]));
					$fiche=new Fiche($cn);
					$fiche->get_by_qcode($p_array['sg_code' . $i]);
					/*
                                         * check if code stock does exist
                                         */
                                        $count=$cn->get_value('select count(*) from fiche_detail where ad_id=$1 and ad_value=$2',
                                                array(ATTR_DEF_STOCK,$stock));
                                        if ( $count==0) {
                                            throw new Exception("Code stock inexistant");
                                        }
					$a->f_id=$fiche->id;
					$a->sg_code = $stock;
					$a->sg_quantity = abs($p_array['sg_quantity' . $i]);
					$a->sg_type = ($p_array['sg_quantity' . $i] > 0) ? 'd' : 'c';
					$a->sg_comment = $p_array['p_motif'];
					$a->tech_user = $_SESSION['g_user'];
					$a->r_id = $p_array['p_depot'];
					$a->sg_exercice = $exercice;
					$a->c_id = $ch->c_id;
					$a->sg_date=$p_array['p_date'];
					$a->insert();
				}
			}
			$cn->commit();
		}
		catch (Exception $exc)
		{
			echo $exc->getTraceAsString();
			throw $exc;
		}
	}
        /**
         * Insert into stock_goods from ACH and VEN
         * @param type $p_array KEY : db => database conx, j_id => jrnx.j_id,goods=> f_id of the goods
         * 'quant' => quantity ,'dir'=> d or c (c for sales OUT and d for purchase IN),'repo'=>r_id of the
         * repository (stock_repository.r_id
         */
        static function insert_goods(&$p_cn,$p_array)
        {
            global $g_user;
			extract ($p_array);
            if ($g_user->can_write_repo($repo) == false)
                return false;

            // Retrieve the good account for stock
            $code = new Fiche($p_cn);
            $code->get_by_qcode($goods);
            $code_marchandise = $code->strAttribut(ATTR_DEF_STOCK);
            if ($code_marchandise == NOTFOUND || $code_marchandise=='')
                return false;

            $exercice = $g_user->get_exercice();

            if ($exercice == 0)
                throw new Exception('Annee invalide erreur');

            $Res = $p_cn->exec_sql("insert into stock_goods (
                            j_id,
                            f_id,
                            sg_code,
                            sg_quantity,
                            sg_type,sg_exercice,r_id ) values ($1,$2,$3,$4,$5,$6,$7)", array(
                $p_array['j_id'],
                $code->id,
                $code_marchandise,
                $p_array['quant'],
                $p_array['dir'],
                $exercice,
                $p_array['repo']
                    )
            );
           return $Res;
    }
    /**
     * Return an array, used by Stock_Goods::input 
     * @global type $cn
     * @param type $p_array
     * @throws Exception
     */
    function take_last_inventory($p_array)
    {
        global $cn;
        $year=HtmlInput::default_value("p_exercice", "", $p_array);
        $depot=HtmlInput::default_value("p_depot", "", $p_array);
        if ($year=="")
            throw new Exception(_('Inventaire invalide'), 10);
        if ($depot=="")
            throw new Exception(_('Dépôt invalide'), 20);

        // compute state_exercice
        $periode=new Periode($cn);
        $periode->p_id=$cn->get_value("select min(p_id) from parm_periode where p_exercice=$1", array($year));
        $first_day=$periode->first_day();

        // compute array for stock
        $array['state_exercice']=$first_day;
        
        $stock=new Stock($cn);
        $rowid=$stock->build_tmp_table($array);

        // compute first day of the next year
        $next_year=$year+1;
        $periode=new Periode($cn);
        $periode->p_id=$cn->get_value("select min(p_id) from parm_periode where p_exercice=$1", array($next_year));
        
        if ($periode->p_id=="")
            $array['p_date']="";
        else
            $array['p_date']=$periode->first_day();
        
        // Compute an array compatible with Stock_Goods::input
        $array['p_motif']=_('Inventaire ').$year;
        $array['p_depot']=$depot;
        
        $result=$cn->get_array("
                select sg_code,sum(coalesce(s_qin,0)-coalesce(s_qout,0)) tot_
                from tmp_stockgood_detail 
                where 
                s_id=$1 and r_id=$2 
                group by sg_code",
            array($rowid,$depot));
        for ($e=0;$e< count($result);$e++) {
            $array['sg_code'.$e]=$result[$e]['sg_code'];
            $array['sg_quantity'.$e]=$result[$e]['tot_'];
        }
        $array['row']=$e;
        return $array;
        
    }
}

?>
