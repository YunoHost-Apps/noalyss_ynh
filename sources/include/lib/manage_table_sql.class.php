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
 * \brief Definition Manage_Table_SQL
 *
 */

/*!\brief Purpose is to propose a librairy to display a table content
 * and allow to update and delete row , handle also the ajax call 
 * thanks the script managetable.js
 * 
 * Code for ajax , here we see the ajax_input for creating a dg box 
  \code
  $objet->set_pk($p_id);
 // It is very important to set the name of the javascript variable 
 // Contained in the http_input variable "ctl"
  $objet->set_object_name($objet_name);

  // Set the ajax to call
  $objet->set_callback("ajax.php");

  // Build the json object for JS
  $http=new HttpInput();
  $plugin_code=$http->request("plugin_code");
  $ac=$http->request("ac");
  $sa=$http->request("sa");
  $aJson=array("gDossier"=>Dossier::id(),
  "ac"=>$ac,
  "plugin_code"=>$plugin_code,
  "sa"=>$sa,
  "sb"=>$sb
  );
  $json=json_encode($aJson);
  $objet->param_set($json);

  // Display the box
    header('Content-type: text/xml; charset=UTF-8');
    $xml=$objet->ajax_input();
    echo $xml->save_XML();
  @endcode
 * @see ManageTable.js
 * @see ajax_accounting.php
 * @see sorttable.js
 * 
 */

class Manage_Table_SQL
{

    protected $table; //!< Object Data_SQL
    protected $a_label_displaid; //!< Label of the col. of the datarow
    protected $a_order; //!< order of the col
    protected $a_prop; //!< property for each col.
    protected $a_type; //!< Type of the column : date , select ... Only in input
    protected $a_select; //!< Possible value if a_type is a SELECT
    protected $object_name; //!< Object_name is used for the javascript
    protected $row_delete; //!< Flag to indicate if rows can be deleted
    protected $row_update; //!< Flag to indicate if rows can be updated
    protected $row_append; //!< Flag to indicate if rows can be added
    protected $json_parameter; //!< Default parameter to add (gDossier...)
    protected $aerror; //!< Array containing the error of the input data
    protected $col_sort; //!< when inserting, it is the column to sort,-1 to disable it and append only
    protected $a_info; //!< Array with the infotip
    protected $sort_column; //!< javascript sort , if empty there is no js sort
    const UPDATABLE=1;
    const VISIBLE=2;

    private $icon_mod; //!< place of right or left the icon update or mod, default right, accepted value=left,right,first column for mod
    private $icon_del; //!< place of right or left the icon update or mod, default right, accepted value=left,right

    /**
     * @brief Constructor : set the label to the column name,
     * the order of the column , set the properties and the
     * permission for updating or deleting row
     * @example test_manage_table_sql.php 
     * @example ajax_manage_table_sql.php
     */

    function __construct(Data_SQL $p_table)
    {
        $this->table=$p_table;
        $order=0;
        foreach ($this->table->name as $key=> $value)
        {

            $this->a_label_displaid[$value]=$value;
            $this->a_order[$order]=$value;
            $this->a_prop[$value]=self::UPDATABLE|self::VISIBLE;
            $this->a_type[$value]=$this->table->type[$value];
            $this->a_select[$value]=null;
            $order++;
        }
        $this->object_name=uniqid("tbl");
        $this->row_delete=TRUE;
        $this->row_update=TRUE;
        $this->row_append=TRUE;
        $this->callback="ajax.php";
        $this->json_parameter=json_encode(array("gDossier"=>Dossier::id(),
            "op"=>"managetable"));
        $this->aerror=[];
        $this->icon_mod="right";
        $this->icon_del="right";
        $this->col_sort=0;
        // By default no js sort
        $this->sort_column="";
    }
    /**
     * send the XML headers for the ajax call 
     */
    function send_header()
    {
        header('Content-type:text/xml;charset="UTF-8"');
    }
    /**
     * When adding an element , it is column we checked to insert before,
     * @return none
     */
    function get_col_sort() {
        return $this->col_sort;
    }
    /**
     * Set the info for a column, use Icon_Action::infobulle
     * the message are in message_javascript.php
     * @param string $p_key Column name
     * @param integer $p_comment comment idx
     * 
     * @see message_javascript.php
     * @see Icon_Action::infobulle()
     */
    function set_col_tips($p_key,$p_comment) {
        $this->a_info[$p_key]=$p_comment;
    }
    /**
     * When adding an element ,we place it thanks the DOM Attribute sort_value
     * set it to -1 if you want one to append
     * @param numeric $pn_num
     * @note you must be aware that the icon_mod or icon_del is in the first col, 
     * this column is skipped
     */
    function set_col_sort($p_num) {
        $this->col_sort=$p_num;
    }
    function get_icon_mod()
    {
        return $this->icon_mod;
    }
    function get_icon_del()
    {
        return $this->icon_del;
    }
    function get_table()
    {
        return $this->table;
    }

