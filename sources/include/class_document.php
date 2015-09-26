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
require_once NOALYSS_INCLUDE.'/class_own.php';
require_once NOALYSS_INCLUDE.'/class_acc_account_ledger.php';
require_once NOALYSS_INCLUDE.'/class_follow_up.php';
require_once NOALYSS_INCLUDE.'/class_acc_tva.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_zip_extended.php';

/*! \file
 * \brief Class Document corresponds to the table document
 */
/*! \brief Class Document corresponds to the table document
 */
class Document
{
    var $db;          /*!< $db Database connexion*/
    var $d_id;        /*!< $d_id Document id */
    var $ag_id;       /*!< $ag_id action_gestion.ag_id (pk) */
    var $d_mimetype;  /*!< $d_mimetype  */
    var $d_filename;  /*!< $d_filename */
    var $d_lob;       /*!< $d_lob the oid of the lob */
    var $d_description;       /*!< Description of the file*/
    var $d_number;    /*!< $d_number number of the document */
    var $md_id;       /*!< $md_id document's template */
    /* Constructor
     * \param $p_cn Database connection
     */
    function Document($p_cn,$p_d_id=0)
    {
        $this->db=$p_cn;
        $this->d_id=$p_d_id;
        $this->counter=0;
    }
    /*!\brief insert a minimal document and set the d_id
     */
    function blank()
    {
        $this->d_id=$this->db->get_next_seq("document_d_id_seq");
        // affect a number
        $this->d_number=$this->db->get_next_seq("seq_doc_type_".$this->md_type);
        $sql=sprintf('insert into document(d_id,ag_id,d_number) values(%d,%d,%d)',
                     $this->d_id,
                     $this->ag_id,
                     $this->d_number);
        $this->db->exec_sql($sql);

    }
	function compute_filename($pj,$filename)
	{
		foreach (array('/','*','<','>',';',',','\\','.',':') as $i) {
			$pj= str_replace($i, "-",$pj);
		}
		// save the suffix
		$pos_prefix=strrpos($filename,".");
		if ($pos_prefix == 0) $pos_prefix=strlen($filename);
		$filename_no=substr($filename,0,$pos_prefix);
		$filename_suff=substr($filename,$pos_prefix,strlen($filename));
		$new_filename=  strtolower($filename_no."-".$pj.$filename_suff);
		return $new_filename;
	}
    /*!
     * \brief Generate the document, Call $this-\>Replace to replace
     *        tag by value
     *@param p_array contains the data normally it is the $_POST
     *@param contains the new filename
     * \return an array : the url where the generated doc can be found, the name
     * of the file and his mimetype
     */
    function Generate($p_array,$p_filename="")
    {
        // create a temp directory in /tmp to unpack file and to parse it
        $dirname=tempnam($_ENV['TMP'],'doc_');


        unlink($dirname);
        mkdir ($dirname);
        // Retrieve the lob and save it into $dirname
        $this->db->start();
        $dm_info="select md_name,md_type,md_lob,md_filename,md_mimetype
                 from document_modele where md_id=".$this->md_id;
        $Res=$this->db->exec_sql($dm_info);

        $row=Database::fetch_array($Res,0);
        $this->d_lob=$row['md_lob'];
        $this->d_filename=$row['md_filename'];
        $this->d_mimetype=$row['md_mimetype'];
        $this->d_name=$row['md_name'];


        chdir($dirname);
        $filename=$row['md_filename'];
        $exp=$this->db->lo_export($row['md_lob'],$dirname.DIRECTORY_SEPARATOR.$filename);
        if ( $exp === false ) echo_warning( __FILE__.":".__LINE__."Export NOK $filename");

        $type="n";
        // if the doc is a OOo, we need to unzip it first
        // and the name of the file to change is always content.xml
        if ( strpos($row['md_mimetype'],'vnd.oasis') != 0 )
        {
            ob_start();
	    $zip = new Zip_Extended;
	    if ($zip->open($filename) === TRUE) {
	      $zip->extractTo($dirname.DIRECTORY_SEPARATOR);
	      $zip->close();
	    } else {
	      echo __FILE__.":".__LINE__."cannot unzip model ".$filename;
	    }

            // Remove the file we do  not need anymore
            unlink($filename);
            ob_end_clean();
            $file_to_parse="content.xml";
            $type="OOo";
        }
        else
            $file_to_parse=$filename;
        // affect a number
        $this->d_number=$this->db->get_next_seq("seq_doc_type_".$row['md_type']);

        // parse the document - return the doc number ?
        $this->ParseDocument($dirname,$file_to_parse,$type,$p_array);

        $this->db->commit();
        // if the doc is a OOo, we need to re-zip it
        if ( strpos($row['md_mimetype'],'vnd.oasis') != 0 )
        {
            ob_start();
	    $zip = new Zip_Extended;
            $res = $zip->open($filename, ZipArchive::CREATE);
            if($res !== TRUE)
	      {
		throw new Exception ( __FILE__.":".__LINE__."cannot recreate zip");
	      }
	    $zip->add_recurse_folder($dirname.DIRECTORY_SEPARATOR);
	    $zip->close();

            ob_end_clean();

            $file_to_parse=$filename;
        }
		if ( $p_filename !="") {

			$this->d_filename=$this->compute_filename($p_filename, $this->d_filename);
		}
        $this->SaveGenerated($dirname.DIRECTORY_SEPARATOR.$file_to_parse);
        // Invoice
        $ret='<A class="mtitle" HREF="show_document.php?d_id='.$this->d_id.'&'.dossier::get().'">Document g&eacute;n&eacute;r&eacute;</A>';
        @rmdir($dirname);
        return $ret;
    }

