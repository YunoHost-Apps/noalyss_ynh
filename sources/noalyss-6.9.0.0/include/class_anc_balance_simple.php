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
  \brief manage the simple balance for CA, inherit from balance_ca
 */

require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once  NOALYSS_INCLUDE.'/class_anc_print.php';
require_once  NOALYSS_INCLUDE.'/class_anc_plan.php';
require_once  NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_pdf.php';
require_once  NOALYSS_INCLUDE.'/header_print.php';
/*! \brief manage the simple balance for CA, inherit from balance_ca
 *
 */

class Anc_Balance_Simple extends Anc_Print
{

    /*!
     * \brief load the data from the database
     *
     * \return array
     */
    function load()
    {
        $filter=$this->set_sql_filter();
        // sum debit

        $sql="select m.po_id,sum(deb) as sum_deb,sum(cred) as sum_cred,";
        $sql.=" po_name||'  '||coalesce(po_description,'') as po_name";
        $sql.=" from ";
        $sql.=" (select po_id,case when oa_debit='t' then oa_amount else 0 end as deb,";
        $sql.="case when oa_debit='f' then oa_amount else 0 end as cred ";
        $sql.=" from operation_analytique join poste_analytique using(po_id)";
        $sql.=(empty($filter) == false)?" where ".$filter:"";
        $sql.=" ) as m join poste_analytique using (po_id)";
        $sql.=" where pa_id=".$this->pa_id;
        $sql.=" group by po_id,po_name,po_description";
        $sql.=" order by po_id";
        $res=$this->db->exec_sql($sql);

        if ( Database::num_row($res) == 0 ) {
            $this->has_data=0;
            return null;
        }
        $a=array();
        $count=0;
        $array=Database::fetch_all($res);
        foreach ($array as $row)
        {
            $a[$count]['po_id']=$row['po_id'];
            $a[$count]['sum_deb']=$row['sum_deb'];
            $a[$count]['sum_cred']=$row['sum_cred'];
            $a[$count]['po_name']=$row['po_name'];
            $a[$count]['solde']=abs($row['sum_deb']-$row['sum_cred']);
            $a[$count]['debit']=($row['sum_deb']>$row['sum_cred'])?"debit":"credit";
            $count++;
        }
        $this->has_data=$count;
        return $a;


    }
    /*!
     * \brief Set the filter (account_date)
     *
     * \return return the string to add to load
     */


