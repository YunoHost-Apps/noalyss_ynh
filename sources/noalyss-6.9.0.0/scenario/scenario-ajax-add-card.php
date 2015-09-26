<?php 
//@description:sc Ajout d'une fiche
$_GET=array (
);
$_POST=array (
  'gDossier' => '42',
  'ctl' => 'div_new_card',
  'fd_id' => '3',
  'av_text1' => 'caisse',
  'av_text3' => '',
  'av_text4' => '',
  'av_text12' => '',
  'av_text5' => '',
  'av_text5_bt' => 'Recherche',
  'av_text13' => '',
  'av_text14' => '',
  'av_text15' => '',
  'av_text16' => '',
  'av_text17' => '',
  'av_text18' => '',
  'av_text23' => '',
  'sc' => 'Sauve',
  'op' => 'sc',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
 ini_set('disable_functions', 'exit,die,header');
include 'ajax_card.php';
?>
