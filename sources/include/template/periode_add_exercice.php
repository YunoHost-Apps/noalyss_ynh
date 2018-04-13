<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<?php echo HtmlInput::title_box(_("Ajout d'un exercice"), "exercice_add",
        "hide"); ?>
<p>
    
        <?php echo _("Réserver un jour d'ouverture : le premier jour de l'exercice sera vue comme une période d'un seul jour pour y placer les opérations d'ouverture") ?>
</p>
<p>
      <?php echo _("Réserver un jour de fermeture : le dernier jour de l'exercice sera vue comme une période d'un seul jour pour y placer les opérations 
de fin d'exercice: amortissements, régulations de compte... Avec une 13ième période, cela simplifie les prévisions, les rapports..."); ?></p>

<form method="post" style="padding-left: 20%" id="exercice_frm" onsubmit="return (validate() && confirm_box($('exercice_frm'), '<?php echo _("Confirmez vous l\'ajout d\'un exercice comptable ?") ?>'))">
    <?php
    echo HtmlInput::hidden("ac", $_REQUEST['ac']);
    echo HtmlInput::hidden("jrn_def_id", "0");
    echo HtmlInput::hidden("add_exercice", "1");
    echo Dossier::hidden();
    ?>
    <table>
        <tr>
            <td>
<?= _("Exercice") ?>
            </td>
            <td>
<?php echo $exercice->input() ?>
            </td>
        </tr>
          <tr>
            <td>
<?= _("A partir du mois de") ?>
            </td>
            <td>
<?php echo $from->input() ?>
            </td>
        </tr>
        <tr>
            <td>
<?= _('Année') ?>
            </td>
            <td>
<?php echo $year->input() ?>
            </td>
        </tr>
        <tr>
            <td>
<?= _('Nombre de mois') ?>
            </td>
            <td>
<?php echo $nb_month->input() ?>
            </td>
        </tr>
      
        <tr>
            <td>
                <?=_("Réservé un jour pour l'ouverture (RAN)")?>
            </td>
            <td>
                <?=$day_opening->input();?>
            </td>
        </tr>
        <tr>
            <td>
                <?=_("Réservé un jour pour les opérations de fermeture ")?>
            </td>
            <td>
                <?=$day_closing->input();?>
            </td>
        </tr>
    </table>
    <?php
    echo HtmlInput::submit("add_exercicebt", _("Ajout d'un exercice comptable"));
    ?>
</form>
<script charset="UTF8" lang="javascript">
    function validate()
    {
        if (trim($('<?php echo $exercice->id ?>').value) == '') {
            $('<?php echo $exercice->id ?>').style.borderColor = 'red';
            smoke.alert('<?= _("Exercice invalide") ?>');
            return false;
        }
        if (trim($('<?php echo $nb_month->id ?>').value) == '') {
            $('<?php echo $nb_month->id ?>').style.borderColor = 'red';
            smoke.alert('<?= _("Nombre de mois invalide") ?>');
                    return false;
        }
        if (trim($('<?php echo $year->id ?>').value) == '') {
            $('<?php echo $year->id ?>').style.borderColor = 'red';
            smoke.alert('<?= _("Année invalide") ?>');
            return false;
        }
        if (trim($('<?php echo $nb_month->id ?>').value) > 60 
            ||trim($('<?php echo $nb_month->id ?>').value) < 1
           ) {
            $('<?php echo $nb_month->id ?>').style.borderColor = 'red';
            smoke.alert('<?= _("Nombre de mois possible entre 1 et 60") ?>');
                    return false;
        }
        return true;

    }
</script>
