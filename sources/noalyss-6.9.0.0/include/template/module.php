<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div id="top">
      <div id="dossier">
	<?php echo h(dossier::name())?>
	</div>
    <div style="clear:both;"></div>
    <div class="name">

<?php

if ( $cn->get_value("select count(*) from profile join profile_user using (p_id)
		where user_name=$1 and with_calc=true",array($_SESSION['g_user'])) ==1):
  echo '<div id="calc">';
	echo IButton::show_calc();
echo '</div>';
endif;

if ( $cn->get_value("select count(*) from profile join profile_user using (p_id)
		where user_name=$1 and with_direct_form=true",array($_SESSION['g_user'])) ==1):
?>
	<div id="direct">
	<form method="get">
		<?php echo HtmlInput::default_value('ac', '', $_REQUEST)?>
		<?php echo Dossier::hidden()?>
		<?php 

			$direct=new IText('ac');
			$direct->style='class="input_text"';
			$direct->value='';
			$direct->size=20;
			echo $direct->input();
			$gDossier=dossier::id();
			?>
		<div id="ac_choices" class="autocomplete" style="width:150"></div>
		<?php 
			echo HtmlInput::submit('go',_('Aller'));
			?>

	</form>
	<script>

		try {
			new Ajax.Autocompleter("ac","ac_choices","direct.php?gDossier=<?php echo $gDossier?>",
                            {paramName:"acs",minChars:1,indicator:null,
                            callback:null,
                             afterUpdateElement:null});} catch (e){$('info_div').innerHTML=e.message;};
        </script>
	</div>
<?php 
endif;?>
	
    </div>

    <div id="module">
	<table>
	    <tr>
		<?php
		foreach ($amodule as $row):
			$js="";
		    $style="";
		    if ( $row['me_code']=='new_line')
		    {
			echo "</tr><tr>";
			continue;
		    }
                    $style="tool";
		    if ($row['me_code']==$selected_module)
		    {
			$style='toolselected';
		    }
		    if ( $row['me_url']!='')
		    {
			$url=$row['me_url'];
		    }
		    elseif ($row['me_javascript'] != '')
			{
				$url="javascript:void(0)";
                                $js_dossier=str_replace('<DOSSIER>', Dossier::id(), $row['me_javascript']);
				$js=sprintf(' onclick="%s"',$js_dossier);
			}
			else
		    {
				$url="do.php?gDossier=".Dossier::id()."&ac=".$row['me_code'];
		    }
		    ?>
		<td class="<?php echo $style?>">
                    <a class="mtitle" href="<?php echo $url?>" title="<?php echo _($row['me_description'])?>" <?php echo $js?> ><?php echo gettext($row['me_menu'])?></a></td>
		<?php 
		    endforeach;
		?>
	    </tr>
	</table>

    </div>
  
</div>
<div style="clear:both;"></div>
