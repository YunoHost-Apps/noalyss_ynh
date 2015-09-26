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
 * \brief display a form to change the name of a predefined operation
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
ob_start();
require_once NOALYSS_INCLUDE.'/class_pre_operation.php';
$op=new Pre_Operation($cn,$_GET['id']);
$array=$op->load();
echo HtmlInput::anchor_close('mod_predf_op');
echo h2(_('Modification du nom'),' class="title"');

echo '
    <form method="POST" onsubmit="save_predf_op(this);return false;">';
$name = new IText('opd_name');
$name->value = $op->od_name;
$name->size = 60;
echo "Nom =" . $name->input();
$opd_description=new ITextarea('od_description');
$opd_description->style=' class="itextarea" style="width:30em;height:4em;vertical-align:top"';
$opd_description->value=$op->od_description;
echo '<p>';
echo _("Description (max 50 car.)");
echo $opd_description->input();
echo '</p>';
echo dossier::hidden() . HtmlInput::hidden('od_id', $_GET['id']);
echo "<hr>";
//////////////////////////////////////////////////////////////////////////////
// Detail operation 
//////////////////////////////////////////////////////////////////////////////
echo $op->display();


echo HtmlInput::submit('save', _('Sauve'));
echo HtmlInput::button('close', _('Annuler'), 'onclick="removeDiv(\'mod_predf_op\')"');
echo '</form>';


$html1 = ob_get_contents();
ob_end_clean();
$html = escape_xml($html1);
if (headers_sent() ) 
    { 
    echo $html1; 
    }
else {
    header('Content-type: text/xml; charset=UTF-8');
}
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>mod_predf_op</ctl>
<code>$html</code>
</data>
EOF;
