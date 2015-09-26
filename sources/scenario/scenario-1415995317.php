<?php 
//@description:FIN Paiement
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUFIN/FIN',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_fin.inc.php';