    function set_table(Data_SQL $p_noalyss_sql)
    {
        $this->table=$p_noalyss_sql;
    }
    function get_order()
    {
        return $this->a_order;
    }
    function set_order($p_order)
    {
        if (! is_array($p_order) )            
                throw new Exception("set_order, parameter is not an array");
        $this->a_order=$p_order;
    }
    /**
     * @brief set the error message for a wrong input
     * @param $p_col the column name 
     * @param $p_message the error message
     * @see check
     */
    function set_error($p_col, $p_message)
    {
        $this->aerror[$p_col]=$p_message;
    }
    /**
     * returns the nb of errors found
     */
    function count_error()
    {
        return count($this->aerror);
    }
    /**
     * @brief retrieve the error message
     * @param $p_col column name
     * @return string with message or empty if no error
     * @see input
     */
    function get_error($p_col)
    {
        if (isset($this->aerror[$p_col]))
            return $this->aerror[$p_col];
        return "";
    }

    /**
     * This function can be overrided to check the data before 
     * inserting , updating or removing, above an example of an overidden check.
     * 
     * Usually , you get the row of the table (get_table) , you check the conditions
     * if an condition is not met then you set the error with $this->set_error 
     * 
     * if there are error (returns false otherwise true
     * 
     * @see set_error get_error count_error
     * @return boolean
     * @code 
function check()
    {
        global $cn;
        $table=$this->get_table();
        $is_error=0;
        $insert=false;
        // sect_codename must be unique 
        if ( $table->exist() > 0) {
            $insert=1;
        }
        $count=$cn->get_value(" select count(*) from syndicat.treasurer where tr_login=$1 and sect_id=$2 and tr_id<>$3",
                array(
                    $table->tr_login,
                    $table->section_full_name,
                    $table->tr_id
                ));
        if ($count > 0 ) {
            $this->set_error("section_full_name",_("Ce trésorier a déjà accès à cette section"));
            $is_error++;
        }
        if ( $is_error > 0 ) return false;
        return true;
    }    
     * @endcode
     */
    function check()
    {
        return true;
    }

    /**
     * @brief set the type of a column , it will change in the input db box , the
     * select must supply an array of possible values [val=> , label=>] with
     * the variable $this->key_name->a_value
     * @param $p_key col name
     * @param $p_type is SELECT NUMERIC TEXT or DATE 
     * @param $p_array if type is  SELECT an array is expected
     */
    function set_col_type($p_key, $p_value, $p_array=NULL)
    {
        if (!isset($this->a_type[$p_key]))
            throw new Exception("invalid key $p_key");

        if (!in_array($p_value,
                        array("text", "numeric", "date", "select", "timestamp")))
            throw new Exception("invalid type $p_value");

        $this->a_type[$p_key]=$p_value;
        $this->a_select[$p_key]=$p_array;
    }

    /**
     * @brief return the type of a column 
     * @param $p_key col name
     * @see set_col_type
     */
    function get_col_type($p_key)
    {
        if (!isset($this->a_type[$p_key]))
            throw new Exception("invalid key");

        return $this->a_type[$p_key];
    }

