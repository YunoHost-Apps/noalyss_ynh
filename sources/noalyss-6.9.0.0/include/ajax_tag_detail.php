<?php
/**
 * @brief display a window with the content of a tag
 */
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( !defined ('ALLOWED') )  die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_tool_uos.php';
require_once NOALYSS_INCLUDE.'/class_tag.php';
ob_start();
$tag=new Tag($cn);
$tag->data->t_id=$_GET['tag'];
$tag->data->load();
echo HtmlInput::title_box(_("Détail du dossier ou tag"), "tag_div");

?>
<?php
// save via POST and reload page 
if ($_GET['form']=='p') :    ?>
    <form id="tag_detail_frm" method="POST" >
<?php 
/*
 * save via javascript and don't reload page
 */
else :
    ?>
    <form id="tag_detail_frm" method="POST" onsubmit="return save_tag();">
<?php        endif; ?>        
    <?php
    echo dossier::hidden();
    echo HtmlInput::hidden('t_id', $_GET['tag']);
    echo HtmlInput::hidden('ac',$_GET['ac']);
    $data=$tag->data;
    require_once 'template/tag_detail.php';
    echo HtmlInput::submit("save_tag_sb", "Valider");
    ?>
</form>
<?php
    $response=  ob_get_clean();
    $html=escape_xml($response);
    header('Content-type: text/xml; charset=UTF-8');
    echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
</data>
EOF;
    exit();
    ?>