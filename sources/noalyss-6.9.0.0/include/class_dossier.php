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
 * \brief the class for the dossier, everywhere we need to know to
 * which folder we are connected, because we can't use $_SESSION, we
 * need to pass the dossier_id via a _GET or a POST variable
 */

/*! \brief manage the current dossier, everywhere we need to know to
 * which folder we are connected, because we can't use $_SESSION, we
 * need to pass the dossier_id via a _GET or a POST variable
 *  private static $variable=array("id"=>"dos_id",
				 "name"=>"dos_name",
				 "desc"=>"dos_description");
 *
 */
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';

class Dossier
{
    private static $variable=array("id"=>"dos_id",
                                   "name"=>"dos_name",
                                   "desc"=>"dos_description");
    function __construct($p_id)
    {
        $this->cn=new Database(); 	// Connect to the repository
        $this->dos_id=$p_id;
    }
    /*!\brief return the $_REQUEST['gDossier'] after a check */
    static function id()
    {
        self::check();
        return $_REQUEST['gDossier'];
    }

    /**
     * @brief Show the folder where user have access. 
     * @param  p_type string   
       - A for all dossiers 
       - R for accessible folders
       - X forbidden folders
     * @param p_login is the user name
     * @param p_text is a part of the name where are looking for
     * @return     nothing
     *
     */
    static function show_dossier($p_type,$p_login="",$p_text="",$limit=0)
    {
        $cn=new Database();
        $str_limit=($limit==0)?'':' limit '.$limit;
        if ( $p_type == "A")
        {
            $l_sql="select *, 'W' as priv_priv from ac_dossier where dos_name ~* $2 or dos_description ~* $2 ORDER BY dos_name $str_limit  ";
            $a_row=$cn->get_array($l_sql,$p_text);
            return $a_row;
        }
        else if ($p_type == "R")
        {
            $l_sql="select * from jnt_use_dos
                   natural join ac_dossier
                   natural join ac_users
                   where
                   use_login=$1
                   and ( dos_name ~* $2 or dos_description ~* $2)
                   
                   order by dos_name 
                   $str_limit
                   ";
            
            $a_row=$cn->get_array($l_sql,array($p_login,$p_text));
            return $a_row;

        } 
        else  if ($p_type == 'X')
        {
            $l_sql=' select * from ac_dossier where dos_id not in 
                  (select dos_id from jnt_use_dos where use_id=$1)
                  and ( dos_name ~* $2 or dos_description ~* $2)
                  order by dos_name '.$str_limit;
            $a_row=$cn->get_array($l_sql,array($p_login,$p_text));
            return $a_row;

        }
        else
        {
            throw new Exception (_("Erreur paramètre"));
        } 
        
       
    }
    /**
     * Count the number of folder in the repository
     * @return integer
     */
    function count() 
    {
        $nb_folder=$this->cn->get_value('select count(*) from ac_dossier');
        return $nb_folder;
    }
    /*!
     * \brief Return all the users
     * as an array
     */
    function get_user_folder($sql="")
    {

        $sql="
            select
                use_id,
                use_first_name,
                use_name,
                use_login,
                use_active,
                use_admin,
                ag_dossier
            from
            ac_users  as ac
            left join    (select array_to_string(array_agg(dos_name),',') as ag_dossier,jt.use_id as jt_use_id
                        from ac_dossier as ds
                        join  jnt_use_dos as jt on (jt.dos_id=ds.dos_id)
                        group by jt.use_id) as dossier_name on (jt_use_id=ac.use_id)
            where
            use_login!='phpcompta'
            $sql
            ";

        $res=$this->cn->get_array($sql);
        return $res;
        }

    /*!\brief check if gDossier is set */
    static function check()
    {
        if ( ! isset ($_REQUEST['gDossier']) )
        {
            echo_error ('Dossier inconnu ');
            exit('Dossier invalide ');
        }
        $id=$_REQUEST['gDossier'];
        if ( is_numeric ($id) == 0 ||
                strlen($id)> 6 ||
                $id > 999999)
            exit('gDossier Invalide : '.$id);

    }
    /*!\brief return a string to put to gDossier into a GET */
    static function get()
    {
        self::check();
        return "gDossier=".$_REQUEST['gDossier'];

    }

    /*!\brief return a string to set gDossier into a FORM */
    static function hidden()
    {
        self::check();
        return '<input type="hidden" id="gDossier" name="gDossier" value="'.$_REQUEST['gDossier'].'">';
    }
    /*!\brief retrieve the name of the current dossier */
    static function name($id=0)
    {
        self::check();

        $cn=new Database();
        $id=($id==0)?$_REQUEST['gDossier']:$id;
        $name=$cn->get_value("select dos_name from ac_dossier where dos_id=$1",array($_REQUEST['gDossier']));
        return $name;
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
    public function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            $this->$idx=$p_value;
        }
        else
           throw new Exception("Attribut inexistant $p_string");


    }
    public function get_info()
    {
        return var_export(self::$variable,true);
    }

    public function save()
    {
        $this->update();
    }

    public function update()
    {
        if ( strlen(trim($this->dos_name))== 0 ) return;

        if ( $this->cn->get_value("select count(*) from ac_dossier where dos_name=$1 and dos_id<>$2",
                                  array($this->dos_name,$this->dos_id)) !=0 )
            return ;

        $sql="update ac_dossier set dos_name=$1,dos_description=$2 ".
             " where dos_id = $3";
        $res=$this->cn->exec_sql(
                 $sql,
                 array(trim($this->dos_name),
                       trim($this->dos_description),
                       $this->dos_id)
             );
    }

    public function load()
    {

        $sql="select dos_name,dos_description from ac_dossier where dos_id=$1";

        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->dos_id)
             );

        if ( Database::num_row($res) == 0 ) return;
        $row=Database::fetch_array($res,0);
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }

    }

    static function get_version($p_cn)
	{
		return $p_cn->get_value('select val from version');
	}

	static function connect()
	{
		$id = Dossier::id();
		$cn = new Database($id);
		return $cn;
	}
	/**
	 *connect to folder and give to admin. the profile Admin(builtin)
	 * @param int $p_id dossier::id()
	 */
	static function synchro_admin($p_id)
	{
		// connect to target
		$cn=new Database($p_id);

		if (! $cn->exist_table("profile_menu"))
		{
			echo_warning("Dossier invalide");
			return;
		}
		// connect to repo
		$repo=new Database();

		$a_admin=$repo->get_array("select use_login from ac_users where
			use_admin=1 and use_active=1");
		try
		{
			/**
			 * synchro global
			 */
			$cn->start();
			for ($i=0;$i<count($a_admin);$i++)
			{
				$exist=$cn->get_value("select p_id from profile_user
					where user_name=$1",array($a_admin[$i]['use_login']));
				if ( $exist == "")
				{
					$cn->exec_sql("insert into profile_user(user_name,p_id) values($1,1)",
							array($a_admin[$i]['use_login']));
				}

			}
			$cn->commit();
		} catch(Exception $e)
		{
			echo_warning($e->getMessage());
			$cn->rollback();
		}
	}
}
