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
 * \brief the todo list is managed by this class
 */

require_once NOALYSS_INCLUDE.'/lib/function_javascript.php';

/*!\brief
 * This class manages the table todo_list
 *
 *
 * Data Member :
 * - $cn database connx
 * - $variable
 *    - id (todo_list.tl_id)
 *    - date (todo_list.tl_Date)
 *    - title (todo_list.title)
 *    - desc (todo_list.tl_desc)
 *    - owner (todo_list.use_id)
 *
 */
class Todo_List
{

    private static $variable=array(
                                 "id"=>"tl_id",
                                 "date"=>"tl_date",
                                 "title"=>"tl_title",
                                 "desc"=>"tl_desc",
                                 "owner"=>"use_login",
                                 "is_public"=>"is_public");
    private $cn;
    private  $tl_id,$tl_date,$tl_title,$use_login,$is_public;

    function __construct ($p_init)
    {
        $this->cn=$p_init;
        $this->tl_id=0;
        $this->tl_desc="";
        $this->use_login=$_SESSION['g_user'];
        $this->is_public="N";

    }
    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }
        else
            throw new Exception("Attribut inexistant $p_string");
    }
    public function check($p_idx,&$p_value)
    {
        if ( strcmp ($p_idx, 'tl_id') == 0 )
        {
            if ( strlen($p_value) > 6 || isNumber ($p_value) == false) return false;
        }
        if ( strcmp ($p_idx, 'tl_date') == 0 )
        {
            if ( strlen(trim($p_value)) ==0 ||strlen($p_value) > 12 || isDate ($p_value) == false) return false;
        }
        if ( strcmp ($p_idx, 'tl_title') == 0 )
        {
            $p_value=mb_substr($p_value,0,120) ;
            return true;
        }
        if ( strcmp ($p_idx, 'tl_desc') == 0 )
        {
            $p_value=mb_substr($p_value,0,400) ;
            return true;
        }
        return true;
    }
    public function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            if ($this->check($idx,$p_value) == true )      $this->$idx=$p_value;
        }
        else
            throw new Exception("Attribut inexistant $p_string");


    }
    public function get_info()
    {
        return var_export(self::$variable,true);
    }
    public function verify()
    {
        if ( isDate($this->tl_date) == false )
        {
			$this->tl_date=date('d.m.Y');
        }
        return 0;
    }
    public function save()
    {
        if (  $this->get_parameter("id") == 0 )
            $this->insert();
        else
            $this->update();
    }

    public function insert()
    {
        if ( $this->verify() != 0 ) return;
        if (trim($this->tl_title)=='')
            $this->tl_title=mb_substr(trim($this->tl_desc),0,30);

        if (trim($this->tl_title)=='')
        {
            alert('La note est vide');
            return;
        }

        /*  limit the title to 35 char */
        $this->tl_title=mb_substr(trim($this->tl_title),0,30);

        $sql="insert into todo_list (tl_date,tl_title,tl_desc,use_login,is_public) ".
             " values (to_date($1,'DD.MM.YYYY'),$2,$3,$4,$5)  returning tl_id";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->tl_date,
                       $this->tl_title,
                       $this->tl_desc,
                       $this->use_login,
                     $this->is_public)
             );
        $this->tl_id=Database::fetch_result($res,0,0);

    }

    public function update()
    {
        if ( $this->verify() != 0 ) return;

        if (trim($this->tl_title)=='')
            $this->tl_title=mb_substr(trim($this->tl_desc),0,40);

        if (trim($this->tl_title)=='')
        {
            
            return;
        }

        /*  limit the title to 35 char */
        $this->tl_title=mb_substr(trim($this->tl_title),0,40);

        $sql="update todo_list set tl_title=$1,tl_date=to_date($2,'DD.MM.YYYY'),tl_desc=$3,is_public=$5 ".
             " where tl_id = $4";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->tl_title,
                       $this->tl_date,
                       $this->tl_desc,
                       $this->tl_id,
                     $this->is_public)
             );

    }
    /*!\brief load all the task
     *\return an array of the existing tasks of the current user
     */
    public function load_all()
    {
        $sql="select tl_id, 
                tl_title,
                tl_desc,
                to_char( tl_date,'DD.MM.YYYY') as tl_date,
                is_public,
                use_login
             from todo_list 
             where 
                use_login=$1
                or is_public = 'Y'
                or tl_id in (select todo_list_id from todo_list_shared where use_login=$1)
             order by tl_date::date desc";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->use_login));
        $array=Database::fetch_all($res);
        
        return $array;
    }
    public function load()
    {

        $sql="select tl_id,tl_title,tl_desc,to_char( tl_date,'DD.MM.YYYY') as tl_date,is_public,use_login
             from todo_list where tl_id=$1 ";

        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->tl_id)
             );

        if ( Database::num_row($res) == 0 ) return;
        $row=Database::fetch_array($res,0);
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }

    }
    public function delete()
    {
        global $g_user;
        if ( $this->use_login != $_SESSION['g_user'] && $g_user->check_action(SHARENOTEREMOVE)==0) return;
        
        $sql="delete from todo_list_shared where todo_list_id=$1 ";
        $res=$this->cn->exec_sql($sql,array($this->tl_id));
        
        $sql="delete from todo_list where tl_id=$1 ";
        $res=$this->cn->exec_sql($sql,array($this->tl_id));
        
      

    }
    /**
     *@brief transform into xml for ajax answer
     */
    public function toXML()
    {
        $id='<tl_id>'.$this->tl_id.'</tl_id>';
        $title='<tl_title>'.escape_xml($this->tl_title).'</tl_title>';
        $desc='<tl_desc>'.escape_xml($this->tl_desc).'</tl_desc>';
        $date='<tl_date>'.$this->tl_date.'</tl_date>';
        $ret='<data>'.$id.$title.$desc.$date.'</data>';
        return $ret;
    }
    /**
     * @brief set a note public
     * @param $p_value is Y or N
     */
    public function set_is_public($p_value)
    {
        global $g_user;
        if ($g_user->check_action(SHARENOTEPUBLIC) == 1 )
        {
            $this->is_public=$p_value;
        }
    }
    /**
     * @brief Insert a share for current note 
     * in the table todo_list_shared
     * @param string (use_login)
     */
    public function save_shared_with($p_array)
    {
        global $g_user;
        if ($g_user->check_action(SHARENOTE) == 1 )
        {
            $this->cn->exec_sql('insert into todo_list_shared (todo_list_id,use_login) values ($1,$2)',
                    array($this->tl_id,$p_array));
            
        }
    }
    /**
     * @brief Insert a share for current note 
     * in the table todo_list_shared
     * The public shared note cannot be removed
     * @param string (use_login)
     */
    public function remove_shared_with($p_array)
    {
          $this->cn->exec_sql('delete from todo_list_shared where todo_list_id = $1 and use_login=$2',
                    array($this->tl_id,$p_array));
    }

    /**
     * Display the note
     * @return html string
     */
    function display()
    {
        ob_start();
        $this->load();
        include NOALYSS_TEMPLATE.'/todo_list_display.php';
        $ret=ob_get_clean();
        
        return $ret;
    }
    /**
     * Highlight today
     * @return string
     */
    function get_class()
    {
        $p_odd="";
        $a=date('d.m.Y');
        if ($a == $this->tl_date) $p_odd='highlight';
        return $p_odd;
    }
    function display_row($p_odd,$with_tag='Y')
    {
        $r="";
        $highlight=$this->get_class();
        $p_odd=($highlight == "")?$p_odd:$highlight;
        if ( $with_tag == 'Y') $r =  '<tr id="tr'.$this->tl_id.'" class="'.$p_odd.'">';
        $r.=
      '<td sorttable_customkey="'.format_date($this->tl_date,'DD.MM.YYYY','YYYYMMDD').'">'.
                $this->tl_date.
      '</td>'.
      '<td>'.
      '<a class="line" href="javascript:void(0)" onclick="todo_list_show(\''.$this->tl_id.'\')">'.
      htmlspecialchars($this->tl_title).
      '</a>'.
       '</td>';
        if ( $this->is_public == 'Y' && $this->use_login != $_SESSION['g_user'] )
        { // This is a public note, cannot be removed
            $r.= '<td></td>';
        }
        elseif ($this->use_login == $_SESSION['g_user'] )
        {
            // This a note the user owns
            $r.=  '<td>'.
         HtmlInput::button('del','X','onClick="todo_list_remove('.$this->tl_id.')"','smallbutton').
         '</td>';
        }
        else
        { 
            // this is a note shared by someone else
            $r.=  '<td>'.
                HtmlInput::button('del','X','onClick="todo_list_remove_share('.$this->tl_id.',\''.$this->use_login.'\','.Dossier::id().')"','smallbutton').
         '</td>';
        }
        
      if ( $with_tag == 'Y')  $r .= '</tr>';
        return $r;
    }
    static  function to_object ($p_cn,$p_array) 
    {
        $end=count($p_array);
        $ret=array();
        for ($i=0;$i < $end;$i++)
        {
            $t=new Todo_List($p_cn);
            $t->tl_id=$p_array[$i]['tl_id'];
            $t->tl_date=$p_array[$i]['tl_date'];
            $t->tl_title=$p_array[$i]['tl_title'];
            $t->tl_desc=$p_array[$i]['tl_desc'];
            $t->is_public=$p_array[$i]['is_public'];
            $t->use_login=$p_array[$i]['use_login'];
            $ret[$i]=clone $t;
        }
        return $ret;
    }
    /**
     * @brief display all the user to select the user with who we want to share
     * the connected user is not shown
     * @global type $g_user
     */
    function display_user()
    {
        global $g_user;
        // Get array of user
        $p_array=User::get_list(Dossier::id());
        $dossier=Dossier::id();
        include NOALYSS_TEMPLATE.'/todo_list_list_user.php';
        
    }
    /**
     * return the todo_list_shared.id of the note, if nothing is found then
     * return 0
     * @param $p_login
     * @return int
     */
    function is_shared_with($p_login)
    {
        $ret=$this->cn->get_value("select id from todo_list_shared where use_login=$1 and todo_list_id=$2",array($p_login,$this->tl_id));
        if ($ret == "")return 0;
        return $ret;
    }
    /**
     * @brief Add a share with someone
     * @param type $p_login
     */
    function add_share($p_login)
    {
        $this->cn->exec_sql("insert into todo_list_shared(todo_list_id,use_login) values ($1,$2)",array($this->tl_id,$p_login));
    }
    /**
     * @brief remove the share with someone
     * @param type $p_login
     */
    function remove_share($p_login)
    {
        $this->cn->exec_sql("delete from todo_list_shared where todo_list_id = $1 and use_login  = $2 ",array($this->tl_id,$p_login));
    }
    /*!\brief static testing function
     */
    static function test_me()
    {
    }

}


