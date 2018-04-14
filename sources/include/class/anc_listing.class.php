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
 * \brief definition of Anc_Listing
 */

require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once  NOALYSS_INCLUDE.'/class/anc_plan.class.php';
require_once  NOALYSS_INCLUDE.'/class/anc_print.class.php';
require_once  NOALYSS_INCLUDE.'/class/anc_operation.class.php';
/*!
 * \brief manage the CA listing
 *
 * \return
 */

class Anc_Listing extends Anc_Print
{
    function display_form($p_string="")
    {
        echo '<form method="get">';
        $r=parent::display_form($p_string);
        $r.=HtmlInput::submit('result', _('Rechercher'));
        $r.= '</form>';
        return $r;

    }
    /*!
     * \brief complete the object with the data in $_REQUEST
     */

    function get_request()
    {
        parent::get_request();
        $this->pa_id=(isset($_REQUEST['pa_id']))?$_REQUEST['pa_id']:"";
    }
    /*!
     * \brief compute the html display
     *
     *
     * \return string
     */

    function display_html()
    {
        $idx=0;
        $r="";
        //---Html
        $array=$this->load();
        if ( is_array($array) == false ||  empty($array) )
        {
            return 0;
        }
        $cred=0;$deb=0;
        bcscale(2);
        $r.= '<table class="result" style="width=100%">';
        $r.= '<tr>'.
             '<th>'._('Date').'</th>'.
             '<th>'._('Poste').'</th>'.
             '<th>'._('Quick_code').'</th>'.
             '<th>'._('Analytique').'</th>'.
	  th(_('Description')).
             '<th>'._('libelle').'</th>'.
             '<th>'._('Num.interne').'</th>'.
             '<th>'._('Montant').'</th>'.
             '<th>'._('D/C').'</th>'.
             '</tr>';
        foreach ( $array as $row )
        {
            $class=($idx%2==0)?'even':'odd';
            $idx++;
            $r.= '<tr class="'.$class.'">';
	    $detail=($row['jr_id'] != null)?HtmlInput::detail_op($row['jr_id'],$row['jr_internal']):'';
	    $post_detail=($row['j_poste'] != null)?HtmlInput::history_account($row['j_poste'],$row['j_poste']):'';
	    $card_detail=($row['f_id'] != null)?HtmlInput::history_card($row['f_id'],$row['qcode']):'';

            $r.=
                '<td>'.$row['oa_date'].'</td>'.
	      td($post_detail).
	      td($card_detail).
	      '<td>'.HtmlInput::history_anc_account($row['po_id'],h($row['po_name'])).'</td>'.
	      '<td>'.h($row['oa_description']).'</td>'.
	      td($row['jr_comment']).
	      '<td>'.$detail.'</td>'.
	      '<td class="num">'.nbm($row['oa_amount']).'</td>'.
                '<td>'.(($row['oa_debit']=='f')?'C':'D').'</td>';
            $r.= '</tr>';
            if ( $row['oa_debit'] == 'f') {$cred=bcadd($cred,$row['oa_amount']);}
            if ( $row['oa_debit'] == 't') {$deb=bcadd($deb,$row['oa_amount']);}
        }
        
        $r.= '</table>';
        ob_start();
        echo _("Total");
        echo '<ol style="list-style:none">';
        echo '<li>'.nbm($deb).' D '.'</li>';
        echo '<li>'.nbm($cred).' C '.'</li>';
        echo '<li>';
        echo _('Solde');
        $solde=abs(bcsub($deb,$cred));
        echo $solde;
        if ( $cred == $deb ) echo " = ";
        else if ( $cred > $deb ) echo " C ";
        else echo ' D ';

        $r_solde=ob_get_clean();
        
        return $r.$r_solde;
    }
    /*!
     * \brief load the data from the database
     *
     * \return array
     */
    function load()
    {
        $op=new Anc_Operation ($this->db);
        $op->pa_id=$this->pa_id;
        $array=$op->get_list($this->from,$this->to,$this->from_poste,$this->to_poste);
        if (! $array ) 
        {
            $this->has_data=0;
        }
        else 
        {
            $this->has_data=count($array);
        }
        return $array;
    }
    /*!
     * \brief Compute the csv export
     * \return string with the csv
     */

    function display_csv()
    {
        $array=$this->load($this->from,$this->to,$this->from_poste,$this->to_poste);
        if ( empty($array) == true )
        {
            return $array;

        }
        $csv=new Noalyss_Csv("anc_listing");
        $csv->send_header();
        $csv->write_header(array("Date","Poste","QuickCode",
            "Activité","description","montant","d/c"));
        foreach ( $array as $row)
        {
            // the name and po_id
            $csv->add($row['oa_date']);
            $csv->add($row['j_poste']);
            $csv->add($row['qcode']);
            $csv->add($row['po_name']);
            $csv->add($row['oa_description']);
            $csv->add($row['oa_amount'],"number");
            $csv->add(sprintf("'%s'",(($row['oa_debit']=='f')?'CREDIT':'DEBIT')));
            $csv->write();
        }
        return;

    }

    /*!
     * \brief show the export button to pdf and CSV
     * \param $p_string string containing some HTML tag as hidden field
     * \param
     * \param
     *
     *
     * \return string containing the html code
     */
    function show_button($p_string='')
    {
        $r="";
        $submit=HtmlInput::submit('','');
        $hidden=new IHidden();
        /* for the export in PDF
         * Not yet needed, the html print should be enough
        $r.= '<form method="GET" action="ca_list_pdf.php" style="display:inline">';
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.= $hidden->input("to",$this->to);
        $r.= $hidden->input("from",$this->from);
        $r.= $hidden->input("pa_id",$this->pa_id);
        $r.= $hidden->input("from_poste",$this->from_poste);
        $r.= $hidden->input("to_poste",$this->to_poste);
        $r.=HtmlInput::submit('bt_pdf',"Export en PDF");
        $r.= '</form>';
        */

        $r.= '<form method="GET" action="export.php"  style="display:inline">';
        $r.= HtmlInput::hidden("to",$this->to);
        $r.= HtmlInput::hidden("from",$this->from);
        $r.= HtmlInput::hidden("pa_id",$this->pa_id);
        $r.= HtmlInput::hidden("from_poste",$this->from_poste);
        $r.= HtmlInput::hidden("to_poste",$this->to_poste);
	$r.=HtmlInput::hidden('act','CSV:AncList');
        $r.=HtmlInput::hidden('ac',$_REQUEST['ac']);
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.=HtmlInput::submit('bt_csv',"Export en CSV");
        $r.= '</form>';
        return $r;

    }
    /*!
     * \brief debugging and test function for dev. only
     * \param
     * \param
     * \param
     *
     *
     * \return
     */
    static  function test_me()
    {
    }
}