    /*! ParseDocument
     * \brief This function parse a document and replace all
     *        the predefined tags by a value. This functions
     *        generate diffent documents (invoice, order, letter)
     *        with the info from the database
     *
     * \param $p_dir directory name
     * \param $p_file filename
     * \param $p_type For the OOo document the tag are &lt and &gt instead of < and >
     * \param $p_array variable from $_POST
     */
    function ParseDocument($p_dir,$p_file,$p_type,$p_array)
    {

        /*!\note Replace in the doc the tags by their values.
         *  - MY_*   table parameter
         *  - ART_VEN* table quant_sold for invoice
         *  - CUST_* table quant_sold and fiche for invoice
         *  - e_* for the invoice in the $_POST
         */
        // open the document
        $infile_name=$p_dir.DIRECTORY_SEPARATOR.$p_file;
        $h=fopen($infile_name,"r");

        // check if tmpdir exist otherwise create it
        $temp_dir=$_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'tmp';
        if ( is_dir($temp_dir) == false )
        {
            if ( mkdir($temp_dir) == false )
            {
                $msg=_("Ne peut pas créer le répertoire ".$temp_dir); 
                throw new Exception($msg);
            }
        }
        // Compute output_name
        $output_name=tempnam($temp_dir,"gen_doc_");
        $output_file=fopen($output_name,"w+");
        // check if the opening is sucessfull
        if (  $h === false )
        {
            echo __FILE__.":".__LINE__."cannot open $p_dir $p_file ";
            $msg=_("Ne peut pas ouvrir $p_dir $p_file"); 
            throw new Exception($msg);
        }
        if ( $output_file == false)
        {
            $msg=_("Ne peut pas ouvrir $p_dir $p_file"); 
            echo $msg;
            throw new Exception($msg);
        }
        // compute the regex
        if ( $p_type=='OOo')
        {
            $regex="/=*&lt;&lt;[A-Z]+_*[A-Z]*_*[A-Z]*_*[A-Z]*_*[0-9]*&gt;&gt;/i";
            $lt="&lt;";
            $gt="&gt;";
        }
        else
        {
            $regex="/=*<<[A-Z]+_*[A-Z]*_*[A-Z]*_*[A-Z]*_*[0-9]*>>/i";
            $lt="<";
            $gt=">";
        }

        //read the file
        while(! feof($h))
	  {
            // replace the tag
            $buffer=fgets($h);
            // search in the buffer the magic << and >>
            // while preg_match_all finds something to replace
            while ( preg_match_all ($regex,$buffer,$f) >0  )
	      {


                foreach ( $f as $apattern )
		  {


		    foreach($apattern as $pattern)
		      {


			$to_remove=$pattern;
			// we remove the < and > from the pattern
			$tag=str_replace($lt,'',$pattern);
			$tag=str_replace($gt,'',$tag);


			// if the pattern if found we replace it
			$value=$this->Replace($tag,$p_array);
			if ( strpos($value,'ERROR') != false ) 		  $value="";
                        /*
                         * Change type of cell to numeric
                         *  allow numeric cel in ODT for the formatting and formula
                         */
			if ( is_numeric($value) && $p_type=='OOo')
			  {
			    $searched='/office:value-type="string"><text:p>'.$pattern.'/';
			    $replaced='office:value-type="float" office:value="'.$value.'"><text:p>'.$pattern;
			    $buffer=preg_replace($searched, $replaced, $buffer,1);
			  }
			// replace into the $buffer
			// take the position in the buffer
			$pos=strpos($buffer,$to_remove);
			// get the length of the string to remove
			$len=strlen($to_remove);
			if ( $p_type=='OOo' )
			  {
			    $value=str_replace('&','&amp;',$value);
			    $value=str_replace('<','&lt;',$value);
			    $value=str_replace('>','&gt;',$value);
			    $value=str_replace('"','&quot;',$value);
			    $value=str_replace("'",'&apos;',$value);
			  }
			$buffer=substr_replace($buffer,$value,$pos,$len);

			// if the pattern if found we replace it
		      }
		  }
	      }
            // write into the output_file
            fwrite($output_file,$buffer);

	  }
        fclose($h);
        fclose($output_file);
        if ( ($ret=copy ($output_name,$infile_name)) == FALSE )
        {
            echo _('Ne peut pas sauver '.$output_name.' vers '.$infile_name.' code d\'erreur ='.$ret);
        }


    }
    /*! SaveGenerated
     * \brief Save the generated Document
     * \param $p_file is the generated file
     *
     *
     * \return 0 if no error otherwise 1
     */
    function SaveGenerated($p_file)
    {
        // We save the generated file
        $doc=new Document($this->db);
        $this->db->start();
        $this->d_lob=$this->db->lo_import($p_file);
        if ( $this->d_lob == false )
        {
            echo "ne peut pas importer [$p_file]";
            return 1;
        }

        $sql="insert into document(ag_id,d_lob,d_number,d_filename,d_mimetype)
             values ($1,$2,$3,$4,$5)";

        $this->db->exec_sql($sql,      array($this->ag_id,
                                             $this->d_lob,
                                             $this->d_number,
                                             $this->d_filename,
                                             $this->d_mimetype));
        $this->d_id=$this->db->get_current_seq("document_d_id_seq");
        // Clean the file
        unlink ($p_file);
        $this->db->commit();
        return 0;
    }
    /*! Upload
     * \brief Upload a file into document
     *  all the needed data are in $_FILES we don't increment the seq
     * \param $p_file : array containing by default $_FILES
     *
     * \return
     */
    function Upload($p_ag_id)
    {
        // nothing to save
        if ( sizeof($_FILES) == 0 ) return;

        /* for several files  */
        /* $_FILES is now an array */
        // Start Transaction
        $this->db->start();
        $name=$_FILES['file_upload']['name'];
        for ($i = 0; $i < sizeof($name);$i++)
        {
            $new_name=tempnam($_ENV['TMP'],'doc_');
            // check if a file is submitted
            if ( strlen($_FILES['file_upload']['tmp_name'][$i]) != 0 )
            {
                // upload the file and move it to temp directory
                if ( move_uploaded_file($_FILES['file_upload']['tmp_name'][$i],$new_name))
                {
                    $oid=$this->db->lo_import($new_name);
                    // check if the lob is in the database
                    if ( $oid == false )
                    {
                        $this->db->rollback();
                        return 1;
                    }
                }
                // the upload in the database is successfull
                $this->d_lob=$oid;
                $this->d_filename=$_FILES['file_upload']['name'][$i];
                $this->d_mimetype=$_FILES['file_upload']['type'][$i];
                $this->d_description=  strip_tags($_POST['input_desc'][$i]);
                // insert into  the table
                $sql="insert into document (ag_id, d_lob,d_filename,d_mimetype,d_number,d_description) values ($1,$2,$3,$4,$5,$6)";
                $this->db->exec_sql($sql,array($p_ag_id,$this->d_lob,$this->d_filename,$this->d_mimetype,1,$this->d_description));
            }
        } /* end for */
        $this->db->commit();

    }
    /**
     * Copy a existing OID (LOB) into the table document
     * @note  use of global variable $cn which is the db connx to the current folder
     * @param type $p_ag_id Follow_Up::ag_id
     * @param type $p_lob oid of existing document
     * @param type $p_filename filename of existing document
     * @param type $p_mimetype mimetype of existing document
     * @param type $p_description Description of existing document (default empty)
     */
    static function insert_existing_document($p_ag_id, $p_lob, $p_filename, $p_mimetype, $p_description = "")
    {
        global $cn;
        // insert into  the table
        $sql = "insert into document (ag_id, d_lob,d_filename,d_mimetype,d_number,d_description) values ($1,$2,$3,$4,$5,$6)";
        $cn->exec_sql($sql, array($p_ag_id, $p_lob, $p_filename, $p_mimetype, 1, $p_description));
    }

