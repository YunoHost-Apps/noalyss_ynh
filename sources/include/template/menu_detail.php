<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
echo HtmlInput::title_box($msg,"divmenu");
$str_code=new IText('me_code',$m->me_code);
if ( $m->me_code != -1) $str_code->setReadOnly (true);

$str_menu=new IText('me_menu',$m->me_menu);
$str_desc=new IText('me_description',$m->me_description);
$str_file=new IText('me_file',$m->me_file);
$str_url=new IText('me_url',$m->me_url);
$str_parameter=new IText('me_parameter',$m->me_parameter);
$str_js=new IText('me_javascript',$m->me_javascript);
$a_type=array (
       array ('label'=>_('Impression'),'value'=>'PR' ),
       array ('label'=>_('Menu'),'value'=>'ME' )
    );
$str_type=new ISelect("me_type", $a_type);
$str_type->selected=$m->me_type;
?>
<table>
    <tr>
        <td>
            <?php echo _("Code du menu")?>
        </td>
        <td>
            <?php echo $str_code->input()?>
        </td>
    </tr>
        <tr>
        <td>
            <?php echo _("Libellé du menu")?>
        </td>
        <td>
            <?php echo $str_menu->input()?>
        </td>
    </tr>
        <tr>
        <td>
            <?php echo _("Description")?>
        </td>
        <td>
            <?php echo $str_desc->input()?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo _("Type")?>
        </td>
        <td>
            <?php echo $str_type->input();?>
        </td>
    </tr>
         <tr>
        <td>
            <?php echo _("Fichier à inclure (depuis le répertoire include)")?>
        </td>
        <td>
            <?php echo $str_file->input()?>
        </td>
    </tr>
    <tr>
        <td>
            URL
        </td>
        <td>
            <?php echo $str_url->input()?>
        </td>
    </tr>
     <tr>
        <td>
            <?php echo _('Paramètre')?>
        </td>
        <td>
            <?php echo $str_parameter->input()?>
        </td>
    </tr>
     <tr>
        <td>
            Javascript
        </td>
        <td>
            <?php echo $str_js->input()?>
        </td>
    </tr>
</table>
