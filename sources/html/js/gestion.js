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

/*!\file
 * \brief javascript scripts for the gestion
 *
 */



/**
 *@brief remove an attached document of an action
 *@param dossier
 *@param dt_id id of the document (pk document:d_id)
*/
function remove_document(p_dossier,p_id)
{
	var queryString="gDossier="+p_dossier+"&a=rm&d_id="+p_id;
	var action=new Ajax.Request (
		"show_document.php",
		{
			method:'get',
			parameters:queryString,
			onFailure:errorRemoveDoc,
			onSuccess:successRemoveDoc
		}

		);

}
/**
 *@brief update the description of an attached document of an action
 *@param dossier
 *@param dt_id id of the document (pk document:d_id)
*/
function update_document(p_dossier,p_id)
{
	var queryString="gDossier="+p_dossier+"&a=upd_doc&d_id="+p_id;
        queryString+="&value="+$('input_desc_txt'+p_id).value;
	var action=new Ajax.Request (
		"show_document.php",
		{
			method:'get',
			parameters:queryString,
			onFailure:errorRemoveDoc,
			onSuccess:function(req){
                                $('input_desc'+p_id).hide();
                                $('print_desc'+p_id).innerHTML=$('input_desc_txt'+p_id).value+'<a class="line" id="desc'+p_id+'" onclick="javascript:show_description('+p_id+')">Modifier</a>';
                                $('print_desc'+p_id).show();
                        }
		}

		);
    return false;
}

/**
 *@brief remove the concerned operation of an action
 *@param dossier
 *@param p_id id pk action_comment_operation
*/
function remove_operation(p_dossier,p_id)
{
	var queryString="gDossier="+p_dossier+"&a=rmop&id="+p_id;
	var action=new Ajax.Request (
		"show_document.php",
		{
			method:'get',
			parameters:queryString,
			onFailure:errorRemoveDoc,
			onSuccess:successRemoveOp
		}

		);

}
function successRemoveOp(request,json)
{
	try{
		var answer=request.responseText.evalJSON(true);
		if ( answer.ago_id == -1 ) { alert_box ('Effacement non autorisé');return;}

		var action="acop"+answer.ago_id;
		$(action).innerHTML="";
		var doc="op"+answer.ago_id;
		$(doc).style.color="red";
		$(doc).href="javascript:alert_box('Commentaire Effacé')";
		$(doc).style.textDecoration="line-through";
	}catch(e){
		alert_box(e.message);
	}
}
/**
 *@brief remove the concerned operation of an action
 *@param dossier
 *@param p_id id pk action_comment_operation
*/
function remove_action(p_dossier,p_id,ag_id)
{
	queryString="gDossier="+p_dossier+"&a=rmaction&id="+p_id+"&ag_id="+ag_id;
	var action=new Ajax.Request (
		"show_document.php",
		{
			method:'get',
			parameters:queryString,
			onFailure:ajax_misc_failure,
			onSuccess:function(request,json) {
				try{
				var answer=request.responseText.evalJSON(true);
				if ( answer.act_id == -1 ) { alert_box ('Effacement non autorisé');return;}
				var action="acact"+answer.act_id;
				$(action).innerHTML="";
				var doc="act"+answer.act_id;
				$(doc).style.color="red";
				$(doc).href="javascript:alert_box('Action Effacée')";
				$(doc).style.textDecoration="line-through";
				} catch (e){ alert_box(e.message);}
			}
		}

		);

}
/**
 *@brief remove comment of an action
 *@param dossier
 *@param p_id pk action_gestion_comment
*/
function remove_comment(p_dossier,p_id)
{
	queryString="gDossier="+p_dossier+"&a=rmcomment&id="+p_id;
	var action=new Ajax.Request (
		"show_document.php",
		{
			method:'get',
			parameters:queryString,
			onFailure:errorRemoveDoc,
			onSuccess:successRemoveComment
		}

		);

}
function successRemoveComment(request,json)
{
	var answer=request.responseText.evalJSON(true);
	if ( answer.agc_id == -1 ) { alert_box ('Effacement non autorisé');return;}
	var action="accom"+answer.agc_id;
	$(action).innerHTML="";
	var doc="com"+answer.agc_id;
	$(doc).style.color="red";
	$(doc).href="javascript:alert_box('Commentaire Effacé')";
	$(doc).style.textDecoration="line-through";

}
/**
 *@brief error if a document if removed
 */
function errorRemoveDoc()
{
	alert_box('Impossible d\'effacer ce document');
}
/**
 *@brief success when removing a document
 */
function successRemoveDoc(request,json)
{
	var answer=request.responseText.evalJSON(true);
	if ( answer.d_id == -1 ) { alert_box ('Effacement non autorisé');return;}
	var action="ac"+answer.d_id;
	$(action).innerHTML="";
	var doc="doc"+answer.d_id;
	$(doc).style.color="red";
	$(doc).href="javascript:alert_box('Document Effacé')";
	$(doc).style.textDecoration="line-through";
        $('desc'+answer.d_id).innerHTML="";

}
/**
* @brief check the format of the hour
* @param p_ctl is the control where the hour is encoded
*/
function check_hour(p_ctl)
{
	try
	{
		var h=document.getElementById(p_ctl);
		var re = /^\d{1,2}:\d{2}$/;
		if ( trim(h.value) !='' && ! h.value.match(re))
			alert_box("Format de l'heure est HH:MM ")
	}
	catch (erreur)
	{
		alert_box('fct : check_hour '+erreur);
	}

}
/**
 *@brief remove an attached document of an action
 *@param dossier
 *@param dt_id id of the document (pk document:d_id)
*/

