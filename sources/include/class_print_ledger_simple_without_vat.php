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
 * \brief this class extends PDF and let you export the detailled printing
 *  of any ledgers
 */
require_once NOALYSS_INCLUDE.'/class_pdf.php';

class Print_Ledger_Simple_Without_Vat extends PDF
{
    public function __construct ($p_cn,$p_jrn)
    {

        if($p_cn == null) die("No database connection. Abort.");

        parent::__construct($p_cn,'L', 'mm', 'A4');
        $this->ledger=$p_jrn;
        $this->jrn_type=$p_jrn->get_type();
        //----------------------------------------------------------------------
        /* report
         *
         * get rappel to initialize amount rap_xx
         *the easiest way is to compute sum from quant_
         */
        $this->previous=$this->ledger->previous_amount($_GET['from_periode']);


        $this->rap_htva=$this->previous['price'];
        $this->rap_tvac=$this->previous['price'];
        $this->rap_priv=$this->previous['priv'];


    }

    function setDossierInfo($dossier = "n/a")
    {
        $this->dossier = dossier::name()." ".$dossier;
    }
    /**
     *@brief write the header of each page
     */
    function Header()
    {
        //Arial bold 12
        $this->SetFont('DejaVu', 'B', 12);
        //Title
        $this->Cell(0,10,$this->dossier, 'B', 0, 'C');
        //Line break
        $this->Ln(20);
        $this->SetFont('DejaVu', 'B', 8);
        /* column header */
        $this->Cell(15,6,'Pièce');
        $this->Cell(15,6,'Date');
        $this->Cell(20,6,'ref');
        if ( $this->jrn_type=='ACH')
            $this->Cell(60,6,'Client');
        else
            $this->Cell(60,6,'Fournisseur');
        $this->Cell(105,6,'Commentaire');
        if ( $this->jrn_type=='ACH')
        {
            $this->Cell(15,6,'Privé',0,0,'R');
        }
        $this->Cell(15,6,'Prix',0,0,'R');

        $this->Ln(5);

        $this->SetFont('DejaVu','',6);
        // page Header
        $this->Cell(215,6,'report',0,0,'R'); /* HTVA */
        if ( $this->jrn_type != 'VEN')
        {
            $this->Cell(15,6,sprintf('%.2f',$this->rap_priv),0,0,'R');  /* prive */
        }
        $this->Cell(15,6,sprintf('%.2f',$this->rap_htva),0,0,'R'); /* HTVA */




        $this->Ln(6);
        //total page
        $this->tp_htva=0.0;
        $this->tp_tvac=0.0;
        $this->tp_priv=0;
        $this->tp_nd=0;
    }
    /**
     *@brief write the Footer
     */
    function Footer()
    {
        //Position at 3 cm from bottom
        $this->SetY(-20);
        /* write reporting  */
        $this->Cell(215,6,'Total page ','T',0,'R'); /* HTVA */
        if ( $this->jrn_type !='VEN')
        {
            $this->Cell(15,6,sprintf('%.2f',$this->tp_priv),'T',0,'R');  /* prive */
        }
        $this->Cell(15,6,sprintf('%.2f',$this->tp_htva),'T',0,'R'); /* HTVA */
        $this->Cell(0,6,'','T',0,'R'); /* line */
        $this->Ln(2);

        $this->Cell(215,6,'report',0,0,'R'); /* HTVA */
        if ( $this->jrn_type !='VEN')
        {
            $this->Cell(15,6,sprintf('%.2f',$this->rap_priv),0,0,'R');  /* prive */
        }
        $this->Cell(15,6,sprintf('%.2f',$this->rap_htva),0,0,'R'); /* HTVA */
        $this->Ln(2);

        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Page number
        $this->Cell(0,8,'Date '.$this->date." - Page ".$this->PageNo().'/{nb}',0,0,'L');
        // Created by NOALYSS
        $this->Cell(0,8,'Created by NOALYSS, online on http://www.aevalys.eu',0,0,'R',false,'http://www.aevalys.eu');
    }

    function Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $txt = str_replace("\\", "", $txt);
        return parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    /**
     *@brief export the ledger in  PDF
     */
    function export()
    {

        $a_jrn=$this->ledger->get_operation($_GET['from_periode'],
                                            $_GET['to_periode']);

        if ( $a_jrn == null ) return;
        for ( $i=0;$i<count($a_jrn);$i++)
        {

            $row=$a_jrn[$i];
            $this->LongLine(15,5,($row['pj']),0);
            $this->Cell(15,5,$row['date_fmt'],0,0);
            $this->Cell(20,5,$row['internal'],0,0);
            list($qc,$name)=$this->get_tiers($row['id'],$this->jrn_type);
            $this->Cell(20,5,$qc,0,0);
            $this->LongLine(40,5,$name,0,'L');

            $this->LongLine(105,5,$row['comment'],0,'L');

            /* get other amount (without vat, total vat included, private, ND */
            $other=$this->ledger->get_other_amount($a_jrn[$i]['jr_grpt_id']);
            $this->tp_htva+=$other['price'];
            $this->tp_priv+=$other['priv'];
            $this->rap_htva+=$other['price'];
            $this->rap_priv+=$other['priv'];


            if ( $this->jrn_type !='VEN')
            {
                $this->Cell(15,6,sprintf("%.2f",$other['priv']),0,0,'R');
            }

            $this->Cell(15,6,sprintf("%.2f",$other['price']),0,0,'R');
            $this->Ln(5);
        }
    }

}
