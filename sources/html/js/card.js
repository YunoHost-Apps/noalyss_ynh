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
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*! \file
 * \brief javascript for searching a card
 */

var card_layer=1;
/**
 *@brief search a card an display the result into a inner box
 */
function boxsearch_card(p_dossier)
{
	try
	{
	waiting_box();
	removeDiv('boxsearch_card_div');
	var queryString="gDossier="+p_dossier+"&op=cardsearch"+"&card="+encodeURI($(card_search).value);
	var action = new Ajax.Request(
				  "ajax_misc.php" ,
				  {
				      method:'get', parameters:queryString,
				      onFailure:ajax_misc_failure,
				      onSuccess:function(req){
						remove_waiting_box();
						var y=posY+15;
						var div_style="left:10%;width:80%;"+";top:"+y+"px";
						add_div({id:'boxsearch_card_div',cssclass:'inner_box',html:loading(),style:div_style,drag:true});
						$('boxsearch_card_div').innerHTML=req.responseText;
						sorttable.makeSortable($('tb_fiche'));
				      }
				  }
				  );
	}catch( e)
	{
		alert_box(e.message);
	}
}
/**
 *@brief show the ipopup with the form to search a card
 * the properties
 *  - jrn for the ledger
 *  - fs for the action
 *  - price for the price of the card (field to update)
 *  - tvaid for the tvaid of the card (field to update)
 *  - inp input text to update with the quickcode
 *  - label field to update with the name
 *  - ctl the id to fill with the HTML answer (ending with _content)
 *  - acc 1 if accounting are visible
 */
function search_card(obj)
{
    try
    {
        var gDossier=$('gDossier').value;
        var inp=obj.inp;
        var string_to_search=$(inp).value;
        var label=obj.label;
        var typecard=obj.typecard;
        var price=obj.price;
        var tvaid=obj.tvaid;
        var jrn=obj.jrn;
        if ( jrn==undefined)
        {
            if ( g('p_jrn'))   {
		jrn=$('p_jrn').value;
	    }
            else 	    {
		jrn=-1;
	    }
        }
        var accvis=obj.accvis;
        if ( accvis == undefined ) {
            accvis=0;
        } else {
            accvis=obj.accvis;
        }
	var query=encodeJSON({'gDossier':gDossier,
                      'inp':inp,'label':label,'price':price,'tvaid':tvaid,
                      'ctl':'search_card','op2':'fs','jrn':jrn,
                      'typecard':typecard,'query':string_to_search,'op':'card',
                      'accvis':accvis
                             });
	if (  $('search_card') ) {
	    removeDiv('search_card');
	}
	
	
        waiting_box();
	

        var action=new Ajax.Request ( 'ajax_misc.php',
                                      {
                                  method:'get',
                                  parameters:query,
                                  onFailure:errorFid,
                                  onSuccess:result_card_search
                                      }
                                    );
    }
    catch(e)
    {
        alert_box('search_card failed'+e.message);
    }
}
/**
 *@brief Display form for select card to add to action : other_concerned
 *action_add_concerned_card
 */
