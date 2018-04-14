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
require_once NOALYSS_INCLUDE.'/class/pdf_operation.class.php';
require_once NOALYSS_INCLUDE.'/lib/progress_bar.class.php';
/**
 * @brief Export DOCUMENT from Analytic accountancy, can transform into PDF
 * and add a stamp on each pages
 * 
 * It depends on PDFTK and CONVERT_GIF_PDF
 */
class Document_Export
{
    /**
     *@brief create 2 temporary folders, store_pdf and store_convert, initialize
     * an array feedback containing messages
     * 
     */
    function __construct()
    {
        // Create 2 temporary folders   1. convert to PDF + stamp
        //                              2. store result
        $this->feedback = array();
        $this->store_convert = tempnam($_ENV['TMP'], 'convert_');
        $this->store_pdf = tempnam($_ENV['TMP'], 'pdf_');
        unlink($this->store_convert);
        unlink($this->store_pdf);
        umask(0);
        mkdir($this->store_convert);
        mkdir($this->store_pdf);
    }
    /**
     * @brief concatenate all PDF into a single one and save it into the
     * store_pdf folder.
     * If an error occurs then it is added to feedback
     */
    function concatenate_pdf()
    {
        try
        {
            $this->check_file();
            $stmt=PDFTK." ".$this->store_pdf.'/stamp_*pdf  output '.$this->store_pdf.'/result.pdf';
            $status=0;
            echo $stmt;
            passthru($stmt, $status);

            if ($status<>0)
            {
                $cnt_feedback=count($this->feedback);
                $this->feedback[$cnt_feedback]['file']='result.pdf';
                $this->feedback[$cnt_feedback]['message']=' cannot concatenate PDF';
                $this->feedback[$cnt_feedback]['error']=$status;
            }
        }
        catch (Exception $exc)
        {
            $cnt_feedback=count($this->feedback);
            $this->feedback[$cnt_feedback]['file']=' ';
            $this->feedback[$cnt_feedback]['message']=$exc->getMessage();
            $this->feedback[$cnt_feedback]['error']=0;
        }
    }

