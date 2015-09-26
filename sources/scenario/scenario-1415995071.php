<?php 
//@description:VEN Appel menu vente
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/VENMENU/VEN',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ven.inc.php';
