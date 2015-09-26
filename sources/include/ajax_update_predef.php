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
 * \brief respond ajax request, the get contains
 *  the value :
 * - l for ledger 
 * - gDossier
 * Must return at least tva, htva and tvac
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_pre_operation.php';

// Check if the needed field does exist
extract ($_GET);
foreach (array('l','t','d','gDossier') as $a)
{
    if ( ! isset (${$a}) )
    {
        echo "error $a is not set ";
        exit();
    }

}
$cn=new Database(dossier::id());
$op=new Pre_operation_detail($cn);
$op->set('ledger',$l);
$op->set('ledger_type',$t);
$op->set('direct',$d);
$url=http_build_query(array('action'=>'use_opd','p_jrn_predef'=>$l,'ac'=>$_GET['ac'],'gDossier'=>dossier::id()));
$html="";

$html.=HtmlInput::title_box(_("Modèle d'opérations"), 'modele_op_div', 'hide');
$html.=$op->show_button('do.php?'.$url);

$html=escape_xml($html);
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code></code>
<value>$html</value>
</data>
EOF;

?>

