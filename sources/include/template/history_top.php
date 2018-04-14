<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div class="bxbutton">
<?php
   if ($div != "popup")
   {
     $callback=$_SERVER['PHP_SELF'];
     $str=$_SERVER['QUERY_STRING']."&op=history&act=de&ajax=$callback";
     
     echo '<A class="icon" HREF="javascript:void(0)" onclick="var a=window.open(\'popup.php?'.$str.'\',\'\',\'fullscreen=yes,location=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,statusbar=no,menubar=no,status=no,location=no\'); a.focus();removeDiv(\''.$div.'\')">&#xf08e;
</A>';
    echo Icon_Action::draggable($div);
    echo Icon_Action::close($div);
}
?>
</div>