function action_add_concerned_card(obj)
{
    try
    {
        var dossier = 0;
        var inp="";
        var ag_id=0;
        
        if (obj.dossier) {
            dossier = obj.dossier; /* From the button */
        } 
        if (obj.ag_id) {
            ag_id=obj.ag_id;
        }
        /* from the form */
        if (obj.elements) {
            if (obj.elements['gDossier']) 
            {
                dossier = obj.elements['gDossier'].value;
            }

            if (obj.elements['query']) {
                inp = obj.elements['query'].value;
            }

            if (obj.elements['ag_id']) {
                ag_id = obj.elements['ag_id'].value;
            }
        }
        if (dossier == 0) {
            throw "obj.dossier not found";
        }
        if (ag_id == 0) {
            throw "obj.ag_id not found";
        }
        var query = encodeJSON({
            'gDossier': dossier,
            'op2': 'action_add_concerned_card',
            'query' : inp,
            'ctl' : 'unused',
            'ag_id' : ag_id,
            'op':'card',
            'accvis':0
        });

        waiting_box();


        var action = new Ajax.Request('ajax_misc.php',
                {
                    method: 'get',
                    parameters: query,
                    onFailure: errorFid,
                    onSuccess: function (req, txt)
                    {
                        try {
                        remove_waiting_box();
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        if (a.length == 0)
                        {
                            var rec = req.responseText;
                            alert_box('erreur :' + rec);
                        }
                        var html = answer.getElementsByTagName('code');
                        var namectl = a[0].firstChild.nodeValue;
                        var nodeXml = html[0];
                        var code_html = getNodeText(nodeXml);
                        code_html = unescape_xml(code_html);

                        var sx = 0;
                        if (window.scrollY)
                        {
                            sx = window.scrollY + 40;
                        }
                        else
                        {
                            sx = document.body.scrollTop + 60;
                        }
                        var div_style = "top:" + sx + "px;height:80%";
                        if ( ! $('search_card')) { add_div({id: 'search_card', cssclass: 'inner_box', html: "", style: div_style, drag: true}); }
                        $('search_card').innerHTML = code_html;
                        $('query').focus();
                        }catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        );
    }
    catch (e)
    {
        alert_box('search_card failed' + e.message);
        return false;
    }
    return false;
}

/**
 *@brief when you submit the form for searching a card
 *@param obj form
 *@note the same as search_card, except it answer to a FORM and not
 * to a click event
 */
function search_get_card(obj)
{
    var dossier=$('gDossier').value;

    var queryString="gDossier="+dossier;
    queryString+="&op2=fs&op=card";

    if ( obj.elements['inp'] )
    {
        queryString+="&inp="+$F('inp');
    }
    if ( obj.elements['typecard'] )
    {
        queryString+="&typecard="+$F('typecard');
    }
    if ( obj.elements['jrn'] )
    {
        queryString+="&jrn="+$F('jrn');
    }
    if ( obj.elements['label'])
    {
        queryString+="&label="+$F('label');
    }
    if ( obj.elements['price'])
    {
        queryString+="&price="+$F('price');
    }
    if ( obj.elements['tvaid'])
    {
        queryString+="&tvaid="+$F('tvaid');
    }
    if( obj.elements['query'])
    {
        queryString+="&query="+$F('query');
    }
    if (obj.ctl )
    {
        queryString+="&ctl="+obj.ctl;
    }
    if ( obj.elements['accvis'] ) 
    {
        queryString+="&accvis="+$F(accvis);
    } else {
        queryString+="&accvis=0";
    }
    $('asearch').innerHTML=loading();
    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:result_card_search
                                  }
                                );
}
/**
 *@brief show the answer of ajax request
 *@param  answer in XML
 */
