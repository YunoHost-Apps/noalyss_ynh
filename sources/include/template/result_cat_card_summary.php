<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div class="content">
<?php echo _("Filtre")?> :    
    <?php
    $col="";$sp="";
    for ($e=0;$e<count($aHeading);$e++) {$col.=$sp.$e; $sp=",";}
    echo HtmlInput::filter_table("fiche_tb_id", $col, '1'); 
    ?>
<table id="fiche_tb_id" class="sortable">
<tr>
<?php 
   echo th(_('Détail'));
for ($i=0;$i<count($aHeading);$i++) :
    $span="";$sort="";
   if ($i==0)
   {
       $span='<span id="sorttable_sortfwdind">&nbsp;&nbsp;&#x25BE;</span>';
       $sort= 'class="sorttable_sorted"';
   }
   echo '<th '.$sort.'>'.$aHeading[$i]->ad_text.$span.'</th>';
   endfor;
?>
</tr>
<?php 
$e=0;
foreach ($array as $row ) :
 $e++;
   if ($e%2==0)
   echo '<tr class="odd">';
   else
   echo '<tr class="even">';
   $fiche=new Fiche($cn);
   $fiche->id=$row['f_id'];
 $fiche->getAttribut();
$detail=HtmlInput::card_detail($fiche->strAttribut(ATTR_DEF_QUICKCODE));
echo td($detail);
 foreach($fiche->attribut as $attr) :
         $sort="";
         
	 if ( $attr->ad_type != 'select'):
                if ($attr->ad_type=="date") :
                    // format YYYYMMDD
                    $sort='sorttable_customkey="'.format_date($attr->av_text, "DD.MM.YYYY", "YYYYMMDD").'"'; 
                endif;
        	echo td($attr->av_text,'style="padding: 0 10 1 10;white-space:nowrap;" '.$sort);
	 else:
		$value=$cn->make_array($attr->ad_extra);
                $row_content="";
                for ($e=0;$e<count($value);$e++):
                        if ( $value[$e]['value']==$attr->av_text):
                                $row_content=h($value[$e]['label']);
                                break;
                        endif;
                endfor;
                echo td($row_content,'style="padding: 0 10 1 10;white-space:nowrap;"');

	 endif;
 endforeach;
 echo '</tr>';
endforeach;

?>
</table>



</div>