function removeStock(s_id,p_dossier)
{
	smoke.confirm("Confirmez-vous l'effacement de cette entrée dans le stock?",
        function (a) {
            if (a)
            {
                queryString="gDossier="+p_dossier+"&op=rm_stock&s_id="+s_id;
                var action=new Ajax.Request (
                        "ajax_misc.php",
                        {
                                method:'get',
                                parameters:queryString,
                                onFailure:errorRemoveStock,
                                onSuccess:successRemoveStock
                        }
		);
                
            }
            else {
                    return ;
            }
        });
}
/**
 *@brief error if a document if removed
 */
function errorRemoveStock()
{
	alert_box('Impossible d\'effacer ');
}
/**
 *@brief success when removing a document
 */
function successRemoveStock(request,json)
{
	try
	{
		var answer=request.responseText.evalJSON(true);
		var doc="stock"+answer.d_id;
		var href="href"+answer.d_id;
		$(href).innerHTML='';

		$(doc).style.color="red";
		//    $(doc).href="javascript:alert_box('Stock Effacé')";
		$(doc).style.textDecoration="line-through";
	} catch (e)
{
		alert_box("success_box"+e.message);
	}
}
/**
 * @brief display details of the last actions in management
 * called from dashboard
 * @param p_dossier : dossier id
 */
function action_show(p_dossier)
{
    try {
        waiting_box();
        var action = new Ajax.Request('ajax_misc.php',
        {
            method:'get',
            parameters : {gDossier:p_dossier,'op':'action_show'},
            onSuccess : function(p_xml, p_text) {
                        remove_waiting_box();
                        add_div({id: 'action_list_div', style:"top:1%;width:90%;left:5%" , cssclass: 'inner_box'});
                        $('action_list_div').innerHTML=p_xml.responseText;
            }
        });
    } catch (e)
    {
        alert_box('action_show '+e.message);
    }
}
/**
 * @brief Display a box for adding a new event 
 * @param {type} p_dossier
 * @returns {undefined}
 */
function action_add(p_dossier) {
     try {
        if ( $('action_add_div')) {
            alert_box('Désolé, événement en cours de création à sauver');
            return;
        }
        waiting_box();
        var action = new Ajax.Request('ajax_misc.php',
        {
            method:'get',
            parameters : {gDossier:p_dossier,'op':'action_add'},
            onSuccess : function(p_xml, p_text) {
                        remove_waiting_box();
                        add_div({id: 'action_add_div',
                            style:"top:1%;width:80%;left:10%" , 
                            cssclass: 'inner_box'});
                        $('action_add_div').innerHTML=p_xml.responseText;
                        p_xml.responseText.evalScripts();
            }
        });
    } catch (e)
    {
        alert_box('action_add '+e.message);
    }
}
/**
 * @brief The new event is entered into the div action_add_div, we try
 * to save and receive as answer a XML file with a code of success and possibly
 * a message
 * If the message is OK then the div is fading out, otherwise the reason of 
 * failure is shown and the div remains
 */
function action_save_short()
{
    try {
         $('action_add_frm_info').innerHTML="";
         $('action_add_frm')['date_event_action_short'].parentNode.className="";
         $('action_add_frm')['title_event'].parentNode.className="";
         $('action_add_frm')['type_event'].parentNode.className="";

        if ( $('action_add_frm')['date_event_action_short'].value.trim() == '') {
            $('action_add_frm')['date_event_action_short'].parentNode.className="notice";
            return false;
        }

        if ( $('action_add_frm')['title_event'].value.trim()=="") {
            $('action_add_frm')['title_event'].parentNode.className="notice";
            return false;
        }

        if ( $('action_add_frm')['type_event'].options[$('action_add_frm')['type_event'].selectedIndex].value == -1 )
        {
            $('action_add_frm')['type_event'].parentNode.className="notice";
            return false;
        }
        var form=$('action_add_frm').serialize();
        waiting_box();
        var action = new Ajax.Request('ajax_misc.php',
                {
                    method: 'get',
                    parameters: form,
                    onSuccess: function (p_xml, p_text) {
                        remove_waiting_box();
                        var answer=p_xml.responseXML;
                        var code_tags=answer.getElementsByTagName('status');
                        var code=getNodeText(code_tags[0]);
                        var message_tags=answer.getElementsByTagName('content');
                        var message=getNodeText(message_tags[0]);

                        if ( code == 'OK') {
                            // Successfully saved
                             $('action_add_frm_info').innerHTML=message;
                             $('action_add_div').remove();
                             
                        }
                        else if (code == 'NOK') {
                            // issue while saving
                            $('action_add_frm_info').innerHTML=message;
                        }
                        
                        
                    }
                });
    } catch (e)
    {
        alert_box('action_add ' + e.message);
    }
    return false;
}