function result_card_search(req)
{
    try
    {
        
        remove_waiting_box();
        var answer=req.responseXML;
        var a=answer.getElementsByTagName('ctl');
        if ( a.length == 0 )
        {
            var rec=req.responseText;
            alert_box ('erreur :'+rec);
        }
        var html=answer.getElementsByTagName('code');

        var name_ctl=a[0].firstChild.nodeValue;
        var nodeXml=html[0];
        var code_html=getNodeText(nodeXml);
        code_html=unescape_xml(code_html);
        
        var sx=0;
	if ( window.scrollY)
	{
            sx=window.scrollY+40;
	}
	else
	{
            sx=document.body.scrollTop+60;
	}

        var div_style="top:"+sx+"px;height:auto";
        add_div({id:'search_card',cssclass:'inner_box',html:"",style:div_style,drag:false,effect:'blinddown'});
        
        $('search_card').innerHTML=code_html;
        
        if ($('query')) { $('query').focus();}
    }
    catch (e)
    {
        alert_box(e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch(e)
    {
        alert_box("Impossible executer script de la reponse\n"+e.message);
    }

}



/*!\brief Set the value of 2 input fields
*
* Set the quick code in the first ctrl and the label of the quickcode in the second one. This function is a variant of SetData for
* some specific need.  This function is called if the caller is searchcardCtrl
*
*\param p_ctrl the input with the name of the quick code
*\param  p_quickcode the found quick_code
*\param p_ctrlname the name of the input field with the label
*\param p_label the label of the quickcode
*/
function setCtrl(p_ctrl,p_quickcode,p_ctrlname,p_label)
{
    var ctrl=g(p_ctrl);
    if ( ctrl )
    {
        ctrl.value=p_quickcode;
    }
    var ctrl_name=g(p_ctrlname);
    if ( ctrl_name )
    {
        ctrl_name.value=p_label;
    }
}



/*!\brief clean the row (the label, price and vat)
 * \param p_ctl the calling ctrl
 */
function clean_Fid(p_ctl)
{
    nSell=p_ctl+"_price";
    nBuy=p_ctl+"_price";
    nTva_id=p_ctl+"_tva_id";
    if ( $(nSell) )
    {
        $(nSell).value="";
    }
    if ( $(nBuy) )
    {
        $(nBuy).value="";
    }
    if ( $(nTva_id) )
    {
        $(nTva_id).value="-1";
    }

}
function errorFid(request,json)
{
    alert_box('erreur : ajax fiche');
}
function update_value(text,li)
{
	   ajaxFid(text);
}
/**
 *@brief is called when something change in ICard
 *@param the input field
 *@see ICard
 */
function fill_data_onchange(ctl)
{
    ajaxFid(ctl);

}
/**
 *@brief is called when something change in ICard
 *@param the input field
 *@see ICard
 */
function fill_data(text,li)
{
    ajaxFid(text);

}
/**
 *@brief is called when something change in ICard
 *@param the input field
 *@see ICard
 */
function fill_fin_data_onchange(ctl)
{
    ajaxFid(ctl);
    ajax_saldo(ctl.id);
}
/**
 *@brief is called when something change in ICard
 *@param the input field
 *@see ICard
 */
function fill_fin_data(text,li)
{
    ajaxFid(text);
    ajax_saldo($(text.id));
}
/**
 *@brief show the ipopup window and display the details of a card,
 * to work some attribute must be set
 *@param obj.qcode is the qcode, obj.nohistory if you don't want to  display
 * the history button, obj.ro is the popin is readonly
 *@note you must the gDossier as hidden in the calling page
 *
 *@see ajax_card.php
 */
function fill_ipopcard(obj)
{

    card_layer++;

    var content='card_'+card_layer;
    var nTop=170+card_layer;
    if ( nTop > 300 ) {
        nTop=170;
    }
    var str_top=fixed_position(250,nTop)
    var str_style=str_top+";width:45em;height:auto;position:absolute";

    var popup={'id':  content,'cssclass':'inner_box','style':str_style,'html':loading(),'drag':false};

    add_div(popup);
    var dossier=$('gDossier').value;
    var qcode='';
    if ( $(obj).qcode != undefined )
    {
        qcode=obj.qcode;
    }
    else
    {
        qcode=$(obj).value;
    }
    //    ctl=$(obj).id;

    var queryString='gDossier='+dossier;
    queryString+='&qcode='+qcode;
    queryString+='&ctl='+content;
    queryString+='&op2=dc'; 	// dc for detail card
    queryString+='&op=card'; 	// dc for detail card
    if ( obj.readonly != undefined) {
     queryString+='&ro';
    }

    if ( obj.nohistory != undefined) {
     queryString+='&nohistory';
    }
    queryString=encodeURI(queryString);
    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:fill_box
                                  }
                                );
}
/**
 *@brief
 * \param request : object request
 * \param json : json answer
\code
\endcode
*/
function  successFill_ipopcard(req,json)
{
    try
    {
        var answer=req.responseXML;
        var a=answer.getElementsByTagName('ctl');
        var html=answer.getElementsByTagName('code');

        if ( a.length == 0 )
        {
            var rec=req.responseText;
            alert_box ('erreur :'+rec);
        }
        var name_ctl=a[0].firstChild.nodeValue;
        var code_html=getNodeText(html[0]);
        code_html=unescape_xml(code_html);

        $(name_ctl).innerHTML=code_html;
    }
    catch (e)
    {
        alert_box(e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch(e)
    {
        alert_box("Impossible executer script de la reponse\n"+e.message);
    }
}
/**
 *@brief show the ipopup for selecting a card type, it is a needed step before adding
 * a card
 *@param input field (obj) it must have the attribute ipopup
 *       possible attribute :
 *        - filter possible values are deb, cred, fd_id list, -1 means there  is no filter
 *        - ref if we want to refresh the window after adding a card
 *        - type type of card (supplier, customer...)
 *@see ajax_card.php
 */
function select_card_type(obj)
{

    var dossier=$('gDossier').value;
    var elementId="";
    // give a filter, -1 if not
    var filter=$(obj).filter;
    if ( filter==undefined)
    {
        filter=-1;
    }
    var content="select_card_div";
    if ( $(content)){removeDiv(content);}
    var sx=0;
    sx=calcy(160);

    var str_style="top:"+sx+"px;height:auto";
    waiting_box();
    var popup={'id':  content,'cssclass':'inner_box','style':str_style,'html':"",'drag':false};

    add_div(popup);

    var queryString='gDossier='+dossier;
    queryString+='&ctl='+content;
    queryString+='&op2=st'; 	// st for selecting type
    queryString+='&op=card'; 	// st for selecting type
    if ( $(obj).win_refresh!=undefined)
    {
        queryString+='&ref';
    }
    /* if an element id must be updated after creating a new card */
    if ( $(obj).elementId) {
        var elementId=$(obj).elementId;
        queryString+="&eltid="+elementId;
    }
    queryString+='&fil='+filter;
    // filter on the ledger, -1 if not
    var oledger=$(obj).jrn;
    if (oledger==undefined)
    {
        ledger=-1;
    }
    else
    {
        ledger=$(obj).jrn;
    }

    queryString+='&ledger='+ledger;

    if ( obj.type_cat)
    {
        queryString+='&cat='+obj.type_cat;
    }

    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:function(req) { 
                                      // Get all the category, 
                                      var answer=req.responseXML.getElementsByTagName("fiche_cat_item");
                                      if (answer.length == 0) {
                                          removeDiv(content);
                                          remove_waiting_box();
                                          alert_box(getNodeText(req.responseXML.getElementsByTagName("code")[0]));
                                          return;
                                      }
                                      if ( answer.length == 1) {
                                          // There is only one category of card
                                          dis_blank_card({"ctl":"div_new_card","fd_id":answer[0].firstChild.nodeValue,"op2":"bc","op":"card",gDossier:dossier,"elementId":elementId});
                                          removeDiv(content);
                                          remove_waiting_box();
                                          return;
                                      }
                                      fill_box(req);
                                       $('lk_cat_card_table').focus();
                                    }
                                  }
                                );
}
/**
 *@brief Show a blank card
 *@param obj Form object (obj)
 *       possible attribute :
 *        - filter is the filter but with a  fd_id list, -1 means there  is no filter
 *        - ref : reload the window after adding card
 *        - content : name of the div
 *@note dis_blank_card({gDossier:15,fd_id:12,ref:1});
 *@see ajax_card.php
 */
