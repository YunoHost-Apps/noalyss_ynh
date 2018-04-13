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
require_once NOALYSS_INCLUDE.'/database/tag_sql.class.php';

class Tag
{
    function __construct($p_cn,$id=-1)
    {
        $this->cn=$p_cn;
        $this->data=new Tag_SQL($p_cn,$id);
    }
    /**
     * Show the list of available tag
     * @return HTML
     */
    function show_list()
    {
        $ret=$this->data->seek(' order by t_tag');
        if ( $this->cn->count($ret) == 0) return "";
        require_once NOALYSS_TEMPLATE.'/tag_list.php';
    }
    /**
     * let select a tag to add
     */
    function select()
    {
        $ret=$this->data->seek("where t_actif='Y' order by t_tag");
        require_once NOALYSS_TEMPLATE.'/tag_select.php';
    }
    /**
     * Display a inner window with the detail of a tag
     */
    function form_add()
    {
        $data=$this->data;
        require_once NOALYSS_TEMPLATE.'/tag_detail.php';
    }
    /**
     * Show the tag you can add to a document
     */
    function show_form_add()
    {
        echo h2(_("Ajout d'un dossier (ou tag)"));
       
        $this->form_add();
    }
    function save($p_array)
    {
        if ( trim($p_array['t_tag'])=="" ) return ;
        $this->data->t_id=$p_array['t_id'];
        $this->data->t_tag=  strip_tags($p_array['t_tag']);
        $this->data->t_description=strip_tags($p_array['t_description']);
        $this->data->t_actif=$p_array['t_actif'];
        $this->data->save();
    }
    function remove($p_array)
    {
        $this->data->t_id=$p_array['t_id'];
        $this->data->delete();
    }
    /**
     * Show a button to select tag for Search
     * @return HTML
     */
    static  function button_search($p_prefix)
    {
        $r="";
        $r.=HtmlInput::button("choose_tag", _("Etiquette"), 'onclick="search_display_tag('.Dossier::id().',\''.$p_prefix.'\')"', "smallbutton");
        return $r;
    }
    /**
     * let select a tag to add to the search
     */
    function select_search($p_prefix)
    {
        $ret=$this->data->seek(' order by t_tag');
        require_once NOALYSS_TEMPLATE.'/tag_search_select.php';
    }
    /**
     * In the screen search add this data to the cell
     */
    function update_search_cell($p_prefix) {
        echo '<span id="sp_'.$p_prefix.$this->data->t_id.'" style="border:1px solid black;margin-right:5px;">';
        echo h($this->data->t_tag);
        echo HtmlInput::hidden($p_prefix.'tag[]', $this->data->t_id);
        $js=sprintf("$('sp_".$p_prefix.$this->data->t_id."').remove();");
        echo HtmlInput::anchor( SMALLX, "javascript:void(0)", "onclick=\"$js\"", ' class="smallbutton " style="padding:0px;display:inline" ');
        echo '</span>';
    }
    /**
     * clear the search cell
     */
    static function add_clear_button($p_prefix) {
        $clear=HtmlInput::button('clear', 'X', 'onclick="search_clear_tag('.Dossier::id().',\''.$p_prefix.'\');"', 'smallbutton');
        return $clear;
    }
}

?>
