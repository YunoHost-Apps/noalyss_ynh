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
 * \brief show the Grand Livre for analytic
 */
require_once NOALYSS_INCLUDE.'/class/anc_print.class.php';
require_once NOALYSS_INCLUDE.'/lib/impress.class.php';

class Anc_GrandLivre extends Anc_Print
{
    
    function set_sql_filter()
    {
        $sql="";
        $and=" and ";
        if ( $this->from != "" )
        {
            $sql.="$and oa_date >= to_date('".$this->from."','DD.MM.YYYY')";
        }
        if ( $this->to != "" )
        {
            $sql.=" $and oa_date <= to_date('".$this->to."','DD.MM.YYYY')";
        }

        return $sql;

    }
      /*!
     * \brief load the data from the database
     *
     * \return array
     */
    function load()
    {
      $filter_date=$this->set_sql_filter();
      $cond_poste='';
      if ($this->from_poste != "" )
            $cond_poste=" and upper(po_name) >= upper('".$this->from_poste."')";
        if ($this->to_poste != "" )
            $cond_poste.=" and upper(po_name) <= upper('".$this->to_poste."')";
        $pa_id_cond="";
        if ( isset ( $this->pa_id) && $this->pa_id !='')
            $pa_id_cond= "pa_id=".$this->pa_id." and";
        $array=$this->db->get_array("	select oa_id,
	po_name,
	oa_description,
	po_description,
	oa_debit,
	to_char(oa_date,'DD.MM.YYYY') as oa_date,
	oa_amount,
	oa_group,
	j_id ,
	jr_internal,
	jr_id,
	coalesce(jr_comment,b.oa_description) as jr_comment,
	case when j_poste is null and b.f_id is not null then
        (select ad_value from fiche_detail where fiche_detail.f_id=b.f_id and ad_id=".ATTR_DEF_ACCOUNT.")
            when j_poste is not null then
            j_poste
            end as j_poste
        ,
	coalesce(jrnx.f_id,b.f_id) as f_id,
        case when jrnx.f_id is not null then 
		 (select ad_value from fiche_Detail where f_id=jrnx.f_id and ad_id=23) 
		 when b.f_id is not null then
		 (select ad_value from fiche_Detail where f_id=b.f_id and ad_id=23)
	end
		 as qcode,
        jr_pj_number,
        jr_tech_per
	from operation_analytique as B join poste_analytique using(po_id)
	left join jrnx using (j_id)
	left join jrn on  (j_grpt=jr_grpt_id)
             where $pa_id_cond oa_amount <> 0.0  $cond_poste  $filter_date
	order by po_name,oa_date::date,qcode,j_poste");
        $this->has_data=count($array);
        return $array;
    }

	function load_csv()
    {
      $filter_date=$this->set_sql_filter();
      $cond_poste='';
      if ($this->from_poste != "" )
            $cond_poste=" and upper(po_name) >= upper('".$this->from_poste."')";
        if ($this->to_poste != "" )
            $cond_poste.=" and upper(po_name) <= upper('".$this->to_poste."')";
        $pa_id_cond="";
        if ( isset ( $this->pa_id) && $this->pa_id !='')
            $pa_id_cond= "pa_id=".$this->pa_id." and";
        $array=$this->db->get_array("	select
	po_name,
	to_char(oa_date,'DD.MM.YYYY') as oa_date,
	case when j_poste is null and b.f_id is not null then
        (select ad_value from fiche_detail where fiche_detail.f_id=b.f_id and ad_id=".ATTR_DEF_ACCOUNT.")
            when j_poste is not null then
            j_poste
            end as j_poste
        ,
        case when jrnx.f_id is not null then 
		 (select ad_value from fiche_Detail where f_id=jrnx.f_id and ad_id=23) 
		 when b.f_id is not null then
		 (select ad_value from fiche_Detail where f_id=b.f_id and ad_id=23)
	end
		 as qcode,
        coalesce(jr_comment,b.oa_description) as jr_comment,
        coalesce (jr_pj_number,'') as jr_pj_number,
	coalesce(jr_internal,'') as jr_internal,
        coalesce(oa_group,0) as oa_group,
	case when oa_debit='t' then oa_amount else  0 end as amount_deb,
	case when oa_debit='f' then oa_amount else  0 end as amount_cred,
        case when oa_debit='f' then 'C' else  'D' end as deb_cred
	from operation_analytique as B join poste_analytique using(po_id)
	left join jrnx using (j_id)
	left join jrn on  (j_grpt=jr_grpt_id)
             where $pa_id_cond oa_amount <> 0.0  $cond_poste $filter_date
	order by po_name,oa_date::date,qcode,j_poste");


        return $array;
    }
    /*!
     * \brief Show the button to export in PDF all the receipt
     * 
     * \param $p_string extra hidden value
     * \return string with the button
     */

    function button_export_pdf($p_string = "")
    {
        if (CONVERT_GIF_PDF <> 'NOT' && PDFTK <> 'NOT')
        {
            $r = "";
            $r.= HtmlInput::hidden("to", $this->to);
            $r.= HtmlInput::hidden("from", $this->from);
            $r.= HtmlInput::hidden("pa_id", $this->pa_id);
            $r.= HtmlInput::hidden("from_poste", $this->from_poste);
            $r.= HtmlInput::hidden("to_poste", $this->to_poste);
            $r.= HtmlInput::hidden("act","PDF:AncReceipt");

            $r.= $p_string;
            $r.= dossier::hidden();
            $r.=HtmlInput::submit('bt_receipt_anal_pdf', _("Export des pièces en PDF"));
        } 
        else 
        {
            
            $r = "";
            $msg = _("Les extensions CONVERT_GIF_PDF et PDFTK pour convertir en pdf ne sont pas installées ");
            $r = HtmlInput::button("bt_receipt_anal", 
                        _('Export des pièces en PDF'), 
                    sprintf('onclick="smoke.alert(\'%s\')"',$msg));
        }
        return $r;
    }
   /*!
     * \brief compute the html display
     *
     *
     * \return string
     */

    function display_html($p_with_ck=1)
   {
        $r = "";
        //---Html
        $array = $this->load();
        if (is_array($array) == false || empty($array))
        {
            return 0;
        }
        $r.= '<table class="result" style="width:100%">';
        $ix = 0;
        $prev = 'xx';
        $idx = 0;
        $tot_deb = $tot_cred = 0;

	bcscale(2);
        foreach ($array as $row)
        {
            if ($prev != $row['po_name'])
            {
                if ($ix > 0)
                {
                    $r.='<tr class="highlight">';
                    $tot_solde = bcsub($tot_cred, $tot_deb);
                    $sign = " ".(($tot_solde > 0) ? 'C' : 'D');
		    $r.=td('') . td('') . td('');
                    $r.=td('') . td('') . td('') . td('') . td('') . td(nbm($tot_deb), ' class="num"') . td(nbm($tot_cred), ' class="num"') . td(nbm($tot_solde) . $sign, ' class="num"');
                }
                $r.='<tr>' . '<td colspan="7" style="width:auto">' . '<h2>' . h($row['po_name'] . ' ' . $row['po_description']) . '</td></tr>';
                $r.= '<tr>' .
                        '<th>' . '</th>' .
                        '<th>' . _('Date') . '</th>' .
                        '<th>' . _('Poste') . '</th>' .
                        '<th>' . _('Quick_code') . '</th>' .
                        '<th>' . _('Libellé') . '</th>' .
                        '<th>' . '</th>' .
                        '<th>' . _('Pièce') . '</th>' .
                        '<th>' . _('Interne') . '</th>' .
                        '<th style="text-align:right">' . _('Débit') . '</th>' .
                        '<th style="text-align:right">' . _('Crédit') . '</th>' .
                        '<th style="text-align:right">' . _('Prog.') . '</th>' .
                        '</tr>';

                $tot_deb = $tot_cred = 0;
                $prev = $row['po_name'];
                $ix++;
            }
            $class = ($idx % 2 == 0) ? 'even' : 'odd';
            $idx++;
            // find out exercice
            $exercice="";
            if ( $row['jr_tech_per'] != null )
            {
                $periode=new Periode($this->db,$row['jr_tech_per']);
                $exercice=$periode->get_exercice();
            }
            $r.='<tr class="' . $class . '">';
            $detail = ($row['jr_id'] != null) ? HtmlInput::detail_op($row['jr_id'], $row['jr_internal']) : '';
            $post_detail = ($row['j_poste'] != null) ? HtmlInput::history_account($row['j_poste'], $row['j_poste'],"",$exercice) : '';
            $card_detail = ($row['f_id'] != null) ? HtmlInput::history_card($row['f_id'], $row['qcode'],"",$exercice) : '';
            $amount_deb = ($row['oa_debit'] == 't') ? $row['oa_amount'] : 0;
            $amount_cred = ($row['oa_debit'] == 'f') ? $row['oa_amount'] : 0;
            $tot_deb = bcadd($tot_deb, $amount_deb);
            $tot_cred = bcadd($tot_cred, $amount_cred);
            $tot_solde=bcsub($tot_cred,$tot_deb);

            /*
             * Checked button
             */
            $str_ck = "";
            $str_document = "";
            if ($row['jr_id'] != null && $p_with_ck==1)
            {
                /*
                 * Get receipt info  
                 */
                $str_document = HtmlInput::show_receipt_document($row['jr_id']);
                if ($str_document != "")
                {
                    $ck = new ICheckBox('ck[]', $row['jr_id']);
                    $str_ck = $ck->input();
                }
            }

            $r.=
                    '<td>' . $str_ck . '</td>' .
                    '<td>' . $row['oa_date'] . '</td>' .
                    td($post_detail) .
                    td($card_detail) .
                    td($row['jr_comment']) .
                    '<td>' . $str_document . '</td>' .
                    td($row['jr_pj_number']) .
                    '<td>' . $detail . '</td>' .
                    '<td class="num">' . nbm($amount_deb) . '</td>' .
                    '<td class="num">' . nbm($amount_cred). '</td>'.
                    '<td class="num">' . nbm($tot_solde). '</td>';
            $r.= '</tr>';
        }
        $r.='<tr class="highlight">';
        $tot_solde = bcsub($tot_cred, $tot_deb);
        $sign = ($tot_solde > 0) ? 'C' : 'D';
	$r.=td('') . td('') . td('');
        $r.=td('') . td('') . td('') . td('') . td('') . td(nbm($tot_deb), ' class="num"') . td(nbm($tot_cred), ' class="num"') . td(nbm($tot_solde) . $sign, '  class="num"');

        $r.= '</table>';
        return $r;
    }
      /*!
     * \brief Show the button to export in PDF or CSV
     * \param $url_csv url of the csv
     * \param $url_pdf url of the pdf
     * \param $p_string hidden data to include in the form
     *
     *
     * \return string with the button
     */
    function show_button($p_string="")
    {
        $r="";
        $r.= '<form method="GET" action="export.php"  style="display:inline">';
        $r.= HtmlInput::hidden("act","CSV:AncGrandLivre");
        $r.= HtmlInput::hidden("to",$this->to);
        $r.= HtmlInput::hidden("from",$this->from);
        $r.= HtmlInput::hidden("pa_id",$this->pa_id);
        $r.= HtmlInput::hidden("from_poste",$this->from_poste);
        $r.= HtmlInput::hidden("to_poste",$this->to_poste);
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.=HtmlInput::submit('bt_csv',"Export en CSV");
        $r.= '</form>';
        return $r;
    }
    function display_csv()
    {
        $r="";
        //---Html
        $array=$this->load_csv();
        if ( is_array($array) == false )
        {
            return $array;

        }

        $ix=0;$prev='xx';
	$tot_deb=$tot_cred=0;
        $aheader=array();
        $aheader[]=array("title"=>'Imp. Analytique','type'=>'string');
        $aheader[]=array("title"=>'Date','type'=>'string');
        $aheader[]=array("title"=>'Poste','type'=>'string');
        $aheader[]=array("title"=>'Quick_Code','type'=>'string');
        $aheader[]=array("title"=>'libelle','type'=>'string');
        $aheader[]=array("title"=>'Pièce','type'=>'string');
        $aheader[]=array("title"=>'Num.interne','type'=>'string');
        $aheader[]=array("title"=>'row','type'=>'string');
        $aheader[]=array("title"=>'Debit','type'=>'num');
        $aheader[]=array("title"=>'Credit','type'=>'num');
        $aheader[]=array("title"=>'D/C','type'=>'string');
        Impress::array_to_csv($array, $aheader,"export-anc-grandlivre");
    }
}
