<?php 
//@description:COMPANY Sauve donnée companie
$_GET=array (
  'gDossier' => '42',
  'ac' => 'PARAM/COMPANY',
);
$_POST=array (
  'gDossier' => '42',
  'p_name' => 'NOALYSS',
  'p_tel' => '',
  'p_fax' => '',
  'p_street' => 'Rue de l\'espoir',
  'p_no' => '14',
  'p_cp' => '1090',
  'p_Commune' => 'Jette',
  'p_pays' => 'Belgique',
  'p_tva' => 'BE99999999',
  'p_compta' => 'nu',
  'p_stock' => 'N',
  'p_strict' => 'Y',
  'p_tva_use' => 'Y',
  'p_pj' => 'Y',
  'p_date_suggest' => 'Y',
  'p_check_periode' => 'N',
  'p_alphanum' => 'N',
  'p_updlab' => 'N',
  'record_company' => 'Sauve',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'company.inc.php';
