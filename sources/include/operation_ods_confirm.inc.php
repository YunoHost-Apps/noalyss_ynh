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
 * \brief confirm ODS operation
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
echo '<div class="content">';
echo h2(_("Confirmation"),'class="info"');
echo '<div id="jrn_name_div">';
echo '<h2 id="jrn_name" style="display:inline">' . $ledger->get_name() . '</h2>';
echo '</div>';

echo '<div id="warning_ven_id" class="notice" style="width: 50%; margin-left: 0px; float: right;">';
echo h2(_("Attention, cette opération n'est pas encore sauvée : vous devez encore confirmer"),' class="notice"');
echo '</div>';

echo '<FORM METHOD="POST" enctype="multipart/form-data" class="print">';
echo HtmlInput::request_to_hidden(array('ac'));
echo $ledger->confirm($_POST,false);


?>
<div id="tab_id" >
    <script>
        var a_tab = ['modele_div_id','reverse_div_id','document_div_id'];
    </script>
<ul class="tabs">
    <li class="tabs_selected"> <a href="javascript:void(0)" title="<?php echo _("Modèle à sauver")?>"  onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';show_tabs(a_tab,'modele_div_id')"> <?php echo _('Modèle')?> </a></li>
    <li class="tabs"> <a href="javascript:void(0)" title="<?php echo _("Document")?>"  onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';show_tabs(a_tab,'document_div_id')"> <?php echo _('Document')?> </a></li>
    <li class="tabs"> <a href="javascript:void(0)" title="<?php echo _("Extourne")?>"  onclick="unselect_other_tab(this.parentNode.parentNode);this.parentNode.className='tabs_selected';show_tabs(a_tab,'reverse_div_id')"> <?php echo _('Extourne')?> </a></li>
</ul>
    <div id="modele_div_id">
        <?php echo Pre_operation::save_propose(); ?>
    </div>
    <div id="reverse_div_id" style="display:none;height:185px;height:10rem">
    <?php
        $reverse_date=new IDate('reverse_date');
        $reverse_ck=new ICheckBox('reverse_ck');
        echo _('Extourne opération')." ".$reverse_ck->input()." ";
        echo $reverse_date->input();
    ?>
    </div>
    <div id="document_div_id" style="display:none;height:185px;height:10rem">
      <?php
      $file = new IFile();
        $file->table = 0;
        echo '<p class="decale">';
        echo _("Ajoutez une pièce justificative ");
        echo $file->input("pj", "");
        echo '</p>';
        ?>
    </div>
</div>
<?php
echo HtmlInput::submit("save",_("Confirmer"));
echo HtmlInput::submit("correct",_("Corriger"));

?>
</FORM>
