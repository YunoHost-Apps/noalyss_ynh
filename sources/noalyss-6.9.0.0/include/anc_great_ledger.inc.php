<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
echo '<div style="content">';

require_once NOALYSS_INCLUDE.'/class_anc_grandlivre.php';

$grandLivre=new Anc_Grandlivre($cn);

$grandLivre->get_request();

/*
 * Form
 */
echo '<form method="get" >';
echo $grandLivre->display_form();
echo '<p>' . HtmlInput::submit('Recherche', _('Rechercher')) . '</p>';
echo HtmlInput::request_to_hidden(array('sa','ac','gDossier'));
echo '</form>';

$result=HtmlInput::default_value_request('result',null);

if ($result != null)
{
    $result=$grandLivre->display_html();
    if ($grandLivre->has_data != 0 )
    {
        echo '<span style="display:block">';
          echo _('Tout sélectionner')." ".ICheckBox::toggle_checkbox('export_pdf_bt1','export_anc_receipt_pdf');
        echo '</span>';
        echo $grandLivre->show_button();
        echo '<form method="GET" id="export_anc_receipt_pdf" action="export.php" style="display:inline">';

        echo $grandLivre->button_export_pdf();
        echo $grandLivre->display_html();
        echo $grandLivre->button_export_pdf();
        echo HtmlInput::get_to_hidden(array('ac','gDossier','sa'));
        echo '</form>';
        echo $grandLivre->show_button();
    }
    else
    {
        echo '<p class="notice">';
        echo _('Aucune donnée trouvée');
        echo '</p>';
    }
    
}
echo '</div>';
?>