function dis_blank_card(obj)
{
    // first we have to take the form elt we need
    if ( obj.fd_id.value != undefined )
		{ var fd_id=$F('fd_id'); }
	else {fd_id=obj.fd_id;}

    var ref="";
    if ( obj.elements &&  obj.elements['ref'] )
    {
        ref='&ref';
    }
    var content='div_new_card';
    var nTop=calcy(150);
    var nLeft=posX;
    var str_style="top:"+nTop+"px;right:"+nLeft+"px;height:auto";

    var popup={'id':  content,'cssclass':'inner_box','style':str_style,'html':loading(),'drag':false};
  
    add_div(popup);

    if ( obj.gDossier.value != undefined ) {
        var dossier=$('gDossier').value;
    } else {
	var dossier=obj.gDossier;
    }

    var queryString='gDossier='+dossier;
    queryString+='&ctl='+content;
    queryString+='&fd_id='+fd_id;
    queryString+=ref;
    queryString+='&op2=bc'; 	// bc for blank card
    queryString+='&op=card'; 	// bc for blank card
    if ( obj.elementId) queryString+="&eltid="+obj.elementId;
    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess: function (req,json) {
                                      
                                      
                                      successFill_ipopcard(req,json);
                                  }
                              }
                            );
}
function form_blank_card(obj)
{
    // first we have to take the form elt we need
    var fd_id=obj.fd_id;
    var content='div_new_card';
    var nTop=posY-40;
    var nLeft=posX-20;
    var str_style="top:"+nTop+"px;left:"+nLeft+"px;width:60em;height:auto";

    var popup={'id':  content,'cssclass':'inner_box','style':str_style,'html':loading(),'drag':true};
    if ( $(content)) {removeDiv(content);} 
    add_div(popup);


    var dossier=$('gDossier').value;

    var queryString='gDossier='+dossier;
    queryString+='&ctl='+content;
    queryString+='&fd_id='+fd_id;
    queryString+='&op2=bc'; 	// bc for blank card
    queryString+='&op=card'; 	// bc for blank card

    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:successFill_ipopcard
                                  }
                                );
}

