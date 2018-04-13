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
 * @brief Manage the progress bar and display it with javascript
 *
 */

/**
 * @brief Use one db for tracking progress bar value, the task id must be unique
 * and let you follow the progress of a task.
 * how it works : when calling an ajax , you have to create the task id and start
 * the monitoring of it (js function = progress_bar_start).
 * In your php script called by ajax you call Progress_Bar->set_value to show 
 * the progress. The timer created by progress_bar_start will check regularly the
 * progress in the db.
 * The ajax parameter for following the task is task_id
 * 
 *@note you have to use session_write_close(); in the ajax file , otherwise, 
 * the function progress_bar_check will be blocked and won't update the progress
 * bar
 * 
 *@see progress_bar_start
 *@see progress_bar_check
 *
 * 
 */
class Progress_Bar 
{
    private $db ; //!< database connexion
    private $task_id ; //! task id (progress_bar.p_id)
    private $value; //!< value of progress (between 0 & 100)
    /**
     * @example progress-bar.test.php test of this class
     * @param type $p_task_id
     */
    function __construct($p_task_id)
    {
        $this->db=new Database();
        $this->task_id=$p_task_id;
        // Find value from db
        $this->value = $this->db->exec_sql("select p_value from progress where p_id=$1",
                [$p_task_id]);
        
        // if task doesn't exists, create it
        if ( $this->db->size()==0) 
        {
            $this->value=0;
            $this->db->exec_sql("insert into progress(p_id,p_value) values ($1,0)",
                    [$p_task_id]);
            $this->db->exec_sql("delete from progress where p_created < now() - interval '3 hours' ");
        }
    }
    /**
     * Store the progress value into the db
     * @param integer $p_value value of the progress between 0 & 100
     *@exceptions code 1005 - if p_value is not in between 0 & 100
     */
    function set_value($p_value) 
    {
        if ( $p_value > 100 || $p_value < 0 ) {
            throw new Exception("Invalid value",EXC_PARAM_VALUE);
        }
        $this->value=$p_value;
        $this->db->start();
        $this->db->exec_sql("update progress set p_value=$1 where p_id=$2",
                [$this->value,$this->task_id]);
        $this->db->commit();
    }
    /**
     * Get the progress value  from db
     * @return integer between 0 & 100
     */
    function get_value()
    {
        $this->value = $this->db->get_value("select p_value from progress where p_id=$1",
                [$this->task_id]);
        
        return $this->value;
    }
    /**
     * Json answer of the  task progressing 
     * if value is equal or greater than 100 , delete the row
     * @return type
     */
    function answer()
    {
        $this->get_value();
        
        header('Content-Type: application/json');
        echo json_encode(["value"=>$this->value]);
        if ($this->value>=100) {
            $this->db->exec_sql("delete from progress where p_id=$1",[$this->task_id]);
        }
        return;
    }
    /**
     * increment value with $p_step
     * @param int $p_step
     */
    function increment($p_step)
    {
        if ($this->value+$p_step > 100 ) {
            $this->set_value(100);
            return;
        }
        $this->set_value($this->value+$p_step);
    }
}
