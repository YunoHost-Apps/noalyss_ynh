<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><fieldset><legend>Ajout d'un exercice</legend>
<ul>
<li>
Exercice en 12 périodes : Ajout d'un exercice comptable de 12 périodes, commençant le 1 janvier et terminant le 31 décembre. </li>
<li>   Exercice en 13 périodes: Ajout d'une période d'un jour le 31/12. Cette période est utilisée
pour faire toutes les écritures de fin d'exercice: amortissements, régulations de compte... Avec une 13ième période, cela simplifie les prévisions, les rapports...</li>
<li>
	Pour ajouter des années, ne commençant pas en janvier ou comptant un nombre de mois supérieur à 12, utilisez le plugin "Outils Comptables"
</li>
</ul>

<form method="post" id="exercice_frm" onsubmit="return confirm_box(this,'<?php echo _("Confirmez vous l\'ajout d\'un exercice comptable ?")?>')">
<?php 
echo HtmlInput::hidden("ac",$_REQUEST['ac']);
echo $nb_exercice->input();
echo HtmlInput::hidden("jrn_def_id","0");
echo HtmlInput::hidden("add_exercice","1");
echo Dossier::hidden();
echo HtmlInput::submit("add_exercicebt",_("Ajout d'un exercice comptable"));
?>

</form>
</fieldset>
