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

/*!\brief
 * \param p_value jrn.jr_id
 */
function modifyModeleDocument(p_value,dossier)
{
    layer++;
    id='det'+layer;
    var pos_y=posY+offsetY-20;
    var pos_x=posX+offsetX+40;
    var style="position:absolute;top:"+pos_y+"px;left:10%;width:80%";
    var popup={'id':'mod_doc',
	       'cssclass':'inner_box',
               'html': loading(),
	       'drag':false,
	       'style':style
	      };

    querystring='gDossier='+dossier+'&op=mod_doc&id='+p_value+'&div=mod_doc';
    if ( ! $('mod_doc'))
    {
	add_div(popup);
    }
    
    var action=new Ajax.Request(
                   "ajax_misc.php",
                   {
                   method:'get',
                   parameters:querystring,
                   onFailure:error_box,
                   onSuccess:modify_document_success_box
                   }
               );
}
/**
 *@brief receive answer from ajax and just display it into the IBox
 * XML must contains at least 2 fields : code is the ID of the IBOX and
 * html which is the contain
 */
function modify_document_success_box(req,json)
{
    try
    {
        $('mod_doc').show();
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
        g(name_ctl).innerHTML=code_html;
        g(name_ctl).style.height='auto';
    }
    catch (e)
    {
        alert_box("success_box"+e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch(e)
    {
        alert_box("answer_box Impossible executer script de la reponse\n"+e.message);
    }
}
