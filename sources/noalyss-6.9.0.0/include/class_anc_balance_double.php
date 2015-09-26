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
 * \brief
 *  Print the crossed balance between 2 plan
 */

/*! \brief
 *  Print the crossed balance between 2 plan
 *
 */
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once  NOALYSS_INCLUDE.'/class_anc_print.php';
require_once  NOALYSS_INCLUDE.'/class_anc_plan.php';
require_once NOALYSS_INCLUDE.'/class_pdf.php';

class Anc_Balance_Double extends Anc_Print
{
    /*!
     * \brief compute the html display
     *
     *
     * \return string
     */

    function display_html ()
    {
        $r="";

        $array=$this->load();
        $odd=0;
        if ( is_array($array) == false )
        {
            return $array;

        }
        $old="";
        $tot_deb=0;
        $tot_cred=0;

        foreach ( $array as $row)
        {
            $odd++;

            $r.=($odd%2==0)?'<tr class="odd">':'<tr class="even">';

            if ( $old == $row['a_po_name'] )
            {
                $r.='<td></td>';
            }
            else
            {

                if ( $tot_deb != 0 || $tot_cred !=0 )
                {
		  $r.="<tr>".td('');
		  $r.="<td>Total </td>".td(nbm($tot_deb),' class="num"').td(nbm($tot_cred),' class="num"');
                    $s=abs($tot_deb-$tot_cred);
                    $d=($tot_deb>$tot_cred)?'debit':'credit';
                    $r.="<td class=\"num\">".nbm($s)."</td><td>$d</td>";
                    $r.="</tr>";
                }
                $tot_deb=0;
                $tot_cred=0;

                // new
                $r.="</table>";
                $r.="<table class=\"result\" style=\"margin-bottom:3px\">";
                $r.="<tr>";
                $r.="<th style=\"width:30%\" >Poste comptable Analytique</th>";
                $r.="<th style=\"width:30%\">Poste comptable Analytique</th>";
                $r.="<th style=\"text-align:right\">D&eacute;bit</th>";
                $r.="<th style=\"text-align:right\">Cr&eacute;dit</th>";
                $r.="<th style=\"text-align:right\">Solde</th>";
                $r.="<th>D/C</th>";
                $r.="</tr>";
		$r.='<tr>';
                $r.=td($row['a_po_name'].' '.$row['a_po_description']);
                $old=$row['a_po_name'];
		$r.= '</tr>';
		$r.= '<tr>';
		$r.=td('');
            }
            $tot_deb+=$row['a_d'];
            $tot_cred+=$row['a_c'];

	    $r.=td($row['b_po_name']." ".$row['b_po_description']);

            $r.=td(nbm($row['a_d']),' class="num"');
            $r.=td(nbm($row['a_c']),' class="num"');
            $r.=td(nbm($row['a_solde']),' class="num"');
            $r.=sprintf("<td>%s</td>",$row['a_debit']);
            $r.="</tr>";
        } /* end loop */

        if ( $tot_deb != 0 || $tot_cred !=0 )
        {
	  $r.="<tr>".td('');
            $r.="<td>Total </td> <td ' class=\"num\"> ".nbm($tot_deb)." </td> <td ' class=\"num\">".nbm($tot_cred)."</td>";
            $s=abs($tot_deb-$tot_cred);
            $d=($tot_deb>$tot_cred)?'debit':'credit';
            $r.=td(nbm($s),' class="num"')."<td>$d</td>";
            $r.="</tr>";
        }

        $r.="</table>";
	$r.=h2info('Résumé');
        $r.='<table class="result">';
	$r.='<tr>';
	$r.=th('Po').
	  th('Nom').
	  th('Débit',' style="text-align:right"').
	  th('Crédit','style="text-align:right" ').
	  th('Solde',' style="text-align:right"');

        $sum=$this->show_sum($array);
	$tot_cred=0;$tot_deb=0;
        foreach ($sum as $row)
        {
            $r.='<tr>';
            $r.='<td>'.$row['poste'].'</td>';
            $r.='<td>'.$row['desc'].'</td>';
            $r.='<td class="num">'.nbm($row['debit']).'</td>';
            $r.='<td class="num">'.nbm($row['credit']).'</td>';
	    $diff=bcsub($row['debit'],$row['credit']);
	    $tot_cred=bcadd($tot_cred,$row['credit']);
	    $tot_deb=bcadd($tot_deb,$row['debit']);

	    $r.=td(nbm($diff),' class="num" ');

            $r.='<td>'.$row['dc'].'</td>';
            $r.='</tr>';
        }
	$r.=td('');
	$r.=td('total');
	$r.=td(nbm($tot_deb),'class="num"');
	$r.=td(nbm($tot_cred),'class="num"');
	$solde=bcsub($tot_deb,$tot_cred);
	$sign=($tot_cred<$tot_deb)?" - ":" + ";
	$r.=td($sign.nbm($solde),'class="num" style="border:solid 1px blue;font-weight:bold"');
	$r.='</tr>';
        $r.='</table>';

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
        if (empty($array))return;
        $pdf=new PDF($this->db);
        $pdf->Setdossierinfo(dossier::name());
        $pdf->setTitle("Balance analytique",true);
        $pdf->SetAuthor('NOALYSS');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pa=new Anc_Plan($this->db,$this->pa_id);
        $pa->get();
        $pb=new Anc_Plan($this->db,$this->pa_id2);
        $pb->get();
        $pdf->SetFont('DejaVu','B',9);
        $pdf->Cell(0,7,sprintf("Balance croise plan %s %s ",
                               $pa->name,
                               $pb->name),1,0,'C');
        $filtre_date="";
        $filtre_pa="";
        $filtre_pb="";

        if ( $this->from !="" ||$this->to !="")
            $filtre_date=sprintf("Filtre date  %s %s",
                                 $this->from,
                                 $this->to);
        if ( $this->from_poste !="" ||$this->to_poste !="")
            $filtre_pa=sprintf("Filtre poste plan1  %s %s",
                               ($this->from_poste!="")?"de ".$this->from_poste:" ",
                               ($this->to_poste!="")?"jusque ".$this->to_poste:"");

        if ( $this->from_poste2 !="" ||$this->to_poste2 !="")
            $filtre_pb=sprintf("Filtre poste plan2   %s  %s",
                               ($this->from_poste2!="")?"de ".$this->from_poste2:" ",
                               ($this->to_poste2!="")?"jusque ".$this->to_poste2:"");

        $pdf->SetFont('DejaVu','',8);
        $pdf->Cell(50,7,$filtre_date);
        $pdf->Cell(50,7,$filtre_pa);
        $pdf->Cell(50,7,$filtre_pb);
        $pdf->Ln();

        $pdf->SetFont('DejaVu','',6);
        $pdf->Cell(20,7,'id','B');
        $pdf->Cell(100,7,'Poste Comptable','B');
        $pdf->Cell(20,7,'Débit','B',0,'L');
        $pdf->Cell(20,7,'Crédit','B',0,'L');
        $pdf->Cell(20,7,'Solde','B',0,'L');
        $pdf->Cell(20,7,'D/C','B',0,'L');
        $pdf->Ln();

        for ($i=0;$i<count($array);$i++)
        {
            $row=$array[$i];
            $pdf->Cell(20,6,$row['a_po_name'],0,0,'L');
            $pdf->Cell(40,6,mb_substr($row['a_po_description'],0,31),0,0,'L');
            $pdf->Cell(20,6,$row['b_po_name'],0,0,'L');
            $pdf->Cell(40,6,mb_substr($row['b_po_description'],0,31),0,0,'L');
            $pdf->Cell(20,6,$row['a_d'],0,0,'R');
            $pdf->Cell(20,6,$row['a_c'],0,0,'R');
            $pdf->Cell(20,6,$row['a_solde'],0,0,'R');
            $pdf->Cell(20,6,$row['a_debit'],0,0,'C');
            $pdf->Ln();
        }

        $sum=$this->show_sum($array);
        $pdf->SetFont('DejaVu','B',8);
        $pdf->Cell(70,6,'Somme',1,0,'C');
        $pdf->Ln(5);
        $pdf->SetFont('DejaVu','',6);

        $pdf->Cell(20,7,'Poste');
        $pdf->Cell(60,7,'Description','B');
        $pdf->Cell(20,7,'Débit','B',0,'L');
        $pdf->Cell(20,7,'Crédit','B',0,'L');
        $pdf->Cell(20,7,'Solde','B',0,'L');
        $pdf->Cell(20,7,'D/C','B',0,'L');
        $pdf->Ln();

        for ($i=0;$i<count($sum);$i++)
        {
            $row=$sum[$i];
            $pdf->Cell(20,6,$row['poste'],0,0,'L');
            $pdf->Cell(60,6,$row['desc'],0,0,'L');
            $pdf->Cell(20,6,sprintf('%.2f',$row['debit']),0,0,'R');
            $pdf->Cell(20,6,sprintf('%.2f',$row['credit']),0,0,'R');
            $pdf->Cell(20,6,sprintf('%.2f',$row['solde']),0,0,'R');
            $pdf->Cell(20,6,$row['dc'],0,0,'R');
            $pdf->Ln();
        }
        $fDate=date('dmy-Hi');
        $pdf->output('crossbalance-'.$fDate.'.pdf','D');
    }


