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
 * \brief the extension class manages the plugins for the security, the access
 * the inclusion...
 */
/*!\brief manage the extension, it involves the table extension
 *
 * Data member
 * - $cn database connection
 * - $variable :
 *    -  id (extension.ex_id)
 *    -  name (extension.ex_name)
 *    - plugin_code (extension.ex_code)
 *    - desc (extension.ex_desc)
 *    - enable (extension.ex_enable)
 *    - filepath (extension.ex_file)
 */
require_once NOALYSS_INCLUDE.'/class_menu_ref_sql.php';
require_once NOALYSS_INCLUDE.'/class_profile_sql.php';
require_once NOALYSS_INCLUDE.'/class_menu_ref.php';
require_once NOALYSS_INCLUDE.'/class_profile_menu.php';

class Extension extends Menu_Ref_sql
{
    public function verify()
    {
        // Verify that the elt we want to add is correct
        if (trim($this->me_code)=="") throw new Exception('Le code ne peut pas être vide');
        if (trim($this->me_menu)=="") throw new Exception('Le nom ne peut pas être vide');
        if (trim($this->me_file)=="") throw new Exception('Chemin incorrect');
        if (file_exists('..'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$this->me_file) == false)
            throw new Exception ('Extension non trouvée, le chemin est-il correct?');
    }
    /*!@brief search a extension, the what is the column (extends_code */
    function search($p_what)
    {
		$this->me_code=strtoupper($p_what);
		if ( $this->load() == -1) return null;
		return 1;
    }
    /*!\brief return 1 if the user given in parameter can access this extension
     * otherwise returns 0
     *\param $p_login the user login
     *\return 1 has access, 0 has no access
     */
    function can_request($p_login)
    {
		$cnt=$this->cn->get_value("select count(*) from menu_ref
										join profile_menu using (me_code)
										join profile_user using (p_id)
										where
										me_code=$1
										and user_name=$2",
								array($this->me_code,$p_login));
		if ( $cnt > 0)        return 1;
		return 0;
    }
    /*!@brief make an array of the available plugin for the current user
     * @return  an array
     *@see ISelect
     */
    static function make_array($cn)
    {
        $sql="select DISTINCT me_code as value, me_menu as label from ".
             " menu_ref join profile_menu using (me_code)
				 join profile_user using (p_id) where ".
             " user_name=$1 and me_type='PL' ORDER BY ME_MENU";
        $a=$cn->get_array($sql,array($_SESSION['g_user']));
        return $a;
    }
    static function check_version($i)
    {
        global $version_noalyss;
        if ( ! isset ($version_noalyss) || $version_noalyss < $i )
        {
            alert('Cette extension ne fonctionne pas sur cette version de NOALYSS'.
                  ' Veuillez mettre votre programme a jour. Version minimum '.$i);
            return;
        }
        Extension::check_plugin_version();
    }
    /**
     * insert into the table profile_menu for the given profile id and depending
     * of the module $p_module
     * @global type $cn
     * @param type $p_id profile.p_id
     * @param type $p_module menu_ref.me_code
     * @throws Exception 10 : profile absent , 20 module absent , 30 No parent menu
     */
    function insert_profile_menu($p_id=1,$p_module='EXT')
    {
        global $cn;
        //profile exists ?
        $profile=new Profile_sql($cn,$p_id);
        if ( $profile->p_id != $p_id) {
                throw new Exception(_('Profil inexistant'),10);
        }
        // Menu exists
        $module=new Menu_Ref($cn,$p_module);
        if ($module->me_code==null) {
                throw new Exception(_('Module inexistant'),20);
        }
        // Dependency
        $dep_id=$cn->get_value('select pm_id from profile_menu 
                where
                p_id=$1
                and me_code = $2 ',array($p_id,$p_module));
        // throw an exception if there is no dependency
        if ($dep_id=="") {
                throw new Exception(_('Pas de menu ').$p_module,30);
        }
        
        $profil_menu=new Profile_Menu($cn);
        $profil_menu->me_code=$this->me_code;
        $profil_menu->me_code_dep=$p_module;
        $profil_menu->p_type_display='S';
        $profil_menu->p_id=$p_id;
        $profil_menu->pm_id_dep=$dep_id;
        
        $cnt=$profil_menu->count(' where p_id=$1 and me_code = $2',array($p_id,$this->me_code));
        if ( $cnt==0) {
            $profil_menu->insert();
        }

        
    }
    function remove_from_profile_menu($p_id)
    {
        global $cn;
       
         $cn->exec_sql('delete from profile_menu  where (me_code = $1 or me_code in (select me_code from menu_ref where me_file=$2)) and p_id=$3',array($this->me_code,$this->me_file,$p_id));
        
    }
    /**
     * Insert a plugin into the given profile, by default always insert into EXT
     * 
     * @param type $p_id profile.p_id
     * @throws Exception if duplicate or error db
     */
	function insert_plugin()
	{
		try
		{
			$this->cn->start();
			$this->verify();
			// check if duplicate
			$this->me_code = strtoupper($this->me_code);
			$count = $this->cn->get_value("select count(*) from menu_ref where me_code=$1", array($this->me_code));
			if ($count != 0)
				throw new Exception("Doublon");
			$this->me_type = 'PL';
			$this->insert();
			$this->cn->commit();
		}
		catch (Exception $exc)
		{
			echo alert($exc->getMessage());
		}
	}
	function update_plugin()
	{
		try
		{
			$this->cn->start();
			$this->verify();
			$this->me_type = 'PL';
			$this->update();
			$this->cn->commit();
		}
		catch (Exception $exc)
		{
			echo alert($exc->getMessage());
		}
	}
	function remove_plugin()
	{
		try
		{
			$this->cn->start();
			$this->delete();
			$this->cn->commit();
		}
		catch (Exception $exc)
		{
			echo alert($exc->getMessage());
		}
	}
	/**
	 *remove all the schema from the plugins
	 * @param Database $p_cn
	 */
	static function clean(Database $p_cn)
	{
		$a_ext=array("tva_belge","amortissement","impdol","coprop","importbank");
		for($i=0;$i<count($a_ext);$i++){
			if ($p_cn->exist_schema($a_ext[$i])) {
				$p_cn->exec_sql("drop schema ".$a_ext[$i]." cascade");
			}
		}
	}
        static function check_plugin_version()
        {
            global $g_user,$version_plugin;
            if ($g_user->Admin() == 1)
            {
                if (SITE_UPDATE_PLUGIN != "")
                {
                    $update = @file_get_contents(SITE_UPDATE_PLUGIN);
                    if ($update > $version_plugin)
                    {
                        echo '<div id="version_plugin_div_id" class="inner_box" style="position:absolute;zindex:2;top:5px;left:37.5%;width:25%">';
                        echo '<p class="notice">';
                        echo "Mise à jour disponible des plugins pour NOALYSS, version actuelle : $update votre version $version_plugin";
                        echo '</p>';
                         echo '<p style="text-align:center">'.
                               '<a id="version_plugin_button" class="button" onclick="$(\'version_plugin_div_id\').remove()">'.
                         _('Fermer').
                         "</a></p>";
                        echo '</div>';
                    }
                }
            }
        }
        /**
         * Check that the xml contains all the needed information to change them into
         * a extension, the exception code is 0 if the element is optional
         * @brief Check XML.
         * @param SimpleXMLElement $xml
         * @throws Exception
         */
        function check_xml(SimpleXMLElement $xml)
        {
            try {
                if ( !isset ($xml->plugin)) throw new Exception(_('Manque plugin'),1);
                $nb_plugin=count($xml->plugin);
            
                for ($i=0;$i<$nb_plugin;$i++)
                {
                    if ( !isset ($xml->plugin[$i]->name)) throw new Exception(_('Manque nom'),1);
                    if ( !isset ($xml->plugin[$i]->description)) throw new Exception(_('Manque description'),0);
                    if ( !isset ($xml->plugin[$i]->code)) throw new Exception(_('Manque code'),1);
                    if ( !isset ($xml->plugin[$i]->author)) throw new Exception(_('Manque auteur'),0);
                    if ( !isset ($xml->plugin[$i]->root)) throw new Exception(_('Manque répertoire racine'),1);
                    if ( !isset ($xml->plugin[$i]->file)) throw new Exception(_('Manque fichier à inclure'),1);
                }
            } catch (Exception $ex) {
                throw $ex;
            }
        }
         /**
         * Parse a XML file to complete an array of extension objects
         * @brief Create extension from XML.
         * @param type $p_file filename
         * @return array of Extension
         */
        static function read_definition($p_file)
        {
            global $cn;
            $dom=new DomDocument('1.0');
            $dom->load($p_file);
            $xml=simplexml_import_dom($dom);
            $nb_plugin=count($xml->plugin);
            $a_extension=array();
            for ($i=0;$i<$nb_plugin;$i++)
            {
                
                $extension=new Extension($cn);
                try {
                        $extension->check_xml($xml);
                } catch (Exception $ex) {
                    echo_warning($e->getMessage());
                    if ( $ex->getCode()==1) {
                        continue;
                    }
                    
                }
                $extension->me_file=trim($xml->plugin[$i]->root).'/'.trim($xml->plugin[$i]->file);
                $extension->me_code=trim($xml->plugin[$i]->code);
                $extension->me_description=(isset ($xml->plugin[$i]->description))?trim($xml->plugin[$i]->description):"";
                $extension->me_description_etendue=(trim($xml->plugin[$i]->author))?trim($xml->plugin[$i]->author):"";
                $extension->me_type='PL';
                $extension->me_menu=trim($xml->plugin[$i]->name);
                $extension->me_parameter='plugin_code='.trim($xml->plugin[$i]->code);
                $a_extension[]=clone $extension;
            }
            return $a_extension;
        }
}