    function move_file($p_source, $target)
    {
        $this->check_file();
        copy($p_source, $this->store_pdf . '/' . $target);
    }
    /**
     * @brief send the resulting PDF to the browser
     */
    function send_pdf()
    {
        header('Content-Type: application/x-download');
        header('Content-Disposition: attachment; filename="result.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo file_get_contents($this->store_pdf . '/result.pdf');
    }
    /**
     * @brief remove folder and its content
     */
    function clean_folder()
    {
        $files=  scandir($this->store_convert);
        $nb_file=count($files);
        for ($i=0;$i < $nb_file;$i++) {
            if (is_file($this->store_convert."/".$files[$i])) unlink($this->store_convert."/".$files[$i]);
        }
        rmdir($this->store_convert);
        $files=  scandir($this->store_pdf);
        $nb_file=count($files);
        for ($i=0;$i < $nb_file;$i++) {
            if (is_file($this->store_pdf."/".$files[$i])) unlink($this->store_pdf."/".$files[$i]);
        }
        rmdir($this->store_pdf);
        
    }

    /**
     * @brief export all the pieces in PDF and transform them into a PDF with
     * a stamp. If an error occurs then $this->feedback won't be empty
     * @param $p_array contents all the jr_id
     */
    function export_all($p_array, Progress_Bar $progress)
    {
        $this->check_file();
        if ( count($p_array)==0) return;
        ob_start();
        $cnt_feedback=0;
        global $cn;
        // follow progress
        $step=round(16/count($p_array),2);
        
        $cn->start();
        foreach ($p_array as $value)
        {
            $progress->increment($step);
            // For each file save it into the temp folder,
            $file = $cn->get_array('select jr_pj,jr_pj_name,jr_pj_number,jr_pj_type from jrn '
                    . ' where jr_id=$1', array($value));
            if ($file[0]['jr_pj'] == '')
                continue;

            $filename=clean_filename($file[0]['jr_pj_name']);
            $cn->lo_export($file[0]['jr_pj'], $this->store_convert . '/' . $filename);

            // Convert this file into PDF 
            if ($file[0]['jr_pj_type'] != 'application/pdf')
            {
                $status = 0;
                $arg=" ".escapeshellarg($this->store_convert.DIRECTORY_SEPARATOR.$filename);
                echo "arg = [".$arg."]";
                passthru(OFFICE . " " . $arg , $status);
                if ($status <> 0)
                {
                    $this->feedback[$cnt_feedback]['file'] = $filename;
                    $this->feedback[$cnt_feedback]['message'] = ' cannot convert to PDF';
                    $this->feedback[$cnt_feedback]['error'] = $status;
                    $cnt_feedback++;
                    continue;
                }
            } 
            // Create a image with the stamp + formula
            $img = imagecreatefromgif(NOALYSS_INCLUDE . '/template/template.gif');
            $font = imagecolorallocatealpha($img, 100, 100, 100, 110);
            imagettftext($img, 40, 25, 500, 1000, $font, NOALYSS_INCLUDE . '/tfpdf/font/unifont/DejaVuSans.ttf', _("Copie certifiée conforme à l'original"));
            imagettftext($img, 40, 25, 550, 1100, $font, NOALYSS_INCLUDE. '/tfpdf/font/unifont/DejaVuSans.ttf', $file[0]['jr_pj_number']);
            imagettftext($img, 40, 25, 600, 1200, $font, NOALYSS_INCLUDE. '/tfpdf/font/unifont/DejaVuSans.ttf', $file[0]['jr_pj_name']);
            imagegif($img, $this->store_convert . '/' . 'stamp.gif');

            // transform gif file to pdf with convert tool
            $stmt = CONVERT_GIF_PDF . " " . escapeshellarg($this->store_convert . '/' . 'stamp.gif') . " " . escapeshellarg($this->store_convert . '/stamp.pdf');
            passthru($stmt, $status);
            if ($status <> 0)
            {
                $this->feedback[$cnt_feedback]['file'] = 'stamp.pdf';
                $this->feedback[$cnt_feedback]['message'] = ' cannot convert to PDF';
                $this->feedback[$cnt_feedback]['error'] = $status;
                $cnt_feedback++;
                continue;
            }
      
             $progress->increment($step);
            // 
            // remove extension
            $ext = strrpos($filename, ".");
            $file_pdf = substr($filename, 0, $ext);
            $file_pdf .=".pdf";
            
            //-----------------------------------
            // Fix broken PDF , actually pdftk can not handle all the PDF
            if ( FIX_BROKEN_PDF == 'YES' && PDF2PS != 'NOT' && PS2PDF != 'NOT') {
                
                $stmpt = PDF2PS." ". escapeshellarg($this->store_convert . '/' . $file_pdf)." ". escapeshellarg($this->store_convert . '/' . $file_pdf.'.ps');
                
                passthru($stmpt,$status);
                
                if ($status <> 0)
                {
                    $this->feedback[$cnt_feedback]['file'] = $this->store_convert . '/' . $file_pdf;
                    $this->feedback[$cnt_feedback]['message'] = ' cannot force to PDF';
                    $this->feedback[$cnt_feedback]['error'] = $status;
                    $cnt_feedback++;
                    continue;
                }
                $stmpt = PS2PDF." ". escapeshellarg($this->store_convert . '/' . $file_pdf.'.ps')." ". escapeshellarg($this->store_convert . '/' . $file_pdf.'.2');
                
                passthru($stmpt,$status);
                
                if ($status <> 0)
                {
                    $this->feedback[$cnt_feedback]['file'] = $this->store_convert . '/' . $file_pdf;
                    $this->feedback[$cnt_feedback]['message'] = ' cannot force to PDF';
                    $this->feedback[$cnt_feedback]['error'] = $status;
                    $cnt_feedback++;
                    continue;
                }
                rename ($this->store_convert . '/' . $file_pdf.'.2',$this->store_convert . '/' . $file_pdf);
            }
            $progress->increment($step);
            // output
            $output = $this->store_convert . '/stamp_' . $file_pdf;
            
            // Concatenate stamp + file
            $stmt = PDFTK . " " . escapeshellarg($this->store_convert . '/' . $file_pdf) . ' stamp ' . $this->store_convert .
                    '/stamp.pdf output ' . $output;

            passthru($stmt, $status);
            if ($status <> 0)
            {

                $this->feedback[$cnt_feedback]['file'] = $file_pdf;
                $this->feedback[$cnt_feedback]['message'] = _(' ne peut pas convertir en PDF');
                $this->feedback[$cnt_feedback]['error'] = $status;
                $cnt_feedback++;
                continue;
            }
            
            // create the pdf with the detail of operation
            $detail_operation = new PDF_Operation($cn,$value);
            $detail_operation->export_pdf(array("acc","anc"));

            // output 2
            $output2 = $this->store_convert . '/operation_' . $file_pdf;
            
            // concatenate detail operation with the output
            $stmt = PDFTK . " " . $detail_operation->get_pdf_filename()." ".$output. 
                    ' output ' . $output2;
            
            $progress->increment($step);
            passthru($stmt, $status);
            if ($status <> 0)
            {

                $this->feedback[$cnt_feedback]['file'] = $file_pdf;
                $this->feedback[$cnt_feedback]['message'] = _('Echec Ajout detail ');
                $this->feedback[$cnt_feedback]['error'] = $status;
                $cnt_feedback++;
                continue;
            }
            // remove doc with detail
            $detail_operation->unlink();
            
            // overwrite old with new PDF
            rename ($output2,$output);
            
            // Move the PDF into another temp directory 
            $this->move_file($output, 'stamp_' . $file_pdf);
        }
        
        $progress->set_value(93);
        // concatenate all pdf into one
        $this->concatenate_pdf();
        
        
        ob_clean();
        $this->send_pdf();

        $progress->set_value(100);
        // remove files from "conversion folder"
        $this->clean_folder();
        
    }
   /**
    * @brief check that the files are installed
    * throw a exception if one is missing
    */
    function check_file()
    {
        try 
        {
            if (CONVERT_GIF_PDF == 'NOT') throw new Exception(_("CONVERT_GIF_PDF n'est pas installé"));
            if (PDFTK          == 'NOT')  throw new Exception(_("TKPDF n'est pas installé"));
            if ( FIX_BROKEN_PDF == 'YES') {
                if (PS2PDF == 'NOT')    throw new Exception(_('PS2PDF non installé'));
                if (PDF2PS == 'NOT')    throw new Exception(_('PDF2PS non installé'));
            }
        } catch (Exception $ex) 
        {
            throw ($ex);
        }
    }
}
