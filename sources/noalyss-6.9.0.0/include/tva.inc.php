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
/** \file
 * \brief included file for customizing with the vat (account,rate...)
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_own.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_itextarea.php';
echo '<div class="content">';
// Confirm remove
if (isset($_POST['confirm_rm']))
{
    if ($cn->count_sql('select * from tva_rate') > 1)
	$cn->exec_sql('select tva_delete($1)', array($_POST['tva_id']));
    else
	echo '<p class="notice">Vous ne pouvez pas effacer tous taux' .
	' Si votre soci&eacute;t&eacute; n\'utilise pas la TVA, changer dans le menu soci&eacute;t&eacute</p>';
}
$both_side=(isset($_REQUEST['both']))?1:0;
//-----------------------------------------------------
// Record Change
if (isset($_POST['confirm_mod'])
	|| isset($_POST['confirm_add']))
{
    extract($_POST);
    // remove space
    $tva_poste = str_replace(" ", "", $tva_poste);
    $err = 0; // Error code

    if (isNumber($tva_rate) == 0)
    {
	$err = 2;
    }

	if ($err == 0)
    {
	if (isset($_POST['confirm_add']))
	{
	    $sql = "select tva_insert($1,$2,$3,$4,$5)";

	    $res = $cn->exec_sql(
		    $sql, array($tva_label,
		$tva_rate,
		$tva_comment,
		$tva_poste,
                        $both_side)
	    );
	    $err = Database::fetch_result($res);
	}
	if (isset($_POST['confirm_mod']))
	{
	    $Res = $cn->exec_sql(
		    "select tva_modify($1,$2,$3,$4,$5,$6)", array($tva_id, $tva_label, $tva_rate, $tva_comment, $tva_poste,$both_side)
	    );
	    $err = Database::fetch_result($Res);
	}
    }
    if ($err != 0)
    {
	$err_code = array(1 => "Tva id n\'est pas un nombre",
	    2 => "Taux tva invalide",
	    3 => "Label ne peut être vide",
	    4 => "Poste invalide",
	    5 => "Tva id doit être unique");
	$str_err = $err_code[$err];
	echo "<script>alert ('$str_err'); </script>";
	;
    }
}
// If company not use VAT
$own = new Own($cn);
if ($own->MY_TVA_USE == 'N')
{
    echo '<h2 class="error">'._("Vous n'êtes pas assujetti à la TVA").'</h2>';
    return;
}
//-----------------------------------------------------
// Display
$sql = "select tva_id,tva_label,tva_rate,tva_comment,tva_poste,tva_both_side from tva_rate order by tva_label";
$Res = $cn->exec_sql($sql);
?>
<TABLE>
    <TR>
        <th>Id</th>
	<th>Label</TH>
	<th>Taux</th>
	<th>Commentaire</th>
	<th>Poste</th>
	<th>Utilisé en même temps au crédit et au débit</th>
    </tr>
<?php
$val = Database::fetch_all($Res);
foreach ($val as $row)
{
    // load value into an array
    $index = $row['tva_id'];
    $tva_array[$index] = array(
	'tva_label' => $row['tva_label'],
	'tva_rate' => $row['tva_rate'],
	'tva_comment' => $row['tva_comment'],
	'tva_poste' => $row['tva_poste'],
	'tva_both_side' => $row['tva_both_side']
    );

    echo "<TR>";
    echo '<FORM METHOD="POST">';

    echo '<td>';
    echo $row['tva_id'];
    echo '</td>';

    echo "<TD>";
    echo HtmlInput::hidden('tva_id', $row['tva_id']);
    echo h($row['tva_label']);
    echo "</TD>";

    echo "<TD>";
    echo $row['tva_rate'];
    echo "</TD>";

    echo "<TD>";
    echo h($row['tva_comment']);
    echo "</TD>";

    echo "<TD>";
    echo $row['tva_poste'];
    echo "</TD>";

    echo "<TD>";
    $str_msg=( $row['tva_both_side']==1)?'Employé au crédit et débit':'normal' ;
    echo $str_msg;
    echo "</TD>";

    echo "<TD>";
    echo HtmlInput::submit("rm", "Efface");
    echo HtmlInput::submit("mod", "Modifie");
    $w = new IHidden();
    $w->name = "tva_id";
    $w->value = $row['tva_id'];
    echo $w->input();
    $w = new IHidden();
    $w->name = "p_action";
    $w->value = "divers";
    echo $w->input();
    $w = new IHidden();
    $w->name = "sa";
    $w->value = "tva";
    echo $w->input();

    echo "</TD>";

    echo '</FORM>';
    echo "</TR>";
}
?>
</TABLE>
    <?php
    // if we add / remove or modify a vat we don't show this button
    if (!isset($_POST['add'])
	    && !isset($_POST['mod'])
	    && !isset($_POST['rm'])
    )
    {
	?>
    <form method="post">
        <input type="submit" class="button" name="add" value="Ajouter un taux de tva">
        <input type="hidden" name="p_action" value="divers">
        <input type="hidden" name="sa" value="tva">
    </form>
    <?php
}


//-----------------------------------------------------
// remove
if (isset($_REQUEST['rm']))
{
    echo "Voulez-vous vraiment effacer ce taux ? ";
    $index = $_POST['tva_id'];
    ?>
    <table>
        <TR>
    	<th>Label</TH>
    	<th>Taux</th>
    	<th>Commentaire</th>
    	<th>Poste</th>
    	<th>Double côté</th>
        </tr>
        <tr>
    	<td> <?php echo $tva_array[$index]['tva_label'];?></td>
    	<td> <?php echo $tva_array[$index]['tva_rate'];?></td>
    	<td> <?php echo $tva_array[$index]['tva_comment'];?></td>
    	<td> <?php echo $tva_array[$index]['tva_poste'];?></td>
    	<td> <?php echo $tva_array[$index]['tva_both_side'];?></td>
        </Tr>
    </table>
		<?php
		echo '<FORM method="post">';
		echo '<input type="hidden" name="tva_id" value="' . $index . '">';
		echo HtmlInput::submit("confirm_rm", "Confirme");
		echo HtmlInput::submit("Cancel", "no");
		echo "</form>";
	    }
	    //-----------------------------------------------------
	    // add
	    if (isset($_REQUEST['add']))
	    {
		echo "<fieldset><legend>Ajout d'un taux de tva </legend>";
		echo '<FORM method="post">';
		?>
    <table >
        <tr> <td align="right"> Label (ce que vous verrez dans les journaux)</td>
    	<td> <?php
    $w = new IText();
    $w->size = 20;
    echo $w->input('tva_label', '')
		?></td>
        </tr>
        <tr><td  align="right"> Taux de tva </td>
    	<td> <?php
    $w = new IText();
    $w->size = 5;
    echo $w->input('tva_rate', '')
    ?></td>
        </tr>
        <tr>
    	<td  align="right"> Commentaire </td>
    	<td> <?php
    $w = new ITextarea;
    $w->heigh = 5;
    $w->width = 50;
    echo $w->input('tva_comment', '')
		?></td>
        </tr>
        <tr>
    	<td  align="right">Poste comptable utilisés format :debit,credit</td>
    	<td> <?php
	    $w = new IText();
	    $w->size = 20;
	    echo $w->input('tva_poste', '')
	    ?></td>
        </Tr>
        <tr>
    	<td  align="right">Utilisé au débit et au crédit afin d'annuler cette tva </td>
    	<td> <?php
	    $w = new ICheckBox("both", 1);
	    $w->size = 20;
	    echo $w->input('both', '')
	    ?></td>
        </Tr>
    </table>
    <input type="submit" class="button" value="Confirme" name="confirm_add">
    <input type="submit" class="button" value="Cancel" name="no">

    </FORM>
    </fieldset>
    <?php
}

//-----------------------------------------------------
// mod
if (isset($_REQUEST['mod']))
{

    echo "Tva à modifier";
    $index = $_POST['tva_id'];
    echo "<fieldset><legend>Modification d'un taux de tva </legend>";
    echo '<FORM method="post">';
    echo '<input type="hidden" name="tva_id" value="' . $index . '">';
    ?>
    <table>
        <tr> <td align="right"> Label (ce que vous verrez dans les journaux)</td>
    	<td> <?php
    $w = new Itext();
    $w->size = 20;
    echo $w->input('tva_label', $tva_array[$index]['tva_label'])
    ?></td>
        </tr>
        <tr><td  align="right"> Taux de tva </td>

    	<td> <?php
    $w = new Itext();
    $w->size = 5;
    echo $w->input('tva_rate', $tva_array[$index]['tva_rate'])
    ?></td>
        </tr>
        <tr>
    	<td  align="right"> Commentaire </td>
    	<td> <?php
	    $w = new ITextarea();
	    $w->heigh = 5;
	    $w->width = 50;
	    echo $w->input('tva_comment', $tva_array[$index]['tva_comment'])
	    ?></td>
        </tr>
        <tr>
    	<td  align="right">Poste comptable utilisés format :debit,credit</td>

    	<td> <?php
	    $w = new IText();
	    $w->size = 20;
	    echo $w->input('tva_poste', $tva_array[$index]['tva_poste'])
	    ?></td>
        </Tr>
        <tr>
    	<td  align="right">Utilisé au débit et au crédit afin d'annuler cette tva </td>
    	<td> <?php
	    $w = new ICheckBox("both",$tva_array[$index]['tva_both_side'] );
            $w->selected=$tva_array[$index]['tva_both_side'];
	    $w->size = 20;
	    echo $w->input('both', '')
	    ?></td>
        </Tr>
    </table>
    <input type="submit" class="button" value="Confirme" name="confirm_mod">
    <input type="submit" class="button" value="Cancel" name="no">
    </FORM>
    </fieldset>
    <?php
}
echo '</div>';
?>