    /*!
     * \brief Compute the csv export
     * \return string with the csv
     */
    function display_csv()
    {
        $r="";

        $r.='"Poste comptable Analytique";';
        $r.='"Poste comptable Analytique";';
        $r.='"Debit";';
        $r.='"Credit";';
        $r.='"Solde";';
        $r.='"D/C"';

        $r.="\r\n";

        $array=$this->load();
        if ( is_array($array) == false )
        {
            return $array;

        }
        foreach ( $array as $row)
        {

            $r.=sprintf('"%s";',$row['a_po_name']);
            $r.=sprintf('"%s";',$row['b_po_name']);
            $r.=sprintf("%s;",nb($row['a_d']));
            $r.=sprintf("%s;",nb($row['a_c']));
            $r.=sprintf("%s;",nb($row['a_solde']));
            $r.=sprintf('"%s"',$row['a_debit']);
            $r.="\r\n";
        }

        return $r;

    }
    /*!
     * \brief Compute  the form to display
     * \param $p_hidden hidden tag to be included (gDossier,...)
     *
     *
     * \return string containing the data
     */
    function display_form($p_string='')
    {
        $r=parent::display_form($p_string);
        // show the second plan
        $r.='<span style="padding:5px;margin:5px;border:2px double  blue;display:block;">';
        $plan=new Anc_Plan($this->db);
        $plan_id=new ISelect("pa_id2");
        $plan_id->value=$this->db->make_array("select pa_id, pa_name from plan_analytique order by pa_name");
        $plan_id->selected=$this->pa_id2;
        $r.= "Plan Analytique :".$plan_id->input();
        $r.=HtmlInput::request_to_hidden(array('ac'));
        $poste=new IText();
        $poste->size=10;
        $r.="Entre le poste ".$poste->input("from_poste2",$this->from_poste2);
        $choose=new IButton();
        $choose->name="Choix Poste";
        $choose->label=_("Recherche");
        $choose->javascript="onClick=search_ca(".dossier::id().",'from_poste2','pa_id2')";
        $r.=$choose->input();

        $r.=" et le poste ".$poste->input("to_poste2",$this->to_poste2);
        $choose->javascript="onClick=search_ca(".dossier::id().",'to_poste2','pa_id2')";
        $r.=$choose->input();
        $r.='<span class="notice" style="display:block">'.
            _('Selectionnez le plan qui vous int&eacute;resse avant de cliquer sur Recherche').
            '</span>';

        $r.='</span>';
        $r.=HtmlInput::submit('Affiche', _('Rechercher'));
        return $r;
    }
    /*!
     * \brief Show the button to export in PDF or CSV
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
        $r.= HtmlInput::hidden("to",$this->to);
        $r.= HtmlInput::hidden("act","PDF:AncBalDouble");
        $r.= HtmlInput::hidden("from",$this->from);
        $r.= HtmlInput::hidden("pa_id",$this->pa_id);
        $r.= HtmlInput::hidden("from_poste",$this->from_poste);
        $r.= HtmlInput::hidden("to_poste",$this->to_poste);
        $r.= HtmlInput::hidden("pa_id2",$this->pa_id2);
        $r.= HtmlInput::hidden("from_poste2",$this->from_poste2);
        $r.= HtmlInput::hidden("to_poste2",$this->to_poste2);
        $r.=dossier::hidden();
        $r.=HtmlInput::submit('bt_pdf',"Export en PDF");
        $r.= '</form>';

        $r.= '<form method="GET" action="export.php"  style="display:inline">';
        $r.= HtmlInput::hidden("to",$this->to);
        $r.= HtmlInput::hidden("act","CSV:AncBalDouble");
        $r.= HtmlInput::hidden("from",$this->from);
        $r.= HtmlInput::hidden("pa_id",$this->pa_id);
        $r.= HtmlInput::hidden("from_poste",$this->from_poste);
        $r.= HtmlInput::hidden("to_poste",$this->to_poste);
        $r.= HtmlInput::hidden("pa_id2",$this->pa_id2);
        $r.= HtmlInput::hidden("from_poste2",$this->from_poste2);
        $r.= HtmlInput::hidden("to_poste2",$this->to_poste2);
        $r.= $p_string;
        $r.= dossier::hidden();
        $r.=HtmlInput::submit('bt_csv',"Export en CSV");
        $r.= '</form>';
        return $r;

    }
    /*!
     * \brief complete the object with the data in $_REQUEST
     */
    function get_request()
    {
        parent::get_request();
        $this->from_poste2=(isset($_REQUEST['from_poste2']))?$_REQUEST['from_poste2']:"";
        $this->to_poste2=(isset($_REQUEST['to_poste2']))?$_REQUEST['to_poste2']:"";
        $this->pa_id2=(isset($_REQUEST['pa_id2']))?$_REQUEST['pa_id2']:"";

    }
    /*!
     * \brief load the data from the database
     *
     * \return array
     */
    function load()
    {
        $filter_poste="";
        $and="";
        if ( $this->from_poste != "" )
        {
            $filter_poste.=" $and upper(pa.po_name)>= upper('".Database::escape_string($this->from_poste)."')";
            $and=" and ";

        }
        if ( $this->to_poste != "" )
        {
            $filter_poste.=" $and upper(pa.po_name)<= upper('".Database::escape_string($this->to_poste)."')";
            $and=" and ";
        }

        if ( $this->from_poste2 != "" )
        {
            $filter_poste.=" $and upper(pb.po_name)>= upper('".Database::escape_string($this->from_poste2)."')";
            $and=" and ";
        }
        if ( $this->to_poste2 != "" )
        {
            $filter_poste.=" $and upper(pb.po_name)<= upper('".Database::escape_string($this->to_poste2)."')";
            $and=" and ";
        }
        if ( $filter_poste != "")
            $filter_poste=" where ".$filter_poste;

        $sql="
             select  a_po_id ,
             pa.po_name as a_po_name,
             pa.po_description as a_po_description,
             pb.po_description as b_po_description,

             b_po_id,
             pb.po_name as b_po_name,
             sum(a_oa_amount_c) as a_c,
             sum(a_oa_amount_d) as a_d
             from (select
			a.j_id,
             a.po_id as a_po_id,
             b.po_id as b_po_id,
             case when a.oa_debit='t' then a.oa_amount else 0 end as a_oa_amount_d,
             case when a.oa_debit='f' then a.oa_amount else 0 end as a_oa_amount_c
             from
             operation_analytique as a join operation_analytique as b on (a.j_id=b.j_id and a.oa_row=b.oa_row)
		join poste_analytique as poa on (a.po_id=poa.po_id)
		join poste_analytique as pob on (b.po_id=pob.po_id)
             where poa.pa_id=".
             $this->pa_id."
             and pob.pa_id=".$this->pa_id2."  ".$this->set_sql_filter()."
             ) as m join poste_analytique as pa on ( a_po_id=pa.po_id)
             join poste_analytique as pb on (b_po_id=pb.po_id)

             $filter_poste

             group by a_po_id,b_po_id,pa.po_name,pa.po_description,pb.po_name,pb.po_description
             order by 2;
             ";


        $res=$this->db->exec_sql($sql);
        $this->has_data=Database::num_row($res);
        if ( Database::num_row($res) == 0 )
            return null;
        $a=array();
        $count=0;
        $array=Database::fetch_all($res);
        foreach ($array as $row)
        {
            $a[$count]['a_po_id']=$row['a_po_id'];
            $a[$count]['a_d']=$row['a_d'];
            $a[$count]['a_c']=$row['a_c'];
            $a[$count]['b_po_id']=$row['b_po_id'];

            $a[$count]['a_po_name']=$row['a_po_name'];
            $a[$count]['a_po_description']=$row['a_po_description'];
            $a[$count]['b_po_name']=$row['b_po_name'];
            $a[$count]['b_po_description']=$row['b_po_description'];
            $a[$count]['a_solde']=abs($row['a_d']-$row['a_c']);
            $a[$count]['a_debit']=($row['a_d']>$row['a_c'])?"debit":"credit";

            $count++;
        }
        return $a;


    }


