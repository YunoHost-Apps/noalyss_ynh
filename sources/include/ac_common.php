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

/**
 * @file
 * @brief common utilities for a lot of procedure, classe
 */

require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';

/**
 * \brief to protect again bad characters which can lead to a cross scripting attack
  the string to be diplayed must be protected
 */

function h($p_string)
{
    return htmlspecialchars($p_string);
}

function span($p_string, $p_extra='')
{
    return '<span ' . $p_extra . '>' . $p_string . '</span>';
}

function hi($p_string)
{
    return '<i>' . htmlspecialchars($p_string) . '</i>';
}

function hb($p_string)
{
    return '<b>' . htmlspecialchars($p_string) . '</b>';
}

function th($p_string, $p_extra='',$raw='')
{
    return '<th  ' . $p_extra . '>' . htmlspecialchars($p_string).$raw . '</th>';
}

function h2info($p_string)
{
    return '<h2 class="info">' . htmlspecialchars($p_string) . '</h2>';
}

function h2($p_string, $p_class="",$raw="")
{
    return '<h2 ' . $p_class . '>' . $raw.htmlspecialchars($p_string) . '</h2>';
}
function h1($p_string, $p_class="")
{
    return '<h1 ' . $p_class . '>' . htmlspecialchars($p_string) . '</h1>';
}
/**
 * \brief surround the string with td
 * \param $p_string string to surround by TD
 * \param $p_extra extra info (class, style, javascript...)
 * \return string surrounded by td
 */

function td($p_string='', $p_extra='')
{
    return '<td  ' . $p_extra . '>' . $p_string . '</td>';
}

function tr($p_string, $p_extra='')
{
    return '<tr  ' . $p_extra . '>' . $p_string . '</tr>';
}

/**
 * @brief escape correctly php string to javascript 
 */
function j($p_string)
{
    $a = preg_replace("/\r?\n/", "\\n", addslashes($p_string));
    $a = str_replace("'", '\'', $a);
    return $a;
}

/**
 * format the number for the CSV export
 * @param $p_number number
 */
function nb($p_number)
{
    $r = sprintf('%.2f', $p_number);
    $r = str_replace('.', ',', $r);

    return $r;
}

/**
 * format the number with a sep. for the thousand
 * @param $p_number number
 */
function nbm($p_number)
{

    if (trim($p_number) == '')
	return '';
    if ($p_number == 0)
	return "0,00";
    
    $a = doubleval($p_number);
    $r = number_format($a, 2, ",", ".");
    if (trim($r) == '')
    {
	var_dump($r);
	var_dump($p_number);
	var_dump($a);
	exit();
    }

    return $r;
}

/**
 * \brief  log error into the /tmp/noalyss_error.log it doesn't work on windows
 *
 * \param p_log message
 * \param p_line line number
 * \param p_message is the message
 *
 * \return nothing
 *
 */

function echo_error($p_log, $p_line="", $p_message="")
{
    echo "ERREUR :" . $p_log . " " . $p_line . " " . $p_message;
    $fdebug = fopen($_ENV['TMP'] . DIRECTORY_SEPARATOR . "noalyss_error.log", "a+");
    if ($fdebug != null)
    {
	fwrite($fdebug, date("Ymd H:i:s") . $p_log . " " . $p_line . " " . $p_message . "\n");
	fclose($fdebug);
    }
}

/**
 * \brief  Compare 2 dates
 * \param p_date
 * \param p_date_oth
 *
 * \return
 *      - == 0 les dates sont identiques
 *      - > 0 date1 > date2
 *      - < 0 date1 < date2
 */

function cmpDate($p_date, $p_date_oth)
{
    date_default_timezone_set('Europe/Brussels');

    $l_date = isDate($p_date);
    $l2_date = isDate($p_date_oth);
    if ($l_date == null || $l2_date == null)
    {
	throw new Exception("erreur date [$p_date] [$p_date_oth]");
    }
    $l_adate = explode(".", $l_date);
    $l2_adate = explode(".", $l2_date);
    $l_mkdate = mktime(0, 0, 0, $l_adate[1], $l_adate[0], $l_adate[2]);
    $l2_mkdate = mktime(0, 0, 0, $l2_adate[1], $l2_adate[0], $l2_adate[2]);
    // si $p_date > $p_date_oth return > 0
    return $l_mkdate - $l2_mkdate;
}

