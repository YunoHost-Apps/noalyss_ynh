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
require_once  NOALYSS_INCLUDE.'/class_acc_account.php';
require_once  NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';

$gDossier=dossier::id();

require_once NOALYSS_INCLUDE.'/class_database.php';

/* Admin. Dossier */
$cn=new Database($gDossier);

require_once  NOALYSS_INCLUDE.'/class_user.php';

require_once  NOALYSS_INCLUDE.'/user_menu.php';
echo '<div id="acc_update" class="inner_box" style="display:none;position:absolute;text-align:left;z-index:1"></div>';

/* Store the p_start parameter */

$g_start=HtmlInput::default_value_get('p_start',1);
?>
<a  id="top"></a>

<div class="content">
<?php
    menu_acc_plan($g_start);
?>
</div>

<DIV CLASS="myfieldset" style="width:auto">
<?php
$Ret=$cn->exec_sql("select pcm_val,pcm_lib,pcm_val_parent,pcm_type,array_to_string(array_agg(j_qcode) , ',') as acode
	from tmp_pcmn left join vw_poste_qcode on (j_poste=pcm_val) where substr(pcm_val::text,1,1)='".$g_start."'".
		"  group by pcm_val,pcm_lib,pcm_val_parent, pcm_type  order by pcm_val::text");
$MaxRow=Database::num_row($Ret);

?>
<span style="display:inline;margin: 15px 15px 15px 15px">
<input type="button" id="pcmn_update_add_bt" class="smallbutton" value="<?php echo _('Ajout poste comptable'); ?>">
</span>
<?php echo _('Filtre')." ".HtmlInput::filter_table("account_tbl_id", "0,1,2,3,4", 1);?>
             <?php
             echo HtmlInput::hidden('p_action','pcmn');
//echo HtmlInput::hidden('sa','detail');
echo dossier::hidden();
$limite=MAX_QCODE;
?>
<TABLE  id="account_tbl_id" class="result">
     <TR>
     <TH> <?php echo _('Poste comptable') ?> </TH>
     <TH> <?php echo _('Libellé') ?> </TH>
     <TH> <?php echo _('Poste comptable Parent') ?> </TH>
     <TH> <?php echo _('Type') ?> </TH>
     <TH> <?php echo _('Fiche') ?></TH>
     </TR>

<?php
$str_dossier=dossier::id();
for ($i=0; $i <$MaxRow; $i++):
    $A=Database::fetch_array($Ret,$i);
   $class=( $i%2 == 0 )?"even":"odd";

?>
     <tr id="row_<?php echo $A['pcm_val']?>" class="<?php echo $class;?>">
    <td class="<?php echo $class;?>">
        <?php
        echo HtmlInput::history_account($A['pcm_val'], $A['pcm_val']);
        ?>
    </td>
    <td class="<?php echo $class;?>">
    <?php
    printf ("<A style=\"text-decoration:underline\" HREF=\"javascript:void(0)\" onclick=\"pcmn_update(%d,'%s')\">",
            $str_dossier, $A['pcm_val']);
    echo h($A['pcm_lib']);
    ?>
    </td>
    <td class="<?php echo $class;?>">
        <?php echo $A['pcm_val_parent']; ?>
    </TD>
    <td class="<?php echo $class;?>">
        <?php    echo $A['pcm_type'];?>
    </TD>
    <td class="<?php echo $class;?>">
    <?php	
        if ( strlen($A['acode']) >0 ) :
            if (strpos($A['acode'], ",") >0 ) :
                $det_qcode=  explode(",", $A['acode']);
		echo '<ul style="display:inline;paddding:0;margin:0px;padding-left:0px;list-style-type:none;padding-start-value:0px">';
		$max=(count($det_qcode)>MAX_QCODE)?MAX_QCODE:count($det_qcode);
		for ($e=0;$e<$max;$e++) :
			echo '<li style="padding-start-value:0;margin:2px;display:inline">'.HtmlInput::card_detail($det_qcode[$e],'',' style="display:inline"').'</li>';
		endfor;
		echo '</ol>';
		if ($max < count($det_qcode)) :
			echo "...";
		else :
			echo HtmlInput::card_detail($A['acode']);
		endif;
            endif;
	endif;
        ?>
	</td>
  </tr>
<?php
endfor;
?>
</TABLE>
    <?php
    /* it will override the classic onscroll (see scripts.js)
     * @see scripts.js
     */
    ?>
    <div id="go_up" class="inner_box" style="padding:0px;left:auto;width:250px;height: 100px;display:none;position:fixed;top:5px;right:20px">
        <?php echo HtmlInput::title_box(_('Navigation'), 'go_up', "hide");?>
        <div style="margin:3%;padding:3%">
            <a class="button" href="#top" ><?php echo "&#8679";?></a>
            <input type="button" id="pcmn_update_add_bt3" class="smallbutton"  value="<?php echo _('Ajout poste comptable'); ?>">
        </div>
    </div>
 <input type="button" id="pcmn_update_add_bt2" class="smallbutton"  value="<?php echo _('Ajout poste comptable'); ?>">
 </div>
 <script>
     $('pcmn_update_add_bt').onclick = function () 
     {
         pcmn_update(<?php echo Dossier::id()?>,'');
     }
     $('pcmn_update_add_bt2').onclick = function () 
     {
         pcmn_update(<?php echo Dossier::id()?>,'');
     }
     $('pcmn_update_add_bt3').onclick = function () 
     {
         pcmn_update(<?php echo Dossier::id()?>,'');
     }
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
</script>
<?php
html_page_stop();
?>
