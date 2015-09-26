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
 * \brief Input HTML for the card show buttons
 *
 */

/*!
 * \brief Input HTML for the card show buttons, in the file, you have to add card.js
 * How to use :
 * - label is the label in the button
 * - extra contents the type (all, deb or cred, a list of FD_ID between parent.  or a SQL clause
 * - attribute are the attribute to set (via ajax). The ledger is either a attribute (jrn) or a
 *  hidden field in the document, if none are set, there is no filter on the ledger
 *\note you must in a hidden field gDossier (dossier::hidden)
 *\see ajaxFid
 *\see card.js
 *\see fid.php
 *\see fid_card.php
 *\see ajax_card.php
 *
 * Set the hidden field or input field to be set by javascript with the function set_attribute
 * call the input method. After selecting a value the update_value function is called. If you need
 * to modify the queryString before the request is sent, you'll use the set_callback; the first
 * parameter is the INPUT field and the second the queryString, the function must returns a
 * queryString
 *\code
// insert all the javascript files
  echo js_include('prototype.js');
  echo js_include('scriptaculous.js');
  echo js_include('effects.js');
  echo js_include('controls.js');

//
  $W1=new ICard();
  $W1->label="Client ".HtmlInput::infobulle(0) ;
  $W1->name="e_client";
  $W1->tabindex=3;
  $W1->value=$e_client;
  $W1->table=0;
// If double click call the javascript fill_ipopcard
  $W1->set_dblclick("fill_ipopcard(this);");

  // Type of card : deb, cred or all
  $W1->set_attribute('typecard','deb');

  $W1->extra='deb';

// Add the callback function to filter the card on the jrn
  $W1->set_callback('filter_card');

// when value selected in the autcomplete
  $W1->set_function('fill_data');

// when the data change
  $W1->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
	    $W1->name);

 // name of the field to update with the name of the card
  $W1->set_attribute('label','e_client_label');
  $client_label=new ISpan();
  $client_label->table=0;
  $f_client=$client_label->input("e_client_label",$e_client_label);

  $f_client_qcode=$W1->input();

// Search button for card
  $f_client_bt=$W1->search();
* \endcode
For searching a card, you need a popup, the script card.js and set
the values for card, popup filter_card callback
@code
$card=new ICard('acc');
$card->name="acc";
$card->extra="all";
$card->set_attribute('typecard','all');
$card->set_callback('filter_card');

echo $card->input();
echo $card->search();
// example 2
$w=new ICard("av_text".$attr->ad_id);
// filter on frd_id
$sql=' select fd_id from fiche_def where frd_id in ('.FICHE_TYPE_CLIENT.','.FICHE_TYPE_FOURNISSEUR.','.FICHE_TYPE_ADM_TAX.')';
$filter=$this->cn->make_list($sql);
$w->set_attribute('ipopup','ipopcard');
$w->set_attribute('typecard',$filter);
$w->set_attribute('inp',"av_text".$attr->ad_id);
$w->set_attribute('label',"av_text".$attr->ad_id."_label");

$w->extra=$filter;
$w->extra2=0;
$label=new ISpan();
$label->name="av_text".$attr->ad_id."_label";
$msg.=td($w->search().$label->input());
@endcode
*/
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';

class ICard extends HtmlInput
{
    function __construct($name="",$value="",$p_id="")
    {
        parent::__construct($name,$value);
        $this->fct='update_value';
        $this->dblclick='';
        $this->callback='null';
        $this->javascript='';
	$this->id=($p_id != "")?$p_id:$name;
        $this->choice=null;
        $this->indicator=null;
        $this->choice_create=1;
	$this->autocomplete=1;
        $this->style=' style="vertical-align:50%"';
    }
    /*!\brief set the javascript callback function
     * by default it is update_value called BEFORE the querystring is send
     *
     *\param $p_name callback function name
     */
    function set_callback($p_name)
    {
        $this->callback=$p_name;
    }

