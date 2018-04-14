<?php

/*
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


/**
 * @file 
 * @brief Manage the CSV : manage files and write CSV record
 *
 */


/**
 * @brief Manage the CSV : manage files and write CSV record
 *
 */
class Noalyss_Csv
{

    private $filename;
    private $element;
    private $sep_field;
    private $sep_dec;
    private $encoding;

    function __construct($p_filename)
    {
        $this->filename=$p_filename;
        $this->element=array();
        $this->size=0;
     
        $a_field=[';',','];
        $this->sep_field=$a_field[$_SESSION['csv_fieldsep']];
        $a_field=['.',','];
        $this->sep_dec=$a_field[$_SESSION['csv_decimal']];
        $this->encoding=$_SESSION['csv_encoding'];
    
    }

    /***
     * @brief
     * Correct the name of the file , remove forbidden character and
     * add extension and date
     */
    private  function correct_name()
    {
        if (trim(strlen($this->filename))==0) {
            record_log('CSV->correct_name filename is empty');
            throw new Exception('CSV-CORRECT_NAME');
        }
        $this->filename.="-".date("ymd-Hi");
        $this->filename.=".csv";
        
        $this->filename=str_replace(";", "", $this->filename);
        $this->filename=str_replace("/", "", $this->filename);
        $this->filename=str_replace(":", "", $this->filename);
        $this->filename=str_replace("*", "", $this->filename);
        $this->filename=str_replace(" ", "_", $this->filename);
        $this->filename=strtolower($this->filename);
    }

    /***
     * Send an header for CSV , the filename is corrected 
     */
    function send_header()
    {
        $this->correct_name();
        header('Pragma: public');
        header('Content-type: application/csv');
        header("Content-Disposition: attachment;filename=\"{$this->filename}\"",
                FALSE);
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: Sun, 1 Jan 2000 12:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
    }

    /***
     * write header
     * @param array $p_array Array of 1 dimension with the contains of a row
     * 
     */
    function write_header($p_array)
    {
        $size_array=count($p_array);
        $sep="";
        for ($i=0; $i<$size_array; $i++)
        {
            
            printf($sep.'"%s"', $this->encode($p_array[$i]));
            $sep=$this->sep_field;
        }
        printf("\r\n");
    }
    /***
     * Add column to export to csv , the string are enclosed with 
     * double-quote,
     * @param $p_item value to export
     * @param $p_type must be text(default) or number
     * @throws Exception
     */
    function add($p_item,$p_type="text") 
    {
        if ( ! in_array($p_type, array("text","number"))) {
                throw new Exception("NOALYSS_CSV::ADD");
        }
        $this->element[$this->size]['value']=$p_item;
        $this->element[$this->size]['type']=$p_type;
        $this->size++;
    }
    /***
     * the string are enclosed with  double-quote,
     *  we remove problematic character and
     * the number are formatted.
     * Clean the row after exporting
     * @return nothing
     */
    function write() 
    {
        if ($this->size == 0 ) return;
        $sep="";
        for ($i=0;$i < $this->size;$i++)
        {
            if ($this->element[$i]['type'] == 'number' )
            {
                printf($sep.'%s', $this->nb($this->element[$i]['value']));
            }
            else
            {
                // remove break-line, 
                $export=str_replace("\n"," ",$this->element[$i]['value']);
                $export=str_replace("\r"," ", $export);
                // remove double quote
                $export=str_replace('"',"", $export);
                printf($sep.'"%s"', $this->encode($export));
            }
            $sep=$this->sep_field;
        }
        printf("\r\n");
        $this->clean();
    }
    /**
     * clean the row
     */
    private function clean()
    {
        $this->element=array();
        $this->size=0;
    }
    /**
    * format the number for the CSV export
    * @param $p_number number
    */
   private function nb($p_number)
   {
       $p_number=trim($p_number);
       if ($p_number=="") {return $p_number;}
       $r=number_format($p_number, 4, $this->sep_dec,'');
       return $r;
   }
   private function encode($str)
   {
       if ($this->encoding=="utf8") return $str;
       if ($this->encoding=="latin1") return utf8_decode ($str);
       throw new Exception(_("Encodage invalide"));
   }
            

}
