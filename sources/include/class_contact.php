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
//!\brief class for the contact, contact are derived from fiche
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/user_common.php';
/*! \file
 * \brief Contact are a card which are own by a another card (customer, supplier...)
 */
/*!
 * \brief Class contact (customer, supplier...)
 */

class contact extends Fiche
{
    var $company; /*!< $company company of the contact (ad_id=ATTR_DEF_COMPANY)*/
    /*!\brief constructor */
    function contact($p_cn,$p_id=0)
    {
        $this->fiche_def_ref=FICHE_TYPE_CONTACT;
        parent::__construct($p_cn,$p_id) ;
        $this->company="";
    }
    /*!   Summary
     **************************************************
     * \brief  show the default screen
     *
     * \param  p_search (filter)
     *
     * \return string to display
     */
    function Summary($p_search="",$p_action="",$p_sql="",$p_nothing=false)
    {
        $p_search=sql_string($p_search);
        $extra_sql="";
        if ( $this->company != "")
        {
            $extra_sql="and f_id in (select f_id from fiche_detail
                       where ad_value=upper('".$this->company."') and ad_id=".ATTR_DEF_COMPANY.") ";
        }
        $url=urlencode($_SERVER['REQUEST_URI']);
        $script=$_SERVER['PHP_SELF'];
        // Creation of the nav bar
        // Get the max numberRow
        $all_contact=$this->count_by_modele($this->fiche_def_ref,$p_search,$extra_sql);
        // Get offset and page variable
        $offset=( isset ($_REQUEST['offset'] )) ?$_REQUEST['offset']:0;
        $page=(isset($_REQUEST['page']))?$_REQUEST['page']:1;
        $bar=navigation_bar($offset,$all_contact,$_SESSION['g_pagesize'],$page);
        // set a filter ?
        $search="";
        if ( trim($p_search) != "" )
        {
            $search=" and f_id in
                    (select f_id from fiche_Detail
                    where
                    ad_id=1 and ad_value ~* '$p_search') ";
        }
        // Get The result Array
        $step_contact=$this->get_by_category($offset,$search.$extra_sql.$p_sql);

		if ( $all_contact == 0 ) return "";
        $r=$bar;
        $r.='<table id="contact_tb" class="sortable">
            <TR>
            <th>Quick Code</th>
            <th>Nom</th>
            <th>Prénom</th>
			<th>Société</th>
            <th>Téléphone</th>
            <th>email</th>
            <th>Fax</th>
            </TR>';
        $base=$_SERVER['PHP_SELF'];
        // Compute the url
        $url="";
        $and="?";
        $get=$_GET;
        if ( isset ($get) )
        {
            foreach ($get as $name=>$value )
            {
                // we clean the parameter offset, step, page and size
                if (  ! in_array($name,array('f_id','detail')))
                {
                    $url.=$and.$name."=".$value;
                    $and="&";
                }// if
            }//foreach
        }// if
        $back_url=urlencode($_SERVER['REQUEST_URI']);
        if ( sizeof ($step_contact ) == 0 )
            return $r;
        $idx=0;
        foreach ($step_contact as $contact )
        {
            $l_company=new Fiche($this->cn);
            $l_company->get_by_qcode($contact->strAttribut(ATTR_DEF_COMPANY),false);
            $l_company_name=$l_company->strAttribut(ATTR_DEF_NAME);
            if ( $l_company_name == NOTFOUND ) $l_company_name="";
            // add popup for detail
            if ( $l_company_name !="")
            {
				$l_company_name=HtmlInput::card_detail($contact->strAttribut(ATTR_DEF_COMPANY),$l_company_name,'style="text-decoration:underline;"');
            }
            $tr=($idx%2==0)?' <tr class="odd">':'<tr class="even">';
            $idx++;
            $r.=$tr;
            $qcode=$contact->strAttribut(ATTR_DEF_QUICKCODE);
            $r.='<TD>'.HtmlInput::card_detail($qcode)."</TD>";
            $r.="<TD>".$contact->strAttribut(ATTR_DEF_NAME)."</TD>";
            $r.="<TD>".$contact->strAttribut(ATTR_DEF_FIRST_NAME)."</TD>";
            $r.="<TD>".$l_company_name."</TD>";
            $r.="<TD>".$contact->strAttribut(ATTR_DEF_TEL)."</TD>";
            $r.="<TD>".$contact->strAttribut(ATTR_DEF_EMAIL)."</TD>".
                "<TD> ".$contact->strAttribut(ATTR_DEF_FAX)."</TD>";

            $r.="</TR>";

        }
        $r.="</TABLE>";
        $r.=$bar;
        return $r;
    }

}
