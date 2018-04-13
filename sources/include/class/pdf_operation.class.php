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

// Copyright Author Dany De Bontridder dany@alchimerys.be(2003-2016)

/**
 * @file
 * Detail Operation ACC + ANC , it will use Acc_Operation and Anc_Operation
 * 
 */
require_once NOALYSS_INCLUDE . '/lib/pdf.class.php';
require_once NOALYSS_INCLUDE . '/class/acc_operation.class.php';
require_once NOALYSS_INCLUDE . '/class/acc_ledger.class.php';
require_once NOALYSS_INCLUDE . '/class/acc_operation.class.php';
require_once NOALYSS_INCLUDE . '/class/anc_operation.class.php';

class PDF_Operation extends PDF {

    private $acc_detail; //!< Acc_Operation object
    private $jr_id; //!< jrn.jr_id operation
    private $pdf;
    var $cn;
    function __construct($p_cn, $pjr_id) {
        $this->cn = $p_cn;
        $this->jr_id = $pjr_id;
        $this->pdf = null;
        $acc_detail=  new Acc_Operation($p_cn,$pjr_id);
        $acc_detail->set_id($pjr_id);
        $this->acc_detail=$acc_detail->get_quant();
    }
    /*!
     * @brief return the name of the ledger of the operation
     */
    private function print_ledger_name() {
        $ledger = new Jrn_Def_sql($this->cn, $this->acc_detail->det->jr_def_id);
        return $ledger->jrn_def_name;
    }

