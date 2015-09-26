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
// Copyright Author Dany De Bontridder danydb@aevalys.eu
if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');


/**
 * export all the selected documents for Ana Accountancy in PDF
 */
require_once NOALYSS_INCLUDE.'/class_document_export.php';

$ck = HtmlInput::default_value_get('ck', 0);
if ($ck == 0)
{
    echo "Aucune sélection";
    exit();
}
$anc=new Document_Export();

$anc->export_all($ck);
