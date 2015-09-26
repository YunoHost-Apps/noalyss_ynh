<?php 
//@description:MENUFIN
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUFIN',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