    function set_sql_filter()
    {
        $sql="";
        $and="";
        if ( $this->from != "" )
        {
            $sql.=" oa_date >= to_date('".$this->from."','DD.MM.YYYY')";
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
    /*!
     * \brief compute the html display
     *
     *
     * \return string
     */
    function display_html()
    {
        $r="<table class=\"result\">";
        $r.="<tr>";
        $r.="<th>Poste comptable Analytique</th>";
        $r.="<th>D&eacute;bit</th>";
        $r.="<th>Cr&eacute;dit</th>";
        $r.="<th>Solde</th>";
        $r.="<th>D/C</th>";
        $r.="</tr>";

        $array=$this->load();
        $odd=0;
        if ( is_array($array) == false )
        {
            return $array;

        }
        foreach ( $array as $row)
        {
            $odd++;

            $r.=($odd%2==0)?'<tr class="odd">':'<tr class="even">';
            // the name and po_id
            //	  $r.=sprintf("<td>%s</td>",$row['po_id']);
            $r.=sprintf("<td align=\"left\">%s</td>",h($row['po_name']));
            $r.=td(nbm($row['sum_deb']),' class="num"');
            $r.=td(nbm($row['sum_cred']),' class="num"');
            $r.=td(nbm($row['solde']),' class="num"');
            $deb=($row['sum_deb'] > $row['sum_cred'])?"D":"C";
            $deb=($row['solde'] == 0 )?'':$deb;
            $r.=sprintf("<td>%s</td>",$deb);
            $r.="</tr>";
        }
        $r.="</table>";
        return $r;
    }
    /*!
     * \brief Compute  the form to display
     * \param $p_hidden hidden tag to be included (gDossier,...)
     *
     *
     * \return string containing the data
     */
    function display_form($p_string="")
    {
        $r=parent::display_form($p_string);

        $r.= HtmlInput::submit('Affiche', _('Rechercher'));

        return $r;
    }

    /*!
     * \brief Display the result in pdf
     *
     * \return none
     */
    function display_pdf()
    {
        $array=$this->load();
        $pdf=new PDFBalance_Simple($this->db);
        $pdf->set_info($this->from_poste,$this->to_poste,$this->from,$this->to);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->setTitle("Balance analytique",true);

        $pdf->SetFont('DejaVu','',6);
        for ($i=0;$i<count($array);$i++)
        {
            $row=$array[$i];
            $pdf->Cell(20,6,$row['po_id'],0,0,'L');
            $pdf->Cell(90,6,$row['po_name'],0,0,'L');
            $pdf->Cell(20,6,sprintf('%s',nbm($row['sum_deb'])),0,0,'R');
            $pdf->Cell(20,6,sprintf('%s',nbm($row['sum_cred'])),0,0,'R');
            $pdf->Cell(20,6,sprintf('%s',nbm($row['solde'])),0,0,'R');
            $pdf->Cell(20,6,$row['debit'],0,0,'R');
            $pdf->Ln();
        }
        $fDate=date('dmy-Hi');
        $pdf->output('simple-balance-'.$fDate.'.pdf','D');

    }
    /*!
     * \brief Compute the csv export
     * \return string with the csv
     */
    function display_csv()
    {
        $array=$this->load();
        if ( is_array($array) == false )
        {
            return $array;

        }
        $r="";
        foreach ( $array as $row)
        {
            // the name and po_id
            $solde=($row['sum_cred']>$row['sum_deb'])?'C':'D';
            $solde=($row['sum_cred']==$row['sum_deb'])?'':$solde;
            $r.=sprintf("'%s';",$row['po_id']);
            $r.=sprintf("'%s';",$row['po_name']);
            $r.=sprintf("%s;",nb($row['sum_deb']));
            $r.=sprintf("%s;",nb($row['sum_cred']));
            $r.=sprintf("%s;",nb($row['solde']));
            $r.=sprintf("'%s'",$row['debit']);
            $r.="\r\n";
        }
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
        $r.= '<form method="GET" action="export.php" style="display:inline">';
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.= HtmlInput::hidden("to",$this->to);
        $r.= HtmlInput::hidden("act","PDF:AncBalSimple");

        $r.= HtmlInput::hidden("from",$this->from);
        $r.= HtmlInput::hidden("pa_id",$this->pa_id);
        $r.= HtmlInput::hidden("from_poste",$this->from_poste);
        $r.= HtmlInput::hidden("to_poste",$this->to_poste);
        $r.=HtmlInput::submit('bt_pdf',"Export en PDF");
        $r.= '</form>';

        $r.= '<form method="GET" action="export.php"  style="display:inline">';
        $r.= HtmlInput::hidden("act","CSV:AncBalSimple");
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

    /*!
     * \brief for testing and debuggind the class
     *        it must never be called from production system, it is
     *        intended only for developpers
     * \param
     * \param
     * \param
     *
     *
     * \return none
     */
    static  function test_me ()
    {
        // call the page with ?gDossier=14
        $a=new Database(dossier::id());

        $bal=new Anc_Balance_Simple($a);
        $bal->get_request();

        echo '<form method="GET">';

        echo $bal->display_form();
        echo '</form>';
        if ( isset($_GET['result']))
        {
            echo $bal->show_button("","");
            echo "<h1>HTML</h1>";
            echo $bal->display_html();
            echo "<h1>CSV</h1>";
            echo $bal->display_csv();
            /* 	echo "<h1>pdf</h1>"; */
            /* 	echo $bal->display_pdf(); */

        }

    }
}