    /*!\brief set the javascript callback function
     * by default it is update_value called AFTER an item has been selected
     *\param $p_name callback function name
     */
    function set_function($p_name)
    {
        $this->fct=$p_name;
    }
    /*!\brief return the html string for creating the ipopup, this ipopup
     * can be used for adding, modifying or display a card
     *@note ipopup is obsolete, the popin is created by javascript
     *\param $p_name name of the ipopup, must be set after with set_attribute
    \code
      $f_add_button=new IButton('add_card');
      $f_add_button->label='Créer une nouvelle fiche';
      $f_add_button->set_attribute('ipopup','ipop_newcard');
      $f_add_button->set_attribute('filter',$this->get_all_fiche_def ());
      $f_add_button->javascript=" select_card_type(this);";
      $str_add_button=$f_add_button->input();

    \endcode
     *\return html string
     *\note must be one of first instruction on a new page, to avoid problem
     * of position with IE
     */
    static function ipopup($p_name)
    {
        $ip_card=new IPopup ($p_name);
        $ip_card->drag=true;
		$ip_card->set_width('45%');
        $ip_card->title='Fiche ';
        $ip_card->value='';
        
        return $ip_card->input();
    }
    /*!\brief set the extra javascript property for a double click on
     *  INPUT field
     *\param $p_action action when a double click happens
     *\note the $p_action cannot contain a double quote
     */
    function set_dblclick($p_action)
    {
        $this->dblclick=$p_action;
    }
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        if ( $p_name == null && $this->name == "")
            throw (new Exception('Le nom d une icard doit être donne'));

        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();

		 $this->id=($this->id=="")?$this->name:$this->id;
        $this->choice=($this->choice==null)?sprintf("%s_choices",$this->id):$this->choice;
        $this->indicator=($this->indicator==null)?sprintf("%s_ind",$this->id):$this->indicator;
        $attr=$this->get_js_attr();

