<?php

/* 
 * Copyright (C) 2018 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
/**
 * @file
 * @brief Example about Progress_Bar, the most important is the 
 * session_write_close in the ajax script to unblock the PHP script.
 * Each time a step is reached , you use Progress_Bar->set_value
 * @see Progress_Bar
 * @see progress_bar_start
 * @see progress_bar_check
 */
require_once NOALYSS_INCLUDE.'/lib/progress_bar.class.php';

$ajax=$http->request("TestAjaxFile","string","no");

// $ajax != no if we are in ajax mode
if ( $ajax != "no") 
{
    session_write_close();
    $task=$http->request("task_id");
    $progress=new Progress_Bar($task);
    sleep(1);
    $progress->set_value(10);
    sleep(2);
    $progress->set_value(20);
    sleep(1);
    $progress->set_value(90);
    sleep(2);
    $progress->set_value(91);
    sleep(5);
    $progress->set_value(95);
    sleep(6);
    $progress->set_value(100);
    return;
}
?>

<script>
    function start_test()
    {
        var task_id='<?php echo uniqid()?>';
        progress_bar_start(task_id);
        new Ajax.Request("ajax_test.php",{
            parameters:{"TestAjaxFile":"<?php echo __FILE__?>",gDossier:<?php echo Dossier::id()?>,'task_id':task_id}
        });
    }
</script>    
<button onclick="start_test()"> Start progress</button>