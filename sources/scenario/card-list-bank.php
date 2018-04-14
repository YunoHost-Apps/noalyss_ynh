<?php 
//@description:CARD Liste des banques
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/CARD',
  'cat' => '3',
  'histo' => '-1',
  'start' => '01.01.2014',
  'end' => '31.12.2014',
  'cat_display' => 'Recherche',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'fiche.inc.php';
