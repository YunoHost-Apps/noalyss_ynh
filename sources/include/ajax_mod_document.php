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
 * \brief show the detail of a document and let you modify it
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_document_modele.php';

  /* 1. Check security */
$cn=new Database(dossier::id());
  /* 2. find the document */
$doc=new Document_Modele($cn,$id);

  /* 3. display it */
$doc->load();
ob_start();
require(NOALYSS_INCLUDE.'/template/modele_document.php');

$html=ob_get_contents();
ob_end_clean();
$html=escape_xml($html);
header('Content-type: text/xml; charset=UTF-8');

echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>mod_doc</ctl>
<code>$html</code>
</data>
EOF;
exit();
