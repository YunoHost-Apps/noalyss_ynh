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

/* !\file
 * \brief definition of the class periode
 */
/* !
 * \brief For the periode tables parm_periode and jrn_periode
 */
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE."/database/parm_periode_sql.class.php";

class Periode
{

    var $cn;   /* !< database connection */
    var $jrn_def_id;  /* !< the jr, 0 means all the ledger */
    var $p_id;   /* !< pk of parm_periode */
    var $status;   /* !< status is CL for closed, OP for
      open and CE for centralized */
    var $p_start;   /* !< start of the periode */
    var $p_end;   /* !< end of the periode */

    function __construct($p_cn, $p_id=0)
    {
        $this->p_id=$p_id;
        $this->cn=$p_cn;
        $this->jrn_def_id=0;
    }

    function set_ledger($p_jrn)
    {
        $this->jrn_def_id=$p_jrn;
    }

    function set_periode($pp_id)
    {
        $this->p_id=$pp_id;
    }

    /* !\brief return the p_id of the start and the end of the exercice
     * into an array
     * \param $p_exercice
     * \return array [start]=>,[end]=>
     */

    function limit_year($p_exercice)
    {
        $sql_start="select p_id from parm_periode where p_exercice=$1 order by p_start  ASC limit 1";
        $start=$this->cn->get_value($sql_start, array($p_exercice));
        $sql_end="select p_id from parm_periode where p_exercice=$1 order by p_end  DESC limit 1";
        $end=$this->cn->get_value($sql_end, array($p_exercice));
        return array("start"=>$start, "end"=>$end);
    }

    /* !\brief check if a periode is closed. If jrn_def_id is set to a no zero value then check only for this ledger
     * @see Periode::set_ledger
     * \return 1 is the periode is closed otherwise return 0
     */

    function is_closed()
    {
        if ($this->jrn_def_id!=0)
            $sql="select status from jrn_periode ".
                    " where jrn_def_id=".$this->jrn_def_id.
                    " and p_id =".$this->p_id;
        else
            $sql="select p_closed as status from parm_periode ".
                    " where ".
                    " p_id =".$this->p_id;
        $res=$this->cn->exec_sql($sql);
        $status=Database::fetch_result($res, 0, 0);
        if ($status=='CL'||$status=='t'||$status=='CE')
            return 1;
        return 0;
    }

    ///Return 1 if the periode is open otherwise 0
    //!\note For only a ledger you must set Periode::jrn_def_id to the ledger id
    ///@see Periode::set_ledger
    function is_open()
    {
        /* if jrn_Def_id == 0 then we check the global otherwise we check
          a ledger */
        if ($this->jrn_def_id!=0)
            $sql="select status from jrn_periode ".
                    " where jrn_def_id=".$this->jrn_def_id.
                    " and p_id =".$this->p_id;
        else
            $sql="select p_closed as status from parm_periode ".
                    " where ".
                    " p_id =".$this->p_id;
        $res=$this->cn->exec_sql($sql);
        $status=Database::fetch_result($res, 0, 0);
        if ($status=='OP'||$status=='f')
            return 1;
        return 0;
    }

    ///Return 1 if periode is centralized
    ///@deprecated
    //!\note deprecated , centralization not used anymore
    function is_centralized()
    {
        if ($this->jrn_def_id!=0)
            $sql="select status from jrn_periode ".
                    " where jrn_def_id=".$this->jrn_def_id.
                    " and p_id =".$this->p_id;
        else
            $sql="select p_centralized as status from parm_periode ".
                    " where ".
                    " p_id =".$this->p_id;
        $res=$this->cn->exec_sql($sql);
        $status=Database::fetch_result($res, 0, 0);
        if ($status=='CE'||$status=='t')
            return 1;
        return 0;
    }

