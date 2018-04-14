<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<div id="security_action_tbl">
    
    <?php

	function display_security_fieldset($p_legend,$p_array,$sec_User) {

	$gDossier=dossier::id();
	?>
<fieldset><legend><?php echo $p_legend;?></legend>
	<TABLE >

		<?php
			foreach  ( $p_array as $l_line){
			?>
		<tr>
			<td align="right">
				<?php echo $l_line['ac_description'];?>
			</td>

			<?php
				$right=$sec_User->check_action($l_line['ac_id']);
                                $is_switch=new Inplace_Switch(sprintf('action%d',$l_line['ac_id']),$right);
                                $is_switch->set_callback("ajax_misc.php");
                                $is_switch->add_json_param("op", "action_access");
                                $is_switch->add_json_param("gDossier",$gDossier);
                                $is_switch->add_json_param("ac_id",$l_line['ac_id']);
                                $is_switch->add_json_param("user_id",$sec_User->id);
                                ?>
			<td >
                            

			<?php  echo $is_switch->input();  ?>
                         
			</td>
		</tr>
		<?php
} // end loop

			?>
	</table>
</fieldset>
<?php

}// end function

?>
<?php  
// Security Card
$array=$cn->get_array("select ac_id, ac_description from action  where ac_id >=$1 and ac_id <=$2 order by ac_id ",
    array(800,1000));
    display_security_fieldset(_('Fiche'),$array,$sec_User); ?>
<?php   
// Security follow-up
$array=$cn->get_array("select ac_id, ac_description from action  where ac_id >=$1 and ac_id <=$2 order by ac_id ",
    array(1001,1100));
    display_security_fieldset(_('Suivi'),$array,$sec_User); ?>

<?php
// Security Accountancy
 $array=$cn->get_array("select ac_id, ac_description from action  where ac_id >=$1 and ac_id <=$2 order by ac_id ",
    array(1101,1200));
    display_security_fieldset(_('Comptabilité'),$array,$sec_User); ?>

<?php
// Note Sharing
 $array=$cn->get_array("select ac_id, ac_description from action  where ac_id >=$1 and ac_id <=$2 order by ac_id ",
    array(1200,1300));
    display_security_fieldset(_('Note'),$array,$sec_User); 
?>
</div>