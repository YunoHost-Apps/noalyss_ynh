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
 */

require_once NOALYSS_INCLUDE.'/tfpdf/tfpdf.php';

class PDF extends TFPDF
{

    var $cn  = null;
    var $own = null;
    var $soc = "";
    var $dossier =  "n/a";
    var $date = "";

    public function __construct ($p_cn = null, $orientation = 'P', $unit = 'mm', $format = 'A4')
    {
		$this->bigger=0;
        if($p_cn == null) die("No database connection. Abort.");

        parent::TFPDF($orientation, $unit, $format);
        $this->AddFont('DejaVu','','DejaVuSans.ttf',true);
        $this->AddFont('DejaVu','B','DejaVuSans-Bold.ttf',true);
        $this->AddFont('DejaVu','BI','DejaVuSans-BoldOblique.ttf',true);
        $this->AddFont('DejaVuCond','','DejaVuSansCondensed.ttf',true);
        $this->AddFont('DejaVuCond','B','DejaVuSansCondensed-Bold.ttf',true);
        $this->AddFont('DejaVuCond','I','DejaVuSansCondensed-Oblique.ttf',true);
        date_default_timezone_set ('Europe/Paris');

        $this->cn  = $p_cn;
        $this->own = new own($this->cn);
        $this->soc = $this->own->MY_NAME;
        $this->date = date('d.m.Y');
    }

    function setDossierInfo($dossier = "n/a")
    {
        $this->dossier = dossier::name()." ".$dossier;
    }

