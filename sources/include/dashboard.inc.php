<?php
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
/**
 *@file
 *@brief Dashboard
 */
require_once NOALYSS_INCLUDE.'/lib/idate.class.php';
require_once NOALYSS_INCLUDE.'/lib/itext.class.php';
require_once  NOALYSS_INCLUDE.'/constant.php';
require_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_report.class.php';
require_once NOALYSS_INCLUDE.'/class/periode.class.php';
require_once  NOALYSS_INCLUDE.'/lib/user_menu.php';
require_once  NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/class/todo_list.class.php';
require_once NOALYSS_INCLUDE.'/lib/itextarea.class.php';
require_once NOALYSS_INCLUDE.'/class/calendar.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger.class.php';
require_once NOALYSS_INCLUDE.'/class/follow_up.class.php';

echo '<div class="content">';
global $g_user;
/* others report */
$cal=new Calendar();
$cal->get_preference();

$obj=sprintf("{gDossier:%d,invalue:'%s',outdiv:'%s','distype':'%s'}",
        dossier::id(),'per','calendar_zoom_div','cal');
$Operation=new Follow_Up($cn);
$last_operation=$Operation->get_today();
$late_operation=$Operation->get_late();

$Ledger=new Acc_Ledger($cn,0);
$last_ledger=array();
$last_ledger=$Ledger->get_last(20);

// Supplier late and now
$supplier_now=$Ledger->get_supplier_now();
$supplier_late=$Ledger->get_supplier_late();

// Customer late and now
$customer_now=$Ledger->get_customer_now();
$customer_late=$Ledger->get_customer_late();

ob_start();
require_once NOALYSS_TEMPLATE.'/dashboard.php';
$ret=ob_get_contents();
ob_end_clean();
echo $ret;

echo '</div>';
?>
