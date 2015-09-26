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
 * Print detail of operation PURCHASE or SOLD plus the items
 * There is no report of the different amounts
 *
 * @author danydb
 */
require_once NOALYSS_INCLUDE.'/class_acc_ledger_sold.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_purchase.php';
class Print_Ledger_Detail_Item extends PDFLand
{
    public function __construct (Database $p_cn,Acc_Ledger $p_jrn)
    {

        if($p_cn == null) die("No database connection. Abort.");

        parent::__construct($p_cn,'L', 'mm', 'A4');
        $this->ledger=$p_jrn;
        $this->show_col=true;
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
        $high=6;
        $this->SetFont('DejaVu', '', 6);
        $this->LongLine(20, $high, _('Date'),0,  'L', false);
        $this->Cell(20, $high, _('Numéro interne'), 0, 0, 'L', false);
        $this->LongLine(50, $high, _('Code'),0,'L',false);
        $this->LongLine(80, $high, _('Libellé'),0,'L',false);
        $this->Cell(20, $high, _('Tot HTVA'), 0, 0, 'R', false);
        $this->Cell(20, $high, _('Tot TVA NP'), 0, 0, 'R', false);
        $this->Cell(20, $high, "", 0, 0, 'R', false);
        $this->Cell(20, $high, _('Tot TVA'), 0, 0, 'R', false);
        $this->Cell(20, $high, _('TVAC'), 0, 0, 'R', false);
        $this->Ln(6);
        $this->show_col=true;
        
    }
    /**
     *@brief write the Footer
     */
    function Footer()
    {
        $this->Ln(2);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(50,8,' Journal '.$this->ledger->get_name(),0,0,'C');
        //Arial italic 8
        //Page number
        $this->Cell(30,8,'Date '.$this->date." - Page ".$this->PageNo().'/{nb}',0,0,'L');
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
      bcscale(2);
      $jrn_type=$this->ledger->get_type();
      switch ($jrn_type)
      {
          case 'VEN':
              $ledger=new Acc_Ledger_Sold($this->cn, $this->ledger->jrn_def_id);
              $ret_detail=$ledger->get_detail_sale($_GET['from_periode'],$_GET['to_periode']);
              break;
          case 'ACH':
                $ledger=new Acc_Ledger_Purchase($this->cn, $this->ledger->jrn_def_id);
                $ret_detail=$ledger->get_detail_purchase($_GET['from_periode'],$_GET['to_periode']);
              break;
          default:
              die (__FILE__.":".__LINE__.'Journal invalide');
              break;
      }
        if ( $ret_detail == null ) return;
        $nb=Database::num_row($ret_detail);
        $this->SetFont('DejaVu', '', 6);
        $internal="";
        $this->SetFillColor(220,221,255);
        $high=4;
        for ( $i=0;$i< $nb ;$i++)
        {
            
            $row=Database::fetch_array($ret_detail, $i);
            if ($internal != $row['jr_internal'])
            {
                // Print the general info line width=270mm
                $this->LongLine(20, $high, $row['jr_date'],1,  'L', true);
                $this->Cell(20, $high, $row['jr_internal'], 1, 0, 'L', true);
                $this->LongLine(50, $high, $row['quick_code']." ".$row['tiers_name'],1,'L',true);
                $this->LongLine(80, $high, $row['jr_comment'],1,'L',true);
                $this->Cell(20, $high, nbm($row['htva']), 1, 0, 'R', true);
                $this->Cell(20, $high, nbm($row['tot_tva_np']), 1, 0, 'R', true);
                $this->Cell(20, $high, "", 1, 0, 'R', true);
                $this->Cell(20, $high, nbm($row['tot_vat']), 1, 0, 'R', true);
                $sum=bcadd($row['htva'],$row['tot_vat']);
                $sum=bcsub($sum,$row['tot_tva_np']);
                $this->Cell(20, $high, nbm($sum), 1, 0, 'R', true);
                $internal=$row['jr_internal'];
                $this->Ln(6);
               // on the first line, the code for each column is displaid
                if ( $this->show_col == true ) {
                    //
                    // Header detail
                    $this->LongLine(30,$high,'QuickCode');
                    $this->Cell(30,$high,'Poste');
                    $this->LongLine(70,$high,'Libellé');
                    $this->Cell(20,$high,'Prix/Unit',0,0,'R');
                    $this->Cell(20,$high,'Quant.',0,0,'R');
                    $this->Cell(20,$high,'HTVA',0,0,'R');
                    $this->Cell(20,$high,'TVA NP',0,0,'R');
                    $this->Cell(20,$high,'Code TVA');
                    $this->Cell(20,$high,'TVA',0,0,'R');
                    $this->Cell(20,$high,'TVAC',0,0,'R');
                    $this->Ln(6);
                    $this->show_col=false;
                 } 
            }
            // Print detail sale / purchase
            $this->LongLine(30,$high,$row['j_qcode']);
            $this->Cell(30,$high,$row['j_poste']);
            $comment=($row['j_text']=="")?$row['item_name']:$row['j_text'];
            $this->LongLine(70,$high,$comment);
            $this->Cell(20,$high,nbm($row['price_per_unit']),0,0,'R');
            $this->Cell(20,$high,nbm($row['quantity']),0,0,'R');
            $this->Cell(20,$high,nbm($row['price']),0,0,'R');
            $this->Cell(20,$high,nbm($row['vat_sided']),0,0,'R');
            $this->Cell(20,$high,$row['vat_code']." ".$row['tva_label']);
            $this->Cell(20,$high,nbm($row['vat']),0,0,'R');
            $sum=bcadd($row['price'],$row['vat']);
            $this->Cell(20,$high,nbm($sum),0,0,'R');
            $this->Ln(6);
            
        }
    }

}
?>