    /*! a_ref
     * \brief create and compute a string for reference the doc <A ...>
     *
     * \return a string
     */
    function anchor()
    {
        if ( $this->d_id == 0 )
            return '';
        $image='<IMG SRC="image/insert_table.gif" title="'.$this->d_filename.'" border="0">';
        $r="";
        $r='<A class="mtitle" HREF="show_document.php?d_id='.$this->d_id.'&'.dossier::get().'">'.$image.'</A>';
        return $r;
    }
    /** Get
     * \brief Send the document
     */
    function Send()
    {
        // retrieve the template and generate document
        $this->db->start();
        $ret=$this->db->exec_sql(
                 "select d_id,d_lob,d_filename,d_mimetype from document where d_id=".$this->d_id );
        if ( Database::num_row ($ret) == 0 )
            return;
        $row=Database::fetch_array($ret,0);
        //the document  is saved into file $tmp
        $tmp=tempnam($_ENV['TMP'],'document_');
        $this->db->lo_export($row['d_lob'],$tmp);
        $this->d_mimetype=$row['d_mimetype'];
        $this->d_filename=$row['d_filename'];

        // send it to stdout
        ini_set('zlib.output_compression','Off');
        header("Pragma: public");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate");
        header('Content-type: '.$this->d_mimetype);
        header('Content-Disposition: attachment;filename="'.$this->d_filename.'"',FALSE);
        header("Accept-Ranges: bytes");
        $file=fopen($tmp,'r');
        while ( !feof ($file) )
        {
            echo fread($file,8192);
        }
        fclose($file);

        unlink ($tmp);

        $this->db->commit();

    }
    /*!\brief get all the document of a given action
     *\param $ag_id the ag_id from action::ag_id (primary key)
     *\return an array of objects document or an empty array if nothing found
     */
    function get_all($ag_id)
    {
        $res=$this->db->get_array('select d_id, ag_id, d_lob, d_number, d_filename,'.
                                  ' d_mimetype,d_description from document where ag_id=$1',array($ag_id));
        $a=array();
        for ($i=0;$i<sizeof($res); $i++ )
        {
            $doc=new Document($this->db);
            $doc->d_id=$res[$i]['d_id'];
            $doc->ag_id=$res[$i]['ag_id'];
            $doc->d_lob=$res[$i]['d_lob'];
            $doc->d_number=$res[$i]['d_number'];
            $doc->d_filename=$res[$i]['d_filename'];
            $doc->d_mimetype=$res[$i]['d_mimetype'];
            $doc->d_description=$row['d_description'];
            $a[$i]=clone $doc;
        }
        return $a;
    }

