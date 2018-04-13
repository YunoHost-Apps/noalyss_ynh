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
/*! \file
 * \brief concerns the management of the "Plan Comptable"
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once  NOALYSS_INCLUDE.'/class/acc_account.class.php';
require_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/lib/function_javascript.php';
$http=new HttpInput();
$gDossier=dossier::id();

require_once NOALYSS_INCLUDE.'/lib/database.class.php';

/* Admin. Dossier */
$cn=Dossier::connect();

require_once  NOALYSS_INCLUDE.'/class/user.class.php';

require_once  NOALYSS_INCLUDE.'/lib/user_menu.php';
echo '<div id="acc_update" class="inner_box" style="display:none;position:absolute;text-align:left;width:auto;z-index:1"></div>';

/* Store the p_start parameter */

$g_start=$http->get('p_start',"number",1);
?>
<a  id="top"></a>

<div class="content">
<?php
    menu_acc_plan($g_start);
?>
</div>

<DIV CLASS="myfieldset" style="width:auto">
<?php
require_once NOALYSS_INCLUDE."/class/acc_plan_mtable.class.php";
require_once NOALYSS_INCLUDE."/lib/manage_table_sql.class.php";
/**
 * @file
 * @brief Test the Acc_Plan_MTable
 */
$obj=new Acc_Plan_SQL($cn);
/**
 * Test $obj
 */

$mtable=new Acc_Plan_MTable($obj);
$mtable->add_json_param("op", "accounting");
$obj->set_limit_fiche_qcode(5);
$mtable->set_callback("ajax_misc.php");
$mtable->create_js_script();

echo $mtable->display_table(" where pcm_val::text like '{$g_start}%' order by pcm_val::text ");
/* it will override the classic onscroll (see scripts.js)
 * @see scripts.js
*/

    ?>
    <div id="go_up" class="inner_box" style="padding:0px;left:auto;width:250px;height: 100px;display:none;position:fixed;bottom:5px;right:20px">
        <div style="margin:3%;padding:3%">
            <a class="icon" href="#up_top" >&#xe81a;</a><a href="javascript:show_calc()" class="icon">&#xf1ec;</a>
            <input type="button" id="pcmn_update_add_bt3"  value="<?php echo _('Ajout poste comptable'); ?>">
        </div>
    </div>
 </div>
 <script>
     window.onscroll=function () {
         if ( document.viewport.getScrollOffsets().top> 0) {
             if ($('go_up').visible() == false) {
                $('go_up').setOpacity(0.8); 
                $('go_up').show();
            }
        } else {
            $('go_up').hide();
        }
     }
     $('pcmn_update_add_bt3').onclick=function() {
         <?php printf("%s.input(-1,'%s')",$mtable->get_object_name(),$mtable->get_object_name());?>
     }
</script>
<?php
html_page_stop();
?>
