/**
 * Global application behaviours
 */

$(document).ready(function()
{
	//If there are any notices currently being displayed, fade them out
	if($('#flash-notices'))
		setTimeout('fade_flash_notices()',5000);
	
	//Auto Focus first form text element on page
	var focused_element_index = null;
	$('input').each(function(i){
		if(this.type == "text" && focused_element_index == null)
		{
			this.focus();
			focused_element_index = i;
		}
	});
	
	//Auto validate forms
	$('form').bind("submit",validate_form);
	
	$('a.popup-link').bind('click',function(){
		window.open(this.href);
		return false;
	});
	
	//sIFR prettify headings
	$('h1#content-title').sifr( { strSWF: './fonts/agency.swf', strColor: '#000000', strWmode: 'transparent' } );
});

function fade_flash_notices()
{
	$('#flash-notices').fadeOut(1500, function() {
       $(this).remove()
    });
}

//Validates required fields on a form
function validate_form()
{
	var validation_errors = 0;
	$('.required input, .required textarea',this).each(function(i){
		if(this.value == null || this.value == "")
		{
			validation_errors++;
			$(this).addClass("error");
			$(this).bind('blur',check_required_field);
		}
		else
			$(this).removeClass("error");
	});
	
	return !(validation_errors);
}

//Checks if a field is still required
function check_required_field()
{
	if(this.value != null && this.value != "")
	{
		$(this).removeClass("error");
		$(this).unbind('blur',check_required_field);
	}
}

