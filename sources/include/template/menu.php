<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div class="<?php echo $style_menu; ?>">
    <?php if ( count($amenu) > 4 && $level == 0) :
	$style ='style= "width:100%"';
     elseif ($level==0):
switch (count($amenu))
{
case 4:
case 3:
   $width=count($amenu)*20;
   $left=round((100-$width)/2);
$style="style=\"width:$width%;margin-left:$left%\"";
break;
default:
$style="";
}
	else:
		$style=" class=\"mtitle\"";

    	endif;?>
<table  <?php echo $style?> >


    <tr>
	<?php
	global $g_user;
	// Display the menu
	for($i=0;$i < count($amenu);$i++):
	    if ( (count($amenu)==1)):
		$class="selectedcell";
?>
	<td class="<?php echo $class?>">
            <a class="mtitle" href="do.php?gDossier=<?php echo Dossier::id()?>&ac=<?php echo $_REQUEST['ac']?>" title="<?php echo h(gettext($amenu[$i]['me_description']))?>" >
	    <?php echo gettext($amenu[$i]['me_menu'])?>
	    </a>
	</td>
<?php 
	    else:
		$class="mtitle";
		$js="";
                
		if ( $amenu[$i]['me_url']!='')
		{
			$url=$amenu[$i]['me_url'];
		}
		elseif ($amenu[$i]['me_javascript'] != '')
		{
			$url="javascript:void(0)";
			$js=sprintf(' onclick="%s"',$amenu[$i]['me_javascript']);
		}
		else
		{
                    $a_request=explode('/', $_REQUEST['ac']);
                    if ( $level == 0) {
                        $url=$a_request[0];
                        
                        if (count($a_request) > 1 &&
                            $url.'/'.$amenu[$i]['me_code'] == $a_request[0].'/'.$a_request[1]) 
                                $class="selectedcell";
                    } elseif ($level == 1)
                    {
                        $url=$a_request[0].'/'.$a_request[1];
                    }
                    $url.='/'.$amenu[$i]['me_code'];
                    if ($url == $_REQUEST['ac']) $class="selectedcell";
                    $url="do.php?gDossier=".Dossier::id()."&ac=".$url;
		}

?>	<td class="<?php echo $class?>">
	    <a class="mtitle" href="<?php echo $url;?>" <?php echo $js?> title="<?php echo h(gettext($amenu[$i]['me_description']))?>">
	    <?php echo gettext($amenu[$i]['me_menu'])?>
	    </a>
	</td>


<?php 
endif;

	?>
	<?php 
	    endfor;
    	?>
    </tr>


</table>
</div>