    /*!\brief Get  complete all the data member thx info from the database
     */
    function get()
    {
        $sql="select * from document where d_id=".$this->d_id;
        $ret=$this->db->exec_sql($sql);
        if ( Database::num_row($ret) == 0 )
            return;
        $row=Database::fetch_array($ret,0);
        $this->ag_id=$row['ag_id'];
        $this->d_mimetype=$row['d_mimetype'];
        $this->d_filename=$row['d_filename'];
        $this->d_lob=$row['d_lob'];
        $this->d_number=$row['d_number'];
        $this->d_description=$row['d_description'];

    }
    /*!
     * \brief replace the TAG by the real value, this value can be into
     * the database or in $_POST
     * The possible tags are
     *  - [CUST_NAME] customer's name
     *  - [CUST_ADDR_1] customer's address line 1
     *  - [CUST_CP] customer's ZIP code
     *  - [CUST_CO] customer's country
     *  - [CUST_CITY] customer's city
     *  - [CUST_VAT] customer's VAT
     *  - [MARCH_NEXT]   end this item and increment the counter $i
     *  - [DATE_LIMIT]
     *  - [VEN_ART_NAME]
     *  - [VEN_ART_PRICE]
     *  - [VEN_ART_QUANT]
     *  - [VEN_ART_TVA_CODE]
     *  - [VEN_ART_STOCK_CODE]
     *  - [VEN_HTVA]
     *  - [VEN_TVAC]
     *  - [VEN_TVA]
     *  - [TOTAL_VEN_HTVA]
     *  - [DATE_CALC]
     *  - [DATE]
     *  - [DATE_LIMIT]
     *  - [DATE_LIMIT_CALC]
     *  - [NUMBER]
     *  - [MY_NAME]
     *  - [MY_CP]
     *  - [MY_COMMUNE]
     *  - [MY_TVA]
     *  - [MY_STREET]
     *  - [MY_NUMBER]
     *  - [TVA_CODE]
     *  - [TVA_RATE]
     *  - [BON_COMMANDE]
     *  - [OTHER_INFO]
     *  - [CUST_NUM]
     *  - [CUST_BANQUE_NAME]
     *  - [CUST_BANQUE_NO]
     *  - [USER]
     *  - [REFERENCE]
     *  - [BENEF_NAME]
     *  - [BENEF_BANQUE_NAME]
     *  - [BENEF_BANQUE_NO]
     *  - [BENEF_ADDR_1]
     *  - [BENEF_CP]
     *  - [BENEF_CO]
     *  - [BENEF_CITY]
     *  - [BENEF_VAT]
     *  - [ACOMPTE]
     *  - [TITLE]
     *  - [DESCRIPTION]
     *
     * \param $p_tag TAG
     * \param $p_array data from $_POST
     * \return String which must replace the tag
     */
    function Replace($p_tag,$p_array)
    {
		global $g_parameter;
        $p_tag=strtoupper($p_tag);
        $p_tag=str_replace('=','',$p_tag);
        $r="Tag inconnu";

        switch ($p_tag)
        {
		case 'DATE':
			$r=(isset ($p_array['ag_timestamp']))?$p_array['ag_timestamp']:$p_array['e_date'];
			break;
        case 'DATE_CALC':
                $r=' Date inconnue ';
            // Date are in $p_array['ag_date']
            // or $p_array['e_date']
            if ( isset ($p_array['ag_timestamp'])) {
                $date=format_date($p_array['ag_timestamp'],'DD.MM.YYYY','YYYY-MM-DD');
                $r=$date;
            }
            if ( isset ($p_array['e_date'])) {
                $date=format_date($p_array['e_date'],'DD.MM.YYYY','YYYY-MM-DD');
                $r=$date;
            }
            break;
            //
            //  the company priv

        case 'MY_NAME':
            $r=$g_parameter->MY_NAME;
            break;
        case 'MY_CP':
            $r=$g_parameter->MY_CP;
            break;
        case 'MY_COMMUNE':
            $r=$g_parameter->MY_COMMUNE;
            break;
        case 'MY_TVA':
            $r=$g_parameter->MY_TVA;
            break;
        case 'MY_STREET':
            $r=$g_parameter->MY_STREET;
            break;
        case 'MY_NUMBER':
            $r=$g_parameter->MY_NUMBER;
            break;
        case 'MY_TEL':
            $r=$g_parameter->MY_TEL;
            break;
        case 'MY_FAX':
            $r=$g_parameter->MY_FAX;
            break;
        case 'MY_PAYS':
            $r=$g_parameter->MY_PAYS;
            break;

            // customer
            /*\note The CUST_* are retrieved thx the $p_array['tiers']
             * which contains the quick_code
             */
        case 'SOLDE':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $p=$tiers->strAttribut(ATTR_DEF_ACCOUNT);
            $poste=new Acc_Account_Ledger($this->db,$p);
            $r=$poste->get_solde(' true' );
            break;
        case 'CUST_NAME':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_NAME);
            break;
        case 'CUST_ADDR_1':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_ADRESS);

