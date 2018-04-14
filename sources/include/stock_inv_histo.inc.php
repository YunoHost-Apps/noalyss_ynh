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
 * @brief history of manuel change
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_exercice.php';

if ( isset($_POST['del']))
{
	if (isset($_POST['ok']))
	{
		if ($g_user->can_write_repo($_POST['r_id']))
		{
			$cn->exec_sql('delete from stock_change where c_id=$1',array($_POST['c_id']));
		}
		else
		{
			alert(_("Vous ne pouvez pas modifier ce dépôt"));
		}
	}
	else
	{
		alert(_("Opération non effacée: vous n'avez pas confirmé"));
	}
}
$profile=$g_user->get_profile();
$gDossier=dossier::id();
$default_exercice=$g_user->get_exercice();
$p_exercice=HtmlInput::default_value_get("p_exercice", $default_exercice);

$a_change=$cn->get_array("select *,to_char(c_date,'DD.MM.YY') as str_date from stock_change as sc
			join stock_repository as sr on (sc.r_id=sr.r_id)
			where sc.r_id in (select r_id from profile_sec_repository where p_id=$1)
                         and c_date >= (select min(p_start) from parm_periode where p_exercice = $2)
                        and c_date <= (select max(p_end) from parm_periode where p_exercice = $2)
		order by c_date",array($profile,$p_exercice));


$exercice=new Exercice($cn);
?>
<div class="content">
    <form method="get" class="print">
      <?php echo HtmlInput::get_to_hidden(array('gDossier','ac',));?>
      <?php echo $exercice->select('p_exercice',$p_exercice)->input();?>
      <?php echo HtmlInput::submit("filter", _('Valider')); ?>
    </form>
<table class="result">
	<tr>

		<th>
			<?php echo _('Date')?>
		</th>
		<th>
			<?php echo _('Commentaire')?>
		</th>
		<th>
			<?php echo _('Dépot')?>
		</th>
		<th>
			<?php echo _('Utilisateur') ?>
		</th>
		<th>

		</th>
	</tr>
	<?php for ($e=0;$e<count($a_change);$e++): ?>
	<?php $class=($e%2==0)?' class="even" ':' class="odd" '; ?>
	<tr <?php echo $class?>>

		<td>
			<?php echo   $a_change[$e]['str_date']?>
		</td>
		<td>
			<?php echo h($a_change[$e]['c_comment'])?>
		</td>
		<td>
			<?php echo h($a_change[$e]['r_name'])?>
		</td>
		<td>
			<?php echo $a_change[$e]['tech_user']?>
		</td>
		<td>
			<?php echo HtmlInput::anchor(_("Détail"),"javascript:void()",sprintf("onclick=\"stock_inv_detail('%s','%s')\"",$gDossier,$a_change[$e]['c_id']));?>
		</td>

	</tr>
	<?php endfor; ?>
</table>
</div>
