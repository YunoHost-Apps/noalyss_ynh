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
 * \brief print a listing of financial
 */
require_once NOALYSS_INCLUDE.'/class_pdf.php';
class Print_Ledger_Misc extends PDF
{
    function __construct($p_cn,$p_jrn)
    {
        parent::__construct($p_cn,'P','mm','A4');
        $this->ledger=$p_jrn;
        $this->jrn_type=$p_jrn->get_type();
    }
    function Header()
    {
        //Arial bold 12
        $this->SetFont('DejaVu', 'B', 12);
        //Title
        $this->Cell(0,10,$this->dossier, 'B', 0, 'C');
        //Line break
        $this->Ln(20);
        $this->SetFont('DejaVu', 'B', 7);
        $this->Cell(30,6,'Piece');
        $this->Cell(10,6,'Date');
        $this->Cell(20,6,'Interne');
        $this->Cell(25,6,'Tiers');
        $this->Cell(80,6,'Commentaire');
        $this->Cell(15,6,'Montant');
        $this->Ln(6);

    }
    function Footer()
    {
        //Position at 2 cm from bottom
        $this->SetY(-20);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Page number
        $this->Cell(0,8,'Date '.$this->date." - Page ".$this->PageNo().'/{nb}',0,0,'C');
        $this->Ln(3);
        // Created by NOALYSS
        $this->Cell(0,8,'Created by NOALYSS, online on http://www.aevalys.eu',0,0,'C',false,'http://www.aevalys.eu');
    }
    /**
     *@brief print the pdf
     *@param
     *@param
     *@return
     *@see
     */
    function export()
    {
        $a_jrn=$this->ledger->get_rowSimple($_GET['from_periode'],
                                            $_GET['to_periode']);
        $this->SetFont('DejaVu', '', 6);
        if ( $a_jrn == null ) return;
        for ( $i=0;$i<count($a_jrn);$i++)
        {
            $row=$a_jrn[$i];
            $this->LongLine(30,5,$row['jr_pj_number']);
            $this->Cell(10,5,  smaller_date($row['date']));
            $this->Cell(20,5,$row['jr_internal']);
	    $type=$this->cn->get_value("select jrn_def_type from jrn_def where jrn_def_id=$1",array($a_jrn[$i]['jr_def_id']));
	    $other=mb_substr($this->ledger->get_tiers($type,$a_jrn[$i]['jr_id']),0,25);
	    $this->LongLine(25,5,$other,0,'L');
            $positive=$row['montant'];
            $this->LongLine(80,5,$row['comment'],0,'L');
             if ( $type == 'FIN' ) {
	       $positive = $this->cn->get_value("select qf_amount from quant_fin  ".
					  " where jr_id=".$row['jr_id']);
             }
            $this->Cell(15,5,nbm($positive),0,0,'R');
            $this->Ln(5);

        }
    }
}
