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

/**\file
 * \brief contains the class for connecting to a postgresql database
 */
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';

/**\brief
 * This class allow you to connect to the postgresql database, execute sql, retrieve data
 *
 */

class Database
{

    private $db;    /**< database connection */
    private $ret;   /**< return value  */
    private $is_open;                   /*!< true is connected */
    /**\brief constructor
     * \param $p_database_id is the id of the dossier, or the modele following the
     * p_type if = 0 then connect to the repository
     * \param $p_type is 'DOS' (defaut) for dossier or 'MOD'
     */

    function __construct($p_database_id=0, $p_type='dos')
    {
        if (IsNumber($p_database_id)==false||strlen($p_database_id)>5)
            die("-->Dossier invalide [$p_database_id]");
        $noalyss_user=(defined("noalyss_user"))?noalyss_user:phpcompta_user;
        $password=(defined("noalyss_password"))?noalyss_password:phpcompta_password;
        $port=(defined("noalyss_psql_port"))?noalyss_psql_port:phpcompta_psql_port;
        $host=(!defined("noalyss_psql_host") )?'127.0.0.1':noalyss_psql_host;
        if (defined("MULTI")&&MULTI=="0")
        {
            $l_dossier=dbname;
        }
        else
        {

            if ($p_database_id==0)
            { /* connect to the repository */
                $l_dossier=sprintf("%saccount_repository", strtolower(domaine));
            }
            else if ($p_type=='dos')
            { /* connect to a folder (dossier) */
                $l_dossier=sprintf("%sdossier%d", strtolower(domaine), $p_database_id);
            }
            else if ($p_type=='mod')
            { /* connect to a template (modele) */
                $l_dossier=sprintf("%smod%d", strtolower(domaine), $p_database_id);
            }
            else if ($p_type=='template')
            {
                $l_dossier='template1';
            }
            else
            {
                throw new Exception('Connection invalide');
            }
        }

        ob_start();
        $a=pg_connect("dbname=$l_dossier host='$host' user='$noalyss_user'
                      password='$password' port=$port");

        if ($a==false)
        {
            if (DEBUG)
            {
                ob_end_clean();
                echo '<h2 class="error">Impossible de se connecter &agrave; postgreSql !</h2>';
                echo '<p>';
                echo "Vos param&egrave;tres sont incorrectes : <br>";
                echo "<br>";
                echo "base de donn&eacute;e : $l_dossier<br>";
                echo "Domaine : ".domaine."<br>";
                echo "Port $port <br>";
                echo "Utilisateur : $noalyss_user <br>";
                echo '</p>';

                die("Connection impossible : v&eacute;rifiez vos param&egrave;tres de base
                  de donn&eacute;es");
            }
            else
            {
                echo '<h2 class="error">Erreur de connexion !</h2>';
            }
        }
        $this->db=$a;
        $this->is_open=TRUE;
        if ($this->exist_schema('comptaproc'))
        pg_exec($this->db, 'set search_path to public,comptaproc;');
        pg_exec($this->db, 'set DateStyle to ISO, MDY;');
        ob_end_clean();
    }

    public function verify()
    {
        // Verify that the elt we want to add is correct
    }

    function set_encoding($p_charset)
    {
        pg_set_client_encoding($this->db, $p_charset);
    }

    /**
     * \brief send a sql string to the database
     * \param $p_string     sql string
     * \param $p_array array for the SQL string (see pg_query_params)
     * \return the result of the query, a resource or false if an
     * error occured
     */

    function exec_sql($p_string, $p_array=null)
    {
        try
        {
            if ( ! $this->is_open ) throw new Exception(' Database is closed');
            $this->sql=$p_string;
            $this->array=$p_array;

            if ($p_array==null)
            {
                if (!DEBUG)
                    $this->ret=pg_query($this->db, $p_string);
                else
                    $this->ret=@pg_query($this->db, $p_string);
            }
            else
            {
                $a=is_array($p_array);
                if (!is_array($p_array))
                {
                    throw new Exception("Erreur : exec_sql attend un array");
                }
                if (!DEBUG) 
                    $this->ret=pg_query_params($this->db, $p_string, $p_array);
                else
                    $this->ret=@pg_query_params($this->db, $p_string, $p_array);
            }
            if (!$this->ret)
            {
                $str_error=pg_last_error($this->db).pg_result_error($this->ret);
                throw new Exception("  SQL ERROR $p_string ".$str_error, 1);
            }
        }
        catch (Exception $a)
        {
            if (DEBUG)
            {
                print_r($p_string);
                print_r($p_array);
                echo $a->getMessage();
                echo $a->getTrace();
                echo $a->getTraceAsString();
                echo pg_last_error($this->db);
            }
            $this->rollback();
            
            throw ($a);
        }

        return $this->ret;
    }

    /** \brief Count the number of row returned by a sql statement
     *
     * \param $p_sql sql string
     * \param $p_array if not null we use the safer pg_query_params
     */

    function count_sql($p_sql, $p_array=null)
    {
        $r_sql=$this->exec_sql($p_sql, $p_array);
        return pg_NumRows($r_sql);
    }

    /**\brief get the current sequence value
     */

    function get_current_seq($p_seq)
    {
        $Res=$this->get_value("select currval('$p_seq') as seq");
        return $Res;
    }

    /**\brief  get the next sequence value
     */

    function get_next_seq($p_seq)
    {
        $Res=$this->exec_sql("select nextval('$p_seq') as seq");
        $seq=pg_fetch_array($Res, 0);
        return $seq['seq'];
    }

    /**
     * @ brief : start a transaction
     *
     */
    function start()
    {
        $Res=$this->exec_sql("start transaction");
    }

    /**
     * Commit the transaction
     *
     */
    function commit()
    {
        if ( ! $this->is_open) return;
        $Res=$this->exec_sql("commit");
    }

    /**
     * rollback the current transaction
     */
    function rollback()
    {
        if ( ! $this->is_open) return;
        $Res=$this->exec_sql("rollback");
    }

    /**
     * @brief alter the sequence value
     * @param $p_name name of the sequence
     * @param $min the start value of the sequence
     */
    function alter_seq($p_name, $min)
    {
        if ($min<1)
            $min=1;
        $Res=$this->exec_sql("alter sequence $p_name restart $min");
    }

    /**
     * \brief Execute a sql script
     * \param $script script name
     */

    function execute_script($script)
    {

        if (!DEBUG)
            ob_start();
        $hf=fopen($script, 'r');
        if ($hf==false)
        {
            throw new Exception ( 'Ne peut ouvrir '.$script);
        }
        $sql="";
        $flag_function=false;
        while (!feof($hf))
        {
            $buffer=fgets($hf);
            $buffer=str_replace("$", "\$", $buffer);
            print $buffer."<br>";
            // comment are not execute
            if (substr($buffer, 0, 2)=="--")
            {
                //echo "comment $buffer";
                continue;
            }
            // Blank Lines Are Skipped
            If (Strlen($buffer)==0)
            {
                //echo "Blank $buffer";
                Continue;
            }
            if (strpos(strtolower($buffer), "create function")===0)
            {
                echo "found a function";
                $flag_function=true;
                $sql=$buffer;
                continue;
            }
            if (strpos(strtolower($buffer), "create or replace function")===0)
            {
                echo "found a function";
                $flag_function=true;
                $sql=$buffer;
                continue;
            }
            // No semi colon -> multiline command
            if ($flag_function==false&&strpos($buffer, ';')==false)
            {
                $sql.=$buffer;
                continue;
            }
            if ($flag_function)
            {
                if (strpos(strtolower($buffer), "language plpgsql")===false&&
                        strpos(strtolower($buffer), "language 'plpgsql'")===false)
                {
                    $sql.=$buffer;
                    continue;
                }
            }
            else
            {
                // cut the semi colon
                $buffer=str_replace(';', '', $buffer);
            }
            $sql.=$buffer;
            if ($this->exec_sql($sql)==false)
            {
                $this->rollback();
                if (!DEBUG)
                    ob_end_clean();
                print "ERROR : $sql";
                throw new Exception("ERROR : $sql");
            }
            $sql="";
            $flag_function=false;
            print "<hr>";
        } // while (feof)
        fclose($hf);
        if (!DEBUG)
            ob_end_clean();
    }

    /**
     * \brief Get version of a database, the content of the
     *        table version
     *
     * \return version number
     *
     */

    function get_version()
    {
        $Res=$this->get_value("select val from version");
        return $Res;
    }

    /**
     * @brief fetch the $p_indice array from the last query
     * @param $p_indice index
     *
     */
    function fetch($p_indice)
    {
        if ($this->ret==false)
            throw new Exception('this->ret is empty');
        return pg_fetch_array($this->ret, $p_indice);
    }

    /**
     * 
     * @brief return the number of rows found by the last query, or the number
     * of rows from $p_ret
     * @param $p_ret is the result of a query, the default value is null, in that case
     * it is related to the last query
     * @note synomym for count()
     */

    function size($p_ret=null)
    {
        if ($p_ret==null)
            return pg_NumRows($this->ret);
        else
            return pg_NumRows($p_ret);
    }

    /**
     * @brief       synomym for size() 
     */

    function count($p_ret=null)
    {
        return $this->size($p_ret);
    }

    /**
     * \brief loop to apply all the path to a folder or
     *         a template
     * \param $p_name database name
     * \param $from_setup == 1 if called from setup.php
     *
     */

    function apply_patch($p_name, $from_setup=1)
    {
        if ( ! $this->exist_table('version')) {
            echo _('Base de donnée vide');
            return;
        }
        $MaxVersion=DBVERSION-1;
        $succeed="<span style=\"font-size:18px;color:green\">&#x2713;</span>";
        echo '<ul style="list-type-style:square">';
        $add=($from_setup==0)?'admin/':'';
        for ($i=4; $i<=$MaxVersion; $i++)
        {
            $to=$i+1;

            if ($this->get_version()<=$i)
            {
                if ($this->get_version()==97)
                {
                    if ($this->exist_schema("amortissement"))
                    {
                        $this->exec_sql('ALTER TABLE amortissement.amortissement_histo
							ADD CONSTRAINT internal_fk FOREIGN KEY (jr_internal) REFERENCES jrn (jr_internal)
							ON UPDATE CASCADE ON DELETE SET NULL');
                    }
                }
                echo "<li>Patching ".$p_name.
                " from the version ".$this->get_version()." to $to ";

                $this->execute_script($add.'sql/patch/upgrade'.$i.'.sql');
                echo $succeed;

                if (!DEBUG)
                    ob_start();
                // specific for version 4
                if ($i==4)
                {
                    $sql="select jrn_def_id from jrn_def ";
                    $Res=$this->exec_sql($sql);
                    $Max=$this->size();
                    for ($seq=0; $seq<$Max; $seq++)
                    {
                        $row=pg_fetch_array($Res, $seq);
                        $sql=sprintf("create sequence s_jrn_%d", $row['jrn_def_id']);
                        $this->exec_sql($sql);
                    }
                }
                // specific to version 7
                if ($i==7)
                {
                    // now we use sequence instead of computing a max
                    //
                    $Res2=$this->exec_sql('select coalesce(max(jr_grpt_id),1) as l from jrn');
                    $Max2=pg_NumRows($Res2);
                    if ($Max2==1)
                    {
                        $Row=pg_fetch_array($Res2, 0);
                        var_dump($Row);
                        $M=$Row['l'];
                        $this->exec_sql("select setval('s_grpt',$M,true)");
                    }
                }
                // specific to version 17
                if ($i==17)
                {
                    $this->execute_script($add.'sql/patch/upgrade17.sql');
                    $max=$this->get_value('select last_value from s_jnt_fic_att_value');
                    $this->alter_seq($p_cn, 's_jnt_fic_att_value', $max+1);
                } // version
                // reset sequence in the modele
                //--
                if ($i==30&&$p_name=="mod")
                {
                    $a_seq=array('s_jrn', 's_jrn_op', 's_centralized',
                        's_stock_goods', 'c_order', 's_central');
                    foreach ($a_seq as $seq)
                    {
                        $sql=sprintf("select setval('%s',1,false)", $seq);
                        $Res=$this->exec_sql($sql);
                    }
                    $sql="select jrn_def_id from jrn_def ";
                    $Res=$this->exec_sql($sql);
                    $Max=pg_NumRows($Res);
                    for ($seq=0; $seq<$Max; $seq++)
                    {
                        $row=pg_fetch_array($Res, $seq);
                        $sql=sprintf("select setval('s_jrn_%d',1,false)", $row['jrn_def_id']);
                        $this->exec_sql($sql);
                    }
                }
                if ($i==36)
                {
                    /* check the country and apply the path */
                    $res=$this->exec_sql("select pr_value from parameter where pr_id='MY_COUNTRY'");
                    $country=pg_fetch_result($res, 0, 0);
                    $this->execute_script($add."sql/patch/upgrade36.".$country.".sql");
                    $this->exec_sql('update tmp_pcmn set pcm_type=find_pcm_type(pcm_val)');
                }
                if ($i==59)
                {
                    $res=$this->exec_sql("select pr_value from parameter where pr_id='MY_COUNTRY'");
                    $country=pg_fetch_result($res, 0, 0);
                    if ($country=='BE')
                        $this->exec_sql("insert into parm_code values ('SUPPLIER',440,'Poste par défaut pour les fournisseurs')");
                    if ($country=='FR')
                        $this->exec_sql("insert into parm_code values ('SUPPLIER',400,'Poste par défaut pour les fournisseurs')");
                }
                if ($i==61)
                {
                    $country=$this->get_value("select pr_value from parameter where pr_id='MY_COUNTRY'");
                    $this->execute_script($add."sql/patch/upgrade61.".$country.".sql");
                }

                if (!DEBUG)
                    ob_end_clean();
            }
        }
        echo '</ul>';
    }

    /**
     * 
     * \brief return the value of the sql, the sql will return only one value
     *        with the value
     * \param $p_sql the sql stmt example :select s_value from
      document_state where s_id=2
     * \param $p_array if array is not null we use the ExecSqlParm (safer)
     * \see exec_sql
     * \note print a warning if several value are found, if only the first value is needed
     * consider using a LIMIT clause
     * \return only the first value or an empty string if nothing is found
     */

    function get_value($p_sql, $p_array=null)
    {
        $this->ret=$this->exec_sql($p_sql, $p_array);
        $r=pg_NumRows($this->ret);
        if ($r==0)
            return "";
        if ($r>1)
        {
            $array=pg_fetch_all($this->ret);
            throw new Exception("Attention $p_sql retourne ".pg_NumRows($this->ret)."  valeurs ".
            var_export($p_array, true)." values=".var_export($array, true));
        }
        $r=pg_fetch_row($this->ret, 0);
        return $r[0];
    }
    /**
     * @brief return the number of rows affected by the previous query
     */
    function get_affected()
    {
        return Database::num_row($this->ret);
    }

    /**
     * \brief  purpose return the result of a sql statment
     * in a array
     * \param $p_sql sql query
     * \param $p_array if not null we use ExecSqlParam
     * \return an empty array if nothing is found
     */

    function get_array($p_sql, $p_array=null)
    {
        $r=$this->exec_sql($p_sql, $p_array);

        if (($Max=pg_NumRows($r))==0)
            return array();
        $array=pg_fetch_all($r);
        return $array;
    }

    function create_sequence($p_name, $min=1)
    {
        if ($min<1)
            $min=1;
        $sql="create sequence ".$p_name." minvalue $min";
        $this->exec_sql($sql);
    }

    /**
     * \brief test if a sequence exist */
    /* \return true if the seq. exist otherwise false
     */

    function exist_sequence($p_name)
    {
        $r=$this->count_sql("select relname from pg_class where relname=lower($1)", array($p_name));
        if ($r==0)
            return false;
        return true;
    }

    /**\brief test if a table exist
     * \param $p_name table name
     * \param  $schema name of the schema default public
     * \return true if a table exist otherwise false
     */

    function exist_table($p_name, $p_schema='public')
    {
        $r=$this->count_sql("select table_name from information_schema.tables where table_schema=$1 and table_name=lower($2)", array($p_schema, $p_name));
        if ($r==0)
            return false;
        return true;
    }

    /**
     * Check if a column exists in a table
     * @param $col : column name
     * @param $table :table name
     * @param $schema :schema name, default public
     * @return true or false
     */
    function exist_column($col, $table, $schema)
    {
        $r=$this->get_value('select count(*) from information_schema.columns where table_name=lower($1) and column_name=lower($2) and table_schema=lower($3)', array($col, $table, $schema));
        if ($r>0)
            return true;
        return false;
    }

    /**
     * return the name of the database with the domain name
     * @param $p_id of the folder WITHOUT the domain name
     * @param $p_type dos for folder mod for template
     * @return formatted name
     */
    function format_name($p_id, $p_type)
    {
        switch ($p_type)
        {
            case 'dos':
                $sys_name=sprintf("%sdossier%d", strtolower(domaine), $p_id);
                break;
            case 'mod':
                $sys_name=sprintf("%smod%d", strtolower(domaine), $p_id);
                break;
            default:
                echo_error(__FILE__." format_name invalid type ".$p_type, __LINE__);
                throw new Exception(__FILE__." format_name invalid type ".$p_type. __LINE__);
        }
        return $sys_name;
    }

    /**
     * Count the database name in a system view
     * @param $p_name string database name
     * @return number of database found (normally 0 or 1)
     */
    function exist_database($p_name)
    {
        $database_exist=$this->get_value('select count(*)
                from pg_catalog.pg_database where datname = lower($1)', array($p_name));
        return $database_exist;
    }

    /**
     * @brief check if the large object exists
     * @param $p_oid of the large object
     * @return return true if the large obj exist or false if not
     */
    function exist_blob($p_oid)
    {
        $r=$this->get_value('select count(loid) from pg_largeobject where loid=$1'
                , array($p_oid));
        if ($r>0)
            return true;
        else
            return false;
    }

    /*
     * !\brief test if a view exist
     * \return true if the view. exist otherwise false
     */

    function exist_view($p_name)
    {
        $r=$this->count_sql("select viewname from pg_views where viewname=lower($1)", array($p_name));
        if ($r==0)
            return false;
        return true;
    }

    /*
     * !\brief test if a schema exists
     * \return true if the schemas exists otherwise false
     */

    function exist_schema($p_name)
    {
        $r=$this->count_sql("select nspname from pg_namespace where nspname=lower($1)", array($p_name));
        if ($r==0)
            return false;
        return true;
    }

    /**
     * \brief create a string containing the value separated by comma
     * for use in a SQL in statement
     * \return the string or empty if nothing is found
     * \see fid_card.php
     */

    function make_list($sql, $p_array=null)
    {
        if ($p_array==null)
        {
            $aArray=$this->get_array($sql);
        }
        else
        {
            $aArray=$this->get_array($sql, $p_array);
        }
        if (empty($aArray))
            return "";
        $aIdx=array_keys($aArray[0]);
        $idx=$aIdx[0];
        $ret="";
        $f="";
        for ($i=0; $i<count($aArray); $i++)
        {
            $row=$aArray[$i];
            $ret.=$f.$aArray[$i][$idx];
            $f=',';
        }
        $ret=trim($ret, ',');
        return $ret;
    }

    /**
     * \brief make a array with the sql.
     *
     * \param $p_sql  sql statement, only the first two column will be returned in
     *  an array. The first col. is the label and the second the value
     *  \param $p_null if the array start with a null value
     *  \param $p_array is the array with the bind value
     * \note this function is used with ISelect when it is needed to have a list of
     * options.
     * \return: a double array like
      \verbatim
      Array
      (
        [0] => Array
                (
                [value] => 1
                [label] => Marchandise A
               )

      [1] => Array
            (
            [value] => 2
            [label] => Marchandise B
            )

      [2] => Array
            (
            [value] => 3
            [label] => Marchandise C
            )
      )
      \endverbatim
     * \see ISelect
     */

    function make_array($p_sql, $p_null=0,$p_array=null)
    {
        $a=$this->exec_sql($p_sql,$p_array);
        $max=pg_NumRows($a);
        if ($max==0&&$p_null==0)
            return null;
        for ($i=0; $i<$max; $i++)
        {
            $row=pg_fetch_row($a);
            $r[$i]['value']=$row[0];
            $r[$i]['label']=h($row[1]);
        }
        // add a blank item ?
        if ($p_null==1)
        {
            for ($i=$max; $i!=0; $i--)
            {
                $r[$i]['value']=$r[$i-1]['value'];
                $r[$i]['label']=h($r[$i-1]['label']);
            }
            $r[0]['value']=-1;
            $r[0]['label']=" ";
        } //   if ( $p_null == 1 )

        return $r;
    }

    /**
     * \brief Save a "piece justificative"
     *
     * \param $seq jr_grpt_id
     * \return $oid of the lob file if success
     *         null if a error occurs
     *
     */

    function save_upload_document($seq)
    {
        /* there is
          no file to
          upload */
        if ($_FILES["pj"]["error"]==UPLOAD_ERR_NO_FILE)
        {
            return;
        }

        $new_name=tempnam($_ENV['TMP'], 'pj');
        if ($_FILES["pj"]["error"]>0)
        {
            print_r($_FILES);
            echo_error(__FILE__.":".__LINE__."Error: ".$_FILES["pj"]["error"]);
        }
        if (strlen($_FILES['pj']['tmp_name'])!=0)
        {
            if (move_uploaded_file($_FILES['pj']['tmp_name'], $new_name))
            {
                // echo "Image saved";
                $oid=pg_lo_import($this->db, $new_name);
                if ($oid==false)
                {
                    echo_error('postgres.php', __LINE__, "cannot upload document");
                    $this->rollback();
                    return;
                }
                // Remove old document
                $ret=$this->exec_sql("select jr_pj from jrn where jr_grpt_id=$seq");
                if (pg_num_rows($ret)!=0)
                {
                    $r=pg_fetch_array($ret, 0);
                    $old_oid=$r['jr_pj'];
                    if (strlen($old_oid)!=0)
                        pg_lo_unlink($cn, $old_oid);
                }
                // Load new document
               $this->exec_sql("update jrn set jr_pj=$1 , jr_pj_name=$2,
                                        jr_pj_type=$3  where jr_grpt_id=$4",
                                        array($oid,$_FILES['pj']['name'] ,$_FILES['pj']['type'],$seq));
                return $oid;
            }
            else
            {
                echo "<H1>Error</H1>";
                $this->rollback();
            }
        }
        return 0;
    }

    /**\brief wrapper for the function pg_NumRows
     * \param $ret is the result of a exec_sql
     * \return number of line affected
     */

    static function num_row($ret)
    {
        return pg_NumRows($ret);
    }

    /**\brief wrapper for the function pg_fetch_array
     * \param $ret is the result of a pg_exec
     * \param $p_indice is the index
     * \return $array of column
     */

    static function fetch_array($ret, $p_indice=0)
    {
        return pg_fetch_array($ret, $p_indice);
    }

    /**\brief wrapper for the function pg_fetch_all
     * \param $ret is the result of pg_exec (exec_sql)
     * \return double array (row x col )
     */

    static function fetch_all($ret)
    {
        return pg_fetch_all($ret);
    }

    /**\brief wrapper for the function pg_fetch_all
     * \param $ret is the result of pg_exec (exec_sql)
     * \param $p_row is the indice of the row
     * \param $p_col is the indice of the col
     * \return a string or an integer
     */

    static function fetch_result($ret, $p_row=0, $p_col=0)
    {
        return pg_fetch_result($ret, $p_row, $p_col);
    }

    /**\brief wrapper for the function pg_fetch_row
     * \param $ret is the result of pg_exec (exec_sql)
     * \param $p_row is the indice of the row
     * \return an array indexed from 0
     */

    static function fetch_row($ret, $p_row)
    {
        return pg_fetch_row($ret, $p_row);
    }

    /**\brief wrapper for the function pg_lo_unlink
     * \param $p_oid is the of oid
     * \return return the result of the operation
     */

    function lo_unlink($p_oid)
    {
        return pg_lo_unlink($this->db, $p_oid);
    }

    /**\brief wrapper for the function pg_prepare
     * \param $p_string string name for pg_prepare function
     * \param $p_sql  is the sql to prepare
     * \return return the result of the operation
     */

    function prepare($p_string, $p_sql)
    {
        return pg_prepare($this->db, $p_string, $p_sql);
    }

    /**\brief wrapper for the function pg_execute
     * \param $p_string string name of the stmt given in pg_prepare function
     * \param $p_array contains the variables
     * \note set this->ret to the return of pg_execute
     * \return return the result of the operation,
     */

    function execute($p_string, $p_array)
    {
        $this->ret=pg_execute($this->db, $p_string, $p_array);
        return $this->ret;
    }

    /**\brief wrapper for the function pg_lo_export
     * \param $p_oid is the oid of the log
     * \param $tmp  is the file
     * \return result of the operation
     */

    function lo_export($p_oid, $tmp)
    {
        return pg_lo_export($this->db, $p_oid, $tmp);
    }

    /**\brief wrapper for the function pg_lo_export
     * \param $p_oid is the oid of the log
     * \param $tmp  is the file
     * \return result of the operation
     */

    function lo_import($p_oid)
    {
        return pg_lo_import($this->db, $p_oid);
    }

    /**\brief wrapper for the function pg_escape_string
     * \param $p_string is the string to escape
     * \return escaped string
     */

    static function escape_string($p_string)
    {
        return pg_escape_string($p_string);
    }

    /**\brief wrapper for the function pg_close
     */

    function close()
    {
        if ( $this->is_open ) pg_close($this->db);
        $this->is_open=FALSE;
    }

    /**\brief
     * \param
     * \return
     * \note
     * \see
     */

    function __toString()
    {
        return "database ";
    }

    static function test_me()
    {
        
    }

    function status()
    {
        return pg_transaction_status($this->db);
    }

    /**
     * with the handle of a successull query, echo each row into CSV and
     * send it directly
     * @param type $ret handle to a query
     * @param type $aheader  double array, each item of the array contains
     * a key type (num) and a key title
     */
    function query_to_csv($ret, $aheader)
    {
        $seq="";
        for ($i=0; $i<count($aheader); $i++)
        {
            echo $seq.'"'.$aheader[$i]['title'].'"';
            $seq=";";
        }
        printf("\n\r");
        // fetch all the rows
        for ($i=0; $i<Database::num_row($ret); $i++)
        {
            $row=Database::fetch_array($ret, $i);
            $sep2="";
            // for each rows, for each value
            for ($e=0; $e<count($row)/2; $e++)
            {
                switch ($aheader[$e]['type'])
                {
                    case 'num':
                        echo $sep2.nb($row[$e]);
                        break;
                    default:
                        echo $sep2.'"'.$row[$e].'"';
                }
                $sep2=";";
            }
            printf("\n\r");
        }
    }

}

/* test::test_me(); */
