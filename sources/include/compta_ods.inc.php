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
 *
 *
 * \brief to write directly into the ledgers,the stock and the tables
 * quant_purchase and quant_sold are not changed by this
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once  NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once  NOALYSS_INCLUDE.'/class_acc_reconciliation.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/class_ipopup.php';

global $g_user;

$cn = new Database(dossier::id());

$id_predef = (isset($_REQUEST['p_jrn_predef'])) ? $_REQUEST['p_jrn_predef'] : -1;
$id_ledger = (isset($_REQUEST['p_jrn'])) ? $_REQUEST['p_jrn'] : $id_predef;
$ledger = new Acc_Ledger($cn, $id_ledger);
$first_ledger = $ledger->get_first('ODS');
if ( empty ($first_ledger))
{
	exit('Pas de journal disponible');
}
$ledger->id = ($ledger->id == -1) ? $first_ledger['jrn_def_id'] : $id_ledger;

/**\brief show a form for quick_writing */
$def = -1;
$ledger->with_concerned = true;




if ($g_user->check_jrn($ledger->id) == 'X')
{
	NoAccess();
	exit - 1;
}
$p_msg="";
if (!isset($_POST['summary']) && !isset($_POST['save']))
{
	require('operation_ods_new.inc.php');
	return;
}
elseif (isset($_POST['summary']))
{
	try {
			$ledger->verify($_POST);
			require_once NOALYSS_INCLUDE.'/operation_ods_confirm.inc.php';
	} catch (Exception $e)
	{
		echo alert($e->getMessage());
                $p_msg=$e->getMessage();
		require('operation_ods_new.inc.php');

	}
	return;
}
elseif (isset($_POST['save']))
{
	$array = $_POST;
        echo '<div class="content">';
	try
	{
		$ledger->save($array);
		$jr_id = $cn->get_value('select jr_id from jrn where jr_internal=$1', array($ledger->internal));

		echo '<h2> Op&eacute;ration enregistr&eacute;e  Piece ' . h($ledger->pj) . '</h2>';
		if (strcmp($ledger->pj, $_POST['e_pj']) != 0)
		{
			echo '<h3 class="notice">' . _('Attention numéro pièce existante, elle a du être adaptée') . '</h3>';
		}
		printf('<a class="detail" style="display:inline" href="javascript:modifyOperation(%d,%d)">%s</a><hr>', $jr_id, dossier::id(), $ledger->internal);

		// show feedback
		echo '<div id="jrn_name_div">'; echo '<h2 id="jrn_name"  style="display:inline">' . $ledger->get_name() . '</h2>'; echo '</div>';
		echo $ledger->confirm($_POST, true);
                 // extourne
                if (isset($_POST['reverse_ck']))
                {
                    $p_date=HtmlInput::default_value_post('reverse_date', '');
                    if (isDate($p_date)==$p_date)
                    {
                        // reverse the operation
                        try
                        {
                            $ledger->reverse($p_date);
                            echo '<p>';
                            echo _('Extourné au ').$p_date;
                            echo '</p>';
                        }
                        catch (Exception $e)
                        {
                            echo '<p class="notice">'._('Opération non extournée').
                                $e->getMessage().
                                '</p>';
                                
                            }
                    }
                    else
                    {
                        // warning because date is invalid
                        echo '<p class="notice">'._('Date invalide, opération non extournée').'</p>';
                    }
                }
                
                echo $ledger->button_new_operation();

	}
	catch (Exception $e)
	{
		require('operation_ods_new.inc.php');
		alert($e->getMessage());
                $p_msg=$e->getMessage();
	}
        echo '</div>';
	return;
}
return;

