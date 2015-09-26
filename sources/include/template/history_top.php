<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div class="bxbutton">
<?php
   if ($div != "popup")
   {
     $callback=$_SERVER['PHP_SELF'];
     $str=$_SERVER['QUERY_STRING']."&act=de&ajax=$callback";
     echo '<A id="close_div" HREF="javascript:void(0)" onclick="var a=window.open(\'popup.php?'.$str.'\',\'\',\'fullscreen=yes,location=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,statusbar=no,menubar=no,status=no,location=no\'); a.focus();removeDiv(\''.$div.'\')">&#11036
</A>';
echo '<A id="close_div" HREF="javascript:void(0)" onclick="removeDiv(\''.$div.'\');">&#10761;</A>';
}
?>
</div>