    /**
     * @brief Write basic information about the operation : date , ledger ,
     * receipt , comment , document name if any
     */
    private function print_operation_info() {
        $this->pdf->SetFont('DejaVu', '', 6);
        $this->pdf->write_cell(50, 6, _('Journal'));
        $this->pdf->write_cell(100, 6, $this->print_ledger_name());
        $this->pdf->line_new(4);


        $this->pdf->write_cell(50, 6, _("Date"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_date);
        $this->pdf->line_new(4);
        $this->pdf->write_cell(50, 6, _("Echéance"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_ech);
        $this->pdf->line_new(4);
        $this->pdf->write_cell(50, 6, _("Paiement"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_date_paid);
        $this->pdf->line_new(4);
        $this->pdf->write_cell(50, 6, _("Numéro interne"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_internal);
        $this->pdf->line_new(4);
        $this->pdf->write_cell(50, 6, _("Pièce"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_pj_number);
        $this->pdf->line_new(4);
        $this->pdf->write_cell(50, 6, _("Commentaire"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_comment);
        $this->pdf->line_new(4);
        $this->pdf->write_cell(50, 6, _("Nom document"));
        $this->pdf->write_cell(100, 6, $this->acc_detail->det->jr_pj_name);
        $this->pdf->line_new(8);
    }
    /**
     * @brief For SALE and PURCHASE , print the customer or supplier and calls
     * a function for the detail (VAT , quantity ...)
     * @return type
     */
    private function print_operation_quant() {
        if ( $this->acc_detail->signature=='FIN' ||
                $this->acc_detail->signature=="ODS") {
            return;
        }
        if ($this->acc_detail->signature=="ACH") {
            $tiers=_("Fournisseur");
            $tiers_id=$this->acc_detail->det->array[0]['qp_supplier'];
        } else {
            $tiers=_("Client");
            $tiers_id=$this->acc_detail->det->array[0]['qs_client'];
        }
        $this->pdf->SetFont('DejaVu', 'B', 10);
        $this->print_section(_("Résumé"));
        $fiche=new Fiche($this->cn,$tiers_id);
        
        $this->pdf->SetFont('DejaVu', 'B', 6);
        $this->pdf->write_cell(50, 6, $tiers);
        $this->pdf->write_cell(140, 6, $fiche->getName().
                " ".$fiche->get_quick_code());
        $this->pdf->line_new(4);
         if ($this->acc_detail->signature=="ACH") {
             $this->print_purchase();
         } else {
             $this->print_sale();
         }
        
    
    }
    /**
     * @brief returns a string with info about tva 
     * @see print_sale print_purchase
     * @param integer $p_tva_id id of TVA_RATE (tva_rate.tva_id)
     * @return type
     */
    private function str_vat($p_tva_id) {
        $tva=new Acc_Tva($this->cn, $p_tva_id);
        $tva->load();
        $auto="";
        if ( $tva->tva_both_side==1) {
            $auto="(=0.0)";
        }
        $ret=sprintf("%d %s %s",$p_tva_id,$tva->tva_label,$auto);
        return $ret;
        
    }
    private function  print_sale() {
        // quick_code , label , montant hors tva,code tva,montant tva 
        $nb=count($this->acc_detail->det->array);
        $width=array(10,30,50,25,25,25,25);
        $sum_amount=0;
        $sum_vat=0;
        bcscale(4);
        $this->pdf->SetFont('DejaVu', 'B', 6);
        $this->pdf->write_cell($width[0],6,_("n°"),"B");
        $this->pdf->write_cell($width[1],6,_("code"),"B");
        $this->pdf->write_cell($width[2],6,_("Libellé"),"B");
        $this->pdf->write_cell($width[3],6,_("Montant HTVA"),"B",0,"R");
        $this->pdf->write_cell($width[4],6,_("TVA"),"B");
        $this->pdf->write_cell($width[5],6,_("Montant TVA"),"B",0,"R");
        $this->pdf->write_cell($width[6],6,_("Total"),"B",0,"R");
        $this->pdf->line_new(6);
        $this->pdf->SetFont('DejaVu', '', 6);
        for ( $i=0;$i<$nb;$i++) {
            $row=$this->acc_detail->det->array[$i];
            $fiche_id=$row['qs_fiche'];
            $fiche=new Fiche($this->cn,$fiche_id);
            $this->pdf->write_cell($width[0],6,$i+1);
            $this->pdf->write_cell($width[1],6,$fiche->get_quick_code());
            $this->pdf->LongLine($width[2],6,$row['j_text']);
            $this->pdf->write_cell($width[3],6,$row["qs_price"],"",0,"R");
            $str=$this->str_vat($row["qs_vat_code"]);
            $this->pdf->write_cell($width[4],6,$str);
            $this->pdf->write_cell($width[5],6,$row["qs_vat"],"",0,"R");
            $this->pdf->write_cell($width[6],6,bcadd($row["qs_price"],$row["qs_vat"]),"",0,"R");
            $this->pdf->line_new(6);

            $sum_amount=bcadd($sum_amount,$row["qs_price"]);
            $sum_vat=bcadd($sum_vat,$row["qs_vat"]);
        }
        $this->pdf->SetFont('DejaVu', 'B', 6);
        $this->pdf->write_cell($width[0],6,"");
        $this->pdf->write_cell($width[1],6,"");
        $this->pdf->write_cell($width[2],6,"");
        $this->pdf->write_cell($width[3],6,$sum_amount,"",0,"R");
        $this->pdf->write_cell($width[4],6,"");
        $this->pdf->write_cell($width[5],6,$sum_vat,"",0,"R");
        $this->pdf->write_cell($width[6],6,bcadd($sum_amount,$sum_vat),"",0,"R");
        $this->pdf->line_new(4);
        
    }    
    private function  print_purchase(){
        // quick_code , label , montant hors tva,code tva,montant tva 
        $nb=count($this->acc_detail->det->array);
        $width=array(10,30,50,25,25,25,25);
        $sum_amount=0;
        $sum_vat=0;
        bcscale(4);
        $this->pdf->SetFont('DejaVu', 'B', 6);
        $this->pdf->write_cell($width[0],6,_("n°"),"B");
        $this->pdf->write_cell($width[1],6,_("code"),"B");
        $this->pdf->write_cell($width[2],6,_("Libellé"),"B");
        $this->pdf->write_cell($width[3],6,_("Montant HTVA"),"B",0,"R");
        $this->pdf->write_cell($width[4],6,_("TVA"),"B");
        $this->pdf->write_cell($width[5],6,_("Montant TVA"),"B",0,"R");
        $this->pdf->write_cell($width[6],6,_("Total"),"B",0,"R");
        $this->pdf->line_new(6);
        $this->pdf->SetFont('DejaVu', '', 6);
        for ( $i=0;$i<$nb;$i++) {
            $row=$this->acc_detail->det->array[$i];
            $fiche_id=$row['qp_fiche'];
            $fiche=new Fiche($this->cn,$fiche_id);
            $this->pdf->write_cell($width[0],6,$i+1);
            $this->pdf->write_cell($width[1],6,$fiche->get_quick_code());
            $this->pdf->LongLine($width[2],6,$row['j_text']);
            $this->pdf->write_cell($width[3],6,$row["qp_price"],"",0,"R");
            $str=$this->str_vat($row["qp_vat_code"]);
            $this->pdf->write_cell($width[4],6,$str);
            $this->pdf->write_cell($width[5],6,$row["qp_vat"],"",0,"R");
            $this->pdf->write_cell($width[6],6,bcadd($row["qp_price"],$row["qp_vat"]),"",0,"R");
            $this->pdf->line_new(6);

            $sum_amount=bcadd($sum_amount,$row["qp_price"]);
            $sum_vat=bcadd($sum_vat,$row["qp_vat"]);
        }
        $this->pdf->SetFont('DejaVu', 'B', 6);
        $this->pdf->write_cell($width[0],6,"");
        $this->pdf->write_cell($width[1],6,"");
        $this->pdf->write_cell($width[2],6,"");
        $this->pdf->write_cell($width[3],6,$sum_amount,"",0,"R");
        $this->pdf->write_cell($width[4],6,"");
        $this->pdf->write_cell($width[5],6,$sum_vat,"",0,"R");
        $this->pdf->write_cell($width[6],6,bcadd($sum_amount,$sum_vat),"",0,"R");
        $this->pdf->line_new(10);
        
    }    
    private function print_section($p_section) {
         $this->pdf->SetFont('DejaVu', 'B', 10);
        $this->pdf->write_cell(60,8,$p_section,"1");
        $this->pdf->line_new(8);
    }
    private function print_acc_writing(){
        $obj1=new Acc_Operation($this->cn);
        $obj1->set_id($this->jr_id);
        $obj=$obj1->get();
        $nb=count($obj->det->array);
        $this->print_section(_("Ecriture comptable"));
        
        bcscale(4);
        $width=array(10,40,40,50,30,10);
        $this->pdf->SetFont('DejaVu', 'B', 6);
        $this->pdf->write_cell($width[0],6,_("n°"),"B");
        $this->pdf->write_cell($width[1],6,_("Poste comptable"),"B");
        $this->pdf->write_cell($width[2],6,_("code"),"B");
        $this->pdf->write_cell($width[3],6,_("Libellé"),"B");
        $this->pdf->write_cell($width[4],6,_("Montant"),"B",0,"R");
        $this->pdf->write_cell($width[5],6,_("D/C"),"B");
        $this->pdf->line_new(6);
        $this->pdf->SetFont('DejaVu', '', 6);
        for ($i=0;$i<$nb;$i++){
            $row=$obj->det->array[$i];
            $this->pdf->write_cell($width[0],6,$row['j_id']);
            $this->pdf->write_cell($width[1],6,$row['j_poste']);
            $this->pdf->write_cell($width[2],6,$row["j_qcode"]);
            $str=$row["j_text"];
            if (trim($str)==""){
                if (trim($row["j_qcode"])=="") {
                    $str=$this->cn->get_value("select pcm_lib from tmp_pcmn where pcm_val=$1",
                            array($row["j_poste"]));
                } else {
                    $str=$this->cn->get_value("select ad_value from fiche_detail where ad_id=1 and f_id=$1",
                            array($row["f_id"]));
                }
            }
            $this->pdf->write_cell($width[3],6,$str);
            $this->pdf->write_cell($width[4],6,$row["j_montant"],"",0,"R");
            $deb=($row["j_debit"]=="t")?"D":"C";
            $this->pdf->write_cell($width[5],6,$deb);
            $this->pdf->line_new(6);
        }
    }
    private function print_anc_header($pa_plan) {
        $nb=count($pa_plan);
        $this->pdf->SetFont('DejaVu', 'B', 8);
        $width=25;
        $this->pdf->SetFillColor(220,221,255);
        for ($i = 0; $i<$nb; $i++) {
             $this->pdf->write_cell($width,8,$pa_plan[$i]['pa_name'],1,"C",1);
        }
        $this->pdf->write_cell($width,8,_('Montant'),1,"C",1);
        $this->pdf->SetFillColor(0,0,0);
        $this->pdf->line_new(8);
    }
    private function print_anc_detail($p_j_id, $pa_plan) {
        // get info from jrnx
        $row_jrnx = $this->cn->get_row("select j_text , j_montant,j_qcode,j_poste from public.jrnx where j_id=$1", array($p_j_id));

        // print row
        $this->pdf->SetFont('DejaVu', 'B', 7);
         $this->pdf->write_cell(25, 8,$p_j_id);
         $this->pdf->write_cell(40, 8,$row_jrnx["j_poste"]);
         $this->pdf->write_cell(40, 8,$row_jrnx["j_qcode"]);
         $this->pdf->write_cell(40, 8,$row_jrnx["j_montant"]);
         $this->pdf->line_new(8);
         // . Display a table with the plan (axis) name in the header
        $this->print_anc_header($pa_plan);
        
        // get all the rows from operation_analytique for this jrnx.j_id
        $a_operation_analytique = $this->cn->get_array(
                "select oa_row,oa_positive,po_name,pa_id,po_id,oa_debit,
                    case when oa_positive='N' then oa_amount*(-1) 
                    else oa_amount end  as signed_amount
	from OPeration_analytique 
	left join public.poste_analytique using(po_id)
	left join public.plan_analytique using (pa_id)
            where 
                    j_id =$1
            order by oa_row,pa_id
                    ", array($p_j_id));
        $this->pdf->SetFont('DejaVu', '', 6);
        $width = 25;
        // For each row print the result for each plan (=col)
        $old_row = null;
        $nb_operation = count($a_operation_analytique);
        $idx_plan = 0;
        $cnt_plan = count($pa_plan);
        bcscale(4);
        $tot_anc=0;
        for ($i = 0; $i<$nb_operation; $i++) {
            $current_row = $a_operation_analytique[$i];
            if ($old_row==null)
                $old_row = $current_row;
            if ($old_row['oa_row']!=$current_row['oa_row']) {
                 if($idx_plan!=0) {
                     for ($e = $idx_plan; $e<$cnt_plan; $e++) 
                    $this->pdf->write_cell($width, 8, "", 1, "C", 0);
                }
                // print last column
                $this->pdf->write_cell($width, 8, $old_row["signed_amount"], 1, "R", 0);
                // Add to total
                $tot_anc=  bcadd($tot_anc, $old_row["signed_amount"]);
                // we start a new line 
                $this->pdf->line_new(8);
                // reset plan
                $idx_plan = 0;
            }
            if ($current_row['pa_id']==$pa_plan[$idx_plan]['pa_id']) {
                    $this->pdf->write_cell($width, 8, $current_row['po_name'], 1, "L", 0);
            } else {
                // print the post code in the right column
                for ($e = $idx_plan; $e<$cnt_plan; $e++) {
                    if ($current_row['pa_id']==$pa_plan[$e]['pa_id']) {
                        $this->pdf->write_cell($width, 8, $current_row['po_name'], 1, "L", 0);
                        $idx_plan=$e;
                       // $idx_plan--;
                        break;
                    } else {
                        $this->pdf->write_cell($width, 8, "", 1, "C", 0);
                    }
                }
            }
            $idx_plan++;
            if ($idx_plan == $cnt_plan ) $idx_plan=0;
            $old_row = $current_row;
        }
        // End
        // print last column
        if($idx_plan!=0) {
             $this->pdf->write_cell($width, 8, "", 1, "C", 0);
        }
        $this->pdf->write_cell($width, 8, $old_row["signed_amount"], 1, "R", 0);
        $this->pdf->line_new(8);
       
        // Add to total
         $tot_anc=  bcadd($tot_anc, $old_row["signed_amount"]);
        
        // Total
         $this->pdf->write_cell(40, 6,_("Comptabilité"));
         $this->pdf->write_cell(40, 6,$row_jrnx["j_montant"],"","R",0);
         $this->pdf->line_new();
         $this->pdf->write_cell(40, 6,_("Analytique"));
         $this->pdf->write_cell(40, 6,$tot_anc,"","R",0);
         $this->pdf->line_new();
         
         $this->pdf->write_cell(40, 6,_("Diff"));
         $this->pdf->write_cell(40, 6, bcsub($row_jrnx['j_montant'], $tot_anc),0,"R",0);
         $this->pdf->line_new();
    }

    private function print_anc_writing() {
        // . Get all the existing plans and store them in an array
        $a_plan=$this->cn->get_array("select pa_id,pa_name 
                            from public.plan_analytique order by pa_id");
        
       
        
        // . Take all the j_id of the concern operation
        $a_jrnxId=$this->cn->get_array(
                "select j_id from jrnx join jrn on (jr_grpt_id=j_grpt)
            where
            jr_id=$1",array($this->jr_id));
        
        // . For each j_id print all the concerned rows ordered by plan 
        $nb=count($a_jrnxId);
        $flag_print_section=0;
        for ($index = 0; $index<$nb; $index++) {
            $count_ana=$this->cn->get_value("select count(*)
                from public.operation_analytique
                where j_id=$1
                ",array($a_jrnxId[$index]["j_id"]));
            if ($count_ana == 0 ) continue;
            if ($flag_print_section==0) $this->print_section (_("Détail"));
            $flag_print_section=1;
            $this->print_anc_detail($a_jrnxId[$index]["j_id"],$a_plan);
        }
        
        
    }
    /**
     * @brief export operation into a PDF
     * 
     * @param Array $p_option  containing [acc anc] or a combination
     */

    function export_pdf($p_option) {
        // create a PDF
        $this->pdf = new PDF($this->cn);
        $this->pdf->Setdossierinfo(_("Détail opération"));
        $this->pdf->setTitle(_("Détail opération"), true);
        $this->pdf->SetAuthor('NOALYSS');
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
        
        //write date + ledger + detail items + total
        $this->print_operation_info();
        
        // Write only for Sale or purchase summary (QCode, label,amount,tva...)
        $this->print_operation_quant();
        
        // if option contains ACC or $this->acc_detail is FIN or ODS
        if ($this->acc_detail->signature=="ODS" ||
            $this->acc_detail->signature=="FIN" ||
            array_search("acc", $p_option) !== false
                ){
                    $this->print_acc_writing();
                }
        // ANC only if exists
        if (array_search("anc", $p_option) !== false )
            $this->print_anc_writing();
        
        // if option contains EXTEND add document name + comment + action name
        // if options contains ANC export ANC plan table
    }
    function get_pdf() {
        return $this->pdf;
    }
    /**
     * @brief export the PDF to a file and returns the filename
     * @retun String filename 
     */
    function get_pdf_filename() {
        $file_name=$_ENV['TMP']."/"."acc_op".$this->acc_detail->det->jr_internal.".pdf";
        $this->pdf->Output($file_name, "F");
        return $file_name;
    }
    function download_pdf() {
        $this->pdf->Output("acc_op".$this->acc_detail->det->jr_internal.".pdf");
    }
    /**
     * @brief unlink the file if exists
     */
    function unlink() {
        $file=$this->get_pdf_filename();
        if ( is_file($file)) unlink ($file);
    }
}
