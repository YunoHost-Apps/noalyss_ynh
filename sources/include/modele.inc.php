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
 * \file
 *
 *
 * \brief concerne only the template
 *
 */
if ( !defined ('ALLOWED')) die('Forbidden');
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once  NOALYSS_INCLUDE.'/class_extension.php';

$sa = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : 'list';
if (isset($_POST['upd']) &&
		isset($_POST['m']))
{
	if (isset($_POST['name']) && isset($_POST['desc']))
	{
		extract($_POST);
		$cn = new Database();
		if (strlen(trim($name)) != 0
				&& $cn->get_value('select count(*) from modeledef where ' .
						'mod_name=$1 and mod_id !=$2', array(trim($name), $m)) == 0
		)
		{

			$cn->exec_sql("update modeledef set mod_name=$1, " .
					" mod_desc=$2 where mod_id=$3 ", array(trim($name), trim($desc), $m));
		}
	}
	$sa = "list";
}

$cn = new Database();



// IF FMOD_NAME is posted then must add a template
if (isset($_POST["FMOD_NAME"]))
{
	$encoding = $cn->get_value("select encoding from pg_database  where " .
			" datname='" . domaine . 'dossier' . sql_string($_POST["FMOD_DBID"]) . "'");

	if ($encoding != 6)
	{
		alert(_('Désolé vous devez migrer ce modèle en unicode'));
		echo '<span class="error">'._('la base de donnée')." " .
		domaine . 'mod' . $_POST["FMOD_DBID"]." " . _("doit être migrée en unicode")."</span>";
		echo '<span class="error"> '._("Pour le passer en unicode, faites-en un backup puis restaurez le fichier reçu").'</span>';

		echo HtmlInput::button_anchor(_('Retour'), 'admin_repo.php?action=dossier_mgt');
		return;
	}

	$mod_name = sql_string($_POST["FMOD_NAME"]);
	$mod_desc = sql_string($_POST["FMOD_DESC"]);
	if ($mod_name != null)
	{
		$Res = $cn->exec_sql("insert into modeledef(mod_name,mod_desc)
                           values ('" . $mod_name . "'," .
				"'" . $mod_desc . "')");

		// get the mod_id
		$l_id = $cn->get_current_seq('s_modid');
		if ($l_id != 0)
		{
			$Sql = sprintf("CREATE DATABASE %sMOD%d encoding='UTF8' TEMPLATE %sDOSSIER%s", domaine, $l_id, domaine, $_POST["FMOD_DBID"]);
			ob_start();
			if ($cn->exec_sql($Sql) == false)
			{
				ob_end_clean();
				echo "<h2 class=\"error\"> Base de donn&eacute;e " . domaine . "dossier" . $_POST['FMOD_DBID'] . "  "._("est accèd&eacute;e, d&eacute;connectez-vous en d'abord")."</h2>";
				$Res = $cn->exec_sql("delete from modeledef where mod_id=" . $l_id);

				exit;
			}
		}
	}// if $mod_name != null

	$cn_mod = new Database($l_id, 'mod');

	// Clean some tables

	$Res = $cn_mod->exec_sql("select distinct jr_pj from jrn where jr_pj is not null ");
	if (Database::num_row($Res) != 0)
	{
		$a_lob = Database::fetch_all($Res);
		for ($i = 0; $i < count($a_lob); $i++)
			$cn_mod->lo_unlink($a_lob[$i]['jr_pj']);
	}
	Extension::clean($cn_mod);
	$Res = $cn_mod->exec_sql("truncate table centralized");
	$Res = $cn_mod->exec_sql("truncate table jrn cascade");
	$Res = $cn_mod->exec_sql("delete from del_jrn");
	$Res = $cn_mod->exec_sql("delete from del_jrnx");
	$Res = $cn_mod->exec_sql("truncate table  jrnx cascade ");
	$Res = $cn_mod->exec_sql("truncate table  todo_list cascade ");
	$Res = $cn_mod->exec_sql("delete from del_action");
	$Res = $cn_mod->exec_sql("delete from profile_user");
	$Res = $cn_mod->exec_sql("delete from jnt_letter");

	$Res = $cn_mod->exec_sql('delete from operation_analytique');

	//	Reset the closed periode
	$Res = $cn_mod->exec_sql("update parm_periode set p_closed='f'");
	$Res = $cn_mod->exec_sql('delete from jrn_periode');
	$Res = $cn_mod->exec_sql(' insert into jrn_periode(p_id,jrn_def_id,status) ' .
			' select p_id,jrn_def_id,\'OP\' ' .
			' from ' .
			' parm_periode cross join jrn_def');

	// Reset Sequence
	$a_seq = array('s_jrn', 's_jrn_op', 's_centralized', 's_stock_goods', 's_internal');
	foreach ($a_seq as $seq)
	{
		$sql = sprintf("select setval('%s',1,false)", $seq);
		$Res = $cn_mod->exec_sql($sql);
	}
	$sql = "select jrn_def_id from jrn_def ";
	$Res = $cn_mod->exec_sql($sql);
	$Max = Database::num_row($Res);
	for ($seq = 0; $seq < $Max; $seq++)
	{
		$row = Database::fetch_array($Res, $seq);
		/* if seq doesn't exist create it */
		if ($cn_mod->exist_sequence('s_jrn_' . $row['jrn_def_id']) == false)
		{
			$cn_mod->create_sequence('s_jrn_' . $row['jrn_def_id']);
		}


		$sql = sprintf("select setval('s_jrn_%d',1,false)", $row['jrn_def_id']);
		$cn_mod->exec_sql($sql);

		$sql = sprintf("select setval('s_jrn_pj%d',1,false)", $row['jrn_def_id']);
		$cn_mod->exec_sql($sql);
		$sql = sprintf("select setval('jnt_letter_jl_id_seq',1,false)");
		$cn_mod->exec_sql($sql);
	}
	//---
	// Cleaning Follow_Up
	//--
	if (isset($_POST['DOC']))
	{
		$Res = $cn_mod->exec_sql("delete from action_gestion");
		$Res = $cn_mod->exec_sql("delete from document");

		// Remove lob file
		$Res = $cn_mod->exec_sql("select distinct loid from pg_largeobject except select md_lob from document_modele");
		if (Database::num_row($Res) != 0)
		{
			$a_lob = Database::fetch_all($Res);
			//var_dump($a_lob);
			foreach ($a_lob as $lob)
			{
				$cn_mod->lo_unlink($lob['loid']);
			}
		}
	}
	if (isset($_POST['CARD']))
	{
		$Res = $cn_mod->exec_sql("delete from  fiche_detail");
		$Res = $cn_mod->exec_sql("delete from   fiche");
		$Res = $cn_mod->exec_sql("delete from action_gestion");
		$Res = $cn_mod->exec_sql("delete from document");
		$Res = $cn_mod->exec_sql("delete from document_modele");
		$Res = $cn_mod->exec_sql("delete from op_predef");

		// Remove lob file
		$Res = $cn_mod->exec_sql("select distinct loid from pg_largeobject");
		if (Database::num_row($Res) != 0)
		{
			$a_lob = Database::fetch_all($Res);
			foreach ($a_lob as $lob)
				$cn_mod->lo_unlink($lob['loid']);
		}
	}
	if (isset($_POST['CANAL']))
	{
		$Res = $cn_mod->exec_sql('delete from poste_analytique');
		$Res = $cn_mod->exec_sql('delete from plan_analytique');
	}
        if ( isset ($_POST['PLUGIN'])) {
            $a_schema=$cn_mod->get_array("
                select nspname from pg_namespace 
                where
                nspname not like 'pg_%'
                and nspname not in ('information_schema','public','comptaproc')
                    ");      
            $nb_schema=count($a_schema);
            for ($i=0;$i < $nb_schema;$i++)
            {
                $cn_mod->exec_sql(" drop schema ".$a_schema[$i]['nspname']." cascade");
            }
        }

}
// Show all available templates
require_once NOALYSS_INCLUDE.'/class_sort_table.php';
$url=$_SERVER['PHP_SELF']."?sa=list&action=".$_REQUEST['action'];

$header=new Sort_Table();
$header->add(_("id"),$url," order by mod_id asc"," order by mod_id desc","ia","id");
$header->add(_("Nom"),$url," order by mod_name asc"," order by mod_name desc","na","nd");
$header->add(_("Description"),$url," order by mod_desc asc"," order by mod_desc desc","da","dd");

$ord=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'na';
$sql_order=$header->get_sql_order($ord);

$Res = $cn->exec_sql("select mod_id,mod_name,mod_desc from
                   modeledef $sql_order");

$count = Database::num_row($Res);
echo '<div class="content" style="width:80%;margin-left:10%">';
echo "<H2>"._('Modèles')."</H2>";
if ($sa == 'list')
{
        echo '<p>';
        echo HtmlInput::button(_('Ajouter'),_('Ajouter un modèle')," onclick=\$('folder_add_id').show()");

        echo '</p>';
	if ($count == 0)
	{
		echo _("Aucun modèle disponible");
	}
	else
	{

		echo '<span style="display:block;margin-top:10">';
		echo _('Filtre').HtmlInput::infobulle(23);
		echo HtmlInput::filter_table("t_modele", "0,1,2","1");
		echo '</span>';
		echo '<table id="t_modele" class="table_large" style="border-spacing:10;border-collapse:separate" >';
		echo "<TR>".
				"<TH>".$header->get_header(0)."</TH>" .
				"<TH>".$header->get_header(1)."</TH>" .
				"<TH>".$header->get_header(2)."</TH>" .
				"<TH>"._('Nom base de données')."</TH>" .
		"<th> </th>" .
		"<th> </th>" .
		"</TR>";

		for ($i = 0; $i < $count; $i++)
		{
			$mod = Database::fetch_array($Res, $i);
			$class = ($i % 2 == 0) ? "odd" : "even";
                        $str_name=domaine.'mod'.$mod['mod_id'];
			printf('<TR class="' . $class . '" style="vertical-align:top">' .
					'<TD>%d </td><td><b> %s</b> </TD>' .
					'<TD><I> %s </I></TD>' .
                                        '<td>'.$str_name.'</td>'.
					'<td> ' .
					HtmlInput::anchor(_('Effacer'), '?action=modele_mgt&sa=del&m=' . $mod['mod_id']," onclick = \"modele_drop('{$mod['mod_id']}') \"") . '</td>' .
					'</td>' .
					'<td>' . HtmlInput::anchor(_('Modifie'), '?action=modele_mgt&sa=mod&m=' . $mod['mod_id']," onclick = \"modele_modify('{$mod['mod_id']}') \"") . '</td>' .
					'</td>' .
					'<td>' . HtmlInput::anchor(_('Backup'), 'backup.php?action=backup&sa=b&t=m&d='
							. $mod['mod_id']) . '</td>' .
					'</TR>', $mod['mod_id'], $mod['mod_name'], $mod['mod_desc']);
		}// for
		echo "</table>";
	}// if count = 0
	echo "<p class=\"notice\">"._("Si vous voulez r&eacute;cup&eacute;rer toutes les adaptations d'un dossier " .
	" dans un autre dossier, vous pouvez en faire un modèle." .
	" Seules les fiches, la structure des journaux, les p&eacute;riodes,... seront reprises " .
	"et aucune donn&eacute;e du dossier sur lequel le dossier est bas&eacute;. Les données contenues dans les extensions ne sont pas effacées")."</p>";
}
?>
<div id="folder_add_id" class="inner_box" style="display:none;top:50px">
    <?php
        echo HtmlInput::title_box(_("Ajout d'un modèle"), 'folder_add_id', "hide");

//---------------------------------------------------------------------------
// Add a template
//---------------------------------------------------------------------------
// Show All available folder
	$Res = $cn->exec_sql("select dos_id, dos_name,dos_description from ac_dossier
                       order by dos_name");
	$count = Database::num_row($Res);
	$available = "";
	if ($count != 0)
	{
		$available = '<SELECT NAME="FMOD_DBID">';
		for ($i = 0; $i < $count; $i++)
		{
			$db = Database::fetch_array($Res, $i);
			$available.='<OPTION VALUE="' . $db['dos_id'] . '">' . $db['dos_name'] . ':' . $db['dos_description'];
		}//for i
		$available.='</SELECT>';
	}//if count !=0
	?>
	<form action="admin_repo.php?action=modele_mgt" METHOD="post">
		<TABLE>
			<tr>
				<td><?php echo _('Nom')?> </TD>
				<TD><INPUT TYPE="TEXT"  class="input_text"  VALUE="" NAME="FMOD_NAME"></TD>
			</TR>
			<TR>
				<TD><?php echo _('Description')?></TD>
				<TD><TEXTAREA ROWS="2" class="input_text"  COLS="60" NAME="FMOD_DESC"></Textarea></TD>
                        </TR>
                        <TR>
                        <TD> <?php echo _("Bas&eacute; sur")?> </TD>
	<TD> <?php echo $available?></TD>
	</TR>
	<TR>
		<TD><?php echo _("Nettoyage des Documents et courriers (ce qui  n'effacera pas les modèles de documents)")?></TD>
		<TD> <input type="checkbox" name="DOC"></TD></TR>
	<TR>
		<TD><?php echo _("Nettoyage de toutes les fiches (ce qui effacera client,
	op&eacute;rations pr&eacute;d&eacute;finies fournisseurs modèles de documents et documents)")?></TD>
		<TD> <input type="checkbox" name="CARD"></TD>
	</TR>

	<TR>
		<TD><?php echo _("Nettoyage de la comptabilit&eacute; analytique : effacement des plans et des postes, les op&eacute;rations
	sont de toute fa&ccedil;on effac&eacute;es")?> </TD>
		<TD> <input class="input_text" type="checkbox" name="CANAL"></TD>
	</TR>
	<TR>
		<TD><?php echo _("Effacement de toutes les donn&eacute;es des plugins")?></TD>
		<TD> <input type="checkbox" name="PLUGIN"></TD>
	</TR>
	</TABLE>
  <INPUT TYPE="SUBMIT" class="button" VALUE="<?php echo _("Ajout d'un modele")?>" >
</form>
</div>
		<?php
	
	//---------------------------------------------------------------------------
	// action = del
	//---------------------------------------------------------------------------
	if ($sa == 'remove')
	{
		if (!isset($_REQUEST['p_confirm']))
		{
			echo _('Désolé, vous n\'avez pas coché la case');
			echo HtmlInput::button_anchor(_('Retour'), '?action=modele_mgt');
			return;
		}

		$cn = new Database();
		$msg = "dossier";
		$name = $cn->get_value("select mod_name from modeledef where mod_id=$1", array($_REQUEST['m']));
		if (strlen(trim($name)) == 0)
		{
			echo "<h2 class=\"error\"> $msg inexistant</h2>";
			return;
		}
		$sql = "drop database " . domaine . "mod" . sql_string($_REQUEST['m']);
		ob_start();
		if ($cn->exec_sql($sql) == false)
		{
			ob_end_clean();

			echo "<h2 class=\"error\">";
                        printf (_("Base de donnée %s mod %s est accèdée, déconnectez-vous d'abord"),domaine,$_REQUEST['m'] )
                                . "</h2>";
			exit;
		}
		ob_flush();
		$sql = "delete from modeledef where mod_id=$1";
		$cn->exec_sql($sql, array($_REQUEST['m']));
		print '<h2 class="error">';
		printf (_("Le modèle %s est effacé")."</H2>",$name );
		echo HtmlInput::button_anchor(_('Retour'), '?action=modele_mgt');
	}
	echo '</div>';
	?>

