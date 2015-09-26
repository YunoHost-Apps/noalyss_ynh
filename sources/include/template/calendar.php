<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?>
<div class="pc_calendar" id="user_cal" style="width:100%">
<?php echo $month_year?>
<?php 
    $js=sprintf("calendar_zoom({gDossier:%d,invalue:'%s',outvalue:'%s',distype:'%s','notitle':%d})",
            dossier::id(),'per_div','calendar_zoom_div','list',$notitle);
    echo HtmlInput::anchor(_('Liste'),''," onclick=\"{$js}\"")   ;
    echo HtmlInput::button_action_add();
 ?>
    
<?php if ($zoom == 1 ): ?>    
<table style="width:100%;height:80%">
    <?php else: ?>
<table style="width:100%;">
    <?php endif; ?>
<tr>
<?php
for ($i=0;$i<=6;$i++){
	echo "<th>";
	echo $week[$i];
	echo "</th>";
}
?>
</tr>
<?php
$ind=1;
$week=0;
$today_month=date('m');
$today_day=date('j');
while ($ind <= $this->day) {
if ( $week == 0 ) echo "<tr>";
$class="workday";
if ( $week == 0 || $week == 6) $class="weekend";
// compute the date
$timestamp_date=mktime(0,0,0,$this->month,$ind,$this->year);
$date_calendar=date('w',$timestamp_date);
$st="";
if ( $today_month==$this->month && $today_day==$ind)
  $st='  style="border:1px solid red" ';
if ( $date_calendar == $week ) {
	echo '<td class="'.$class.'" '.$st.'>'.'<span class="day">'.$ind."</span>";
	echo $cell[$ind];
	echo '</td>';
	$ind++;$week++;
} else {
   echo "<td></td>";
   $week++;
}
//if ( $ind > $this->day ) exit();
if ( $week == 7 ) { echo "</tr>";$week=0;}
}
if ( $week != 0 ) { echo "</tr>";}
?>

</table>
</div>