            break ;
        case 'CUST_CP':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_CP);

            break;
        case 'CUST_CITY':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_CITY);

            break;

        case 'CUST_CO':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_PAYS);

            break;
            // Marchandise in $p_array['e_march*']
            // \see user_form_achat.php or user_form_ven.php
        case 'CUST_VAT':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_NUMTVA);
            break;
        case 'CUST_NUM':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_NUMBER_CUSTOMER);
            break;
        case 'CUST_BANQUE_NO':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_BQ_NO);
            break;
        case 'CUST_BANQUE_NAME':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_dest'])?$p_array['qcode_dest']:$p_array['e_client'];
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_BQ_NAME);
            break;
            /* -------------------------------------------------------------------------------- */
            /* BENEFIT (fee notes */
        case 'BENEF_NAME':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_NAME);
            break;
        case 'BENEF_ADDR_1':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_ADRESS);

            break ;
        case 'BENEF_CP':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_CP);

            break;
        case 'BENEF_CITY':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_CITY);

            break;

        case 'BENEF_CO':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_PAYS);

            break;
            // Marchandise in $p_array['e_march*']
            // \see user_form_achat.php or user_form_ven.php
        case 'BENEF_VAT':
            $tiers=new Fiche($this->db);

            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_NUMTVA);
            break;
        case 'BENEF_NUM':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_NUMBER_CUSTOMER);
            break;
        case 'BENEF_BANQUE_NO':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_BQ_NO);
            break;
        case 'BENEF_BANQUE_NAME':
            $tiers=new Fiche($this->db);
            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            if ( $qcode=='')
            {
                $r='';
                break;
            }
            $tiers->get_by_qcode($qcode,false);
            $r=$tiers->strAttribut(ATTR_DEF_BQ_NAME);
            break;

            // Marchandise in $p_array['e_march*']
            // \see user_form_achat.php or user_form_ven.php
        case 'NUMBER':
            $r=$this->d_number;
            break;

        case 'USER' :
            return $_SESSION['use_name'].', '.$_SESSION['use_first_name'];

            break;
        case 'REFERENCE':
            $act=new Follow_Up($this->db);
            $act->ag_id=$this->ag_id;
            $act->get();
            $r=$act->ag_ref;
            break;

            /*
             *  - [VEN_ART_NAME]
             *  - [VEN_ART_PRICE]
             *  - [VEN_ART_QUANT]
             *  - [VEN_ART_TVA_CODE]
             *  - [VEN_ART_STOCK_CODE]
             *  - [VEN_HTVA]
             *  - [VEN_TVAC]
             *  - [VEN_TVA]
             *  - [TOTAL_VEN_HTVA]
             *  - [DATE_LIMIT]
             */
        case 'DATE_LIMIT_CALC':
            extract ($p_array);
            $id='e_ech' ;
            if ( !isset (${$id}) ) return "";
            $r=format_date(${$id},'DD.MM.YYYY','YYYY-MM-DD');
            break;
      case 'DATE_LIMIT':
            extract ($p_array);
            $id='e_ech' ;
            if ( !isset (${$id}) ) return "";
            $r=${$id};
            break;
        case 'MARCH_NEXT':
            $this->counter++;
            $r='';
            break;

        case 'VEN_ART_NAME':
            extract ($p_array);
            $id='e_march'.$this->counter;
            // check if the march exists
            if ( ! isset (${$id})) return "";
            // check that something is sold
            if ( ${'e_march'.$this->counter.'_price'} != 0 && ${'e_quant'.$this->counter} != 0 )
            {
                $f=new Fiche($this->db);
                $f->get_by_qcode(${$id},false);
                $r=$f->strAttribut(ATTR_DEF_NAME);
            }
            else $r = "";
            break;
       case 'VEN_ART_LABEL':
            extract ($p_array);
            $id='e_march'.$this->counter."_label";
            // check if the march exists

            if (! isset (${$id}) || (isset (${$id}) && strlen(trim(${$id})) == 0))
                {
                    $id = 'e_march' . $this->counter;
                    // check if the march exists
                    if (!isset(${$id}))
                        $r= "";
                    else 
                    {
                    // check that something is sold
                        if (${'e_march' . $this->counter . '_price'} != 0 && ${'e_quant' . $this->counter} != 0)
                        {
                            $f = new Fiche($this->db);
                            $f->get_by_qcode(${$id}, false);
                            $r = $f->strAttribut(ATTR_DEF_NAME);
                        } else
                            $r = "";
                    }
                }
                else
                    $r=${'e_march'.$this->counter.'_label'};
            break;
        case 'VEN_ART_STOCK_CODE':
            extract ($p_array);
                    $id = 'e_march' . $this->counter;
                    // check if the march exists
                    if (!isset(${$id}))
                        $r= "";
                    else 
                    {
                    // check that something is sold
                        if (${'e_march' . $this->counter . '_price'} != 0 && ${'e_quant' . $this->counter} != 0)
                        {
                            $f = new Fiche($this->db);
                            $f->get_by_qcode(${$id}, false);
                            $r = $f->strAttribut(ATTR_DEF_STOCK);
                            $r=($r == NOTFOUND)?'':$r;
                        } 
                    }
            break;
        case 'VEN_ART_PRICE':
            extract ($p_array);
            $id='e_march'.$this->counter.'_price' ;
            if ( !isset (${$id}) ) return "";
			if (${$id} == 0 ) return "";
            $r=${$id};
            break;

        case 'TVA_RATE':
        case 'VEN_ART_TVA_RATE':
            extract ($p_array);
            $id='e_march'.$this->counter.'_tva_id';
            if ( !isset (${$id}) ) return "";
            if ( ${$id} == -1 || ${$id}=='' ) return "";
            $march_id='e_march'.$this->counter.'_price' ;
            if ( ! isset (${$march_id})) return '';
            $tva=new Acc_Tva($this->db);
            $tva->set_parameter("id",${$id});
            if ( $tva->load() == -1) return '';
            return $tva->get_parameter("rate");
            break;

        case 'TVA_CODE':
        case 'VEN_ART_TVA_CODE':
            extract ($p_array);
            $id='e_march'.$this->counter.'_tva_id';
            if ( !isset (${$id}) ) return "";
            if ( ${$id} == -1 ) return "";
            $qt='e_quant'.$this->counter;
            $price='e_march'.$this->counter.'_price' ;
            if ( ${$price} == 0 || ${$qt} == 0
                    || strlen(trim( $price )) ==0
                    || strlen(trim($qt)) ==0)
                return "";

            $r=${$id};
            break;

        case 'TVA_LABEL':
            extract ($p_array);
            $id='e_march'.$this->counter.'_tva_id';
            if ( !isset (${$id}) ) return "";
            $march_id='e_march'.$this->counter.'_price' ;
            if ( ! isset (${$march_id})) return '';
            if ( ${$march_id} == 0) return '';
            $tva=new Acc_Tva($this->db,${$id});
            if ($tva->load() == -1 ) return "";
            $r=$tva->get_parameter('label');

            break;

            /* total VAT for one sold */
        case 'TVA_AMOUNT':
        case 'VEN_TVA':
            extract ($p_array);
            $qt='e_quant'.$this->counter;
            $price='e_march'.$this->counter.'_price' ;
            $tva='e_march'.$this->counter.'_tva_id';
            /* if we do not use vat this var. is not set */
            if ( !isset(${$tva}) ) return '';
            if ( !isset (${'e_march'.$this->counter}) ) return "";
            // check that something is sold
            if ( ${$price} == 0 || ${$qt} == 0
                    || strlen(trim( $price )) ==0
                    || strlen(trim($qt)) ==0)
                return "";
            $r=${'e_march'.$this->counter.'_tva_amount'};
            break;
            /* TVA automatically computed */
        case 'VEN_ART_TVA':
        
            extract ($p_array);
            $qt='e_quant'.$this->counter;
            $price='e_march'.$this->counter.'_price' ;
            $tva='e_march'.$this->counter.'_tva_id';
            if ( !isset (${'e_march'.$this->counter}) ) return "";
            // check that something is sold
            if ( ${$price} == 0 || ${$qt} == 0
                    || strlen(trim( $price )) ==0
                    || strlen(trim($qt)) ==0)
                return "";
            $oTva=new Acc_Tva($this->db,${$tva});
            if ($oTva->load() == -1 ) return "";
            $r=round(${$price},2)*$oTva->get_parameter('rate');
            $r=round($r,2);
            break;

        case 'VEN_ART_TVAC':
            extract ($p_array);
            $qt='e_quant'.$this->counter;
            $price='e_march'.$this->counter.'_price' ;
            $tva='e_march'.$this->counter.'_tva_id';
            if ( !isset (${'e_march'.$this->counter}) ) return "";
            // check that something is sold
            if ( ${$price} == 0 || ${$qt} == 0
                    || strlen(trim( $price )) ==0
                    || strlen(trim($qt)) ==0)
                return "";
            if ( ! isset (${$tva}) ) return '';
            $tva=new Acc_Tva($this->db,${$tva});
            if ($tva->load() == -1 )
            {
                $r=round(${$price},2);
            }
            else
            {
                $r=round(${$price}*$tva->get_parameter('rate')+${$price},2);
            }

            break;

        case 'VEN_ART_QUANT':
            extract ($p_array);
            $id='e_quant'.$this->counter;
            if ( !isset (${$id}) ) return "";
            // check that something is sold
            if ( ${'e_march'.$this->counter.'_price'} == 0
                    || ${'e_quant'.$this->counter} == 0
                    || strlen(trim( ${'e_march'.$this->counter.'_price'} )) ==0
                    || strlen(trim(${'e_quant'.$this->counter})) ==0 )
                return "";
            $r=${$id};
            break;

        case 'VEN_HTVA':
            extract ($p_array);
            $id='e_march'.$this->counter.'_price' ;
            $quant='e_quant'.$this->counter;
            if ( !isset (${$id}) ) return "";

            // check that something is sold
            if ( ${'e_march'.$this->counter.'_price'} == 0 || ${'e_quant'.$this->counter} == 0
                    || strlen(trim( ${'e_march'.$this->counter.'_price'} )) ==0
                    || strlen(trim(${'e_quant'.$this->counter})) ==0)
                return "";
			bcscale(4);
            $r=bcmul(${$id},${$quant});
			$r=round($r,2);
            break;

        case 'VEN_TVAC':
            extract ($p_array);
            $id='e_march'.$this->counter.'_tva_amount' ;
            $price='e_march'.$this->counter.'_price' ;
            $quant='e_quant'.$this->counter;
            if ( ! isset(${'e_march'.$this->counter.'_price'})|| !isset(${'e_quant'.$this->counter}))     return "";
            // check that something is sold
            if ( ${'e_march'.$this->counter.'_price'} == 0 || ${'e_quant'.$this->counter} == 0 ) return "";
			bcscale(4);
            // if TVA not exist
            if ( ! isset(${$id}))
                $r=  bcmul(${$price},${$quant});
            else{
                $r=  bcmul(${$price},${$quant});
                $r=bcadd($r,${$id});
			}
			$r=round($r,2);
			return $r;
            break;

        case 'TOTAL_VEN_HTVA':
            extract($p_array);
			bcscale(4);
            $sum=0.0;
            for ($i=0;$i<$nb_item;$i++)
            {
                $sell='e_march'.$i.'_price';
                $qt='e_quant'.$i;

                if ( ! isset (${$sell}) ) break;

                if ( strlen(trim(${$sell})) == 0 ||
                        strlen(trim(${$qt})) == 0 ||
                        ${$qt}==0 || ${$sell}==0)
                    continue;
                $tmp1=bcmul(${$sell},${$qt});
                $sum=bcadd($sum,$tmp1);


            }
            $r=round($sum,2);
            break;
        case 'TOTAL_VEN_TVAC':
            extract($p_array);
            $sum=0.0;
			bcscale(4);
            for ($i=0;$i<$nb_item;$i++)
            {
                $tva='e_march'.$i.'_tva_amount';
                $tva_amount=0;
                /* if we do not use vat this var. is not set */
                if ( isset(${$tva}) )
                {
                    $tva_amount=${$tva};
                }
                $sell=${'e_march'.$i.'_price'};
                $qt=${'e_quant'.$i};
				$tot=bcmul($sell,$qt);
				$tot=bcadd($tot,$tva_amount);
				$sum=bcadd($sum,$tot);
            }
            $r=round($sum,2);

            break;
        case 'TOTAL_TVA':
            extract($p_array);
            $sum=0.0;
            for ($i=0;$i<$nb_item;$i++)
            {
                $tva='e_march'.$i.'_tva_amount';
                if (! isset(${$tva})) $tva_amount=0.0;
                else $tva_amount=$
                                     {
                                         $tva
                                     };
                $sum+=$tva_amount;
                $sum=round($sum,2);
            }
            $r=$sum;

            break;
        case 'BON_COMMANDE':
            if ( isset($p_array['bon_comm']))
                return $p_array['bon_comm'];
            else
                return "";
            break;
        case 'PJ':
            if ( isset($p_array['e_pj']))
                return $p_array['e_pj'];
            else
                return "";

        case 'OTHER_INFO':
            if ( isset($p_array['other_info']))
                return $p_array['other_info'];
            else
                return "";
            break;
        case 'COMMENT':
            if ( isset($p_array['e_comm']))
                return $p_array['e_comm'];
            break;
        case 'ACOMPTE':
            if ( isset($p_array['acompte']))
                return $p_array['acompte'];
			return "0";
            break;
        case 'STOCK_NAME':
                if ( ! isset ($p_array['repo'])) return "";
                $ret=$this->db->get_value('select r_name from public.stock_repository where r_id=$1',array($p_array['repo']));
                return $ret;
        case 'STOCK_ADRESS':
                if ( ! isset ($p_array['repo'])) return "";
                $ret=$this->db->get_value('select r_adress from public.stock_repository where r_id=$1',array($p_array['repo']));
                return $ret;
        case 'STOCK_COUNTRY':
                if ( ! isset ($p_array['repo'])) return "";
                $ret=$this->db->get_value('select r_country from public.stock_repository where r_id=$1',array($p_array['repo']));
                return $ret;
        case 'STOCK_CITY':
                if ( ! isset ($p_array['repo'])) return "";
                $ret=$this->db->get_value('select r_city from public.stock_repository where r_id=$1',array($p_array['repo']));
                return $ret;
        case 'STOCK_PHONE':
                if ( ! isset ($p_array['repo'])) return "";
                $ret=$this->db->get_value('select r_phone from public.stock_repository where r_id=$1',array($p_array['repo']));
                return $ret;
        case 'TITLE':
            $title=HtmlInput::default_value_request("ag_title", "");
            return $title;

		}
        /*
         * retrieve the value of ATTR for e_march
         */
        if (preg_match('/^ATTR/', $p_tag) == 1)
        {
            // Retrieve f_id
            if ( isset ($p_array['e_march'.$this->counter]))
            {
                $id = $p_array['e_march' . $this->counter];
                $r=$this->replace_special_tag($id,$p_tag);
            }
        }
        /*
         * retrieve the value of ATTR for e_march
         */
        if (preg_match('/^BENEFATTR/', $p_tag) == 1)
        {
            $qcode=isset($p_array['qcode_benef'])?$p_array['qcode_benef']:'';
            // Retrieve f_id
             $r=$this->replace_special_tag($qcode,$p_tag);
        }
        if (preg_match('/^CUSTATTR/', $p_tag) == 1)
        {
            if ( isset($p_array['qcode_dest']) || isset($p_array['e_client']) )
            {
                $qcode=(isset($p_array['qcode_dest']))?$p_array['qcode_dest']:$p_array['e_client'];
                $r=$this->replace_special_tag($qcode,$p_tag);
            }
        }
        return $r;
    }
    /*!\brief remove a row from the table document, the lob object is not deleted
     *        because can be linked elsewhere
     */
    function remove()
    {
      $d_lob=$this->db->get_value('select d_lob from document where d_id=$1',
				  array($this->d_id));
        $sql='delete from document where d_id='.$this->d_id;
        $this->db->exec_sql($sql);
        if ( $d_lob != 0 )
            $this->db->lo_unlink($d_lob);
    }
    /*!\brief Move a document from the table document into the concerned row
     *        the document is not copied : it is only a link
     *
     * \param $p_internal internal code
     *
     */
    function MoveDocumentPj($p_internal)
    {
        $sql="update jrn set jr_pj=$1,jr_pj_name=$2,jr_pj_type=$3 where jr_internal=$4";

        $this->db->exec_sql($sql,array($this->d_lob,$this->d_filename,$this->d_mimetype,$p_internal));
        // clean the table document
        $sql='delete from document where d_id='.$this->d_id;
        $this->db->exec_sql($sql);


    }
    /**
     *Replace a special tag *TAGxxxx with the value from fiche_detail, the xxxx
     * is the ad_value
     * @param $p_qcode qcode of the card
     * @param $p_tag tag to parse
     * @return  the ad_value contained in fiche_detail or for the type "select" the
     *          label
     */
    function replace_special_tag($p_qcode, $p_tag)
    {
        // check if the march exists
        if ($p_qcode == "")
            return "";

        $f = new Fiche($this->db);
        $found = $f->get_by_qcode($p_qcode, false);
        // if not found exit
        if ($found == 1)
            return "";

        // get the ad_id
        $attr=preg_replace("/^.*ATTR/","",$p_tag);

        if (isNumber($attr) == 0) return "";
        $ad_type=$this->db->get_value("select ad_type from attr_def where ad_id=$1",array($attr));

        // get ad_value
        $ad_value=$this->db->get_value("select ad_value from fiche_detail where f_id=$1 and ad_id=$2",array($f->id,$attr));

        // if ad_id is type select execute select and get value
        if ( $ad_type=="select")
        {
            $sql=$this->db->get_value("select ad_extra from attr_def where ad_id=$1",array($attr));
            $array= $this->db->make_array($sql);
            for ($a=0;$a<count ($array);$a++)
            {
                if ($array[$a]['value']==$ad_value)
                    return $array[$a]['label'];
            }
            
        }
        // if ad_id is not type select get value
        return $ad_value;
    }
    function update_description ($p_desc)
    {
        $this->db->exec_sql('update document set d_description = $1 where d_id=$2',
                array($p_desc,$this->d_id));
    }

}
