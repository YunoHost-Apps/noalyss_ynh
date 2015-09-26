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
 * \brief Display the calendar
 */
class Calendar
{
    var $current_date;
    private static $nb_day=array(31,28,31,30,31,30,31,31,30,31,30,31);

    function __construct()
    {
        /* get the current month */
        $this->current_date=getdate();
        $this->month=$this->current_date['mon'];
        $this->day=self::$nb_day[$this->month-1];
        $this->year=$this->current_date['year'];
		$this->action_div=array();
		$this->action=array();
		$this->title=array();

        if ( $this->year % 4 == 0 && $this->month=2)
            $this->day=29;
    }

    /*!\brief fill the array given as parameter with the data from action_gestion
     *\param $p_array array of the date of the month
     * \param $p_style is either short or long, short: for a small title, long for a complete one
     */
    function fill_from_action(&$p_array,$p_style)
    {
		global $g_user;
		$profile=$g_user->get_profile();

        $cn=new Database(dossier::id());
        $sql="select ag_id,to_char(ag_remind_date,'DD')::integer as ag_timestamp_day,ag_title,ag_hour,
             coalesce(name,'interne') as str_name
			".
             " from action_gestion ".
             "  left join vw_fiche_name  on (f_id=f_id_dest) ".
             " where ".
             " to_char(ag_remind_date,'MM')::integer=$1 ".
             " and to_char(ag_remind_date,'YYYY')::integer=$2 ".
             "  and ag_dest in (select p_granted from user_sec_action_profile where p_id =$3)
				 and ag_state IN (2, 3)
				";

		$array=$cn->get_array($sql,array($this->month,$this->year,$profile));
        for ($i=0;$i<count($array);$i++)
        {
            $ind=$array[$i]['ag_timestamp_day'];
            $this->action[$ind][]=$array[$i]['ag_id'];
            $this->title[$ind][]=$array[$i]['ag_title'];
            $this->hour[$ind][]=$array[$i]['ag_hour'];
            $this->str_name[$ind][]=$array[$i]['str_name'];

        }
		/*
		 * Fill foreach day
		 */
        if ( $p_style == "short")
        {
		foreach ($this->action as $day=>$aAction)
		{
			if ($p_array[$day]=="")  {
                                $p_array[$day]='<span class="input_text" onclick="display_task(\'tsk'.$day.'\');">'." ".count($aAction)." "._("Tâches").'</span>';
                        }
			$this->action_div[$day]='<div id="tsk'.$day.'" class="inner_box" style="width:200;display:none">';
			$this->action_div[$day].=HtmlInput::title_box($day."/".$this->month."/".$this->year, "tsk".$day, "hide");
			 $this->action_div[$day].="<ol>";
			for ($i=0;$i<count($aAction);$i++)
			{
				$this->action_div[$day].='<li>'.HtmlInput::detail_action($aAction[$i], $this->title[$day][$i]).'</li>';
			}
			$this->action_div[$day].='</ol></div>';
		}
        }
        else if ( $p_style == "long")
        {
		foreach ($this->action as $day=>$aAction)
		{
                        $p_array[$day].="<ol>";
			for ($i=0;$i<count($aAction);$i++)
			{
				$p_array[$day].='<li>'.hb($this->str_name[$day][$i]).'&rarr;'.HtmlInput::detail_action($aAction[$i], $this->hour[$day][$i]." ".$this->title[$day][$i]).'</li>';
			}
			$p_array[$day].='</ol>';
		}
        }
    }
    /*!\brief fill the array given as parameter with the data from todo
     *\param $p_array array of the date of the month
     * \param $p_style is either short or long, short: for a small title, long for a complete one
     */
    function fill_from_todo(&$p_array,$p_style)
    {
        $cn=new Database(dossier::id());
        if ($p_style=="short")
        {
            $sql="select count(*) as nb,to_char(tl_date,'DD')::integer as tl_date_day ".
                 " from todo_list ".
                 " where ".
                 " to_char(tl_date,'MM')::integer=$1 ".
                 " and to_char(tl_date,'YYYY')::integer=$2 ".
                 " and use_login=$3 group by to_char(tl_date,'DD')::integer ";
            $array=$cn->get_array($sql,array($this->month,$this->year,$_SESSION['g_user']));
            for ($i=0;$i<count($array);$i++)
            {
                $ind=$array[$i]['tl_date_day'];
                $p_array[$ind].="<span style=\"display:block\" class=\"todo\">".h($array[$i]['nb'])." "._('Notes').'</span>';
            }
        } else if ($p_style=="long")
        {
            $sql="select to_char(tl_date,'DD')::integer as tl_date_day,tl_title ".
                 " from todo_list ".
                 " where ".
                 " to_char(tl_date,'MM')::integer=$1 ".
                 " and to_char(tl_date,'YYYY')::integer=$2 ".
                 " and use_login=$3  ";
            $array=$cn->get_array($sql,array($this->month,$this->year,$_SESSION['g_user']));
            for ($i=0;$i<count($array);$i++)
            {
                $ind=$array[$i]['tl_date_day'];
                
               $p_array[$ind].="<span style=\"display:block\" class=\"todo\">".h($array[$i]['tl_title']).'</span>';
            }
        }
    }
    /*!\brief display a calendar after a call to Calendar::fill
     *\param $p_type long or short
     * 
     *\return HTML String
     */
    function display($p_type,$p_notitle)
    {
        global $g_user;
        if  ($p_type != 'long' && $p_type != 'short') {
            throw new Exception("Calendar::display, unknow type");
        }
        $exercice_user=$g_user->get_exercice();
        /* day */
        $cell=array();
        for ($i=0;$i<42;$i++)
        {
            $cell[$i]="";
        }
        $this->set_month_year();
        /* weekday */
        $week=array(_('Dimanche'),_('Lundi'),_('Mardi'),_('Mercredi'),_('Jeudi'),_('Vendredi'),_('Samedi'));

        $notitle=$p_notitle;
        
        $this->fill_from_action($cell,$p_type);
        $this->fill_from_todo($cell,$p_type);
        $wMonth=new ISelect('per');
        $cn=new Database(dossier::id());
        $wMonth->value=$cn->make_array("select p_id,to_char(p_start,'MM/YYYY') from parm_periode where p_exercice = '$exercice_user' order by p_start");
        $wMonth->selected=$this->default_periode;
        $wMonth->javascript="onchange=change_month(this)";
        $wMonth->set_attribute('gDossier',dossier::id());
        $wMonth->set_attribute('type_display',$p_type);
        $wMonth->set_attribute('notitle',$notitle);
        $month_year=$wMonth->input().$wMonth->get_js_attr();
        ob_start();
        $zoom=($p_type=='short')?0:1;
        
        require_once NOALYSS_INCLUDE.'/template/calendar.php';

        if (count($this->action_div) > 0)
        {
                foreach ($this->action_div as $day)
                {
                        echo $day;
                }
        }
        $ret=ob_get_contents();
        ob_end_clean();
        return $ret;
    }
    /**
     *@brief set correctly the month and the year with the default_periode
     */
    function set_month_year()
    {
        $cn=new Database(dossier::id());
        $array=$cn->get_array("select to_char(p_start,'MM') as month, to_char(p_start,'YYYY') as year ".
                              " from parm_periode where p_id=$1",array($this->default_periode));
        $this->month=(int)$array[0]['month'];
        $this->year=(int)$array[0]['year'];
        $this->day=self::$nb_day[$this->month-1];
        if ( $this->year % 4 == 0 && $this->month==2)
            $this->day=29;
    }
    /**
     *@brief get the periode from the preference of the current user
     * change the value of default_periode to today
     *@return $this->default_periode
     */
    function get_preference()
    {
        global $g_user;
        $cn=new Database(dossier::id());
        $today=date('d.m.Y');
        $p_id=$cn->get_value("
                select p_id from parm_periode
                where
                p_start <= to_date($1,'DD.MM.YYYY')
                and
                p_end >= to_date($1,'DD.MM.YYYY')",
                array($today));
        if ( $p_id == '')
        {
            $p_id=$g_user->get_periode();
        }
	$this->default_periode=$p_id;
        return  $p_id;
    }
    /**
     *@brief set the periode to the parameter, change the value of $this->default_periode
     * there is no check on the periode
     */
    function set_periode($p_per)
    {
        $this->default_periode=$p_per;
    }
    /**
     * @brief zoom the calendar
     */
    function zoom_calendar($notitle)
    {
        global $g_user;
        $exercice_user=$g_user->get_exercice();
        /* day */
        $cell=array();
        for ($i=0;$i<42;$i++)
        {
            $cell[$i]="";
        }
        $this->set_month_year();
        /* weekday */
        $week=array(_('Dimanche'),_('Lundi'),_('Mardi'),_('Mercredi'),_('Jeudi'),_('Vendredi'),_('Samedi'));

        $this->fill_from_action($cell,"long");
        $this->fill_from_todo($cell,"long");
        $wMonth=new ISelect('per_div');
        $cn=new Database(dossier::id());
        $wMonth->value=$cn->make_array("select p_id,to_char(p_start,'MM/YYYY') from parm_periode where p_exercice = '$exercice_user' order by p_start");
        $wMonth->selected=$this->default_periode;
        $wMonth->javascript=sprintf("onchange=calendar_zoom({gDossier:%d,invalue:'%s',outvalue:'%s',distype:'%s',notitle:%d})",
            dossier::id(),'per_div','calendar_zoom_div','cal',$notitle);
        $wMonth->set_attribute('gDossier',dossier::id());
        $month_year=$wMonth->input().$wMonth->get_js_attr();
        ob_start();
         $zoom=1;
         $notitle=HtmlInput::default_value_get('notitle', 0);
        require_once NOALYSS_INCLUDE.'/template/calendar.php';

        if (count($this->action_div) > 0)
        {
                foreach ($this->action_div as $day)
                {
                        echo $day;
                }
        }
	$ret=ob_get_contents();
        ob_end_clean();
        return $ret;
    }
    /**
     * Display the next events for 30 days 
     * todo list + action to remind
     */
    function zoom_list($notitle)
    {
        global $g_user;
        $cn=new Database(dossier::id());
        $profile=$g_user->get_profile();

        // Get the event from now and before 30 before
        // union the TODO list
        $sql = "
          select ag_id,ag_remind_date,to_char(ag_remind_date,'DD.MM.YY') as str_date,ag_title,ag_hour,
             coalesce(name,'interne') as str_name,
             case when ag_remind_date < now() then 'R' 
                when ag_remind_date = now() then 'N' 
                else 'F'
              end as status,
              coalesce (ag_remind_date::date,current_date) - current_date as delta_days
              from action_gestion 
               left join vw_fiche_name  on (f_id=f_id_dest)
              where 
               ag_dest in (select p_granted from user_sec_action_profile where p_id =$1)
              and ag_state IN (2, 3)
              order by ag_remind_date,ag_hour
        ";
        $a_event=$cn->get_array($sql,array($profile));
        ob_start();
        require_once NOALYSS_INCLUDE.'/template/calendar-list.php';
        $ret=ob_get_clean();
        return $ret;
                
        
    }
    
    function zoom($p_type,$p_notitle)
    {
        switch ($p_type)
        {
            case 'cal':
                return $this->zoom_calendar($p_notitle);
                break;
            case 'list':
                return $this->zoom_list($p_notitle);
                break;
        }
    }
        
    static function test_me() {
        
    }

}
