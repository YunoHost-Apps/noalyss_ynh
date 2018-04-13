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
 *@file
 *@brief API for sending email 
 */

/**
 * Description of Sendmail
 *
 * @author dany
 */
require_once NOALYSS_INCLUDE.'/lib/filetosend.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';

class Sendmail
{

    private $mailto;
    private $afile;
    private $subject;
    private $message;
    private $from;
    private $content;
    private $header;

    /**
     * set the from
     * @param $p_from has the form name <info@phpcompta.eu>
     */
    function set_from($p_from)
    {
        $this->from = $p_from;
    }

    /**
     * 
     * @param $p_subject set the subject
     */
    function set_subject($p_subject)
    {
        $this->subject = $p_subject;
    }

    /**
     * set the recipient
     * @param type $p_mailto has the form name <email@email.com>
     */
    function mailto($p_mailto)
    {
        $this->mailto = $p_mailto;
    }

    /**
     * body of the message (utf8)
     * @param type $p_message
     */
    function set_message($p_message)
    {
        $this->message = $p_message;
    }

    /**
     * Add file to the message
     * @param FileToSend $file file to add to the message
     */
    function add_file(FileToSend $file)
    {
        $this->afile[] = $file;
    }

    /**
     *  verify that the message is ready to go
     * @throws Exception
     */
    function verify()
    {
        $array = explode(",", "from,subject,mailto,message");
        for ($i = 0; $i < count($array); $i++)
        {
            $name = $array[$i];
            if (trim($this->$name) == "")
            {
                throw new Exception( sprintf(_("%s est vide",$name)));
            }
        }
    }
    /**
    * create the message before sending
    */
    function compose()
    {
        $this->verify();
	$this->header="";
	$this->content="";

        // a random hash will be necessary to send mixed content
        $separator = md5(time());

        // carriage return type (we use a PHP end of line constant)
        $eol = PHP_EOL;

        // main header (multipart mandatory)
        $this->header = "From: " . $this->from . $eol;
        $this->header .= "MIME-Version: 1.0" . $eol;
        $this->header .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\""  ;
        //$headers .= "Content-Transfer-Encoding: 7bit" . $eol;
        //$headers .= "This is a MIME encoded message." . $eol ;
        //$headers .= $eol . $eol;

        // message
        $this->content .= "--" . $separator . $eol;
        $this->content .= "Content-Type: text/plain; charset=\"utf-8\"" . $eol;
        $this->content .= "Content-Transfer-Encoding: 7bit" . $eol.$eol ;
        $this->content .= $this->message . $eol ;

        // attachment
        for ($i = 0; $i < count($this->afile); $i++)
        {
            $file = $this->afile[$i];
            $file_size = filesize($file->full_name);
            $handle = fopen($file->full_name, "r");
            $content = fread($handle, $file_size);
            fclose($handle);
            $content = chunk_split(base64_encode($content));
            $this->content .= "--" . $separator . $eol;
            $this->content .= "Content-Type: " . $file->type . "; name=\"" . $file->filename . "\"" . $eol;
            $this->content .= "Content-Disposition: attachment; filename=\"" . $file->filename . "\"" . $eol;
            $this->content .= "Content-Transfer-Encoding: base64" . $eol;
            $this->content.=$eol;
            $this->content .= $content . $eol ;
        }
        if ( count ($this->afile) == 0 ) $this->content.=$eol;

        $this->content .= "--" . $separator . "--";
    }
    /**
     * Send the message 
     * @throws Exception
     */
    function send()
    {
        if ( $this->can_send() == false )            throw new Exception(_('Email non envoyé'),EMAIL_LIMIT);

	if (!mail($this->mailto, $this->subject, $this->content,$this->header))
        {
            throw new Exception('send failed');
        }
        // Increment email amount
        $repo =new Database();
        $date=date('Ymd');
        $http=new HttpInput();
        $dossier=$http->request("gDossier","string", -1);
        $this->increment_mail($repo,$dossier,$date);
    }
    /**
     * Check if email can be sent from a folder
     * @return boolean
     */
    function can_send() {
        /**
         * if send from a dossier , then  check limit of this dossier 
         * otherwise send true
         */
        $http=new HttpInput();
        $dossier=$http->request("gDossier","string", -1);
        if ($dossier == -1 ) return true;
        
        /**
         * Compare max value in repo 
         */
        $repo =new Database();
        $date=date('Ymd');
        // get max email 
        $max_email = $this->get_max_email($repo,$dossier);
        // 
        // This folder cannot send email
        if ($max_email == 0 ) return false;
        // 
        // This folder has unlimited email 
        if ($max_email == -1 ) return true;

        // get email sent today from account_repository
        $email_sent = $this->get_email_sent($repo,$dossier , $date);
        // 
        if (   $email_sent >= $max_email) return false;
               
        return true;
    }
    /**
     * return max email the folder can send
     * @param $p_repo Database 
     * @param $p_dossier_id int
     */
    function get_max_email(Database $p_repo,$p_dossier_id)
    {
        $max_email = $p_repo->get_value("select dos_email from ac_dossier where dos_id=$1",
            array($p_dossier_id));
        return $max_email;
    }
    /**
     * Return the amount of send emails for the date (YYYYMMDD)
     * @param Database $p_repo
     * @param $p_dossier_id int 
     * @param $p_date string YYYYMMDD
     */
    function get_email_sent(Database $p_repo,$p_dossier_id,$p_date)
    {
        
        $email_sent = $p_repo->get_value ('select de_sent_email from dossier_sent_email where dos_id = $1 and de_date=$2',
                array($p_dossier_id,$p_date));
        return $email_sent;
                
                
    }
    /**
     * Add $p_amount_email to email sent
     * @param $p_repo Database 
     * @param $p_dossier int id of the folder (dossier.dos_id)
     * @param $p_date string (YYYYMMDD)
     */
    function increment_mail(Database $p_repo,$p_dossier,$p_date)
    {
        if ( $p_dossier == -1) return ; 
        $email_sent = $this->get_email_sent($p_repo,$p_dossier,$p_date);
        if (  $email_sent == 0 ){
                $p_repo->exec_sql("insert into public.dossier_sent_email(de_sent_email,dos_id,de_date) values($1,$2,$3)",
                        array(1,$p_dossier,$p_date));
                return;
        } else {
            // update + sp_emaoun_email
            $p_repo->exec_sql("update dossier_sent_email set de_sent_email=de_sent_email+1 where dos_id=$1 and de_date=$2",
                    array($p_dossier,$p_date));
        }
    }

}
