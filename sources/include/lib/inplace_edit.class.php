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

// Copyright Author Dany De Bontridder dany@alchimerys.be

/**
 * @file
 * @brief Inplace_edit class for ajax update of HtmlInput object
 */
/**
 * @class
 * @brief Inplace_edit class for ajax update of HtmlInput object.
 * You need an ajax to response and modify the data. Some parameters will be sent
 * by default when you click on the element
 *  - input : htmlInput object serialized
 *  - action : ok or cancel , nothing if you just want to display the input
 * 
 * @example inplace_edit.test.php
 */
class Inplace_Edit
{
  

    /// HtmlInput object 
    private $input;
    /// Json object to pass to JavaScript
    private $json;
    /// Php file which answered the ajax
    private $callback;
    /// Message to display if value is empty
    private $message;
    /**
     * Create a Inplace_Edit, initialise JSON and fullfill the default json value:
     * input which is the HtmlInput object serialized
     * @param HtmlInput $p_input
     */
    function __construct(HtmlInput $p_input) {
        $this->input=$p_input;
        $x["input"]=serialize($p_input);
        $this->json=json_encode($x, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
        $this->message=_("Cliquez pour éditer");
    }
    ///@brief build a Inplace_Edit object from
    /// a serialized string (ajax json parameter = input)
    static function build($p_serialize)
    {
        
        $input=  unserialize($p_serialize);
        $obj=new Inplace_Edit($input);
        return $obj;
    }
    function display()
    {
        echo $this->input->value;
    }
    /**
     * @brief response in ajax to be edited
     */
     function ajax_input() {
         ob_start();
        echo $this->input->input();
        echo '<a style="display:inline" class="smallbutton"  id="inplace_edit_ok'.$this->input->id.'">'._('ok').'</a>';
        echo '<a style="display:inline" class="smallbutton" id="inplace_edit_cancel'.$this->input->id.'">'._('cancel').'</a>';
        echo <<<EOF
        <script>
        $('{$this->input->id}edit').addClassName('inplace_edit_input');
            {$this->input->id}edit.onclick=null;
            inplace_edit_ok{$this->input->id}.onclick= function () {
                var json={$this->json};
                json['ieaction']='ok';
                json['value']=$('{$this->input->id}').value;
                new Ajax.Updater('{$this->input->id}edit'
                ,'{$this->callback}',
                 {parameters:  json ,evalScripts:true});}
            inplace_edit_cancel{$this->input->id}.onclick= function () {
                var json={$this->json};
                json['ieaction']='cancel';
                new Ajax.Updater('{$this->input->id}edit'
                ,'{$this->callback}',
                 {parameters:  json ,evalScripts:true});}
            
        </script>
EOF;
                $ret= ob_get_contents();
                ob_end_clean();
                return $ret;
    }
    /**
     * @brief display only the value , if the action after saving or cancelling
     * 
     */
    function value()
    {
        $v=$this->input->get_value();
        $v=(trim($v)=="")?$this->message:$v;
        echo $v,
                 '<span class="smallicon icon" style="margin-left:5px">&#xe80d;</span> ',
                "
            <script>
            $('{$this->input->id}edit').removeClassName('inplace_edit_input');
        {$this->input->id}edit.onclick=function() {
                 new Ajax.Updater('{$this->input->id}edit'
                ,'{$this->callback}',
                 {parameters:  {$this->json} ,evalScripts:true});}
            </script>
              ";   
    }
    /***
     * @brief display the value with the click event
     */
    function input() {
        ob_start();
        echo <<<EOF
            <span class="inplace_edit" id="{$this->input->id}edit" >
EOF;
        $v=$this->input->get_value();
        $v=(trim($v)=="")?$this->message:$v;
        echo $v;
        echo'<span class="smallicon icon" style="margin-left:5px">&#xe80d;</span> ';
        echo "</span>";
        echo "
            
            <script>
        {$this->input->id}edit.onclick=function() {
                 new Ajax.Updater('{$this->input->id}edit'
                ,'{$this->callback}',
                 {parameters:  {$this->json} ,evalScripts:true});
             }
            </script>
              ";   
         $ret= ob_get_contents();
         ob_end_clean();
         return $ret;
    }
    /**
     * @brief the php callback file to call for ajax
     */
    function set_callback($callback) {
        $this->callback=$callback;
    }
    /***
     * @brief the JSON parameter to give to the script, 
     * this function shouldn't be used since it override the default JSON value
     * @see add_json_parameter
     */
    function set_json($json) {
        $this->json=$json;
    }
     /**
     * Add json parameter to the current one, if there attribute already exists
     * it will be overwritten
     */
    function add_json_param($p_attribute,$p_value) {
        $x=json_decode($this->json,TRUE);
        $x[$p_attribute]=$p_value;
        $this->json=json_encode($x, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
    /**
     * \brief return the HtmlObject , var input
     * 
     */
    function get_input() {
        return $this->input;
    }
    /**
     * @brief set the var input (HtmlObject) and update the
     * json attribute input
     * @param HtmlInput $p_input
     */
    function set_input(HtmlInput $p_input) {
        $this->input = $p_input;
        $x=json_decode($this->json,TRUE);
        $x["input"]=serialize($p_input);
        $this->json=json_encode($x, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
    /**
     * Set the value of the HtmlInput object $input
     * @param type $p_value
     */
    function set_value($p_value) {
        $input=$this->get_input();
        $this->input->set_value(strip_tags($p_value));
        $this->set_input($input);
    }
    /**
     * Message to display if the value is empty 
     * @param string $p_str
     */
    function set_message($p_str) {
        $this->message=$p_str;
    }
}