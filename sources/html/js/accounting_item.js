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
 * \brief
 * containing the javascript for opening a windows to search an account (poste comptable)
 */

function set_poste_parent(p_ctl,p_value)
{
    var f=g(p_ctl);
    f.value+='['+p_value+']';
}

function set_jrn_parent(p_ctl,p_value)
{
    var f=g(p_ctl);
    if ( f )
    {
        if ( trim(f.value)!="") f.value+=' ';
        f.value+=p_value;
    }
}
/**
 *@brief show the popup for search an accounting item
 *@param object this, it must contains some attribute as
 * - jrn if set and different to 0, will filter the accounting item for a
 *   ledger
 * - account the tag which will contains the  number
 * - label the tag which will contains the label
 * - bracket if the value must be surrounded by [ ]
 * - acc_query for the initial query
 *\see ajax_poste.php
 */
function search_poste(obj)
{
	var sx=0;
	if ( window.scrollY)
	{
            sx=window.scrollY+40;
	}
	else
	{
            sx=document.body.scrollTop+60;
	}

	var div_style="top:"+sx+"px";
	removeDiv('search_account');
	add_div({id:'search_account',cssclass:'inner_box',html:loading(),style:div_style,drag:false});

    var dossier=$('gDossier').value;

    var queryString="gDossier="+dossier;

    queryString+="&op2=sf";
    queryString+="&op=account";
    try
    {
        if ( obj.jrn)
        {
            queryString+="&j="+obj.jrn;
        }
        if ( obj.account)
        {
            queryString+="&c="+obj.account;
        } 
        if ( obj.label)
        {
            queryString+="&l="+obj.label;
        }
        if ( obj.bracket)
        {
            queryString+="&b="+obj.bracket;
        }
        if( obj.noquery)
        {
            queryString+="&nq";
        }
        if( obj.no_overwrite)
        {
            queryString+="&nover";
        }
        if( obj.bracket)
        {
            queryString+="&bracket";
        }
        if ( ! obj.noquery)
        {
            if( obj.acc_query)
            {
                queryString+="&q="+obj.acc_query;
            }
            else
            {
                if ($(obj).account)
                {
                    var e=$(obj).account;
                    var str_account=$(e).value;
                    queryString+="&q="+str_account;
                }
            }
        }

        queryString+="&ctl="+'search_account';
        queryString=encodeURI(queryString);
        var action=new Ajax.Request ( 'ajax_misc.php',
                                      {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorPoste,
                                  onSuccess:result_poste_search
                                      }
                                    );
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
/**
 *@brief when you submit the form for searching a accounting item
 *@param obj form
 *@note the same as search_poste, except it answer to a FORM and not
 * to a click event
 */
function search_get_poste(obj)
{
    var dossier=$('gDossier').value;
    var queryString="gDossier="+dossier;

    queryString+="&op=account";
    queryString+="&op2=sf";

    if ( obj.elements['jrn'] )
    {
        queryString+="&j="+$F('jrn');
    }
    if ( obj.elements['account'])
    {
        queryString+="&c="+$F('account');
    }
    if ( obj.elements['label'])
    {
        queryString+="&l="+$F('label');
    }
    if( obj.elements['acc_query'])
    {
        queryString+="&q="+$F('acc_query');
    }
    if (obj.ctl )
    {
        queryString+="&ctl="+obj.ctl;
    }
    if( obj.elements['nosearch'])
    {
        queryString+="&nq";
    }
    if( obj.elements['nover'])
    {
        queryString+="&nover";
    }
    if( obj.elements['bracket'])
    {
        queryString+="&bracket";
    }

    $('asearch').innerHTML=loading();
    var action=new Ajax.Request ( 'ajax_misc.php',
                                  {
                                  method:'get',
                                  parameters:queryString,
                                  onFailure:errorPoste,
                                  onSuccess:result_poste_search
                                  }
                                );
}

/**
 *@brief show the answer of ajax request
 *@param  answer in XML
 */
function result_poste_search(req)
{
    try
    {
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
        $('search_account').innerHTML=code_html;
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
*@brief error for ajax
*/
function errorPoste()
{
    alert_box('Ajax failed');
}
function pausecomp(millis)
 {
  var date = new Date();
  var curDate = null;
  do { curDate = new Date(); }
  while(curDate-date < millis);
}
