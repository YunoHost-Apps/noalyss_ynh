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
 *  \brief this class is the mother class for the CA printing
 */

/*! \brief this class is the mother class for the CA printing
 *
 *
 */
require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once  NOALYSS_INCLUDE.'/class_anc_plan.php';
require_once NOALYSS_INCLUDE.'/class_ianccard.php';
class Anc_Print
{
    var $db;						/*!< $db database connection */
    var $to;						/*!< $to start date */
    var $from; 					/*!< $from end date */
    var $from_poste;				/*!< $from_poste from poste  */
    var $to_poste;				/*!< $to_poste to the poste */

    function Anc_Print($p_cn)
    {
        $this->db=$p_cn;
        $this->from="";
        $this->to="";
        $this->from_poste="";
        $this->to_poste="";
        $this->has_data=0;

    }
    /*!
     * \brief complete the object with the data in $_REQUEST
     */
    function get_request()
    {
        if ( isset($_REQUEST['from']))
            $this->from=$_REQUEST['from'];

        if ( isset($_REQUEST['to']))
            $this->to=$_REQUEST['to'];

        if ( isset($_REQUEST['from_poste']))
            $this->from_poste=$_REQUEST['from_poste'];

        if ( isset($_REQUEST['to_poste']))
            $this->to_poste=$_REQUEST['to_poste'];
        if ( isset($_REQUEST['pa_id']))
            $this->pa_id=$_REQUEST['pa_id'];
        else
            $this->pa_id="";

    }
    /*!
     * \brief Compute  the form to display
     * \param $p_hidden hidden tag to be included (gDossier,...)
     *
     *
     * \return string containing the data
     */
    function display_form($p_hidden="")
    {
        /* if there is no analytic plan return */
        $pa=new Anc_Plan($this->db);
        if ( $pa->count() == 0 )
        {
            echo '<div class="content">';
            echo '<h2 class="error">'._('Aucun plan défini').'</h2>';
            echo '</div>';
            return;
        }

        $from=new IDate('from','from');
        $from->size=10;
        $from->value=$this->from;

        $to=new IDate('to','to');
        $to->value=$this->to;
        $to->size=10;

        $from_poste=new IAncCard('from_poste','from_poste');
        $from_poste->size=10;
        $from_poste->plan_ctl='pa_id';
        $from_poste->value=$this->from_poste;

        $to_poste=new IAncCard('to_poste','to_poste');
        $to_poste->value=$this->to_poste;
        $to_poste->size=10;

        $hidden=new IHidden();
        $r=dossier::hidden();
        $r.=$hidden->input("result","1");
        
        $r.=HtmlInput::request_to_hidden(array('ac'));
        $r.=$p_hidden;
        $plan=new Anc_Plan($this->db);
        $plan_id=new ISelect("pa_id");
        $plan_id->value=$this->db->make_array("select pa_id, pa_name from plan_analytique order by pa_name");
        $plan_id->selected=$this->pa_id;
        $choose_from=new IButton();
        $choose_from->name=_("Choix Poste");
        $choose_from->label=_("Recherche");
        $choose_from->javascript="onClick=search_ca(".dossier::id().",'from_poste','pa_id')";
        

        $choose_to=new IButton();
        $choose_to->name=_("Choix Poste");
        $choose_to->label=_("Recherche");

        
        $choose_to->javascript="onClick=search_ca(".dossier::id().",'to_poste','pa_id')";
      
        $r.=HtmlInput::request_to_hidden(array('ac'));
        ob_start();
        ?>
<table>
    <tr>
        <td>
            <?php 
                echo _('Depuis') ;
                echo HtmlInput::infobulle(37);
            ?>
        </td>
        <td>
            <?php 
                echo $from->input(); 
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php 
                echo _('Jusque') ;
                echo HtmlInput::infobulle(37);
            ?>
        </td>
        <td>
            <?php 
                echo $to->input(); 
            ?>
        </td>
    </tr>
    
</table>
<span style="padding:5px;margin:5px;display:block;">
    <?php echo _( "Plan Analytique :").$plan_id->input(); 
        echo HtmlInput::infobulle(42);
    ?>
</span>

<?php
        $r.=ob_get_clean();
        $r.=_("Entre l'activité ").$from_poste->input();
        $r.=$choose_from->input();
        $r.=_(" et l'activité ").$to_poste->input();
        $r.=$choose_to->input();

        $r.='</span>';
        return $r;
    }
    /*!
     * \brief Set the filter (account_date)
     *
     * \return return the string to add to load
     */

    function set_sql_filter()
    {
        $sql="";
        $and=" and ";
        if ( $this->from != "" )
        {
            $sql.="$and a.oa_date >= to_date('".$this->from."','DD.MM.YYYY')";
        }
        if ( $this->to != "" )
        {
            $sql.=" $and a.oa_date <= to_date('".$this->to."','DD.MM.YYYY')";
        }

        return $sql;

    }
  function check()
  {

    /*
     * check date
     */
    if (($this->from != '' && isDate ($this->from) == 0)
	||
	($this->to != '' && isDate ($this->to) == 0))
      return -1;

    return 0;
  }


}
