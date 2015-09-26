<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*!\file
 * \brief let you see the list of the connexion
 */
if ( !defined ('ALLOWED')) die('Forbidden');
?>
<DIV class="content" style="width:80%;margin-left:10%">
<span class="notice">Liste limitée aux 100 dernières connexions</span>
<?php

    $cn=new Database();
    $cn->exec_sql("select ac_user,ac_ip,to_char(ac_date,'DD.MM.YYYY HH24:MI') as fmt_date,ac_state,ac_module from audit_connect order by ac_date desc limit 100 ");
?>
<TABLE CLASS="result" style="border-collapse:separate;border-spacing:2">
<tr>
<th><?php echo _("Utilisateur");?> </th>
<th><?php echo _("Date");?> </th>
<th><?php echo _("Adresse");?> </th>
<th><?php echo _("Module");?></th>
<th><?php echo _("Résultat");?></th>
</tr>
<TR>
  <?php
  $max=$cn->count();
  for ($i=0;$i < $max ;$i++):
    $r=$cn->fetch($i);
?>
<td>
    <?php echo h($r['ac_user']);?>
</td>

<td>
<?php echo $r['fmt_date'];?>
</td>

<td>
<?php echo $r['ac_ip'];?>
</td>

<td>
<?php echo $r['ac_module'];?>
</td>


<?php 
switch ( $r['ac_state'] )
  {
  case 'FAIL';
  echo '<td style="background-color:red;color:white">';
  break;
  case 'SUCCESS';
  echo '<td style="background-color:green;color:white">';
  break;
  default:
	  echo '<td>';
  }
?>
<?php echo $r['ac_state']?>
</td>

</TR>
<?php 
  endfor;
?>

</DIV>