jQuery.sifr={blnFlashValid:null,_checkFlash:function(intMajor,intMinor,intRevision){var arrFlash=null;var strFlash='';if(navigator.plugins&&navigator.mimeTypes.length){var objFlash=navigator.plugins["Shockwave Flash"];if(objFlash&&objFlash.description)arrFlash=objFlash.description.replace(/([a-zA-Z]|\s)+/,"").replace(/(\s+r|\s+b[0-9]+)/,".").split(".");objFlash=null}if(!arrFlash){try{var objFlash=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7")}catch(errB){try{arrFlash=[6,0,21];var objFlash=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");objFlash.AllowScriptAccess="always";if(objFlash&&objFlash.major)arrFlash=[objFlash.major,objFlash.minor,objFlash.revision];objFlash=null}catch(errC){}}if(!arrFlash){try{objFlash=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");arrFlash=objFlash.GetVariable("$version").split(" ")[1].split(",");objFlash=null}catch(errA){arrFlash=[0,0,0]}}}jQuery.sifr.blnFlashValid=((arrFlash[0]>=intMajor)&&(arrFlash[1]>=intMinor)&&(arrFlash[2]>=intRevision))},_escapeHex:function(strInput){if(jQuery.browser.msie){return strInput.replace(new RegExp("%\d{0}","g"),"%25")}return strInput.replace(new RegExp("%(?!\d)","g"),"%25")},_fetchContent:function(objNode,objNodeNew,strCase,intLinks,strLinkVars){var strContent="";var objSearch=objNode.firstChild;var objRemove,objNodeRemoved,objTemp,sValue;if(intLinks==null)intLinks=0;if(strLinkVars==null)strLinkVars="";while(objSearch){if(objSearch.nodeType==3){sValue=objSearch.nodeValue.replace("<","&lt;");switch(strCase){case"lower":strContent+=sValue.toLowerCase();break;case"upper":strContent+=sValue.toUpperCase();break;default:strContent+=sValue}}else if(objSearch.nodeType==1){if(jQuery.sifr._matchNodeName(objSearch,"a")&&!objSearch.getAttribute("href")==false){if(objSearch.getAttribute("target"))strLinkVars+="&sifr_url_"+intLinks+"_target="+objSearch.getAttribute("target");strLinkVars+="&sifr_url_"+intLinks+"="+jQuery.sifr._escapeHex(objSearch.getAttribute("href")).replace(/&/g,"%26");strContent+='<a href="asfunction:_root.launchURL,'+intLinks+'">';intLinks++}else if(jQuery.sifr._matchNodeName(objSearch,"br")){strContent+="<br/>"}if(objSearch.hasChildNodes()){objTemp=jQuery.sifr._fetchContent(objSearch,null,strCase,intLinks,strLinkVars);strContent+=objTemp.strContent;intLinks=objTemp.intLinks;strLinkVars=objTemp.strLinkVars;objTemp=null}if(jQuery.sifr._matchNodeName(objSearch,"a")){strContent+="</a>"}}objRemove=objSearch;objSearch=objSearch.nextSibling;if(objNodeNew!=null){objNodeRemoved=objRemove.parentNode.removeChild(objRemove);objNodeNew.appendChild(objNodeRemoved)}}return{'strContent':strContent,'intLinks':intLinks,'strLinkVars':strLinkVars}},_matchNodeName:function(objNode,strMatch){return(strMatch=="*")?true:(objNode.nodeName.toLowerCase().replace("html:","")==strMatch.toLowerCase())},_normalize:function(strInput){return strInput.replace(/\s+/g," ")},build:function(arrConfig){var arrOptions=jQuery.extend({intRequiredFlashVersion:[6,0,0],strSWF:'',strColor:'#000000',strBgColor:'',strLinkColor:'',strHoverColor:'',intPadding:[0,0,0,0],strFlashVars:'',strCase:'',strWmode:''},arrConfig||{});if(arrOptions.intPadding.length!=4){alert('Wrong number of arguments for the padding!');return}if(!jQuery.sifr.blnFlashValid){jQuery.sifr._checkFlash(arrOptions.intRequiredFlashVersion[0],arrOptions.intRequiredFlashVersion[1],arrOptions.intRequiredFlashVersion[2])}if(arrOptions.strFlashVars!='')arrOptions.strFlashVars=jQuery.sifr._normalize(arrOptions.strFlashVars);if(arrOptions.strFlashVars.substr(0,1)=='&')arrOptions.strFlashVars=arrOptions.strFlashVars.substr(1,arrOptions.strFlashVars.length);if(arrOptions.strColor!='')arrOptions.strFlashVars+="textcolor="+arrOptions.strColor+'&';if(arrOptions.strHoverColor!=null)arrOptions.strFlashVars+='hovercolor='+arrOptions.strHoverColor+'&';if((arrOptions.strLinkColor!=null)||(arrOptions.strHoverColor!=null))arrOptions.strFlashVars+='linkcolor='+(arrOptions.strLinkColor||arrOptions.strColor)+'&';return this.each(function(){jqThis=jQuery(this);if(jQuery.sifr.blnFlashValid&&!jqThis.is('.sIFR-flash')){intWidth=parseInt(this.offsetWidth);intHeight=parseInt(this.offsetHeight);if(isNaN(intWidth)||isNaN(intHeight)){alert('fook it');return}intWidth-=(arrOptions.intPadding[0]+arrOptions.intPadding[2]);intHeight-=(arrOptions.intPadding[1]+arrOptions.intPadding[3]);if(arrOptions.strFlashVars.substr(arrOptions.strFlashVars.length,1)!='&')arrOptions.strFlashVars+='&';objAlternate=jQuery('<span class="sIFR-alternate"></span>')[0];objContent=jQuery.sifr._fetchContent(this,objAlternate,arrOptions.strCase);strVars="txt="+jQuery.sifr._normalize(jQuery.sifr._escapeHex(objContent.strContent).replace(/\+/g,"%2B").replace(/&/g,"%26").replace(/\"/g,"%22"))+'&';strVars+=arrOptions.strFlashVars;strVars+="w="+intWidth+"&h="+intHeight+objContent.strLinkVars;objContent=null;if(!jQuery.browser.msie){strHTML='<embed type="application/x-shockwave-flash" src="'+arrOptions.strSWF+'" quality="best" ';strHTML+=(arrOptions.strWmode!='')?'wmode="'+arrOptions.strWmode+'" ':'';strHTML+=(arrOptions.strBgColor!='')?'bgcolor="'+arrOptions.strBgColor+'" ':'';strHTML+='flashvars="'+strVars+'" width="'+intWidth+'" height="'+intHeight+'"></embed>'}else{strHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+intWidth+'" height="'+intHeight+'">';strHTML+='<param name="movie" value="'+arrOptions.strSWF+"?"+strVars+'"></param>';strHTML+='<param name="quality" value="best"></param>';strHTML+=(arrOptions.strWmode!='')?'<param name="wmode" value="'+arrOptions.strWmode+'"></param>':'';strHTML+=(arrOptions.strBgColor!='')?'<param name="bgcolor" value="'+arrOptions.strBgColor+'"></param>':'';strHTML+='</object>'}jqThis.addClass('sIFR-flash').empty().append(objAlternate).append(strHTML);if(jQuery.browser.msie){this.outerHTML=this.outerHTML}}})}};jQuery.fn.extend({sifr:jQuery.sifr.build});