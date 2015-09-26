<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt

/**
 * @file
 * @brief show the calendar
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/class_calendar.php';
$cal=new Calendar();
$cal->default_periode=(isset ($_GET['in']))?$_GET['in']:$g_user->get_periode();

?>
<div id="calendar_zoom_div">
    
<?php echo $cal->display('long',1); ?>
</div>