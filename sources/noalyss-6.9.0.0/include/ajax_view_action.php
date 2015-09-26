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

/**
 * @file
 * @brief show the detail of an action
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
ob_start();
require_once NOALYSS_INCLUDE.'/class_follow_up.php';
require_once NOALYSS_INCLUDE.'/class_default_menu.php';

echo HtmlInput::title_box(_("Détail action"), $div);
$act = new Follow_Up($cn);
$act->ag_id = $ag_id;
$act->get();
$code='nok';
if ($g_user->can_write_action($ag_id) == true || $g_user->can_read_action($ag_id) == true || $act->ag_dest == -1)
{   
        $menu=new Default_Menu();
	echo $act->Display('READ', false, "ajax", "");
	//$action=HtmlInput::array_to_string(array("gDossier","ag_id"), $_GET)."&ac=FOLLOW&sa=detail";
        $action=  "do.php?".http_build_query(array("gDossier"=>Dossier::id(),"ag_id"=>$ag_id,"ac"=>$menu->get('code_follow'),"sa"=>"detail"));
        $code='ok';
	if ( $_GET['mod']== 1) :
            $forbidden=_("Accès interdit : vous n'avez pas accès à cette information, contactez votre responsable");
	?>
<a href="<?php echo $action?>" target="_blank" class="smallbutton"><?php echo _("Modifier")?> </a>
    <?php 
        $code='nok';
	endif;
}
else
{
	$forbidden = _("Ce document n'est pas accessible");
	?>
	<div style="margin:0px;padding:0px;background-color:red;text-align:center;">
        <h2 class="error"><?php echo $forbidden ?></h2>;
</div>
	<?php 
}
echo HtmlInput::button_close($div);
$response =  ob_get_clean();
$html=escape_xml($response);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$code</ctl>
<code>$html</code>
</data>
EOF;
exit();

?>