/**
 *@brief save the data contained into the form 'save_card'
 *@param input field (obj) it must have the attribute ipopup
 *       possible attribute :
 *@see ajax_card.php
 */
function save_card(obj)
{
    var content=$(obj).ipopup;
    var accounting= $(obj)['av_text5'];
    if ( accounting && accounting.value.length > 40 ) {
      smoke.alert('Poste comptable trop grand');
      return false;
    }
    // Data must be taken here

    var    data=$('save_card').serialize(false);
    waiting_box();
    var dossier=$('gDossier').value;
    var queryString='gDossier='+dossier;
    queryString+='&ctl='+content;
    queryString+=data;
    queryString+='&op2=sc'; 	// sc for save card
    queryString+='&op=card'; 	// sc for save card

    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'post',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:function (req,json) {
                                     
                                      var elt=req.responseXML.getElementsByTagName("eltid");
                                      var status=req.responseXML.getElementsByTagName("status");
                                      var status_value='OK';
                                      if ( status.length !=0) {
                                        status_value=getNodeText(status[0]);
                                      }
                                      if ( status_value == 'OK') {
                                             fill_box(req,json);
                                      }
                                      remove_waiting_box();
                                      if ( elt.length != 0) {
                                         var eltid=getNodeText(elt[0]);
                                         if ( eltid !="") {
                                            var eltvalue=req.responseXML.getElementsByTagName("elt_value");
                                            $(eltid).value=getNodeText(eltvalue[0]);
                                            fill_data_onchange(eltid);
                                            $(eltid).focus();
                                        }
                                      }
                                      if (status_value == "OK") {
                                            Effect.SlideUp(content, { duration: 1.0 });    
                                        }
                                      if ( status_value == 'NOK') {
                                          var xml_message=req.responseXML.getElementsByTagName("code");
                                          var message=getNodeText(xml_message[0]);
                                          smoke.alert(message);
                                      }
                                    
                                      
                                  }
                              }
                        );
}
/**
 *@brief add a category of card,
 *@param obj with the attribute
 * - ipopup the ipopup to show
 * - type_cat the category of card we want to add
 */
function add_category(obj)
{
    var sx=0;
	if ( window.scrollY)
	{
            sx=window.scrollY+120;
	}
	else
	{
            sx=document.body.scrollTop+120;
	}

	var div_style="top:"+sx+"px;width:60%;height:80%";
    // show ipopup
	var div={id:obj.ipopup,
			cssclass:"inner_box",drag:1,style:div_style};
	if ( $(div) ) {
		removeDiv(div);
	}
	add_div(div);
	waiting_box();
    var dossier=$('gDossier').value;
    var queryString='gDossier='+dossier;
    queryString+='&op2=ac';
    queryString+='&op=card';
    queryString+='&ctl='+obj.ipopup;
    if ( obj.type_cat)
    {
        queryString+='&cat='+obj.type_cat;
    }
    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:fill_box
                                  }
                                );

}
/**
 * @brief save the form and add a new category of card
 * @param obj if the form object
 */
function save_card_category(obj)
{
    if ( ! $(obj).ipopup)
    {
        alert_box('Erreur pas d\' attribut ipopup '+obj.id);
        return;
    };
	try {
		// Data must be taken here

    data=$('newcat').serialize(false);
    var dossier=$('gDossier').value;
    var queryString='ctl='+obj.ipopup+'&';
    queryString+=data;
    queryString+='&op2=scc'; 	// sc for save card
    queryString+='&op=card'; 	// sc for save card

    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorFid,
                                  onSuccess:fill_box
                                  }
                                );
	} catch(e)
	{
		alert_box(e.message);
		return false;
	}
	return false;
}
/**
 *@brief Remove a definition of an  attribut
 *@param attr_def.ad_id
 *@param gDossier
 *@param table_id to rm the row
 *@param special this pointer of the row
 */

