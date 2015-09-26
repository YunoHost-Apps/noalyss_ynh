<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_menu_ref.php';
$msg=_("Création");
$m=new Menu_Ref($cn);
echo '<form method="POST" id="ajax_create_menu_frm" onsubmit="return confirm_box(this,\''._('Vous confirmez ?').'\')">';
echo HtmlInput::hidden('create_menu', 1);
require_once 'template/menu_detail.php';
echo HtmlInput::submit('create_menubt',_('Sauver'));
echo HtmlInput::button_close('divmenu');
echo '</form>';
?>
