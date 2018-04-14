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

define ('FICADD',800);	 /* Ajout de fiche */
define ("FIC",805);  //Création, modification et effacement de fiche
define ("FICCAT",910);  //création, modification et effacement de catégorie de fiche
define ('RMDOC',1020);   // Effacement de document pour follow up & comptabilité
define ('VIEWDOC',1010);   // Voir document pour follow up
define ('PARCATDOC',1050);   // modifier type document pour follow up
define ('RMRECEIPT',1110);   // Effacer un document d'une pièce comptable
define ('RMOPER',1120);   // Effacer une opération comptable
define ('SHARENOTE',1210); // Can share a note
define ('SHARENOTEPUBLIC',1220); // Can create public note
define ('SHARENOTEREMOVE',1230); // Can drop drop of other
global $audit; 
$audit=false;
if (defined('AUDIT_ENABLE') && AUDIT_ENABLE == true ) $audit=true;
?>
