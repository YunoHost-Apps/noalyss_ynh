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
 *
 *
 * \brief Show the table menu and let you add your own
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_menu_ref.php';
require_once NOALYSS_INCLUDE.'/class_sort_table.php';
require_once NOALYSS_INCLUDE.'/class_extension.php';


echo '<div class="content">';
/**
 * if post save then we save a new one
 */
if ( isset($_POST['save_plugin']))
{
	extract($_POST);
	$plugin=new Extension($cn);
	$plugin->me_code=$me_code;
	$plugin->me_menu=$me_menu;
	$plugin->me_file=$me_file;
	$plugin->me_description=$me_description;
	$plugin->me_parameter='plugin_code='.$me_code;
	$plugin->insert_plugin();
}
/**
 * if post update then we update
 */
if (isset($_POST['mod_plugin']))
{
	extract ($_POST);
	$plugin=new Extension($cn);
	$plugin->me_code=strtoupper($me_code);
	$plugin->me_menu=$me_menu;
	$plugin->me_file=$me_file;
	$plugin->me_description=$me_description;
	$plugin->me_parameter='plugin_code='.strtoupper($me_code);
	if ( !isset ($delete_pl))
	{
		$plugin->update_plugin();
	}
	else
	{
		$plugin->remove_plugin();
	}
}
/**
 * if post save then we save a new one
 */
if ( isset($_POST['create_menu'])|| isset($_POST['modify_menu']))
{
	extract($_POST);
	$menu_ref=new Menu_Ref($cn);
	$menu_ref->me_code=strtoupper($me_code);
	$menu_ref->me_menu=$me_menu;
	$menu_ref->me_file=$me_file;
	$menu_ref->me_description=$me_description;
	$menu_ref->me_parameter=$me_parameter;
	$menu_ref->me_url=$me_url;
	$menu_ref->me_javascript=$me_javascript;
	$menu_ref->me_type='ME';
	$check=$menu_ref->verify();
	if ($check == 0)
	{
		if (isset($_POST['create_menu']))
		{
			$menu_ref->insert();
		}
		elseif (isset($_POST['modify_menu']))
		{
			if ($menu_ref->verify() == 0)
				$menu_ref->update();
		}
	}
}
//////////////////////////////////////////////////////////////////////////////
// Show the list of menu
//////////////////////////////////////////////////////////////////////////////
global $cn;

$table=new Sort_Table();
$url=$_SERVER['REQUEST_URI'];

$table->add(_('Code'),$url,"order by me_code asc","order by me_code desc","codea","coded");
$table->add(_('Menu'),$url,"order by me_menu asc","order by me_menu desc","menua","menud");
$table->add(_('Description'),$url,"order by me_description asc","order by me_description desc","desa","desd");
$table->add(_('Type'),$url,"order by me_type asc","order by me_type desc","ta","td");
$table->add(_('Fichier'),$url,"order by me_file asc","order by me_file desc","fa","fd");
$table->add(_('URL'),$url,"order by me_url asc","order by me_url desc","urla","urld");
$table->add(_('Paramètre'),$url,"order by me_parametere asc","order by me_parameter desc","paa","pad");
$table->add(_('Javascript'),$url,"order by me_javascript asc","order by me_javascript desc","jsa","jsd");

$ord=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'codea';

$order=$table->get_sql_order($ord);



$iselect=new ISelect('p_type');
$iselect->value=array(
	array("value"=>'',"label"=>_("Tout")),
	array("value"=>'ME',"label"=>_("Menu")),
	array("value"=>'PR',"label"=>_("Impression")),
	array("value"=>'PL',"label"=>_("Extension / Plugin")),
	array("value"=>'SP',"label"=>_("Valeurs spéciales"))
	);
$iselect->selected=(isset($_REQUEST['p_type']))?$_REQUEST['p_type']:'';
$sql="";
if ( $iselect->selected != '')
{
	$sql="where me_type='".sql_string($_REQUEST['p_type'])."'  ";
}
$menu=new Menu_Ref_sql($cn);
$ret=$menu->seek($sql.$order);
?>
<fieldset><legend><?php echo _('Recherche')?></legend>
<form method="GET">
	<?php echo $iselect->input()?>
	<?php echo HtmlInput::submit("search", _("Recherche"))?>
	<?php echo HtmlInput::request_to_hidden(array('ac','gDossier','ord'))?>
</form>
     <?php echo _('Filtre'),HtmlInput::filter_table('menu_tb', '0,1,2,4', '1'); ?>
</fieldset>
<?php 
$gDossier=Dossier::id();
echo HtmlInput::button("Add_plugin", _("Ajout d'un plugin"), "onclick=add_plugin($gDossier)");
echo HtmlInput::button("Add_menu", _("Ajout d'un menu"), "onclick=create_menu($gDossier)");

echo '<table class="result" id="menu_tb">';
echo '<tr>';
echo '<th>'.$table->get_header(0).'</th>';
echo '<th>'.$table->get_header(1).'</th>';
echo '<th>'.$table->get_header(2).'</th>';
echo '<th>'.$table->get_header(3).HtmlInput::infobulle(33).'</th>';
echo '<th>'.$table->get_header(4).'</th>';
echo '<th>'.$table->get_header(5).'</th>';
echo '<th>'.$table->get_header(6).'</th>';
echo '<th>'.$table->get_header(7).'</th>';
echo '</tr>';

for ($i = 0; $i < Database::num_row($ret); $i++)
{
    $row = $menu->get_object($ret, $i);
    $js = $row->me_code;
    switch ($row->me_type)
    {
        case 'PL':
            $js = sprintf('<A class="line" href="javascript:void(0)"  onclick="mod_plugin(\'%s\',\'%s\')">%s</A>', $gDossier, $row->me_code, $row->me_code);
            break;
        case 'ME':
            $js = sprintf('<A class="line" href="javascript:void(0)"  onclick="modify_menu(\'%s\',\'%s\')">%s</A>', $gDossier, $row->me_code, $row->me_code);
            break;
    }
    $class = ( $i % 2 == 0) ? $class = ' class="odd"' : $class = ' class="even"';
    echo "<tr $class>";
    echo td($js);
    echo td(_($row->me_menu));
    echo td(h(_($row->me_description)));
    echo td(h($row->me_type));
    echo td(h($row->me_file));
    echo td(h($row->me_url));
    echo td(h($row->me_parameter));
    echo td(h($row->me_javascript));
    echo '</tr>';
}
echo '</table>';

?>