function removeCardAttribut(ad_id,gDossier,table_id,row)
{
    var queryString='gDossier='+gDossier;
    queryString+='&op=card';
    queryString+='&op2=rmfa';
    queryString+='&ctl=debug'; 	// debug id
    queryString+='&ad_id='+ad_id;
    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:null,
                                  onSuccess:null
                                  }
                                );
    deleteRowRec(table_id,row);


}
/**
* update a card in ajax
*/
function update_card(obj)
{
try {
    var name=obj.id;
    var qs=Form.serialize(name)+'&op2=upc&op=card';
    var action=new Ajax.Request ( 'ajax_misc.php',
				  {
				      method:'get',
				      parameters:qs,
				      onFailure:errorFid,
				      onSuccess:successFill_ipopcard
				  }
				);
    } catch (e) {
	alert_box(e.message);
	return false;
    }
}
/***
 * In Follow-up, update, it is possible to add several card as concerned person or company
 * this function save it into the database, display the result and remove the search_card div
 * @param {type} p_dossier dossier
 * @param {type} p_fiche_id fiche.f_id
 * @param {type} p_action_id action_gestion.ag_id
 * @returns {undefined} nothing
 */
function action_save_concerned(p_dossier, p_fiche_id, p_action_id) {
    var query = encodeJSON({'gDossier': p_dossier, 'f_id': p_fiche_id, 'ag_id': p_action_id,'op':'card','op2':'action_save_concerned','ctl':'unused'});
    var a=new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: query,
                onFailure: errorFid,
                onSuccess: function (req, txt)
                {
                    try {
                        remove_waiting_box();
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        if (a.length == 0)
                        {
                            var rec = req.responseText;
                            alert_box('erreur :' + rec);
                        }
                        var html = answer.getElementsByTagName('code');
                        var namectl = a[0].firstChild.nodeValue;
                        var nodeXml=html[0];
                        var code_html = getNodeText(nodeXml);
                        code_html = unescape_xml(code_html);
                        removeDiv('search_card');
                        $('concerned_card_td').innerHTML = code_html;
                    } catch (e) {
                       
                    }
                }
            }
    );
    }
function action_remove_concerned(p_dossier,p_fiche_id,p_action_id)
{
 var query = encodeJSON({'gDossier': p_dossier, 'f_id': p_fiche_id, 'ag_id': p_action_id,'op':'card','op2':'action_remove_concerned','ctl':'unused'});
    var a=new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: query,
                onFailure: errorFid,
                onSuccess: function (req, txt)
                {
                    try {
                        remove_waiting_box();
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('ctl');
                        if (a.length == 0)
                        {
                            var rec = req.responseText;
                            alert_box('erreur :' + rec);
                        }
                        var html = answer.getElementsByTagName('code');
                        var namectl = a[0].firstChild.nodeValue;
                        var nodeXml=html[0];
                        var code_html = getNodeText(nodeXml);
                        code_html = unescape_xml(code_html);
                        removeDiv('search_card');
                        $('concerned_card_td').innerHTML = code_html;
                    } catch (e) {
                        if ( console) { console.log('Erreur ') + e.message;}
                        alert_box('action_remove_concerned '+e.message);
                    }
                }
            }
    );
    }
/**
 * Remove a card after checking it is not used
 * @param object obj {gDossier,op,op2:rm_card,ctl,f_id}
 */    
function delete_card(obj) {
    console.debug("delete_card");
    console.debug(obj);
    smoke.confirm("Confirmez ? ", function (e) {
        if (e) {
            waiting_box();
            new Ajax.Request("ajax_misc.php", {
                "method": "get",
                parameters: obj,
                onSuccess: function (req) {
                    remove_waiting_box();
                    var answer = req.responseXML;
                    var a = answer.getElementsByTagName('ctl');
                    if (a.length == 0)
                    {
                        var rec = req.responseText;
                        alert_box('erreur :' + rec);
                    }
                    var html = answer.getElementsByTagName('code');
                    var namectl = a[0].firstChild.nodeValue;
                    var nodeXml = html[0];
                    var code_html = getNodeText(nodeXml);
                    code_html = unescape_xml(code_html);
                    if ( code_html == "OK") {
                        Effect.Fade(obj['ctl'], { duration: 1.5 });    
                    } else {
                        smoke.alert(code_html);
                    }
                }

            });
        }
    });
}