    function Header()
    {
        //Arial bold 12
        $this->SetFont('DejaVu', 'B', 12);
        //Title
        $this->Cell(0,10,$this->dossier, 'B', 0, 'C');
        //Line break
        $this->Ln(20);
    }
    function Footer()
    {
        //Position at 2 cm from bottom
        $this->SetY(-20);
        //Arial italic 8
        $this->SetFont('Arial', '', 8);
        //Page number
        $this->Cell(0,8,'Date '.$this->date." - Page ".$this->PageNo().'/{nb}',0,0,'C');
        $this->Ln(3);
        // Created by NOALYSS
        $this->Cell(0,8,'Created by NOALYSS, online on http://www.aevalys.eu',0,0,'C',false,'http://www.aevalys.eu');
    }
    function Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $txt = str_replace("\\", "", $txt);
        return parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
		}
	function LongLine($w,$h,$txt,$border=0,$align='',$fill=false)
	{
		$x_m=$this->GetX();
		$y_m=$this->GetY();
		$txt = str_replace("\\", "", $txt);
		if ( $y_m > ($this->h-$this->bMargin-10 ))		{
			$this->AddPage ();
			$y_m=$this->GetY();

		}
		$this->MultiCell($w,$h,$txt,$border,$align,$fill);
		$x_m=$x_m+$w;
		$tmp=$this->GetY()-$y_m;
		if ( $tmp > $this->bigger) $this->bigger=$tmp;
		$this->SetXY($x_m,$y_m);
	}
	function Ln($p_step=null){
		if ( $this->bigger==0) parent::Ln($p_step);
		parent::Ln($this->bigger);
		$this->bigger=0;
	}
    /**
     *@brief retrieve the client name and quick_code
     *@param $p_jr_id jrn.jr_id
     *@param $p_jrn_type ledger type ACH VEN FIN
     *@return array (0=>qcode,1=>name) or for FIN 0=>customer qc 1=>customer name 2=>bank qc 3=>bank name
     *@see class_print_ledger_simple, class_print_ledger_simple_without_vat
     */
    function get_tiers($p_jr_id,$p_jrn_type)
    {
        if ( $p_jrn_type=='ACH' )
        {
            $array=$this->cn->get_array('SELECT
                                        jrnx.j_grpt,
                                        quant_purchase.qp_supplier,
                                        quant_purchase.qp_internal,
                                        jrn.jr_internal
                                        FROM
                                        public.quant_purchase,
                                        public.jrnx,
                                        public.jrn
                                        WHERE
                                        quant_purchase.j_id = jrnx.j_id AND
                                        jrnx.j_grpt = jrn.jr_grpt_id and jr_id=$1',array($p_jr_id));
            if (count($array)==0) return array("ERREUR $p_jr_id",'');
            $customer_id=$array[0]['qp_supplier'];
            $fiche=new Fiche($this->cn,$customer_id);
            $customer_qc=$fiche->get_quick_code($customer_id);
            $customer_name=$fiche->getName();
            return array($customer_qc,$customer_name);
        }
        if ( $p_jrn_type=='VEN' )
        {
            $array=$this->cn->get_array('SELECT
                                        quant_sold.qs_client
                                        FROM
                                        public.quant_sold,
                                        public.jrnx,
                                        public.jrn
                                        WHERE
                                        quant_sold.j_id = jrnx.j_id AND
                                        jrnx.j_grpt = jrn.jr_grpt_id and jr_id=$1',array($p_jr_id));
            if (count($array)==0) return array("ERREUR $p_jr_id",'');
            $customer_id=$array[0]['qs_client'];
            $fiche=new Fiche($this->cn,$customer_id);
            $customer_qc=$fiche->get_quick_code($customer_id);
            $customer_name=$fiche->getName();
            return array($customer_qc,$customer_name);
        }
        if ( $p_jrn_type=='FIN' )
        {
            $array=$this->cn->get_array('SELECT
                                        qf_other,qf_bank
                                        FROM
                                        public.quant_fin
                                        WHERE
                                        quant_fin.jr_id =$1',array($p_jr_id));
            if (count($array)==0) return array("ERREUR $p_jr_id",'','','');
            $customer_id=$array[0]['qf_other'];
            $fiche=new Fiche($this->cn,$customer_id);
            $customer_qc=$fiche->get_quick_code($customer_id);
            $customer_name=$fiche->getName();

            $bank_id=$array[0]['qf_bank'];
            $fiche=new Fiche($this->cn,$bank_id);
            $bank_qc=$fiche->get_quick_code($bank_id);
            $bank_name=$fiche->getName();

            return array($customer_qc,$customer_name,$bank_qc,$bank_name);
        }
    }


}

class PDFLand extends PDF
{

    public function __construct ($p_cn = null, $orientation = 'P', $unit = 'mm', $format = 'A4')
    {

        if($p_cn == null) die("No database connection. Abort.");
        $this->bigger=0;

        parent::TFPDF('L', $unit, $format);
        date_default_timezone_set ('Europe/Paris');
        $this->AddFont('DejaVu','','DejaVuSans.ttf',true);
        $this->AddFont('DejaVu','B','DejaVuSans-Bold.ttf',true);
        $this->AddFont('DejaVu','BI','DejaVuSans-BoldOblique.ttf',true);
        $this->AddFont('DejaVuCond','','DejaVuSansCondensed.ttf',true);
        $this->AddFont('DejaVuCond','B','DejaVuSansCondensed-Bold.ttf',true);
        $this->AddFont('DejaVuCond','I','DejaVuSansCondensed-Oblique.ttf',true);

        $this->cn  = $p_cn;
        $this->own = new own($this->cn);
        $this->soc = $this->own->MY_NAME;
        $this->date = date('d.m.Y');
    }
    function Header()
    {
        //Arial bold 12
        $this->SetFont('DejaVu', 'B', 10);
        //Title
        $this->Cell(0,10,$this->dossier, 'B', 0, 'C');
        //Line break
        $this->Ln(20);
    }
    function Footer()
    {
        //Position at 2 cm from bottom
        $this->SetY(-20);
        //Arial italic 8
        $this->SetFont('DejaVuCond', 'I', 8);
        //Page number
        $this->Cell(0,8,'Date '.$this->date." - Page ".$this->PageNo().'/{nb}',0,0,'C');
        $this->Ln(3);
        // Created by NOALYSS
        $this->Cell(0,8,'Created by NOALYSS, online on http://www.aevalys.eu',0,0,'C',false,'http://www.aevalys.eu');

    }
}

class PDFBalance_simple extends PDF
{
    /**
     *@brief set_info(dossier,from poste,to poste, from periode, to periode)
     *@param $p_from_poste start = poste
     *@param $p_to_poste   end   = poste
     *@param $p_from       periode start
     *@param $p_to         periode end
     */
    function set_info($p_from_poste,$to_poste,$p_from,$p_to)
    {
        $this->dossier='Balance simple '.dossier::name();
        $this->from_poste=$p_from_poste;
        $this->to_poste=$to_poste;
        $this->from=$p_from;
        $this->to=$p_to;
    }
    function Header()
    {
        parent::Header();
        $this->SetFont('DejaVu','B',8);
        $titre=sprintf("Balance simple poste %s %s date %s %s",
                       $this->from_poste,
                       $this->to_poste,
                       $this->from,
                       $this->to);
        $this->Cell(0,7,$titre,1,0,'C');

        $this->Ln();
        $this->SetFont('DejaVu','',6);
        $this->Cell(20,7,'id','B');
        $this->Cell(90,7,'Poste Comptable','B');
        $this->Cell(20,7,'Débit','B',0,'L');
        $this->Cell(20,7,'Crédit','B',0,'L');
        $this->Cell(20,7,'Solde','B',0,'L');
        $this->Cell(20,7,'D/C','B',0,'L');
        $this->Ln();

    }
}
