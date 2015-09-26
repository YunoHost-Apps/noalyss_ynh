<?php

/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief delete insert or update data from account_update (need right CFGPCMN)
 * called from ajax_misc.php
 * @param array
 * @code
 *   'op' => string 'account_update' (length=14)
  'gDossier' => string '44' (length=2)
  'action' => string 'update' (length=6)
  'p_oldu' => string '4124' (length=4)
  'p_valu' => string '4124' (length=4)
  'p_libu' => string 'Impôt belge sur le résultat' (length=29)
  'p_parentu' => string '412' (length=3)
  'acc_delete' => string '0' (length=1)
  'p_typeu' => string '0' (length=1)
 * @endcode
 */
if ($g_user->check_module('CFGPCMN') == 0)
			exit();

$var=array('action', 'p_oldu', 'p_valu', 'p_libu', 'p_parentu', 'acc_delete', 'p_typeu');
for ($i=0; $i<count($var); $i++)
{
    $name=$var[$i];
    if (!isset($$name))
        throw new Exception($name." is not set");
}
$ctl='ok';
extract($_GET);
//----------------------------------------------------------------------
// Modification
//----------------------------------------------------------------------
$message=_("opération réussie");

if ($action=="update" &&$acc_delete==0)
{
    // Check if the data are correct
    try
    {
        $p_val=trim($p_valu);
        $p_lib=trim($p_libu);
        $p_parent=trim($p_parentu);
        $old_line=trim($p_oldu);
        $p_type=htmlentities($p_typeu);
        $acc=new Acc_Account($cn);
        $acc->set_parameter('libelle', $p_lib);
        $acc->set_parameter('value', $p_val);
        $acc->set_parameter('parent', $p_parent);
        $acc->set_parameter('type', $p_type);
        $acc->check();
    }
    catch (Exception $e)
    {
        $message=_("Valeurs invalides, pas de changement")." \n ".
                $e->getMessage();
        $ctl='nok';
    }
    if (strlen($p_val)!=0&&strlen($p_lib)!=0&&strlen($old_line)!=0)
    {
        if (strlen($p_val)==1)
        {
            $p_parent=0;
        }
        else
        {
            if (strlen($p_parent)==0)
            {
                $p_parent=substr($p_val, 0, strlen($p_val)-1);
            }
        }
        /* Parent existe */
        $Ret=$cn->exec_sql("select pcm_val from tmp_pcmn where pcm_val=$1", array($p_parent));
        if (($p_parent!=0&&Database::num_row($Ret)==0)||$p_parent==$old_line)
        {
            $message=_("Ne peut pas modifier; aucun poste parent");
            $ctl='nok';
        }
        else
        {
            try
            {
                $acc->update($old_line);
            }
            catch (Exception $e)
            {
                $message=$e->getMessage();
                $ctl='nok';
            }
        }
    }
    else
    {
        $message=_('Update Valeurs invalides');
        $ctl='nok';
    }
}
//-----------------------------------------------------
/* Ajout d'une ligne */
if ($action=="new")
{
    $p_val=trim($p_valu);
    $p_parent=trim($p_parentu);

    if (isset($p_valu)&&isset($p_libu))
    {
        $p_val=trim($p_valu);

        if (strlen($p_valu)!=0&&strlen($p_libu)!=0)
        {
            if (strlen($p_valu)==1)
            {
                $p_parentu=0;
            }
            else
            {
                if (strlen(trim($p_parentu))==0&&
                        (string) $p_parentu!=(string) (int) $p_parentu)
                {
                    $p_parentu=substr($p_val, 0, strlen($p_valu)-1);
                }
            }
            /* Parent existe */
            $Ret=$cn->exec_sql("select pcm_val from tmp_pcmn where pcm_val=$1", array($p_parentu));
            if ($p_parent!=0&&Database::num_row($Ret)==0)
            {
                $message=_(" Ne peut pas modifier; aucun poste parent");
                $ctl='nok';
            }
            else
            {
                // Check if the account already exists

                $Count=$cn->get_value("select count(*) from tmp_pcmn where pcm_val=$1", array($p_val));
                if ($Count!=0)
                {
                    // Alert message account already exists
                    $message=_("Ce poste existe déjà ");
                    $ctl='nok';
                }
                else
                {
                    $Ret=$cn->exec_sql("insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent,pcm_type) values ($1,$2,$3,$4)", array($p_val, $p_libu, $p_parent, $p_typeu));
                }
            }
        }
        else
        {
            $message=_("Valeurs invalides ");
            $ctl='nok';
        }
    }
}

//-----------------------------------------------------
// Action == remove a line
if ($action=="update"&&$acc_delete==1)
{
    /* Ligne a enfant */
    $R=$cn->exec_sql("select pcm_val from tmp_pcmn where pcm_val_parent=$1", array($p_valu));
    if (Database::num_row($R)!=0)
    {
        $message=_("Ne peut pas effacer le poste: d'autres postes en dépendent");
        $ctl='nok';
    }
    else
    {
        /* Vérifier que le poste n'est pas utilisé qq part dans les journaux */
        $Res=$cn->exec_sql("select * from jrnx where j_poste=$1", array($p_valu));
        if (Database::num_row($Res)!=0)
        {
            $message=_("Ne peut pas effacer le poste: il est utilisé dans les journaux");
            $ctl='nok';
        }
        else
        {
            $Del=$cn->exec_sql("delete from tmp_pcmn where pcm_val=$1", array($p_valu));
        } // if Database::num_row
    } // if Database::num_row
} //$action == del
$message=escape_xml($message);
 if ( ! headers_sent()) {     header('Content-type: text/xml; charset=UTF-8');} else { echo "HTML".unescape_xml($html);}
 
 echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$message</code>
</data>
EOF;
?>
