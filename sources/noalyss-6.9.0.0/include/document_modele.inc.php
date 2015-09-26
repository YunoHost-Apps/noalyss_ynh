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
 * \brief Manage the document template
 */

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_document_modele.php';
$sub_action=(isset ($_REQUEST['sa']))?$_REQUEST['sa']:"";
echo js_include('modele_document.js');
echo '<div class="content">';
// show the form for adding a template
//
$doc=new Document_modele($cn);

//-----------------------------------------------------
// Document 	add a template
//-----------------------------------------------------
if ( $sub_action=='add_document')
{
    require_once NOALYSS_INCLUDE.'/class_document_modele.php';
    $doc=new Document_modele($cn);
    $doc->md_name=$_POST['md_name'];
    $doc->md_id=-1; // because it is a new model
    $doc->md_type=$_POST['md_type'];
    $doc->start=$_POST['start_seq'];
    $doc->md_affect=$_POST['md_affect'];
    $doc->Save();
}
//-----------------------------------------------------
// Document remove a template
//-----------------------------------------------------
if ( $sub_action=='rm_template')
{
    require_once NOALYSS_INCLUDE.'/class_document_modele.php';
    // Get all the document to remove

    foreach ( $_POST as $name=>$value )
    {
        list ($id) = sscanf ($name,"dm_remove_%d");
        if ( $id == null ) continue;
        // a document has to be removed
        $doc=new Document_modele($cn);
        $doc->md_id=$id;
        $doc->Delete();
    }

}
//----------------------------------------------------------------------
// Document modify a template
//----------------------------------------------------------------------
if ( $sub_action == 'mod_template')
  {
    require_once NOALYSS_INCLUDE.'/class_document_modele.php';
    $doc=new Document_modele($cn,$_POST['id']);
    $doc->update($_POST);
  }
//-----------------------------------------------------
// Default action : Show the list
//-----------------------------------------------------
echo $doc->myList();
echo '<div id="add_modele" class="inner_box" style="display:none">';
echo HtmlInput::title_box("Ajout d'un modèle", "add_modele", "hide");
echo $doc->form('');
echo '</div>';

?>