    /**
     * Get the object name
     * @details : return the object name , it is useful it
     * the javascript will return coded without the create_js_script function
     * @see create_js_script
     */
    function get_js_variable()
    {
        return $this->object_name;
    }
    /**
     * Add json parameter to the current one
     */
    function add_json_param($p_attribute,$p_value) {
        $x=json_decode($this->json_parameter,TRUE);
        $x[$p_attribute]=$p_value;
        $this->json_parameter=json_encode($x, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
    function get_json()
    {
        return $this->json_parameter;
    }
    function get_object_name() {
        return $this->object_name;
    }
    /**
     * Set the parameter of the object (gDossier, ac, plugin_code...)
     * @detail By default , only gDossier will be set . The default value
     * is given in the constructor
     * @param string with json format $p_json 
     * @deprecated since version 692
     * @see set_json
     */
    function param_set($p_json)
    {
        $this->set_json($p_json);
    }
    /**
     * Set the parameter of the object (gDossier, ac, plugin_code...)
     * @detail By default , only gDossier will be set . The default value
     * is given in the constructor
     * @param string with json format $p_json 
     */
    function set_json($p_json)
    {
        $this->json_parameter=$p_json;
        
    }
    /**
     * @brief set the callback function that is passed to javascript
     * @param $p_file  : callback file by default ajax.php
     */
    function set_callback($p_file)
    {
        $this->callback=$p_file;
    }

    /**
     * @brief we must create first the javascript if we want to update, insert 
     * or delete  rows. It is the default script . 
     */
    function create_js_script()
    {
        echo "
		<script>
		var {$this->object_name}=new ManageTable(\"{$this->table->table}\");
		{$this->object_name}.set_callback(\"{$this->callback}\");
		{$this->object_name}.param_add({$this->json_parameter});
		{$this->object_name}.set_sort({$this->get_col_sort()});
		</script>

	";
    }

    /**
     * Set the object_name 
     * @param string $p_object_name name of the JS var, used in ajax response,id
     * of the part of the id DOMElement to modify
     */
    function set_object_name($p_object_name)
    {
        $this->object_name=$p_object_name;
    }

    /**
     * @brief set a column of the data row updatable or not
     * @param string $p_key data column
     * @param bool $p_value Boolean False or True
     */
    function set_property_updatable($p_key, $p_value)
    {
        if (! isset($this->a_prop[$p_key]))
            throw new Exception(__FILE__.":".__LINE__."$p_key invalid index");
        // if already done returns 
        if ( $this->get_property_updatable($p_key) == $p_value)return;
        if ($p_value==False)
            $this->a_prop[$p_key]=$this->a_prop[$p_key]-self::UPDATABLE;
        elseif ($p_value==True)
            $this->a_prop[$p_key]=$this->a_prop[$p_key]|self::UPDATABLE;
        else
            throw new Exception("set_property_updatable [ $p_value ] incorrect");
    }

    /**
     * @brief return false if the update of the row is forbidden
     */
    function can_update_row()
    {

        return $this->row_update;
    }
    /**
     * Set the icon to modify at the right ,the first col or left of the row
     * 
     * @param type $pString
     * @throws Exception
     */
    function set_icon_mod($pString) {
        if ($pString != "right" && $pString != "left" && $pString!="first") 
            throw new Exception('set_icon_mod invalide '.$pString);
        $this->icon_mod=$pString;
    }
    /**
     * Set the icon to delete at the right or left of the row
     * @param type $pString
     * @throws Exception
     */
    function set_icon_del($pString) {
        if ($pString != "right" && $pString != "left" ) 
            throw new Exception('set_icon_del invalide '.$pString);
        $this->icon_del=$pString;
    }
    /**
     * @brief return false if the append of the row is forbidden
     */
    function can_append_row()
    {

        return $this->row_append;
    }

    /**
     * @brief Enable or disable the deletion of rows
     * @param $p_value Boolean : true enable the row to be deleted
     */
    function set_delete_row($p_value)
    {
        if ($p_value!==True&&$p_value!==False)
            throw new Exception("Valeur invalide set_delete_row [$p_value]");
        $this->row_delete=$p_value;
    }

    /**
     * @brief Enable or disable the appending of rows
     * @param $p_value Boolean : true enable the row to be appended
     */
    function set_append_row($p_value)
    {
        if ($p_value!==True&&$p_value!==False)
            throw new Exception("Valeur invalide set_append_row [$p_value]");
        $this->row_append=$p_value;
    }

    /**
     * @brief Enable or disable the updating of rows
     * @param $p_value Boolean : true enable the row to be updated 
     */
    function set_update_row($p_value)
    {
        if ($p_value!==True&&$p_value!==False)
            throw new Exception("Valeur invalide set_update_row [$p_value]");
        $this->row_update=$p_value;
    }

    /**
     * @brief return false if the delete of the row is forbidden
     */
    function can_delete_row()
    {
        return $this->row_delete;
    }

    /**
     * @brief return True if the column is updatable otherwise false
     * @param $p_key data column
     */
    function get_property_updatable($p_key)
    {
        $val=$this->a_prop[$p_key]&self::UPDATABLE;
        if ($val==self::UPDATABLE)
            return true;
        return false;
    }

    /**
     * @brief set a column of the data row visible  or not
     * @param string $p_key data column
     * @param bool $p_value Boolean False or True
     */
    function set_property_visible($p_key, $p_value)
    {
        if (!isset ($this->a_prop[$p_key]) )
            throw new Exception(__FILE__.":".__LINE__."$p_key invalid index");
        // if already done return
        if ( $this->get_property_visible($p_key) == $p_value)return;
        
        if ($p_value==False)
            $this->a_prop[$p_key]=$this->a_prop[$p_key]-self::VISIBLE;
        elseif ($p_value==True)
            $this->a_prop[$p_key]=$this->a_prop[$p_key]|self::VISIBLE;
        else
            throw new Exception("set_property_updatable [ $p_value ] incorrect");
    }

    /**
     * @brief return True if the column is visible otherwise false
     * @param $p_key data column
     */
    function get_property_visible($p_key)
    {
        $val=$this->a_prop[$p_key]&self::VISIBLE;
        if ($val===self::VISIBLE)
            return true;
        return false;
    }

    /**
     * @brief set the name to display for a column
     * @param string $p_key data column
     * @param string $p_display Label to display
     *
     */
    function set_col_label($p_key, $p_display)
    {
        $this->a_label_displaid[$p_key]=$p_display;
    }

    /**
     * @brief get the position of a column
     * @param $p_key data column
     */
    function get_current_pos($p_key)
    {
        $nb_order=count($this->a_order);
        for ($i=0; $i<$nb_order; $i++)
                if ($this->a_order[$i]==$p_key)
                return $i;
        throw new Exception("COL INVAL ".$p_key);
    }

    /** 	
     * @brief if we change a column order , the order
     * of the other columns is impacted.
     *
     * With a_order[0,1,2,3]=[x,y,z,a]
     * if we move the column x (idx=0) to 2	
     * we must obtain [y,z,x,a]
     * @param string $p_key data column
     * @param integer $p_idx new location
     */
    function move($p_key, $p_idx)
    {
        // get current position of p_key
        $cur_pos=$this->get_current_pos($p_key);

        if ($cur_pos==$p_idx)
            return;

        if ($cur_pos<$p_idx)
        {
            $nb_order=count($this->a_order);
            for ($i=0; $i<$nb_order; $i++)
            {
                // if col_name is not the searched one we continue		
                if ($this->a_order[$i]!=$p_key)
                    continue;
                if ($p_idx==$i)
                    continue;
                // otherwise we swap with i+1
                $old=$this->a_order[$i+1];
                $this->a_order[$i]=$this->a_order[$i+1];
                $this->a_order[$i+1]=$p_key;
            }
        } else
        {

            $nb_order=count($this->a_order)-1;
            for ($i=$nb_order; $i>0; $i--)
            {
                // if col_name is not the searched one we continue		
                if ($this->a_order[$i]!=$p_key)
                    continue;
                if ($p_idx==$i)
                    continue;
                // otherwise we swap with i+1
                $old=$this->a_order[$i-1];
                $this->a_order[$i]=$this->a_order[$i-1];
                $this->a_order[$i-1]=$p_key;
            }
        }
    }

    /**
     * @brief display the data of the table
     * @param $p_order is the cond or order of the rows, 
     * if empty the primary key will be used
     * @param $p_array array of the bind variables
     * @note the function create_js_script MUST be called before this function
     */
    function display_table($p_order="", $p_array=NULL)
    {
        if ($p_order=="")
        {
            $p_order="order by {$this->table->primary_key}";
        }
        $ret=$this->table->seek($p_order, $p_array);
        $nb=Database::num_row($ret);
        if ($this->can_append_row()==TRUE)
        {
            echo HtmlInput::button_action(" "._("Ajout"),
                    sprintf("%s.input('-1','%s')", 
                            $this->object_name,
                            $this->object_name), "xx", "smallbutton", BUTTONADD);
        }
        $nb_order=count($this->a_order);
        $virg=""; $result="";
        // filter only on visible column
        $visible=0;
        for ($e=0; $e<$nb_order; $e++)
        {
            if ($this->get_property_visible($this->a_order[$e])==TRUE)
            {
                $result.=$virg."$visible";
                $virg=",";
                $visible++;
            }
        }
        echo _('Cherche')." ".HtmlInput::filter_table("tb".$this->object_name, $result, 1);
        
        // Set a sort on a column if sort_column is not empty
        if ( $this->sort_column =="")
        {
            printf('<table class="result" id="tb%s">', $this->object_name); 
        } else {
           printf('<table class="result sortable" id="tb%s">', $this->object_name);
        }
        for ($i=0; $i<$nb; $i++)
        {
            if ($i==0)
            {
                $this->display_table_header();
            }
            $row=Database::fetch_array($ret, $i);
            $this->display_row($row);
        }
        echo "</table>";
        if ($this->can_append_row()==TRUE)
        {
            echo HtmlInput::button_action(" "._("Ajout"),
                    sprintf("%s.input('-1','%s')", 
                            $this->object_name,
                            $this->object_name), "xx", "smallbutton", BUTTONADD);
        }
        printf('<script> alternate_row_color("tb%s");</script>',
                $this->object_name);
    }

    /**
     * @brief display the column header excepted the not visible one
     * and in the order defined with $this->a_order
     */
    function display_table_header()
    {
        $nb=count($this->a_order);
        echo "<tr>";

        if ($this->can_update_row() && $this->icon_mod=="left")
        {
            echo th("  ", 'style="width:40px"  class="sorttable_nosort"');
        }
        if ($this->can_delete_row() && $this->icon_del=="left")
        {
            echo th(" ", 'style="width:40px"  class="sorttable_nosort"');
        }
        for ($i=0; $i<$nb; $i++)
        {

            $key=$this->a_order[$i];
            $sorted="";
            if ( $key == $this->sort_column) {
                $sorted=' class="sorttable_sorted"';
            }
            if ($this->get_property_visible($key)==true)
                echo th("",$sorted,$this->a_label_displaid[$key]);
        }
        if ($this->can_update_row() && $this->icon_mod=="right")
        {
            echo th("  ", 'style="width:40px"  class="sorttable_nosort"');
        }
        if ($this->can_delete_row() && $this->icon_del=="right")
        {
            echo th(" ", 'style="width:40px"  class="sorttable_nosort" ');
        }
        echo "</tr>";
    }
    /**
     * set the column to sort by default
     */
    function set_sort_column($p_col)
    {
        $this->sort_column=$p_col;
    }
    /**
     * return the column to sort
     */
    function get_sort_column()
    {
        return $this->sort_column;
    }
    
    /**
     * @brief set the id value of a data row and load from the db
     */
    function set_pk($p_id)
    {
        $this->table->set_pk_value($p_id);
        $this->table->load();
    }

    /**
     * @brief get the data from http request strip the not update or not visible data to their 
     * initial value. Before saving , it is important to set the pk and load from db
     * @see set_pk
     */
    function from_request()
    {
        $nb=count($this->a_order);
        for ($i=0; $i<$nb; $i++)
        {
            
            $key=$this->a_order[$i];
            if ($this->get_property_visible($key)==TRUE&&$this->get_property_updatable($key)
                    ==TRUE)
            {
                $v=HtmlInput::default_value_request($this->a_order[$i], "");
                $this->table->$key=strip_tags($v);
            }
        }
    }

    function display_icon_mod($p_row)
    {
        if ($this->can_update_row())
        {
            echo "<td>";
            $js=sprintf("%s.input('%s','%s');", $this->object_name,
                    $p_row[$this->table->primary_key], $this->object_name
            );
            echo HtmlInput::image_click("crayon-mod-b24.png", $js, _("Modifier"));
            echo "</td>";
        }
    }

    function display_icon_del($p_row)
    {
        if ($this->can_delete_row())
        {
            echo "<td>";
            $js=sprintf("%s.remove('%s','%s');", $this->object_name,
                    $p_row[$this->table->primary_key], $this->object_name
            );
            echo HtmlInput::image_click("trash-24.gif", $js, _("Effacer"));
            echo "</td>";
        }
    }

    /**
     * @brief display a data row in the table, with the order defined
     * in a_order and depending of the visibility of the column
     * @see display_table
     */
    function display_row($p_row)
    {

        printf('<tr id="%s_%s">', $this->object_name,
                $p_row[$this->table->primary_key])
        ;
        
        if ($this->icon_mod=="left")
            $this->display_icon_mod($p_row);
        if ($this->icon_del=="left")
            $this->display_icon_del($p_row);
        
        $nb_order=count($this->a_order);
        for ($i=0; $i<$nb_order; $i++)
        {
            $v=$this->a_order[$i];
            if ($i==0&&$this->icon_mod=="first"&&$this->can_update_row())
            {
                $js=sprintf("onclick=\"%s.input('%s','%s');\"", $this->object_name,
                        $p_row[$this->table->primary_key], $this->object_name);
                $td=($i == $this->col_sort ) ? sprintf('<td sort_value="X%s" >',$p_row[$v]):"<td>";
                echo $td.HtmlInput::anchor($p_row[$v], "", $js).'</td>';
            }
            elseif ( $i == $this->col_sort && $this->get_property_visible($v))
            {
                echo td($p_row[$v],sprintf(' sort_value="X%s" ',$p_row[$v]));
            }
            elseif ( ! $this->get_property_visible($v)) { 
                continue;
            }
            else
            {
                if ( $this->get_col_type($v)=="select")
                {
                    $idx=$p_row[$v];
                    if ( ! isset($this->a_select[$v][$idx])) {
                        echo td("--");
                    } else {
                        echo td($this->a_select[$v][$idx]["label"]);
                    }
                    
                }else {
                    echo td($p_row[$v]);
                }
            }
        }
        if ($this->icon_mod=="right")
            $this->display_icon_mod($p_row);
        if ($this->icon_del=="right")
            $this->display_icon_del($p_row);



        echo '</tr>';
    }

    /**
     * @brief display into a dialog box the datarow in order 
     * to be appended or modified. Can be override if you need
     * a more complex form
     */
    function input()
    {
        $nb_order=count($this->a_order);
        echo "<table>";
        for ($i=0; $i<$nb_order; $i++)
        {
            echo "<tr>";
            $key=$this->a_order[$i];
            $label=$this->a_label_displaid[$key];
            $value=$this->table->get($key);
            $error=$this->get_error($key);
            $error=($error=="")?"":HtmlInput::errorbulle($error);
            if ($this->get_property_visible($key)===TRUE)
            {
                // Label
                $info="";
                if ( isset($this->a_info[$key])) {
                    $info=Icon_Action::infobulle($this->a_info[$key]);
                }
                // Label
                echo "<td> {$label} {$info} {$error}</td>";

                if ($this->get_property_updatable($key)==TRUE)
                {
                    echo "<td>";
                    if ($this->a_type[$key]=="select")
                    {
                        $select=new ISelect($key);
                        $select->value=$this->a_select[$key];
                        $select->selected=$value;
                        echo $select->input();
                    }
                    elseif ($this->a_type[$key]=="text")
                    {
                        $text=new IText($key);
                        $text->value=$value;
                        $min_size=(strlen($value)<30)?30:strlen($value)+5;
                        $text->size=$min_size;
                        echo $text->input();
                    }
                    elseif ($this->a_type[$key]=="numeric")
                    {
                        $text=new INum($key);
                        $text->value=$value;
                        $min_size=(strlen($value)<10)?10:strlen($value)+1;
                        $text->size=$min_size;
                        echo $text->input();
                    }
                    elseif ($this->a_type[$key]=="date")
                    {
                        $text=new IDate($key);
                        $text->value=$value;
                        $min_size=10;
                        $text->size=$min_size;
                        echo $text->input();
                    }
                    echo "</td>";
                }
                else
                {
                    printf('<td>%s %s</td>', h($value),
                            HtmlInput::hidden($key, $value)
                    );
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    /**
     * @brief Save the record from Request into the DB and returns an XML
     * to update the Html Element
     * @return \DOMDocument
     */
    function ajax_save()
    {

        $status="NOK";
        $xml=new DOMDocument('1.0', "UTF-8");
        try
        {
            // fill up object with $_REQUEST
            $this->from_request();
            // Check if the data are valid , if not then display the
            // input values with the error message 
            //
            if ($this->check()==false)
            {
                $xml=$this->ajax_input("NOK");
                return $xml;
            }
            else
            {
                // Data are valid so we can save them
                $this->save();
                // compose the answer
                $status="OK";
                $s1=$xml->createElement("status", $status);
                $ctl=$this->object_name."_".$this->table->get_pk_value();
                $s2=$xml->createElement("ctl_row", $ctl);
                $s4=$xml->createElement("ctl", $this->object_name);
                ob_start();
                $this->table->load();
                $array=$this->table->to_array();
                $this->display_row($array);
                $html=ob_get_contents();
                ob_end_clean();
                $s3=$xml->createElement("html");
                $t1=$xml->createTextNode($html);
                $s3->appendChild($t1);
            }

            $root=$xml->createElement("data");
            $root->appendChild($s1);
            $root->appendChild($s2);
            $root->appendChild($s3);
            $root->appendChild($s4);
            $xml->appendChild($root);
        }
        catch (Exception $ex)
        {
            $s1=$xml->createElement("status", "NOK");
            $s2=$xml->createElement("ctl_row",
                    $this->object_name+"_"+$this->table->get_pk_value());
            $s4=$xml->createElement("ctl", $this->object_name);
            $s3=$xml->createElement("html", $ex->getTraceAsString());
            $root=$xml->createElement("data");
            $root->appendChild($s1);
            $root->appendChild($s2);
            $root->appendChild($s3);
            $root->appendChild($s4);
            $xml->appendChild($root);
        }
        return $xml;
    }

    /**
     * @brief send an xml with input of the object, create an xml answer.
     * XML Tag 
     *   - status  : OK , NOK 
     *   - ctl     : Dom id to update 
     *   - content : Html answer
     * @return DomDocument
     */
    function ajax_input($p_status="OK")
    {
        $xml=new DOMDocument("1.0", "UTF-8");
        $xml->createElement("status", $p_status);
        try
        {
            $status=$p_status;
            ob_start();

            echo HtmlInput::title_box("Donnée", "dtr","close","","y");
            printf('<form id="frm%s_%s" method="POST" onsubmit="%s.save(\'frm%s_%s\');return false;">',
                    $this->object_name, $this->table->get_pk_value(),
                    $this->object_name, $this->object_name,
                    $this->table->get_pk_value());
            $this->input();
            // JSON param to hidden
            echo HtmlInput::json_to_hidden($this->json_parameter);
            echo HtmlInput::hidden("p_id", $this->table->get_pk_value());
            // button Submit and cancel
            $close=sprintf("\$('%s').remove()", "dtr");
            // display error if any
            $this->display_error();
            echo '<ul class="aligned-block">',
            '<li>',
            HtmlInput::submit('update', _("OK")),
            '</li>',
            '<li>',
            HtmlInput::button_action(_("Cancel"), $close, "", "smallbutton"),
            '</li>',
            '</ul>';
            echo "</form>";
            

            $html=ob_get_contents();
            ob_end_clean();

            $s1=$xml->createElement("status", $status);
            $ctl=$this->object_name."_".$this->table->get_pk_value();
            $s2=$xml->createElement("ctl_row", $ctl);
            $s4=$xml->createElement("ctl", $this->object_name);
            $s3=$xml->createElement("html");
            $t1=$xml->createTextNode($html);
            $s3->appendChild($t1);

            $root=$xml->createElement("data");
            $root->appendChild($s1);
            $root->appendChild($s2);
            $root->appendChild($s3);
            $root->appendChild($s4);
        }
        catch (Exception $ex)
        {
            $s1=$xml->createElement("status", "NOK");
            $s3=$xml->createElement("ctl", $this->object_name);
            $s2=$xml->createElement("ctl_row",
                    $this->object_name+"_"+$this->table->get_pk_value());
            $s4=$xml->createElement("html", $ex->getTraceAsString());
            
            $root=$xml->createElement("data");
            $root->appendChild($s1);
            $root->appendChild($s2);
            $root->appendChild($s3);
            $root->appendChild($s4);
        }
        $xml->appendChild($root);
        return $xml;
    }

    /**
     * @brief delete a datarow , the id must be have set before 
     * @see from_request
     */
    function delete()
    {
        $this->table->delete();
    }

    /**
     * Delete a record and return an XML answer for ajax. If a check is needed before
     * deleting you can override this->delete and throw an exception if the deleting
     * is not allowed
     * @return \DOMDocument
     */
    function ajax_delete()
    {
        $status="NOK";
        $xml=new DOMDocument('1.0', "UTF-8");
        try
        {
            $this->delete();
            $status="OK";
            $s1=$xml->createElement("status", $status);
            $ctl=$this->object_name."_".$this->table->get_pk_value();
            $s2=$xml->createElement("ctl_row", $ctl);
            $s3=$xml->createElement("html", _("Effacé"));
            $s4=$xml->createElement("ctl", $this->object_name);

            $root=$xml->createElement("data");
            $root->appendChild($s1);
            $root->appendChild($s2);
            $root->appendChild($s3);
            $root->appendChild($s4);
        }
        catch (Exception $ex)
        {
            $s1=$xml->createElement("status", "NOK");
            $s2=$xml->createElement("ctl",
                    $this->object_name."_".$this->table->get_pk_value());
            $s3=$xml->createElement("html", $ex->getMessage());
            $s4=$xml->createElement("ctl", $this->object_name);

            $root=$xml->createElement("data");
            $root->appendChild($s1);
            $root->appendChild($s2);
            $root->appendChild($s3);
            $root->appendChild($s4);
        }
        $xml->appendChild($root);
        return $xml;
    }

    /**
     * @brief save the Data_SQL Object
     * The noalyss_SQL is not empty
     * @see from_request
     */
    function save()
    {
        if ($this->table->exist()==0)
        {
            $this->table->insert();
        }
        else
        {
            $this->table->update();
        }
    }

    /**
     * @brief insert a new value
     * @see set_pk_value
     * @see from_request
     */
    function insert()
    {
        $this->table->insert();
    }

    /**
     * @brief
     * @see set_pk_value
     * @see from_request
     */
    function update()
    {
        $this->table->update();
    }

    /**
     * @brief
     * @see set_pk_value
     * @see from_request
     */
    function set_value($p_key, $p_value)
    {
        $this->table->set($p_key, $p_value);
    }

    /**
     * Display a list of the error collected
     * @see get_error set_error 
     * 
     */
    function display_error()
    {
        $nb_order=count($this->a_order);
        if (count($this->aerror)==0)
            return;
        echo "<span class=\"notice\">Liste erreurs :</span>";
        for ($i=0; $i<$nb_order; $i++)
        {
            $key=$this->a_order[$i];
            $label=$this->a_label_displaid[$key];
            $error=$this->get_error($key);
            $error=($error=="")?"":"<span class=\"notice\" style=\"font-weight:normal;font-style:normal;display:block\">".h($label)." : ".h($this->get_error($key))."</span>";

            echo $error;
        }
        echo "</ul>";
    }

}
