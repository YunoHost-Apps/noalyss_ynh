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
/*!\file
 * \brief create the infobulle, the internalization is not yet implemented
 * \code
 // Example
  echo JS_INFOBULLE;
  echo HtmlInput::infobulle(x);
 \endcode
 */

var posX=0,posY=0,offsetX=10,offsetY=10;
document.onmousemove=getPosition;

function showBulle(p_ctl){
    var d=document.getElementById('bulle');
    var viewport = document.viewport.getDimensions();
    if ( posX+offsetX > viewport.width-d.getWidth()) { posX-=d.getWidth()+20;}
    if ( posY+offsetY > viewport.height-d.getHeight()-20) { posY-=d.getHeight()+20}
    d.innerHTML=content[p_ctl];
    d.style.top=posY+offsetY+"px";
    d.style.left=posX+offsetX-10+"px";
    d.style.visibility="visible";
}
function getPosition(e)
{
    if (document.all)
    {
        posX=event.x+document.body.scrollLeft;
        posY=event.y+document.body.scrollTop;
    }
    else
    {
        posX=e.pageX;
        posY=e.pageY;
    }
}
function hideBulle(p_ctl)
{
    var d=document.getElementById('bulle');
    d.style.visibility="hidden";
}
function displayBulle(p_comment)  {
    var d=document.getElementById('bulle');
    var viewport = document.viewport.getDimensions();
    d.innerHTML=p_comment;
    if ( posX+offsetX > viewport.width-d.getWidth()) { posX-=d.getWidth()+20;}
    if ( posY+offsetY > viewport.height-d.getHeight()-20) { posY-=d.getHeight()+20}
    d.style.top=posY+offsetY+"px";
    d.style.left=posX+offsetX+"px";
    d.style.visibility="visible";
}