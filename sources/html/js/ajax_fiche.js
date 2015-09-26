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
 * \brief This file permit to use the AJAX function to fill up
 *        info from fiche
 *
 */

/*!\brief clean the row (the label, price and vat)
 * \param p_ctl the calling ctrl
 */
function clean_Fid(p_ctl)
{
    nSell=p_ctl+"_price";
    nTvaAmount=p_ctl+"_tva_amount";
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
    if ( $(nTvaAmount))
    {
        $(nTvaAmount).value=0;
    }
}
function errorFid(request,json)
{
    alert_box('erreur : ajax fiche');
}
/*!\brief this function fills the data from fid.php,
 * \param p_ctl object : field of the input,
 *  possible object member
 * - label field to update with the card's name
 * - price field to update with the card's price
 * - tvaid field to update with the card's tva_id
 * - jrn field to force the ledger
  *\see successFid errorFid fid.php
 */
function ajaxFid(p_ctl)
{
	try
	{
	var gDossier=$('gDossier').value;
    var jrn=$(p_ctl).jrn;
    $(p_ctl).value=$(p_ctl).value.toUpperCase();
    if ( jrn == undefined )
    {
        if ($('p_jrn')!=undefined)
        {
            jrn=$('p_jrn').value;
        }
    }
    if ( jrn == undefined )
    {
        jrn=-1;
    }
    if ( trim($(p_ctl).value)=="" )
    {
        nLabel=$(p_ctl).label;
        if ($(nLabel) )
        {
            $(nLabel).value="";
            $(nLabel).innerHTML="&nbsp;";
            clean_Fid(p_ctl);
            return;
        }
    }
    var queryString="FID="+trim($(p_ctl).value);
    if ( $(p_ctl).label)
    {
        queryString+='&l='+$(p_ctl).label;
    }
    if ( $(p_ctl).tvaid)
    {
        queryString+='&t='+$(p_ctl).tvaid;
    }
    if ( $(p_ctl).price)
    {
        queryString+='&p='+$(p_ctl).price;
    }
    if ( $(p_ctl).purchase)
    {
        queryString+='&b='+$(p_ctl).purchase;
    }
    if ( $(p_ctl).typecard)
    {
        queryString+='&d='+$(p_ctl).typecard;
    }
    queryString=queryString+"&j="+jrn+'&gDossier='+gDossier;
    queryString=queryString+'&ctl='+p_ctl.id;

    var action=new Ajax.Request (
                   "fid.php",
                   {
                   method:'get',
                   parameters:queryString,
                   onFailure:errorFid,
                   onSuccess:successFid
                   }

               );
	}catch (e)  {
		alert_box(e.message);
		alert_box(p_ctl);
	}

}
/*!\brief callback function for ajax
 * \param request : object request
 * \param json : json answer
\verbatim
 {"answer":"ok",
 "flabel":"none",
 "name":"Chambre de commerce",
 "ftva_id":"none",
 "tva_id":" ",
 "fPrice_sale":"none",
 "sell":" ",
 "fPrice_purchase":"none",
 "buy":" "}
\endverbatim
 */
function successFid(request,json)
{
    var answer=request.responseText.evalJSON(true);
    var flabel=answer.flabel;
    if ( answer.answer=='nok' )
    {
        set_value(flabel," Fiche inexistante");
        return;
    }

    var ftva_id=answer.ftva_id;
    var fsale=answer.fPrice_sale;
    var fpurchase=answer.fPrice_purchase;

    if ( ftva_id != 'none')
    {
        set_value(ftva_id,answer.tva_id);
    }
    if ( flabel != 'none')
    {
        set_value(flabel,answer.name);
    }
    if ( fsale != 'none')
    {
        set_value(fsale,answer.sell);
    }
    if ( fpurchase != 'none')
    {
        set_value(fpurchase,answer.buy);
    }


}
function ajax_error_saldo(request,json)
{
    alert_box('erreur : ajax solde ');
}
/*!\brief this function get the saldo
 * \param p_ctl the ctrl where we take the quick_code
 */
function ajax_saldo(p_ctl)
{
    var gDossier=$('gDossier').value;
    var ctl_value=trim($(p_ctl).value);
    var jrn=$('p_jrn').value;
    queryString="FID="+ctl_value+"&op=saldo";
    queryString=queryString+'&gDossier='+gDossier+'&j='+jrn;
    queryString=queryString+'&ctl='+ctl_value;
    /*  alert_box(queryString); */
    var action=new Ajax.Request (
                   "ajax_misc.php",
                   {
                   method:'get',
                   parameters:queryString,
                   onFailure:ajax_error_saldo,
                   onSuccess:ajax_success_saldo
                   }

               );

}
/*!\brief callback function for ajax
 * \param request : object request
 * \param json : json answer */
function ajax_success_saldo(request,json)
{
    var answer=request.responseText.evalJSON(true);
    $('first_sold').value=answer.saldo;

}
/*!\brief this function get data from ajax_card.php and fill the hidden div with the return html string
* \param p_dossier
* \param f_id fiche.f_id
* \param p_operation what to do : op : history of operation
* \param ctl : id of the div to show
* \param page
*/
function ajax_card(p_dossier,f_id,p_operation,ctl,page)
{
    $(ctl).show();
    var queryString="gDossier="+p_dossier+"&f_id="+f_id+"&op="+p_operation+"&p="+page+'&ctl='+ctl;
    var action = new Ajax.Request(
                 "ajax_card.php" , { method:'get', parameters:queryString,onFailure:ajax_get_failure,onSuccess:ajax_get_success}
                 );
}
/*!\brief callback function for ajax_get when successuf
*/
function ajax_get_success(request,json)
{
    var answer=request.responseText.evalJSON(false);
    $(answer.ctl).show();
    $(answer.ctl).innerHTML=answer.html;
}
/*!\brief callback function for ajax_get when fails
*/
function ajax_get_failure(request,json)
{
    alert_box("Ajax do not work for ajax_get");

}

//-->
