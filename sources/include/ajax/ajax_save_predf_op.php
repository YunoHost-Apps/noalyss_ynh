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
 * \brief save the new predefined operation 
 * included from ajax_misc
 */
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
if ($g_user->check_module('PREDOP') == 0) exit();
$name=$http->post("opd_name","string", "");
if ( trim($name) != '')
  {
      try
      {
        $od_id=$http->post("od_id", "number");
        $cn->exec_sql('delete from op_predef where od_id=$1',
                      array($od_id));

        $cn->exec_sql("delete from op_predef_detail where od_id=$1",array($od_id));

        $jrn_type=$http->post("jrn_type");
        switch ($jrn_type) {
            case 'ACH':
            $operation=new Pre_op_ach($cn);
            break;
            case 'VEN':
            $operation=new Pre_op_ven($cn);
            break;
            case 'ODS':
            $operation=new Pre_Op_Advanced($cn);
            break;
        default :
            throw new Exception(_('Type de journal invalide'));
        }
        $operation->get_post();
        $operation->save();
        $cn->commit();
          
      }
      catch (Exception $exc)
      {
          error_log($exc->getTraceAsString());
          throw $exc;
      }

  }
?>