<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_menu_ref.php';
$m=new Menu_Ref($cn,$me_code);
$msg="Modification ".$m->me_code.' '.h($m->me_menu);
echo '<form method="POST" id="ajax_mod_menu_frm" onsubmit="return confirm_box(this,\'Vous confirmez ?\')">';
echo HtmlInput::hidden('modify_menu', 1);

require_once NOALYSS_INCLUDE.'/template/menu_detail.php';

echo HtmlInput::submit('modify_menutb',_('Sauver'));
echo HtmlInput::button_close('divmenu');
echo '</form>';
?>
