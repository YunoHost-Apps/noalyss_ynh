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
 * \brief Html Input
 */
/*! \brief          Generate the form for the periode
* Data Members
 *   - $cn connexion to the current folder
 *   - $type the type of the periode OPEN CLOSE NOTCENTRALIZED or ALL, IT MUST BE SET
 *   - $filter_year make a filter on the default exercice by default true
 *   - $user if a filter_year is required then we need who is the user (object User)
 *   - $show_end_date; $show_end_date is not set or false, do not show the end date  default = true
 *   - $show_start_date; $show_start_date is not set or false, do not show the start date  default=true
*/
require_once NOALYSS_INCLUDE.'/class_html_input.php';
class IPeriod extends HtmlInput
{
    var $type; /*!< $type the type of the periode OPEN CLOSE NOTCENTRALIZED or ALL */
    var $cn;  /*!< $cn is the database connection */
    var $show_end_date; /*!< $show_end_date is not set or false, do not show the end date */
    var $show_start_date; /*!< $show_start_date is not set or false, do not show the start date */
    var $filter_year; /*!< $filter_year make a filter on the default exercice by default yes */
    var $user;  /*! $user if a filter is required then we need who is the user (object User)*/
    function __construct($p_name="",$p_value="",$p_exercice='')
    {
        $this->name=$p_name;
        $this->readOnly=false;
        $this->size=20;
        $this->width=50;
        $this->heigh=20;
        $this->value=$p_value;
        $this->selected="";
        $this->table=0;
        $this->disabled=false;
        $this->javascript="";
        $this->extra2="all";
        $this->show_start_date=true;
        $this->show_end_date=true;
		$this->exercice=$p_exercice;
    }
    /*!
     * \brief show the input html for a periode
     *\param $p_name is the name of the widget
     *\param $p_value is the default value
     *\param $p_exercice is the exercice, if not set then the user preference is used
     * \return string containing html code for the HTML
     *
     *
     */
    public function input($p_name=null,$p_value=null)
    {
        foreach (array('type','cn') as $a)
        {
            if ( ! isset ($this->$a) ) throw new Exception('Variable non définie [ '.$a.']');
        }
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();

        switch ($this->type)
        {
        case CLOSED:
            $sql_closed="where p_closed=true and p_central = false ";
            break;
        case OPEN:
            $sql_closed="where p_closed=false";
            break;
        case NOTCENTRALIZED:
            $sql_closed="where p_closed=true and p_central = false ";
            break;
        case ALL:
            $sql_closed="";
            break;
        default:
            throw new Exception("invalide p_type in ".__FILE__.':'.__LINE__);
        }
        $sql="select p_id,to_char(p_start,'DD.MM.YYYY') as p_start_string,
             to_char(p_end,'DD.MM.YYYY') as p_end_string
             from parm_periode
             $sql_closed ";

	$cond="";


        /* Create a filter on the current exercice */
        if ( ! isset($this->filter_year) || (isset($this->filter_year) && $this->filter_year==true))
        {
	  if ( $this->exercice=='')
	    {
	      if (! isset($this->user) ) throw new Exception (__FILE__.':'.__LINE__.' user is not set');
	      $this->exercice=$this->user->get_exercice();
	    }

            $cond='';
	    if ( $sql_closed=="") $and=" where " ; else $and=" and ";
            if ($this->type == 'all' ) $cond=$and.'   true ';
            $cond.=" $and p_exercice='".sql_string($this->exercice)."'";
        }

        $sql.=$cond."  order by p_start,p_end";

        $Res=$this->cn->exec_sql($sql);
        $Max=$this->cn->size($Res);
        if ( $Max == 0 )  throw new Exception(_('Aucune periode trouvée'),1);
        $ret='<SELECT NAME="'.$this->name.'" '.$this->javascript.'>';
        for ( $i = 0; $i < $Max;$i++)
        {
            $l_line=$this->cn->fetch($i);
            if ( $this->value==$l_line['p_id'] )
                $sel="SELECTED";
            else
                $sel="";

            if ( $this->show_start_date == true && $this->show_end_date==true )
            {
                $ret.=sprintf('<OPTION VALUE="%s" %s>%s - %s',$l_line['p_id']
                              ,$sel
                              ,$l_line['p_start_string']
                              ,$l_line['p_end_string']);
            }
            else if ($this->show_start_date == true )
            {
                $ret.=sprintf('<OPTION VALUE="%s" %s>%s ',$l_line['p_id']
                              ,$sel
                              ,$l_line['p_start_string']
                             );
            }
            else if ( $this->show_end_date == true )
            {
                $ret.=sprintf('<OPTION VALUE="%s" %s>%s ',$l_line['p_id']
                              ,$sel
                              ,$l_line['p_end_string']
                             );
            }

        }
        $ret.="</SELECT>";
        return $ret;


    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r="not implemented ".__FILE__.":".__LINE__;
        return $r;

    }
    static public function test_me()
    {
    }
}
