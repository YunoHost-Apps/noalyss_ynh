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
 * \brief contains HTML features
 */

class Html_Table
{
  /**
   * Receives a SQL command and returns a string with the HTML code
   * to display it as a table.
   * Simple table without any feature (link in certain cell, sort,...)
   * @param $cn database object
   * @param $a_col header of the column it is an array of array
   *        indexes are link, name,image, style
   * @param $sql query to execute
   * @param $table_style style of the table
   * @parm $a_sql_var array variable for the $sql DEFAULT NULL
@code
 static function test_me()
  {
    $cn=new Database(Dossier::id());
    $order=" order by f_id desc ";
    $url=HtmlInput::get_to_string(array("gDossier","test_select"));

    if ( isset($_GET['sb']))
      {
	$order=" order by f_id";
	$img="image/select1.gif";
      }
    else
      {
	$url=$url."&sb=as";
	$img="image/select2.gif";
      }
    $sql="select f_id,name,quick_code from vw_client  $order limit 10";
    echo $sql;


    echo Html_Table::sql2table($cn,
			       array(
				     array('name'=>'N° de fiche',
					   'style'=>'text-align:right',
					   'link'=>$url,
					   'image'=>$img),
				     array('name'=>'Nom',
					   'style'=>'text-align:right'),
				     array('name'=>'QuickCode')
				     )
			       ,
			       $sql
			       );
  }
@endcode
   */
  static function sql2table($cn,$a_col,$sql,$table_style='class="result"',$a_sql_var=null)
  {
    $r='';
    $r=sprintf('<table %s>',$table_style);
    $r.='<tr>';
    for ( $i=0;$i <count($a_col);$i++)
      {
	$content=h($a_col[$i]['name']);
	$style=(isset($a_col[$i]['style']))?$a_col[$i]['style']:"";
	if ( isset($a_col[$i]['image']) && $a_col[$i]['image'] != '')
	  {
	    $content=sprintf('<img src="%s" border="0"></img>%s',$a_col[$i]['image'],$content);
	  }
	if ( isset($a_col[$i]['link']) )
	  {
	    $content=sprintf('<a href="%s">%s</a>',
			     $a_col[$i]['link'],
			     $content);
	    $r.="<th $style>$content</th>";
	  }
	else
	  $r.= "<th $style>". h($content)."</th>";
      }
    $r.='</tr>';
    $ret=$cn->exec_sql($sql,$a_sql_var);
    for ($i=0;$i<Database::num_row($ret);$i++)
      {
	$r.='<tr>';
	$row=Database::fetch_row($ret,$i);
	for ($e=0;$e<count($row);$e++)
	  {
	    $style='';$content=h($row[$e]);

	    if ( isset($a_col[$e]['style']) )
	      $style=$a_col[$e]['style'];
		if  ( isset ($a_col[$e]['raw']))
			$r.='<td $style>'.$row[$e].'</td>';
		else
			$r.=td($content,$style);
	  }
	$r.='</tr>';
      }
    $r.='</table>';
    return $r;
  }
  static function test_me()
  {
    $cn=new Database(Dossier::id());
    $order=" order by f_id desc ";
    $url=HtmlInput::get_to_string(array("gDossier","test_select"));

    if ( isset($_GET['sb']))
      {
	$order=" order by f_id";
	$img="image/select1.gif";
      }
    else
      {
	$url=$url."&sb=as";
	$img="image/select2.gif";
      }
    $sql="select f_id,name,quick_code from vw_client  $order limit 10";
    echo $sql;


    echo Html_Table::sql2table($cn,
			       array(
				     array('name'=>'N° de fiche',
					   'style'=>'text-align:right',
					   'link'=>$url,
					   'image'=>$img),
				     array('name'=>'Nom',
					   'style'=>'text-align:right'),
				     array('name'=>'QuickCode')
				     )
			       ,
			       $sql
			       );
  }
}