    /*!
     * \brief add extra lines  with sum of each account
     * \param $p_array array returned by load()
     */
    function show_sum ($p_array)
    {
        $tot_deb=0;
        $tot_cred=0;
        $old="";
        $first=true;
        $array=array();
        foreach ( $p_array as $row)
        {

            if ( $old != $row['a_po_name'] && $first==false )

            {
                $s=abs($tot_deb-$tot_cred);
                $d=($tot_deb>$tot_cred)?'debit':'credit';
                $array[]=array('poste'=>$old,'desc'=>$old_desc
                                                    ,'debit'=>$tot_deb,'credit'=>$tot_cred,
                               'solde'=>$s,'dc'=>$d);

                $tot_deb=0;
                $tot_cred=0;

                $old=$row['a_po_name'];
                $old_desc=$row['a_po_description'];
            }

            if ( $first )
            {
                $first=false;
                $old=$row['a_po_name'];
                $old_desc=$row['a_po_description'];
            }

            $tot_deb+=$row['a_d'];
            $tot_cred+=$row['a_c'];


        }
        $s=abs($tot_deb-$tot_cred);
        $d=($tot_deb>$tot_cred)?'debit':'credit';
        $array[]=array('poste'=>$old,'desc'=>$old_desc
		       ,'debit'=>$tot_deb,'credit'=>$tot_cred,

                       'solde'=>$s,'dc'=>$d);


        return $array;

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
    static function test_me()
    {
        $a=new Database(dossier::id());

        $bal=new Anc_Balance_Double($a);
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

        }
    }
}