/***!
 * @brief check if the argument is a number
 *
 * \param $p_int number to test
 *
 * \return
 *        - 1 it's a number
 *        - 0 it is not
 */
function isNumber(&$p_int)
{
    if (strlen(trim($p_int)) == 0)
	return 0;
    if (is_numeric($p_int) === true)
	return 1;
    else
	return 0;
}

/***
 * \brief Verifie qu'une date est bien formaté
 *           en d.m.y et est valable
 * \param $p_date
 *
 * \return
 * 	- null si la date est invalide ou malformaté
 *      - $p_date si tout est bon
 *
 */

function isDate($p_date)
{
    if (strlen(trim($p_date)) == 0)
	return null;
    if (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.20[0-9]{2}$/", $p_date) == 0)
    {

	return null;
    }
    else
    {
	$l_date = explode(".", $p_date);

	if (sizeof($l_date) != 3)
	    return null;

	if ($l_date[2] > COMPTA_MAX_YEAR || $l_date[2] < COMPTA_MIN_YEAR)
	{
	    return null;
	}

	if (checkdate($l_date[1], $l_date[0], $l_date[2]) == false)
	{
	    return null;
	}
    }
    return $p_date;
}

/**
 * \brief Default page header for each page
 *
 * \param p_theme default theme
 * \param $p_script
 * \param $p_script2  another js script
 * Must be called only once
 * \return none
 */

function html_page_start($p_theme="", $p_script="", $p_script2="")
{
    // check not called twiced
    static  $already_call=0;
    if ( $already_call==1)return;
    $already_call=1;

    $cn = new Database();
    if ($p_theme != "")
    {
	$Res = $cn->exec_sql("select the_filestyle from theme
                           where the_name='" . $p_theme . "'");
	if (Database::num_row($Res) == 0)
	    $style = "style-classic.css";
	else
	{
	    $s = Database::fetch_array($Res, 0);
	    $style = $s['the_filestyle'];
	}
    }
    else
    {
	$style = "style-classic.css";
    } // end if
	$title="NOALYSS";

	if ( isset ($_REQUEST['ac'])) {
		if (strpos($_REQUEST['ac'],'/') <> 0)
		{
			$m=  explode('/',$_REQUEST['ac']);
			$title=$m[count($m)-1]."  ".$title;
		}
		else
			$title=$_REQUEST['ac']."  ".$title;
	}
    $is_msie=is_msie();
    
    if ($is_msie == 0 ) 
    {
        echo '<!doctype html>';
        printf("\n");
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        printf("\n");
    }
    else {
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 FINAL//EN" >';
        printf("\n");
    }
    echo "<HTML>";

    if ($p_script2 != "")
	$p_script2 = '<script src="' . $p_script2 . '?version='.SVNINFO.'" type="text/javascript"></script>';
    $style=trim($style);
    echo "<HEAD>";
    if ( $is_msie == 1 )echo '      <meta http-equiv="x-ua-compatible" content="IE=edge"/>';
    echo "
    <TITLE>$title</TITLE>
	<link rel=\"icon\" type=\"image/ico\" href=\"favicon.ico\" />
    <META http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
    <LINK REL=\"stylesheet\" type=\"text/css\" href=\"".$style."?version=".SVNINFO."\" media=\"screen\"/>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"./style-print.css?version=".SVNINFO."\" media=\"print\"/>" .
    $p_script2 . "
    ";
    echo '<script language="javascript" src="js/calendar.js"></script>
    <script type="text/javascript" src="js/lang/calendar-en.js"></script>
    <script language="javascript" src="js/calendar-setup.js"></script>
    <LINK REL="stylesheet" type="text/css" href="./calendar-blue.css" media="screen">
    ';
    echo load_all_script();
    echo '    </HEAD>    ';

    echo "<BODY $p_script>";
    echo '<div id="info_div"></div>';
    echo '<div id="error_div">'.
            HtmlInput::title_box(_("Erreur"), 'error_div','hide').
            '<div id="error_content_div">'.
            '</div>'.
            '<p style="text-align:center">'.
            HtmlInput::button_action('Valider','$(\'error_div\').style.visibility=\'hidden\';$(\'error_content_div\').innerHTML=\'\';').
            '</p>'.
            '</div>';
// language
    if (isset($_SESSION['g_lang']))
    {
		set_language();
    }

}

/**
 * \brief Minimal  page header for each page, used for small popup window
 *
 * \param p_theme default theme
 * \param $p_script
 * \param $p_script2  another js script
 *
 * \return none
 */

function html_min_page_start($p_theme="", $p_script="", $p_script2="")
{

    $cn = new Database();
    if ($p_theme != "")
    {
	$Res = $cn->exec_sql("select the_filestyle from theme
                           where the_name='" . $p_theme . "'");
	if (Database::num_row($Res) == 0)
	    $style = "style-classic.css";
	else
	{
	    $s = Database::fetch_array($Res, 0);
	    $style = $s['the_filestyle'];
	}
    }
    else
    {
	$style = "style-classic.css";
    } // end if
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 FINAL//EN">';
    echo "<HTML>";


    if ($p_script2 != "")
	$p_script2 = '<script src="' . $p_script2 . '" type="text/javascript"></script>';

    echo "<HEAD>
    <TITLE>NOALYSS</TITLE>
    <META http-equiv=\"Content-Type\" content=\"text/html; charset=UTF8\">
    <LINK REL=\"stylesheet\" type=\"text/css\" href=\"$style\" media=\"screen\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"style-print.css\" media=\"print\">" .
    $p_script2 . "
    <script src=\"js/scripts.js\" type=\"text/javascript\"></script>
    <script src=\"js/acc_ledger.js\" type=\"text/javascript\"></script>
    <script src=\"js/smoke.js\" type=\"text/javascript\"></script>";
    echo '</HEAD>
    ';

    echo "<BODY $p_script>";
    /* If we are on the user_login page */
    if (basename($_SERVER['PHP_SELF']) == 'user_login.php')
    {
	return;
    }
}

/**
 * \brief end tag
 *
 */

function html_page_stop()
{
    echo "</BODY>";
    echo "</HTML>";
}

/**
 * \brief Echo no access and stop
 *
 * \return nothing
 */

function NoAccess($js=1)
{
    if ($js == 1)
    {
	echo "<script>";
	echo "alert ('" . _('Cette action ne vous est pas autorisée Contactez votre responsable') . "');";
	echo "</script>";
    }
    else
    {
	echo '<div class="redcontent">';
	echo '<h2 class="error">' . _(' Cette action ne vous est pas autorisée Contactez votre responsable') . '</h2>';
	echo '</div>';
    }
    exit - 1;
}
/**
 * replaced by sql_string
 * @deprecated
 */
function FormatString($p_string)
{
    return sql_string($p_string);
}
/**
 * \brief Fix the problem with the quote char for the database
 *
 * \param $p_string
 * \return a string which won't let strange char for the database
 */

function sql_string($p_string)
{
    $p_string = trim($p_string);
    if (strlen($p_string) == 0)
	return null;
    $p_string = str_replace("'", "''", $p_string);
    $p_string = str_replace('\\', '\\\\', $p_string);
    return $p_string;
}

/**
  /* \brief store the string which print
 *           the content of p_array in a table
 *           used to display the menu
 * \param  $p_array array like ( 0=>HREF reference, 1=>visible item (name),2=>Help(opt),
 * 3=>selected (opt) 4=>javascript (normally a onclick event) (opt)
 * \param $p_dir direction of the menu (H Horizontal  V vertical)
 * \param $class CSS for TD tag
 * \param $class_ref CSS for the A tag
 * \param $default selected item
 * \param $p_extra extra code for the table tag (CSS or javascript)
 *
  /* \return : string */

function ShowItem($p_array, $p_dir='V', $class="mtitle", $class_ref="mtitle", $default="", $p_extra="")
{

    $ret = "<TABLE $p_extra>";
    // direction Vertical
    if ($p_dir == 'V')
    {
	foreach ($p_array as $all => $href)
	{
	    $javascript = (isset($href[4])) ? $href[4] : "";
	    $title = "";
	    $set = "XX";
	    if (isset($href[2]))
		$title = $href[2];
	    if (isset($href[3]))
		$set = $href[3];

	    if ($set == $default)
		$ret.='<TR><TD CLASS="selectedcell"><A class="' . $class_ref . '" HREF="' . $href[0] . '" title="' . $title . '" ' . $javascript . '>' . $href[1] . '</A></TD></TR>';
	    else
		$ret.='<TR><TD CLASS="' . $class . '"><A class="' . $class_ref . '" HREF="' . $href[0] . '" title="' . $title . '" ' . $javascript . '>' . $href[1] . '</A></TD></TR>';
	}
    }
    //direction Horizontal
    else if ($p_dir == 'H')
    {

	$ret.="<TR>";
	foreach ($p_array as $all => $href)
	{
	    $title = "";
	    $javascript = (isset($href[4])) ? $href[4] : "";

	    $set = "A";
	    if (isset($href[2]))
		$title = $href[2];

	    if (isset($href[3]))
		$set = $href[3];

	    if ($default === $href[0] || $set === $default)
	    {
		$ret.='<TD CLASS="selectedcell"><A class="' . $class_ref . '" HREF="' . $href[0] . '" title="' . $title . '" ' . $javascript . '>' . $href[1] . '</A></TD>';
	    }
	    else
	    {
		$ret.='<TD CLASS="' . $class . '"><A class="' . $class_ref . '" HREF="' . $href[0] . '" title="' . $title . '" ' . $javascript . '>' . $href[1] . '</A></TD>';
	    }
	}
	$ret.="</TR>";
    }
    $ret.="</TABLE>";
    return $ret;
}

/**
 * \brief warns
 *
 * \param p_string error message
 * gen :
 * 	- none
 * \return:
 *      - none
 */

function echo_warning($p_string)
{
    echo '<H2 class="error">' . $p_string . "</H2>";
}

/**
 * \brief Show the periode which found thanks its id
 *
 *
 * \param  $p_cn database connection
 * \param p_id
 * \param pos Start or end
 *
 * \return: string
 */

function getPeriodeName($p_cn, $p_id, $pos='p_start')
{
    if ($pos != 'p_start' and
	    $pos != 'p_end')
	echo_error('ac_common.php' . "-" . __LINE__ . '  UNDEFINED PERIODE');
    $ret = $p_cn->get_value("select to_char($pos,'Mon YYYY') as t from parm_periode where p_id=$p_id");
    return $ret;
}

/**
 * \brief Return the period corresponding to the
 *           date
 *
 * \param p_cn database connection
 * \param p_date the month + year 'MM.YYYY'
 *
 * \return:
 *       parm_periode.p_id
 */

function getPeriodeFromMonth($p_cn, $p_date)
{
    $R = $p_cn->get_value("select p_id from parm_periode where
                        to_char(p_start,'DD.MM.YYYY') = '01.$p_date'");
    if ($R == "")
	return -1;
    return $R;
}

/**\brief Decode the html for the widegt richtext and remove newline
 * \param $p_html string to decode
 * \return the html code without new line
 */

function Decode($p_html)
{
    $p_html = str_replace('%0D', '', $p_html);
    $p_html = str_replace('%0A', '', $p_html);
    $p_html = urldecode($p_html);
    return $p_html;
}

/**\brief Create the condition to filter on the j_tech_per
 *        thanks a from and to date.
 * \param $p_cn database conx
 * \param $p_from start date (date)
 * \param $p_to  end date (date)
 * \param $p_form if the p_from and p_to are date or p_id
 * \param $p_field column name
 * \return a string containg the query
 */

function sql_filter_per($p_cn, $p_from, $p_to, $p_form='p_id', $p_field='jr_tech_per')
{

    if ($p_form != 'p_id' &&
	    $p_form != 'date')
    {
	echo_error(__FILE__, __LINE__, 'Mauvais parametres ');
	exit(-1);
    }
    if ($p_form == 'p_id')
    {
	// retrieve the date
	$pPeriode = new Periode($p_cn);
	$a_start = $pPeriode->get_date_limit($p_from);
	$a_end = $pPeriode->get_date_limit($p_to);
	if ($a_start == null || $a_end == null)
	    throw new Exception(__FILE__ . __LINE__ . sprintf(_('Attention periode 
		     non trouvee periode p_from= %s p_to_periode = %s'), $p_from ,
		    $p_to));


	$p_from = $a_start['p_start'];
	$p_to = $a_end['p_end'];
    }
    if ($p_from == $p_to)
	$periode = " $p_field = (select p_id from parm_periode " .
		" where " .
		" p_start = to_date('$p_from','DD.MM.YYYY')) ";
    else
	$periode = "$p_field in (select p_id from parm_periode " .
		" where p_start >= to_date('$p_from','DD.MM.YYYY') and p_end <= to_date('$p_to','DD.MM.YYYY')) ";
    return $periode;
}

/**\brief alert in javascript
 * \param $p_msg is the message
 * \param $buffer if false, echo directly and execute the javascript, if $buffer is true, the alert javascript
 * is in the return string
 * \return string with alert javascript if $buffer is true
 */

function alert($p_msg, $buffer=false)
{
    $r = '<script>';
    $r.= 'alert_box(\'' . j($p_msg) . '\')';
    $r.= '</script>';

    if ($buffer)
	return $r;
    echo $r;
}

/**
 * @brief set the lang thanks the _SESSION['g_lang'] var.
 */
function set_language()
{
    // desactivate local check
    if ( defined("LOCALE") && LOCALE==0 ) return;
    if ( ! isset ($_SESSION['g_lang'])) return;
    $dir = "";
    // set differently the language depending of the operating system
    if (what_os() == 1)
    {
	$dir = setlocale(LC_MESSAGES, $_SESSION['g_lang']);
	if ($dir == "")
	{
	    $g_lang = 'fr_FR.utf8';
	    $dir = setlocale(LC_MESSAGES, $g_lang);
	   // echo '<span class="notice">' . $_SESSION['g_lang'] . ' domaine non supporté</h2>';
	}
	bindtextdomain('messages', './lang');
	textdomain('messages');
	bind_textdomain_codeset('messages', 'UTF8');

	return;
    }
    // for windows
    putenv('LANG=' . $_SESSION['g_lang']);
    $dir = setlocale(LC_ALL, $_SESSION['g_lang']);
    bindtextdomain('messages', '.\\lang');
    textdomain('messages');
    bind_textdomain_codeset('messages', 'UTF8');
}

/**
 * @brief try to determine on what os you are running the pĥpcompte
 * server
 * @return
 *  0 it is a windows
 *  1 it is a Unix like
 */
function what_os()
{
    $inc_path = get_include_path();

    if (strpos($inc_path, ";") != 0)
    {
	$os = 0;   /* $os is 0 for windoz */
    }
    else
    {
	$os = 1;   /* $os is 1 for unix */
    }
    return $os;
}

/**
 * @brief shrink the date, make a date shorter for the printing
 * @param $p_date format DD.MM.YYYY
 * @return date in the format DDMMYY (size = 13 mm in arial 8)
 */
function shrink_date($p_date)
{
    $date = str_replace('.', '', $p_date);
    $str_date = substr($date, 0, 4) . substr($date, 6, 2);
    return $str_date;
}
/**
 * @brief shrink the date, make a date shorter for the printing
 * @param $p_date format DD.MM.YYYY
 * @return date in the format DDMMYY (size = 13 mm in arial 8)
 */
function smaller_date($p_date)
{
    $str_date = substr($p_date, 0, 6) . substr($p_date, 8, 2);
    return $str_date;
}

/**
 * @brief format the date, when taken from the database the format
 * is MM-DD-YYYY
 * @param $p_date format
 * @param
 * @return date in the format DD.MM.YYYY
 */
function format_date($p_date, $p_from_format = 'YYYY-MM-DD',$p_to_format='DD.MM.YYYY')
{
    if ($p_from_format == 'YYYY-MM-DD')
    {
        $date = explode('-', $p_date);
        if (count($date) != 3)
            return $p_date;
    }
    if ($p_from_format == 'DD.MM.YYYY')
    {
        $temp_date = explode('.', $p_date);
        if (count($temp_date) != 3)
            return $p_date;
        $date[0] = $temp_date[2]; // 0 is year
        $date[1] = $temp_date[1]; // 1 for month
        $date[2] = $temp_date[0]; // 2 for day
    }

    switch ($p_to_format)
    {
        case 'DD.MM.YYYY':
            $str_date = $date[2] . '.' . $date[1] . '.' . $date[0];
            break;
        case 'YYYY-MM-DD':
            $str_date = $date[0] . '-' . $date[1] . '-' . $date[2];
            break;
       case 'YYYYMMDD':
            $str_date = $date[0] . $date[1] . $date[2];
            break;
		 case 'YYYY/MM/DD':
            $str_date = $date[0] . '/' . $date[1] . '/' . $date[2];
            break;

		}
    return $str_date;
}



/**
 * Should a dialog box when you are disconnected from an ajax call
 * propose to reload or to connect in another tab
 */
function ajax_disconnected($div)
{
    /**
     * if $_SESSION['g_user'] is not set : echo a warning
     */
    if (!isset($_SESSION['g_user']))
    {
	$script = 'var a=$("' . $div . '");a.style.height="70%";a.style.width="60%";';
	$script.='a.style.top=posY-20+offsetY;a.style.left=posX+offsetX;';
	$script = create_script($script);
	$html = $script;
	$html.=HtmlInput::anchor_close($div);
	$html.='<div>';
	$html.=h2(_('Données non disponibles'), 'class="title" style="width:auto"');
	$html.=h2(_('Veuillez vous reconnecter soit dans une autre fenêtre soit '
                . ' en cliquant sur le lien'), 'class="error"');
        // Reload button
        $reload=new IButton("reload");
        $reload->value=_("Se reconnecter pour revenir ici");
        $reload->class="button";
        $reload->javascript='window.location.reload()';
        // Link to log in another tab
        $html.='<p style="text-align:center">';
        $html.='<a href="index.php" class="button" target="_blank">'.
                _('Cliquez ici pour vous reconnecter dans une autre page').
                '</a>';
        $html.=$reload->input();
        $html.='</p>';
	$html = escape_xml($html);
	header('Content-type: text/xml; charset=UTF-8');
	echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$div</ctl>
<code>$html</code>
</data>
EOF;
	exit();
    }
}

/**
 *Show the modules
 * @param int $selected module selected profile_menu.pm_id
 */
function show_module($selected)
{
    global $g_user;
    $cn = Dossier::connect();
    $amodule = $cn->get_array("select
	me_code,me_menu,me_url,me_javascript,p_order,me_type,me_description
	from v_all_menu
	where
	p_id=$1
	and p_type_display='M'
	order by p_order", array($g_user->get_profile()));

    if ($selected != -1)
    {
        $selected_module=$cn->get_value('select me_code from profile_menu where'
                . ' pm_id = $1 ', array($selected));
	require_once NOALYSS_INCLUDE.'/template/module.php';
	$file = $cn->get_array("select me_file,me_parameter,me_javascript,me_type,me_description from v_all_menu
	    where pm_id=$1 and p_id=$2", array($selected,$g_user->get_profile()));
	if ( count($file ) == 0 )
	{
		echo '</div>';
		echo '</div>';
		echo '<div class="content">';
		echo_warning(_("Module inexistant")."[ $selected ] ");
		echo '</div>';
		exit();
	}
	if ($file[0]['me_file'] != '')
	{
	    if ($file[0]['me_parameter'] != "")
	    {
		// if there are paramter put them in superglobal
		$array=compute_variable($file[0]['me_parameter']);
		put_global($array);
	    }

		// if file is not a plugin, include the file, otherwise
		// include the plugin launcher
		if ($file[0]['me_type'] != 'PL')
			{
				require_once $file[0]['me_file'];
			}
			else
			{
				// nothing  : direct call to plugin
			}
	}
	if ( $file[0]['me_javascript'] != '')
	{
		create_script($file[0]['me_javascript']);
	}
    }
}
/**
 * Find the default module or the first one
 * @global $g_user $g_user
 * @return default module (string)
 */
function find_default_module()
{
    global $g_user;
    $cn = Dossier::connect();

    $default_module = $cn->get_array("select me_code
	    from profile_menu join profile_user using (p_id)
	    where
	    p_type_display='M' and
	    user_name=$1 and pm_default=1", array($g_user->login));

	/*
	 * Try to find the smallest order for module
	 */
    if (empty($default_module))
    {
		$default_module = $cn->get_array("select me_code
	    from profile_menu join profile_user using (p_id)
	    where
	    p_type_display='M' and
	    user_name=$1 order by p_order limit 1", array($g_user->login));

		// if no default try to find the default menu
		if ( empty ($default_module))
		{
			$default_module = $cn->get_array("select me_code
			 from profile_menu join profile_user using (p_id)
			   where
			   p_type_display='E' and
			   user_name=$1 and pm_default=1 ", array($g_user->login));
			/*
			 * Try to find a default menu by order
			 */
			if (empty ($default_module))
			{
				$default_module = $cn->get_array("select me_code
				from profile_menu join profile_user using (p_id)
				where
				user_name=$1 and p_order=(select min(p_order) from profile_menu join profile_user using (p_id)
				where user_name=$2) limit 1", array($g_user->login, $g_user->login));
			}

			/*
			* if nothing found, there is no profile for this user => exit
			*/
			if (empty ($default_module))
			{
                            /* 
                             * If administrateur, then we insert a default profile (1)
                             * for him
                             */
                            if ( $g_user->admin == 1 )
                            {
                                $cn->exec_sql('insert into profile_user(user_name,p_id) values ($1,1) ',array($g_user->login));
                                return find_default_module();
                            }
                            echo_warning(_("Utilisateur n'a pas de profil, votre administrateur doit en configurer un dans CFGSEC"));
                            exit();
			}
		}
		return $default_module[0]['me_code'];
    }

    if (count($default_module) > 1)
    {
		// return the first module found
		return $default_module[0]['me_code'];
    }
    elseif (count($default_module) == 1)
    {
	return $default_module[0]['me_code'];
    }
}

/**
 * show the module
 * @global $g_user
 * @param $module the $_REQUEST['ac'] exploded into an array
 * @param  $idx the index of the array : the AD code is splitted into an array thanks the slash
 */
function show_menu($module)
{
    if ($module == 0)return;
    static $level=0;
    global $g_user;
    
    $cn = Dossier::connect();
    /**
     * Show the submenus
     */
    $amenu = $cn->get_array("
        select 
            pm_id,
            me_code,
            pm_id_dep,
            me_file,
            me_javascript,
            me_url,
            me_menu,
            me_description,
            me_description_etendue
            from profile_menu 
            join menu_ref using (me_code) 
            where pm_id_dep=$1 and p_id=$2
	 order by p_order", array($module, $g_user->get_profile()));
    
    // There are submenuS, so show them
    if (!empty($amenu) && count($amenu) > 1)
    {
        $a_style_menu=array('topmenu','menu2','menu3');
        if ( $level > count($a_style_menu))
            $style_menu='menu3';
        else {
            $style_menu=$a_style_menu[$level];
        }
		require 'template/menu.php';
    } // there is only one submenu so we include the code or javascript 
      // or we show the submenu
    elseif (count($amenu) == 1)
    {
        if ( trim($amenu[0]['me_url']) != "" ||
             trim ($amenu[0]['me_file']) != "" ||
             trim ($amenu[0]['me_javascript']) != "" )
        {
		echo '<div class="topmenu">';
		echo h2info(_($amenu[0]['me_menu']));
		echo '</div>';
		$module = $amenu[0]['pm_id'];
        } else {
           $url=$_REQUEST['ac'].'/'.$amenu[0]['me_code'];
           echo '<a href="do.php?gDossier='.Dossier::id().'&ac='.$url.'">';
           echo _($amenu[0]['me_menu']);
           echo '</a>';
           $level++;
           return;
        }
    }
    
    // There is no submenu or only one
    if (empty($amenu) || count($amenu) == 1)
    {
		$file = $cn->get_array("select me_file,me_parameter,me_javascript,me_type
		from menu_ref
		join profile_menu using (me_code)
		join profile_user using (p_id)
		where
		pm_id=$1 and
		user_name=$2 and
		(me_file is not null or trim(me_file) <>'' or
		me_javascript is not null or trim (me_javascript) <> '')", array($module,$g_user->login));

		if (count($file)==0)
		{
                        return;
		}

		if ($file[0]['me_file'] != "")
		{
			if ($file[0]['me_parameter'] !== "")
			{
				// if there are paramter put them in superglobal
				$array=compute_variable($file[0]['me_parameter']);
				put_global($array);
			}
                        if ( DEBUG ) echo  $file[0]['me_file']," param : ",$file[0]['me_parameter'] ;
                        /*
                         * Log the file we input to put in the folder test-noalyss for replaying it
                         */
                        if (LOGINPUT) {
                                $file_loginput=fopen($_ENV['TMP'].'/scenario-'.$_SERVER['REQUEST_TIME'].'.php','a+');
                                fwrite($file_loginput, "include '".$file[0]['me_file']."';");
                                fwrite($file_loginput,"\n");
                                fclose($file_loginput);
                        }
			// if file is not a plugin, include the file, otherwise
			// include the plugin launcher
			if ( $file[0]['me_type'] != 'PL')
				require_once $file[0]['me_file'];
			else
				require 'extension_get.inc.php';

			exit();
		}
		if ( $file[0]['me_javascript'] != '')
		{
                    $js=  str_replace('<DOSSIER>', dossier::id(), $file[0]['me_javascript']);
                    echo create_script($js);
		}
    }
    $level++;
}
/**
 * Put in superglobal (get,post,request) the value contained in
 * the parameter field (me_parameter)
 * @param $array [key] [value]
 */
function put_global($array)
{
    for ($i=0;$i<count($array);$i++)
    {
	$key=$array[$i]['key'];
	$value=$array[$i]['value'];
	$_GET[$key]=$value;
	$_POST[$key]=$value;
	$_REQUEST[$key]=$value;
    }
}
/**
 * the string has the format a=b&c=d, it is parsed and an array[][key,value]
 * is returned
 * @param $p_string
 * @return $array usable in put_global
 */
function compute_variable($p_string)
{
    $array=array();
    if ($p_string == '') return $array;

    $var=explode("&",$p_string);
    if (empty ($var))	return $array;
    for ($i=0;$i < count($var);$i++)
    {
	$var2=explode('=',$var[$i]);
	$array[$i]['key']=$var2[0];
	$array[$i]['value']=$var2[1];
    }
    return $array;
}
function ajax_xml_error($p_code,$p_string)
{
    $html = escape_xml($p_string);
    header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$p_code</code>
<value>$html</value>
</data>
EOF;
}
/**
 * @brief Display a box with the contains
 * @param type $p_array Data to display
 * @param type $p_title Title of the box
 * @param type $p_div id of the box
 */
function display_dashboard_operation($p_array,$p_title,$p_div)
{
	?>
<div id="<?php echo $p_div;?>" class="inner_box" style="display:none;position:fixed;top:250px;left:12%;width: 75%;min-height:50%;overflow:auto;">
	<?php
	echo HtmlInput::title_box($p_title, $p_div, "hide");
	?>
	<?php if (count($p_array)>0) :?>
	<table class="result">
		<tr>
			<th><?php echo _('Date')?></th>
			<th><?php echo _('Code Interne')?></th>
			<th><?php echo _('Pièce')?></th>
			<th><?php echo _('Description')?></th>
			<th>
				<?php echo _('Montant')?>
			</th>

		</tr>
		<?php
			for ($i=0;$i<count($p_array);$i++):
		?>
		<tr class="<?php echo (($i%2)==0)?'odd':'even';?>">
			<td>
				<?php echo smaller_date(format_date($p_array[$i]['jr_date']) );?>
			</td>
			<td>
				<?php echo HtmlInput::detail_op($p_array[$i]['jr_id'], $p_array[$i]['jr_internal']) ?>
			</td>
                        <td>
                            <?php echo h($p_array[$i]['jr_pj_number'])?>
                        </td>
			<td>
				<?php echo h($p_array[$i]['jr_comment']) ?>
			</td>
			<td>
				<?php echo nbm($p_array[$i]['jr_montant']) ?>
			</td>
		</tr>
		<?php
		endfor;
		?>
	</table>
	<?php else: ?>
	<h2 class="notice"><?php echo _('Aucune donnée')?></h2>
	<?php
	endif;
	?>
</div>
<?php
}
function get_array_column($p_array,$key)
{
    $array=array();
    for ($i=0;$i<count($p_array);$i++)
    {
        $r=$p_array[$i];
        if ( isset($r[$key])) {
            $array[]=$r[$key];
        }
    }
    return $array;
}

/**
 * This function create a ledger object and return the right one.
 * It uses the factory pattern
 * @param Database $p_cn
 * @param type $ledger_id 
 * @return Acc_Ledger
 * @throws Exception
 */
function factory_Ledger(Database &$p_cn, $ledger_id)
{
    include_once 'class_acc_ledger_sold.php';
    include_once 'class_acc_ledger_purchase.php';
    include_once 'class_acc_ledger_fin.php';
    
    $ledger=new Acc_Ledger($p_cn, $ledger_id);
    $type=$ledger->get_type();

    switch ($type)
    {
        case 'VEN':
            $obj=new Acc_Ledger_Sold($p_cn, $ledger_id);
            break;
        case 'ACH':
            $obj=new Acc_Ledger_Purchase($p_cn, $ledger_id);
            break;
        case 'FIN':
            $obj= new Acc_Ledger_Fin($p_cn, $ledger_id);
            break;
        case 'ODS':
            $obj=$ledger;
            break;

        default:
            throw new Exception('Ledger type not found');
    }
    return $obj;
}
/**
 * Check if we use IE 8 or 9
 * @return int 1 for IE8-9;0 otherwise
 */
function is_msie()
{
    if ( strpos ($_SERVER['HTTP_USER_AGENT'],'MSIE 8.0')  != 0 ||
         strpos ($_SERVER['HTTP_USER_AGENT'],'MSIE 9.0')  != 0 )
       $is_msie=1;
    else
        $is_msie=0;
    return $is_msie;
}
?>