    function reopen()
    {
        if ($this->jrn_def_id==0)
        {
            $this->cn->exec_sql("update parm_periode set p_closed='f',p_central='f' where p_id=$1",
                    array($this->p_id));

            $this->cn->exec_sql("update jrn_periode set status='OP' ".
                    " where p_id = $1", [$this->p_id]);

            return;
        }
        else
        {
            $this->cn->exec_sql("update jrn_periode set status='OP'
                                 where jrn_def_id=$1 and 
                                 p_id = $2 ", [$this->jrn_def_id, $this->p_id]);
            /* if one ledger is open then the periode is open */
            $this->cn->exec_sql("update parm_periode set p_closed=false where p_id=".$this->p_id);
            return;
        }
    }

    ///Close a periode , if Periode::jrn_def_id is set to a different value
    /// than 0 , it close only for this ledger id ; otherwise close for all 
    /// periode
    function close()
    {
        if ($this->jrn_def_id==0)
        {
            $this->cn->exec_sql("update parm_periode set p_closed=true where p_id=$1",
                    [$this->p_id]);
            $this->cn->exec_sql("update jrn_periode set status='CL' 
                                 where p_id = $1", [$this->p_id]);

            return;
        }
        else
        {
            $this->cn->exec_sql("update jrn_periode set status='CL' ".
                    " where jrn_def_id=$1  and 
                                 p_id = $2", [$this->jrn_def_id, $this->p_id]);
            /* if all ledgers have this periode closed then synchro with
              the table parm_periode
             */
            $nJrn=$this->cn->count_sql("select * from jrn_periode where ".
                    " p_id=$1", [$this->p_id]);
            $nJrnPeriode=$this->cn->count_sql("select * from jrn_periode where ".
                    " p_id=$1  and status='CL'", [$this->p_id]);

            if ($nJrnPeriode==$nJrn)
                $this->cn->exec_sql("update parm_periode set p_closed=true where p_id=$1",
                        [$this->p_id]);
            return;
        }
    }
    /**
     * @deprecated since version 5
     * @return type
     */
    function centralized()
    {
        if ($this->jrn_def_id==0)
        {
            $this->cn->exec_sql("update parm_periode set p_central=true");
            return;
        }
        else
        {
            $this->cn->exec_sql("update jrn_periode set status='CE' ".
                    " where ".
                    " p_id = $1", [$this->p_id]);
            return;
        }
    }
    /**
     * Add new periode
     * @param date $p_date_start
     * @param date $p_date_end
     * @param int $p_exercice
     * @return int p_id of the new periode
     * @exception Exception 10 Invalide date or exercice, 20 overlapping periode
     */
    function insert($p_date_start, $p_date_end, $p_exercice)
    {
        try
        {

            if (isDate($p_date_start)==null      ||
                    isDate($p_date_end)==null    ||
                    strlen(trim($p_exercice))==0 ||
                    isNumber($p_exercice) ==0    ||
                    $p_exercice<COMPTA_MIN_YEAR  ||
                    $p_exercice>COMPTA_MAX_YEAR)
            {
                throw new Exception(_("Paramètre invalide"),10);
            }
            $overlap_start=$this->cn->get_value("select count(*) from parm_periode 
                where
                    p_start <= to_date($1,'DD-MM-YYYY')
                    and p_end >= to_date($1,'DD-MM-YYYY')
                        
                ",[$p_date_start]);
            $overlap_end=$this->cn->get_value("select count(*) from parm_periode 
                where
                    p_start <= to_date($1,'DD-MM-YYYY')
                    and p_end >= to_date($1,'DD-MM-YYYY')
                        
                ",[$p_date_end]);
            if ( $overlap_start > 0 || $overlap_end > 0)
            {
                throw new Exception (_("Période chevauchant une autre"),20);
            }
            $p_id=$this->cn->get_next_seq('s_periode');
            $sql=" insert into parm_periode(p_id,p_start,p_end,p_closed,p_exercice)
                    values 
                        ($1,
                        to_date($2,'DD.MM.YYYY'),
                        to_date($3,'DD.MM.YYYY'),
                        'f',
                        $4)";
            
            $this->cn->start();
            $Res=$this->cn->exec_sql($sql,[$p_id, $p_date_start, $p_date_end, $p_exercice]);
            $Res=$this->cn->exec_sql("insert into jrn_periode (jrn_def_id,p_id,status) ".
                    "select jrn_def_id,$p_id,'OP' from jrn_def");
            $this->cn->commit();
            return $p_id;
        }
        catch (Exception $e)
        {
            record_log($e->getTraceAsString());
            $this->cn->rollback();
            throw $e;
        }
    }

    /* !\brief load data from database
     * \return 0 on success and -1 on error
     */

    function load()
    {
        if ($this->p_id=='')
            $this->p_id=-1;
        $row=$this->cn->get_array("select p_start,p_end,p_exercice,p_closed,p_central from parm_periode where p_id=$1",
                array($this->p_id));
        if ($row==null)
            return -1;

        $this->p_start=$row[0]['p_start'];
        $this->p_end=$row[0]['p_end'];
        $this->p_exercice=$row[0]['p_exercice'];
        $this->p_closed=$row[0]['p_closed'];
        $this->p_central=$row[0]['p_central'];
        return 0;
    }

    /* !\brief return the max and the min periode of the exercice given
     * in parameter
     * \param $p_exercice is the exercice
     * \return an array of Periode object
     */

    function get_limit($p_exercice)
    {

        $max=$this->cn->get_value("select p_id from parm_periode where p_exercice=$1 order by p_start asc limit 1",
                array($p_exercice));
        $min=$this->cn->get_value("select p_id from parm_periode where p_exercice=$1 order by p_start desc limit 1",
                array($p_exercice));
        $rMax=new Periode($this->cn);
        $rMax->p_id=$max;
        if ($rMax->load())
            throw new Exception('Periode n\'existe pas');
        $rMin=new Periode($this->cn);
        $rMin->p_id=$min;
        if ($rMin->load())
            throw new Exception('Periode n\'existe pas');
        return array($rMax, $rMin);
    }

    /* !
     * \brief Give the start & end date of a periode
     * \param $p_periode is the periode id, if omitted the value is the current object
     * \return array containing the start date & the end date, index are p_start and p_end or NULL if
     * nothing is found
      \verbatim
      $ret['p_start']=>'01.01.2009'
      $ret['p_end']=>'31.01.2009'
      \endverbatim
     */

    public function get_date_limit($p_periode=0)
    {
        if ($p_periode==0)
            $p_periode=$this->p_id;
        $sql="select to_char(p_start,'DD.MM.YYYY') as p_start,
             to_char(p_end,'DD.MM.YYYY')   as p_end
             from parm_periode
             where p_id=$1";
        $Res=$this->cn->exec_sql($sql, array($p_periode));
        if (Database::num_row($Res)==0)
            return null;
        return Database::fetch_array($Res, 0);
    }

    /* !\brief return the first day of periode
     * the this->p_id must be set
     * \return a string with the date (DD.MM.YYYY)
     */

    public function first_day($p=0)
    {
        if ($p==0)
            $p=$this->p_id;
        list($p_start, $p_end)=$this->get_date_limit($p);
        return $p_start;
    }

    /* !\brief return the last day of periode
     * the this->p_id must be set
     * \return a string with the date (DD.MM.YYYY)
     */

    public function last_day($p=0)
    {
        if ($p==0)
            $p=$this->p_id;
        list($p_start, $p_end)=$this->get_date_limit($p);
        return $p_end;
    }

    function get_exercice($p_id=0)
    {
        if ($p_id==0)
            $p_id=$this->p_id;
        $sql="select p_exercice from parm_periode where p_id=".$p_id;
        $Res=$this->cn->exec_sql($sql);
        if (Database::num_row($Res)==0)
            return null;
        return Database::fetch_result($Res, 0, 0);
    }

    /* !\brief retrieve the periode thanks the date_end
     * \param $p_date format DD.MM.YYYY
     * \return the periode id
     * \exception if not periode is found or if more than one periode is found
     */

    function find_periode($p_date)
    {
        $sql="select p_id from parm_periode where p_start <= to_date($1,'DD.MM.YYYY') and p_end >= to_date($1,'DD.MM.YYYY') ";
        $ret=$this->cn->exec_sql($sql, array($p_date));
        $nb_periode=Database::num_row($ret);
        if ($nb_periode==0)
            throw (new Exception('Aucune période trouvée', 101));
        if ($nb_periode>1)
            throw (new Exception("Trop de périodes trouvées $nb_periode pour $p_date",
            100));
        $per=Database::fetch_result($ret, 0);
        $this->p_id=$per;
        return $per;
    }

    /**
     * add a exercice starting in year p_year with p_month month, with a starting 
     * and a closing 
     * @param $p_exercice the exercice
     * @param $p_year the starting year
     * @param $p_from_month starting month
     * @param $p_month number of month of the exercice
     * @param $p_opening 1 if we create a one-day periode for opening writings
     * @param $p_closing  1 if we create a one-day periode for closing writings
     */
    function insert_exercice($p_exercice, $p_year, $p_from_month, $p_month,
            $p_opening, $p_closing)
    {
        try
        {
            if (isNumber($p_exercice)==0)
                throw new Exception(_("Exercice n'est pas un nombre"));

            if ($p_exercice>COMPTA_MAX_YEAR||$p_exercice<COMPTA_MIN_YEAR)
                throw new Exception(sprintf(_("Exercice doit être entre %s et %s "), COMPTA_MIN_YEAR,
                        COMPTA_MAX_YEAR));
            if (isNumber($p_year)==0)
                throw new Exception(_("Année n'est pas un nombre"));

            if ($p_year>COMPTA_MAX_YEAR||$p_year<COMPTA_MIN_YEAR)
                throw new Exception(sprintf(_("Année doit être entre %s et %s "), COMPTA_MIN_YEAR,
                        COMPTA_MAX_YEAR));

            if (isNumber($p_month)==0)
                throw new Exception(_("Nombre de mois n'est pas un nombre"));
            if ($p_month<1||$p_month>60)
                throw new Exception(_("Nombre de mois doit être compris entre 1 & 60 "));
            if (isNumber($p_month)==0)
                throw new Exception(_("Mois de début n'existe pas "));
            if ($p_from_month>13||$p_from_month<1)
                throw new Exception(_("Mois de début n'existe pas "));
            
            $this->cn->start();
            $year=$p_year;
            $month=$p_from_month;
            for ($i=1; $i<=$p_month; $i++)
            {

                // create first a periode of day 
                if ($i==1&&$p_opening==1)
                {
                    $fdate_start=sprintf('01.%02d.%d', $month, $year);
                    $this->insert($fdate_start, $fdate_start, $p_exercice);

                    $date_start=sprintf('02.%02d.%d', $month, $year);
                    $date_end=$this->cn->get_value("select to_char(to_date($1,'DD.MM.YYYY')+interval '1 month'-interval '1 day','DD.MM.YYYY')",
                            array($fdate_start));

                    $this->insert($date_start, $date_end, $p_exercice);
                }
                // The last month, we create a one-day periode for closing
                elseif ($i==$p_month && $p_closing ==1 )
                {
                    $fdate_start=sprintf('01.%02d.%d', $month, $year);
                    $date_end=$this->cn->get_value("select to_char(to_date($1,'DD.MM.YYYY')+interval '1 month'-interval '2 day','DD.MM.YYYY')",
                            array($fdate_start));
                    $this->insert($fdate_start, $date_end, $p_exercice);

                    $date_end=$this->cn->get_value("select to_char(to_date($1,'DD.MM.YYYY')+interval '1 month'-interval '1 day','DD.MM.YYYY')",
                            array($fdate_start));

                    $this->insert($date_end, $date_end, $p_exercice);
                    
                }
                else
                {
                    $date_start=sprintf('01.%02d.%d', $month, $year);
                    $date_end=$this->cn->get_value("select to_char(to_date($1,'DD.MM.YYYY')+interval '1 month'-interval '1 day','DD.MM.YYYY')",
                            array($date_start));
                    $this->insert($date_start, $date_end, $p_exercice);
                }
                $month++;
                if ($month == 13 )
                {
                        $year++;
                        $month=1;
                }
            }
        
        $this->cn->commit();
        }
        catch (Exception $e)
        {
            record_log($e->getTraceAsString());
            $this->cn->rollback();
            throw $e;
        }
    }

    /**
     * Display a table with all the periode
     * @param $p_js javascript variable
     * @see scripts.js
     */
    static function display_periode_global($p_js)
    {
        $cn=Dossier::connect();
        $periode=new Parm_Periode_SQL($cn);
        $ret=$periode->seek(" order by   p_start asc");
        $nb_periode=Database::num_row($ret);

        if ($nb_periode==0)
            return;
        echo '<table class="result" id="periode_tbl">';
        echo "<thead>";
        echo "<tr>";
        echo th("");
        echo th(_("Date Début"));
        echo th(_("Date Fin"));
        echo th(_("Exercice"));
        echo th(_("nb opérations"));
        echo th(_("Status"));
        echo "</tr>";
        echo "</thead>";
        echo '<tbody>';

        for ($i=0; $i<$nb_periode; $i++)
        {
            $obj=$periode->next($ret, $i);
            Periode::display_row_global($obj, $i, $p_js);
        }
        echo '</tbody>';
        echo '</table>';
    }

    /**
     * count the number of operation of a Periode
     * @return integer
     */
    function count_operation()
    {
        $count=$this->cn->get_value("
                select count(*) 
                from 
                    jrn 
                where 
                    jr_tech_per = $1", [$this->p_id]);
        return $count;
    }

    /**
     * @brief Display each row for the global
     * @param $obj Parm_Periode_SQL
     * @param $p_nb not used so far
     * @param $p_js javascript variable
     */
    static function display_row_global(Parm_Periode_SQL $obj, $p_nb, $p_js)
    {
        $periode=new Periode($obj->cn, $obj->p_id);
        $class=($p_nb%2==0)?"even":"odd";
        printf('<tr id="row_per_%d" customkey="%s" per_exercice="%s" p_id="%s" class="%s"> ',
                $obj->getp("p_id"), 
                $obj->getp("p_start"),
                $obj->getp("p_exercice"),
                $obj->getp("p_id"), $class);
        /**
         * Display a checkbox to select several month to close
         */
        if ($obj->getp("p_closed")=="f")
        {
            $checkbox=new ICheckBox("sel_per_close[]");
            $checkbox->set_attribute("per_id", $obj->getp("p_id"));
            $checkbox->value=$obj->getp("p_id");
            echo "<td>".$checkbox->input()."</td>";
        }
        else
        {
            echo td("");
        }
        echo td(format_date($obj->getp("p_start"), "YYYY-MM-DD", "DD.MM.YYYY"));
        echo td(format_date($obj->getp("p_end"), "YYYY-MM-DD", "DD.MM.YYYY"));
        echo td($obj->getp("p_exercice"));
        $nb_operation=$periode->count_operation();
        echo td($nb_operation);
        $closed=$obj->getp('p_closed');
        $status=($closed=='t')?_("Fermée"):_("Ouvert");
        echo td($status);

        // if no operation then this periode can be removed or updated
        if ($nb_operation==0)
        {
            // Updatable 
            $js=sprintf("%s.box_display('%d')", $p_js, $obj->p_id);
            echo "<td>";
            echo HtmlInput::image_click("crayon-mod-b24.png", $js, _("Effacer"));
            echo "</td>";
            //removable
            $js=sprintf("%s.remove('%d')", $p_js, $obj->p_id);
            echo "<td>";
            echo HtmlInput::image_click("trash-24.gif", $js, _("Effacer"));
            echo "</td>";
        }
        else
        {
            echo td(""), td("");
        }

        /// Can close if open
        echo "<td>";
        if ($obj->getp("p_closed")=='f')
        {
            $javascript=sprintf('%s.close_periode(\'%d\')', $p_js, $obj->p_id);
            echo Icon_Action::iconon(uniqid(), $javascript);
        }
        else
        {
            $javascript=sprintf("%s.open_periode('%d')", $p_js, $obj->p_id);
            echo Icon_Action::iconoff(uniqid(),$javascript );
        }
        echo "</td>";
        echo "</tr>";
    }

    /**
     * @brief display a form (method POST) to input a new exercice 
     * variable in the FORM 
     *  - p_exercice
     *  - p_year
     *  - nb_month
     *  - from_month
     *  - day_opening
     *  - day_closing
     */
    static function form_exercice_add()
    {
        $cn=Dossier::connect();
        $exercice=new INum('p_exercice');
        $exercice->prec=0;
        $exercice->value=$cn->get_value('select max(p_exercice::float)+1 from parm_periode');
        $year=new INum('p_year');
        $year->prec=0;
        $year->value=$exercice->value;
        $nb_month=new INum('nb_month');
        $nb_month->prec=0;
        $nb_month->value=12;
        $from=new ISelect('from_month');
        $amonth=array();
        $month=[_('Janvier'), _('Février'), _('Mars'), _('Avril'),
            _('Mai'), _('Juin'), _('Juillet'), _('Août'), _('Septembre'),
            _('Octobre'), _('Novembre'), _('Décembre')];
        for ($i=1; $i<13; $i++)
        {
            $strMonth=$month[($i-1)];
            $amonth[]=array("value"=>$i, "label"=>$strMonth);
        }
        $from->value=$amonth;
        $day_opening=new ICheckBox("day_opening");
        $day_closing=new ICheckBox("day_closing");
        $day_closing->value=1;
        $day_opening->value=1;
        $day_closing->set_check(1);
        require_once NOALYSS_TEMPLATE.'/periode_add_exercice.php';
    }
    function delete() {
        $this->cn->exec_sql("delete from parm_periode where p_id=$1",[$this->p_id]);
    }
    /**
     * Verify before delete that the month is not used
     * @exception Exception code 1 if periode used
     */
    function verify_delete() {
        try {
            if ( $this->cn->get_value("select count(*) from jrn where jr_tech_per =$1 ",[$this->p_id]) > 0) {
                throw new Exception(_("Effacement impossible"), 1);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    /**
     * @brief Display a form for the input of a new periode
     */
    static  function form_periode_add($p_js_var)
    {
        $cn=Dossier::connect();
        $p_exercice=new ISelect('p_exercice');
        $p_exercice->value=$cn->make_array("select distinct p_exercice,p_exercice from parm_periode order by 1 desc");
        $title=_('Ajout période');
        $title_par="<p>"._('On ne peut ajouter une période que sur un exercice qui existe déjà').
                "</p>";

        $p_start=new IDate('p_start');
        $p_end=new IDate('p_end');

        $html='';
        $html.=HtmlInput::title_box($title, 'periode_add','hide');
        $html.=$title_par;
        $html.='<form method="post" id="insert_periode_frm" onsubmit="'.$p_js_var.'.insert_periode();return false;">' ;
        $html.=HtmlInput::hidden("ac", $_REQUEST['ac']);
        $html.=Dossier::hidden();
        $html.='<table>';

        $html.=tr(td(_(' Début période : ')).td($p_start->input()));
        $html.=tr(td(_(' Fin période : ')).td($p_end->input()));
        $html.=tr(td(_(' Exercice : ')).td($p_exercice->input()));
        $html.='</table>';
        $html.=HtmlInput::submit('add_per', _('sauver'));
        $html.=HtmlInput::button('close', _('fermer'),
                        'onclick="$(\'periode_add\').hide()"');
        $html.='</form>';
        echo $html;
    }
    /**
     *@brief 
     */
    static function filter_exercice($p_sel) 
    {
        $cn=Dossier::connect();
        $i_exercice=new ISelect("p_exercice_sel");
        $i_exercice->value=$cn->make_array("select distinct p_exercice,p_exercice from parm_periode order by 1 desc", 1);
        $i_exercice->javascript="onchange=\"Periode.filter_exercice('periode_tbl')\"";
        $i_exercice->selected=$p_sel;
        echo $i_exercice->input();
    }
}
