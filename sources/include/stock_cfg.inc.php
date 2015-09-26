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

/**
 * @file
 * @brief Manage the repository
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_stock_sql.php';
require_once NOALYSS_INCLUDE.'/class_sort_table.php';

global $g_user, $cn,$g_parameter;

if ($g_parameter->MY_STOCK == 'N')
{
	echo '<h2 class="notice">';
	echo _("Vous n'utilisez pas de gestion de stock");
	echo '</h2>';
	return;
}
if ( isset ($_POST['add_stock']))
{
    $post_name=HtmlInput::default_value_post('r_name', "");
    if ( strlen(trim($post_name)) != 0)
    {
        $st=new Stock_Sql($cn);
	$st->from_array($_POST);
	$st->insert();
    }
}
if ( isset ($_POST['mod_stock']))
{
    $post_name=HtmlInput::default_value_post('r_name', "");
    if ( strlen(trim($post_name)) != 0)
    {

	$st=new Stock_Sql($cn,$_POST['r_id']);
	$st->from_array($_POST);
	$st->update();
    }
}
$tb=new Sort_Table();
$p_url=HtmlInput::get_to_string(array("ac","gDossier"));

$tb->add(_("Nom"), $p_url, " order by r_name asc", "order by r_name desc", "ona", "ond");
$tb->add(_("Adresse"), $p_url, " order by r_adress asc", "order by r_adress desc", "oaa", "oad");
$tb->add(_("Ville"), $p_url, " order by r_city asc", "order by r_city desc", "ova", "ovd");
$tb->add(_("Pays"), $p_url, " order by r_country asc", "order by r_country desc", "opa", "opd");
$tb->add(_("Téléphone"), $p_url, " order by r_phone asc", "order by r_phone desc", "opa", "opd");

$sql="select * from stock_repository ";

$ord=(isset($_GET['ord']))?$_GET['ord']:"ona";

$order=$tb->get_sql_order($ord);

$array=$cn->get_array($sql." ".$order);

?>
<div class="content">

<table class="result">
	<tr>
		<th><?php echo $tb->get_header(0)?></th>
		<th><?php echo $tb->get_header(1)?></th>
		<th><?php echo $tb->get_header(2)?></th>
		<th><?php echo $tb->get_header(3)?></th>
		<th><?php echo $tb->get_header(4)?></th>
	</tr>
<?php for ($i=0;$i<count($array);$i++): ?>
	<tr>
		<td>
			<?php echo h($array[$i]['r_name'])?>
		</td>
		<td>
			<?php echo h($array[$i]['r_adress'])?>
		</td>
		<td>
			<?php echo h($array[$i]['r_city'])?>
		</td>
		<td>
			<?php echo h($array[$i]['r_country'])?>
		</td>
		<td>
			<?php echo h($array[$i]['r_phone'])?>
		</td>
		<td>
			<?php
				$js=' onclick="stock_repo_change(\''.dossier::id().'\',\''.$array[$i]['r_id'].'\')"';
				echo HtmlInput::button("mod", _("Modifier"), $js);
			?>
		</td>
	</tr>

<?php endfor;?>
</table>
	<?php echo HtmlInput::button("show_add_depot_d", "Ajout d'un dépot", "onclick=\"$('add_depot_d').show();\"");?>
	<div id="add_depot_d" class="inner_box" style="display:none">
	<?php echo HtmlInput::title_box("Ajouter un dépôt","add_depot_d","hide")?>
	<form method="post">
		<table>
			<tr>
				<td>
					Nom
				</td>
				<td>
					<?php $name=new IText("r_name",""); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					Adresse
				</td>
				<td>
					<?php $name=new IText("r_adress",""); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					Ville
				</td>
				<td>
					<?php $name=new IText("r_city",""); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					Pays
				</td>
				<td>
					<?php $name=new IText("r_country",""); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					Téléphone
				</td>
				<td>
					<?php $name=new IText("r_phone",""); echo $name->input();?>
				</td>
			</tr>

		</table>
		<?php echo HtmlInput::submit("add_stock","Sauver")?>
	</form>
	</div>
</div>