        $label='';
        if ( $this->dblclick != '')
        {
            $e=sprintf(' ondblclick="%s" ',
                       $this->dblclick);
            $this->dblclick=$e;
        }
        $input=sprintf('<INPUT TYPE="Text"  class="input_text"  '.
                       ' NAME="%s" ID="%s" VALUE="%s" SIZE="%d" %s %s  %s>',
                       $this->name,
                       $this->id,
                       $this->value,
                       $this->size,
                       $this->dblclick,
                       $this->javascript,
                       $this->style
                      );
		if ( $this->autocomplete == 1)
		{
                    $this->indicator="ind_".$this->id;
			$ind=sprintf('<span id="%s" class="autocomplete" style="position:absolute;display:none">Un instant... <img src="image/loading.gif" alt="Chargement..."/>'.
						'</span>',
						$this->indicator);
                        $this->indicator="null";
			$div=($this->choice_create == 1) ? sprintf('<div id="%s"  class="autocomplete"></div>',$this->choice):"";

			$query=dossier::get().'&e='.urlencode($this->typecard);

			$javascript=sprintf('try { new Ajax.Autocompleter("%s","%s","fid_card.php?%s",'.
								'{paramName:"FID",minChars:1,indicator:%s, '.
								'callback:%s, '.
								' afterUpdateElement:%s});} catch (e){alert(e.message);};',
								$this->id,
                                                                $this->choice,
                                                                $query,
								$this->indicator,
								$this->callback,
								$this->fct);

			$javascript=create_script($javascript.$this->dblclick);

			$r=$label.$input.$attr.$ind.$div.$javascript;
		}
		else
		{
			$r=$label.$input;
		}
        if ( $this->table == 1 )
            $r=td($r);
        return $r;

    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r=sprintf('         <INPUT TYPE="hidden" NAME="%s" id="%s" VALUE="%s" SIZE="8">',
                   $this->name,
                   $this->name,
                   $this->value
                  );
        $r.='<span>'.$this->value.'</span>';
        return $r;

    }
    /**
     *@brief return a string containing the button for displaying
     * a search form. When clicking on the result, update the input text file
     * the common used attribute as
     *   - jrn   the ledger
     *   - label the field to update
     *   - name name of the input text
     *   - price amount
     *   - tvaid
     *   - typecard (deb, cred, filter or list of value)
     * will be set
     * if ICard is in readOnly, the button disappears, so the return string is empty
    \code
      // search ipopup
    $search_card=new IPopup('ipop_card');
    $search_card->title=_('Recherche de fiche');
    $search_card->value='';
    echo $search_card->input();

    $a=new ICard('test');
    $a->search();

    \endcode
     *\see ajax_card.php
     *\note the ipopup id is hard coded : ipop_card
     *@return HTML string with the button
     */
    function search()
    {
        if ( $this->readOnly==true) return '';
		if ( ! isset($this->id )) $this->id=$this->name;
        $a="";
        foreach (array('typecard','jrn','label','price','tvaid') as $att)
        {
            if (isset($this->$att) )
                $a.="this.".$att."='".$this->$att."';";
        }
        if (isset($this->id) && $this->id != "")
            $a.="this.inp='".$this->id."';";
		else
            $a.="this.inp='".$this->name."';";
        $a.="this.popup='ipop_card';";
        $javascript=$a.' search_card(this);return false;';
        
        $button=HtmlInput::button_image($javascript,$this->name."_bt", 'alt="'._('Recherche').'" class="image_search"',"image/magnifier13.png");
        return $button;
    }

    static public function test_me()
    {
        require_once NOALYSS_INCLUDE.'/class_itext.php';
        $_SESSION['isValid']=1;
        $a=new ICard('testme');
        $a->extra="all";
        $a->set_attribute('label','ctl_label');
        $a->set_attribute('tvaid','ctl_tvaid');
        $a->set_attribute('price','ctl_price');
        $a->set_attribute('purchase','ctl_purchase');
        $a->set_attribute('type','all');
        echo <<<EOF
	  <div id="debug" style="border:solid 1px black;overflow:auto"></div>
	  <script type="text/javascript" language="javascript"  src="js/prototype.js">
	  </script>
	  <script type="text/javascript" language="javascript"  src="js/scriptaculous.js">
	  </script>
	  <script type="text/javascript" language="javascript"  src="js/effects.js">
	  </script>
	  <script type="text/javascript" language="javascript"  src="js/controls.js">
	  </script>
	  <script type="text/javascript" language="javascript"  src="js/ajax_fid.js">
	  </script>
	  <script type="text/javascript" language="javascript"  >
	  function test_value(text,li)
	  {
	    alert("premier"+li.id);

	    str="";
	    str=text.id+'<hr>';
	    if ( text.js_attr1)
	      {
		str+=text.js_attr1;
		str+='<hr>';
	      }
	    if ( text.js_attr2)
	      {
		str+=text.js_attr2;
		str+='<hr>';
	      }
	    if ( text.js_attr3)
	      {
		str+=text.js_attr3;
		str+='<hr>';
	      }
	    for (var i in text)
	      {
		str+=i+'<br>';
	      }

	    // $('debug').innerHTML=str;
	    ajaxFid(text);
	  }
	</script>

EOF;
        echo "<form>";
        $l=new IText('ctl_label');
        $t=new IText('ctl_tvaid');
        $p=new IText('ctl_price');
        $b=new IText('ctl_purchase');

        echo "Label ".$l->input().'<br>';
        echo "Tva id  ".$t->input().'<br>';
        echo "Price ".$p->input().'<br>';
        echo "Purchase ".$b->input().'<br>';

        if ( isset($_REQUEST['test_select']) )
            echo HtmlInput::hidden('test_select',$_REQUEST['test_select']);
        $a->set_function('test_value');
        $a->javascript=' onchange="alert(\'onchange\');" onblur="alert(\'onblur\');" ';
        echo $a->input();
        echo dossier::hidden();
        echo HtmlInput::submit('Entree','entree');
        echo '</form>';
        echo <<<EOF
EOF;
    }
}
