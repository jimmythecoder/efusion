/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * This is the integration file for JavaScript.
 *
 * It defines the FCKeditor class that can be used to create editor
 * instances in a HTML page in the client side. For server side
 * operations, use the specific integration system.
 */

// FCKeditor Class
var FCKeditor = function( instanceName, width, height, toolbarSet, value )
{
	// Properties
	this.InstanceName	= instanceName ;
	this.Width			= width			|| '100%' ;
	this.Height			= height		|| '200' ;
	this.ToolbarSet		= toolbarSet	|| 'efusion' ;
	this.Value			= value			|| '' ;
	this.BasePath		= '/fckeditor/' ;
	this.CheckBrowser	= true ;
	this.DisplayErrors	= true ;
	this.EnableSafari	= true ;		// This is a temporary property, while Safari support is under development.
	this.EnableOpera	= true ;		// This is a temporary property, while Opera support is under development.

	this.Config			= new Object() ;

	// Events
	this.OnError		= null ;	// function( source, errorNumber, errorDescription )
}

FCKeditor.prototype.Version			= '2.4.3' ;
FCKeditor.prototype.VersionBuild	= '15657' ;

FCKeditor.prototype.Create = function()
{
	document.write( this.CreateHtml() ) ;
}

FCKeditor.prototype.CreateHtml = function()
{
	// Check for errors
	if ( !this.InstanceName || this.InstanceName.length == 0 )
	{
		this._ThrowError( 701, 'You must specify an instance name.' ) ;
		return '' ;
	}

	var sHtml = '<div>' ;

	if ( !this.CheckBrowser || this._IsCompatibleBrowser() )
	{
		sHtml += '<input type="hidden" id="' + this.InstanceName + '" name="' + this.InstanceName + '" value="' + this._HTMLEncode( this.Value ) + '" style="display:none" />' ;
		sHtml += this._GetConfigHtml() ;
		sHtml += this._GetIFrameHtml() ;
	}
	else
	{
		var sWidth  = this.Width.toString().indexOf('%')  > 0 ? this.Width  : this.Width  + 'px' ;
		var sHeight = this.Height.toString().indexOf('%') > 0 ? this.Height : this.Height + 'px' ;
		sHtml += '<textarea name="' + this.InstanceName + '" rows="4" cols="40" style="width:' + sWidth + ';height:' + sHeight + '">' + this._HTMLEncode( this.Value ) + '<\/textarea>' ;
	}

	sHtml += '</div>' ;

	return sHtml ;
}

FCKeditor.prototype.ReplaceTextarea = function()
{
	if ( !this.CheckBrowser || this._IsCompatibleBrowser() )
	{
		// We must check the elements firstly using the Id and then the name.
		var oTextarea = document.getElementById( this.InstanceName ) ;
		var colElementsByName = document.getElementsByName( this.InstanceName ) ;
		var i = 0;
		while ( oTextarea || i == 0 )
		{
			if ( oTextarea && oTextarea.tagName.toLowerCase() == 'textarea' )
				break ;
			oTextarea = colElementsByName[i++] ;
		}

		if ( !oTextarea )
		{
			alert( 'Error: The TEXTAREA with id or name set to "' + this.InstanceName + '" was not found' ) ;
			return ;
		}

		oTextarea.style.display = 'none' ;
		this._InsertHtmlBefore( this._GetConfigHtml(), oTextarea ) ;
		this._InsertHtmlBefore( this._GetIFrameHtml(), oTextarea ) ;
	}
}

FCKeditor.prototype._InsertHtmlBefore = function( html, element )
{
	if ( element.insertAdjacentHTML )	// IE
		element.insertAdjacentHTML( 'beforeBegin', html ) ;
	else								// Gecko
	{
		var oRange = document.createRange() ;
		oRange.setStartBefore( element ) ;
		var oFragment = oRange.createContextualFragment( html );
		element.parentNode.insertBefore( oFragment, element ) ;
	}
}

FCKeditor.prototype._GetConfigHtml = function()
{
	var sConfig = '' ;
	for ( var o in this.Config )
	{
		if ( sConfig.length > 0 ) sConfig += '&amp;' ;
		sConfig += encodeURIComponent( o ) + '=' + encodeURIComponent( this.Config[o] ) ;
	}

	return '<input type="hidden" id="' + this.InstanceName + '___Config" value="' + sConfig + '" style="display:none" />' ;
}

FCKeditor.prototype._GetIFrameHtml = function()
{
	var sFile = 'fckeditor.html' ;

	try
	{
		if ( (/fcksource=true/i).test( window.top.location.search ) )
			sFile = 'fckeditor.original.html' ;
	}
	catch (e) { /* Ignore it. Much probably we are inside a FRAME where the "top" is in another domain (security error). */ }

	var sLink = this.BasePath + sFile + '?InstanceName=' + encodeURIComponent( this.InstanceName ) ;
	if (this.ToolbarSet) sLink += '&amp;Toolbar=' + this.ToolbarSet ;

	return '<iframe id="' + this.InstanceName + '___Frame" src="' + sLink + '" width="' + this.Width + '" height="' + this.Height + '" frameborder="0" scrolling="no"></iframe>' ;
}

FCKeditor.prototype._IsCompatibleBrowser = function()
{
	return FCKeditor_IsCompatibleBrowser( this.EnableSafari, this.EnableOpera ) ;
}

FCKeditor.prototype._ThrowError = function( errorNumber, errorDescription )
{
	this.ErrorNumber		= errorNumber ;
	this.ErrorDescription	= errorDescription ;

	if ( this.DisplayErrors )
	{
		document.write( '<div style="COLOR: #ff0000">' ) ;
		document.write( '[ FCKeditor Error ' + this.ErrorNumber + ': ' + this.ErrorDescription + ' ]' ) ;
		document.write( '</div>' ) ;
	}

	if ( typeof( this.OnError ) == 'function' )
		this.OnError( this, errorNumber, errorDescription ) ;
}

FCKeditor.prototype._HTMLEncode = function( text )
{
	if ( typeof( text ) != "string" )
		text = text.toString() ;

	text = text.replace(
		/&/g, "&amp;").replace(
		/"/g, "&quot;").replace(
		/</g, "&lt;").replace(
		/>/g, "&gt;") ;

	return text ;
}

function FCKeditor_IsCompatibleBrowser( enableSafari, enableOpera )
{
	var sAgent = navigator.userAgent.toLowerCase() ;

	// Internet Explorer
	if ( sAgent.indexOf("msie") != -1 && sAgent.indexOf("mac") == -1 && sAgent.indexOf("opera") == -1 )
	{
		var sBrowserVersion = navigator.appVersion.match(/MSIE (.\..)/)[1] ;
		return ( sBrowserVersion >= 5.5 ) ;
	}

	// Gecko (Opera 9 tries to behave like Gecko at this point).
	if ( navigator.product == "Gecko" && navigator.productSub >= 20030210 && !( typeof(opera) == 'object' && opera.postError ) )
		return true ;

	// Opera
	if ( enableOpera && sAgent.indexOf( 'opera' ) == 0 && parseInt( navigator.appVersion, 10 ) >= 9 )
			return true ;

	// Safari
	if ( enableSafari && sAgent.indexOf( 'safari' ) != -1 )
		return ( sAgent.match( /safari\/(\d+)/ )[1] >= 312 ) ;	// Build must be at least 312 (1.3)

	return false ;
}

/*
 * Interface elements for jQuery - http://interface.eyecon.ro
 *
 * Copyright (c) 2006 Stefan Petre
 * Dual licensed under the MIT (MIT-LICENSE.txt) 
 * and GPL (GPL-LICENSE.txt) licenses.
 */
 eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('5.R={4x:z(e,s){J l=0;J t=0;J 2C=0;J 2t=0;J w=5.H(e,\'23\');J h=5.H(e,\'27\');J 10=e.3V;J T=e.3E;3C(e.3S){l+=e.2O+(e.1c?F(e.1c.3d)||0:0);t+=e.2X+(e.1c?F(e.1c.3f)||0:0);9(s){2C+=e.1z.1V||0;2t+=e.1z.1J||0}e=e.3S}l+=e.2O+(e.1c?F(e.1c.3d)||0:0);t+=e.2X+(e.1c?F(e.1c.3f)||0:0);2t=t-2t;2C=l-2C;B{x:l,y:t,5t:2C,4Z:2t,w:w,h:h,10:10,T:T}},1N:z(e){J x=0;J y=0;J 3N=g;Y=e.K;9(5(e).H(\'Q\')==\'15\'){3a=Y.2d;39=Y.1k;Y.2d=\'3B\';Y.Q=\'20\';Y.1k=\'36\';3N=C}A=e;3C(A){x+=A.2O+(A.1c&&!5.1O.2P?F(A.1c.3d)||0:0);y+=A.2X+(A.1c&&!5.1O.2P?F(A.1c.3f)||0:0);A=A.3S}A=e;3C(A&&A.50.51()!=\'12\'){x-=A.1V||0;y-=A.1J||0;A=A.1z}9(3N){Y.Q=\'15\';Y.1k=39;Y.2d=3a}B{x:x,y:y}},1D:z(e){J w=5.H(e,\'23\');J h=5.H(e,\'27\');J 10=0;J T=0;Y=e.K;9(5(e).H(\'Q\')!=\'15\'){10=e.3V;T=e.3E}S{3a=Y.2d;39=Y.1k;Y.2d=\'3B\';Y.Q=\'20\';Y.1k=\'36\';10=e.3V;T=e.3E;Y.Q=\'15\';Y.1k=39;Y.2d=3a}B{w:w,h:h,10:10,T:T}},43:z(e){9(e){w=e.2s;h=e.2J}S{2q=I.1f;w=2c.3H||3b.3H||(2q&&2q.2s)||I.12.2s;h=2c.3I||3b.3I||(2q&&2q.2J)||I.12.2J}B{w:w,h:h}},53:z(e){9(e){t=e.1J;l=e.1V;w=e.3F;h=e.3G;3h=0;3c=0}S{9(I.1f&&I.1f.1J){t=I.1f.1J;l=I.1f.1V;w=I.1f.3F;h=I.1f.3G}S 9(I.12){t=I.12.1J;l=I.12.1V;w=I.12.3F;h=I.12.3G}3h=3b.3H||I.1f.2s||I.12.2s||0;3c=3b.3I||I.1f.2J||I.12.2J||0}B{t:t,l:l,w:w,h:h,3h:3h,3c:3c}},4y:z(e,2f){A=5(e);t=A.H(\'2A\')||\'\';r=A.H(\'2m\')||\'\';b=A.H(\'2x\')||\'\';l=A.H(\'2n\')||\'\';9(2f)B{t:F(t)||0,r:F(r)||0,b:F(b)||0,l:F(l)};S B{t:t,r:r,b:b,l:l}},5p:z(e,2f){A=5(e);t=A.H(\'54\')||\'\';r=A.H(\'55\')||\'\';b=A.H(\'56\')||\'\';l=A.H(\'57\')||\'\';9(2f)B{t:F(t)||0,r:F(r)||0,b:F(b)||0,l:F(l)};S B{t:t,r:r,b:b,l:l}},2Q:z(e,2f){A=5(e);t=A.H(\'3f\')||\'\';r=A.H(\'58\')||\'\';b=A.H(\'59\')||\'\';l=A.H(\'3d\')||\'\';9(2f)B{t:F(t)||0,r:F(r)||0,b:F(b)||0,l:F(l)||0};S B{t:t,r:r,b:b,l:l}},3x:z(2E){x=2E.5a||(2E.5b+(I.1f.1V||I.12.1V))||0;y=2E.5c||(2E.5d+(I.1f.1J||I.12.1J))||0;B{x:x,y:y}}};5.u={2e:[],1w:{},D:g,2h:N,4a:z(){9(5.j.7==N){B}J i;5.u.D.G(0).2z=5.j.7.6.1U;1o=5.u.D.G(0).K;1o.Q=\'20\';5.u.D.E=5.R.4x(5.u.D.G(0));1o.23=5.j.7.6.E.10+\'19\';1o.27=5.j.7.6.E.T+\'19\';2G=5.R.4y(5.j.7);1o.2A=2G.t;1o.2m=2G.r;1o.2x=2G.b;1o.2n=2G.l;9(5.j.7.6.18==C){c=5.j.7.4L(C);2l=c.K;2l.2A=\'1m\';2l.2m=\'1m\';2l.2x=\'1m\';2l.2n=\'1m\';2l.Q=\'20\';5.u.D.3q().1M(c)}5(5.j.7).4E(5.u.D.G(0));5.j.7.K.Q=\'15\'},4e:z(e){9(!e.6.1T&&5.v.1j.3L){9(e.6.1a)e.6.1a.1E(7);5(e).H(\'1k\',e.6.3X||e.6.2j);5(e).3O();5(5.v.1j).4I(e)}5.u.D.2F(e.6.1U).5f(\'&4C;\');5.u.2h=N;1o=5.u.D.G(0).K;1o.Q=\'15\';2I=[];2B=g;1B(i 1L 5.u.2e){q=5.v.16[5.u.2e[i]].G(0);U=5.1x(q,\'U\');2H=5.u.2W(U);9(q.n.3u!=2H.32){q.n.3u=2H.32;9(2B==g&&q.n.24){2B=q.n.24}2H.U=U;2I[2I.1i]=2H}}9(2B!=g&&2I.1i>0){2B(2I)}5.u.2e=[]},33:z(e,o){9(!5.j.7)B;5.u.D.G(0).K.Q=\'20\';J 1H=g;J i=0;9(e.n.A.4z()>0){1B(i=e.n.A.4z();i>0;i--){9(e.n.A.G(i-1)!=5.j.7){9(!e.2k.3R){9((e.n.A.G(i-1).1P.y+e.n.A.G(i-1).1P.T/2)>5.j.7.6.1l){1H=e.n.A.G(i-1)}S{5h}}S{9((e.n.A.G(i-1).1P.x+e.n.A.G(i-1).1P.10/2)>5.j.7.6.1t&&(e.n.A.G(i-1).1P.y+e.n.A.G(i-1).1P.T/2)>5.j.7.6.1l){1H=e.n.A.G(i-1)}}}}}9(1H&&5.u.2h!=1H){5.u.2h=1H;5(1H).5i(5.u.D.G(0))}S 9(!1H&&(5.u.2h!=N||5.u.D.G(0).1z!=e)){5.u.2h=N;5(e).1M(5.u.D.G(0))}},3v:z(e){9(5.j.7==N){B}J i;e.n.A.1r(z(){k.1P=5.1u(5.R.1D(k),5.R.1N(k))})},2W:z(s){J i;J h=\'\';J o={};9(s){9(5.u.1w[s]){o[s]=[];5(\'#\'+s+\' .\'+5.u.1w[s]).1r(z(){9(h.1i>0){h+=\'&\'}h+=s+\'[]=\'+5.1x(k,\'U\');o[s][o[s].1i]=5.1x(k,\'U\')})}S{1B(a 1L s){9(5.u.1w[s[a]]){o[s[a]]=[];5(\'#\'+s[a]+\' .\'+5.u.1w[s[a]]).1r(z(){9(h.1i>0){h+=\'&\'}h+=s[a]+\'[]=\'+5.1x(k,\'U\');o[s[a]][o[s[a]].1i]=5.1x(k,\'U\')})}}}}S{1B(i 1L 5.u.1w){o[i]=[];5(\'#\'+i+\' .\'+5.u.1w[i]).1r(z(){9(h.1i>0){h+=\'&\'}h+=i+\'[]=\'+5.1x(k,\'U\');o[i][o[i].1i]=5.1x(k,\'U\')})}}B{32:h,o:o}},4J:z(e){9(!e.5j){B}B k.1r(z(){9(!k.2k||!5.2z.3t(e,k.2k.1n))5(e).2N(k.2k.1n);5(e).3o(k.2k.6)})},2a:z(o){9(o.1n&&5.R&&5.j&&5.v){9(!5.u.D){5(\'12\',I).1M(\'<34 U="4D">&4C;</34>\');5.u.D=5(\'#4D\');5.u.D.G(0).K.Q=\'15\'}k.42({1n:o.1n,2R:o.2R?o.2R:g,2U:o.2U?o.2U:g,1I:o.1I?o.1I:g,2v:z(3P,L){5.u.D.4E(3P);9(L>0){5(3P).5k(L)}},29:o.29||o.4m,22:o.22||o.4n,3L:C,1s:o.1s||o.24,L:o.L?o.L:g,18:o.18?C:g,1S:o.1S?o.1S:\'X\'});B k.1r(z(){6={1R:o.1R?C:g,4F:4G,14:o.14?3J(o.14):g,1U:o.1I?o.1I:g,L:o.L?o.L:g,1T:C,18:o.18?C:g,1F:o.1F?o.1F:N,M:o.M?o.M:N,1p:o.1p&&o.1p.1b==1Z?o.1p:g,1a:o.1a&&o.1a.1b==1Z?o.1a:g,W:/2D|2L/.4w(o.W)?o.W:g,1W:o.1W?F(o.1W)||0:g,V:o.V?o.V:g};5(\'.\'+o.1n,k).3o(6);k.5m=C;k.2k={1n:o.1n,1R:o.1R?C:g,4F:4G,14:o.14?3J(o.14):g,1U:o.1I?o.1I:g,L:o.L?o.L:g,1T:C,18:o.18?C:g,1F:o.1F?o.1F:N,M:o.M?o.M:N,3R:o.3R?C:g,6:6}})}}};5.3A.1u({5o:5.u.2a,4I:5.u.4J});5.5r=5.u.2W;5.j={D:N,7:N,3g:z(){B k.1r(z(){9(k.3i){k.2K=N;5(k).3s(\'3Z\',5.j.3y)}})},40:z(e){9(5.j.7!=N){5.j.2S(e);B g}J 8=k.2K;5(I).3n(\'47\',5.j.3w).3n(\'48\',5.j.2S);8.6.X=5.R.3x(e);8.6.1d=8.6.X;8.6.2Z=g;8.6.5s=k!=k.2K;5.j.7=8;9(8.6.1X&&k!=k.2K){3T=5.R.1N(8.1z);3U=5.R.1D(8);3W={x:F(5.H(8,\'1g\'))||0,y:F(5.H(8,\'1h\'))||0};O=8.6.1d.x-3T.x-3U.10/2-3W.x;P=8.6.1d.y-3T.y-3U.T/2-3W.y;5.3z.5u(8,[O,P])}B g},3y:z(e){8=5.j.7;8.6.2Z=C;2T=8.K;8.6.21=5.H(8,\'Q\');8.6.2j=5.H(8,\'1k\');9(!8.6.3X)8.6.3X=8.6.2j;8.6.Z={x:F(5.H(8,\'1g\'))||0,y:F(5.H(8,\'1h\'))||0};8.6.30=0;8.6.31=0;9(5.1O.3m){3Y=5.R.2Q(8,C);8.6.30=3Y.l||0;8.6.31=3Y.t||0}8.6.E=5.1u(5.R.1N(8),5.R.1D(8));9(8.6.2j!=\'4K\'&&8.6.2j!=\'36\'){2T.1k=\'4K\'}5.j.D.3q();1e=8.4L(C);5(1e).H({Q:\'20\',1g:\'1m\',1h:\'1m\'});1e.K.2A=\'0\';1e.K.2m=\'0\';1e.K.2x=\'0\';1e.K.2n=\'0\';5.j.D.1M(1e);9(8.6.1p)8.6.1p.1E(8,[1e]);17=5.j.D.G(0).K;9(8.6.3K){17.23=\'4M\';17.27=\'4M\'}S{17.27=8.6.E.T+\'19\';17.23=8.6.E.10+\'19\'}17.Q=\'20\';17.2A=\'1m\';17.2m=\'1m\';17.2x=\'1m\';17.2n=\'1m\';5.1u(8.6.E,5.R.1D(1e));9(8.6.V){9(8.6.V.1g){8.6.Z.x+=8.6.X.x-8.6.E.x-8.6.V.1g;8.6.E.x=8.6.X.x-8.6.V.1g}9(8.6.V.1h){8.6.Z.y+=8.6.X.y-8.6.E.y-8.6.V.1h;8.6.E.y=8.6.X.y-8.6.V.1h}9(8.6.V.3j){8.6.Z.x+=8.6.X.x-8.6.E.x-8.6.E.T+8.6.V.3j;8.6.E.x=8.6.X.x-8.6.E.10+8.6.V.3j}9(8.6.V.3k){8.6.Z.y+=8.6.X.y-8.6.E.y-8.6.E.T+8.6.V.3k;8.6.E.y=8.6.X.y-8.6.E.T+8.6.V.3k}}8.6.1t=8.6.Z.x;8.6.1l=8.6.Z.y;9(8.6.2u||8.6.M==\'3e\'){2p=5.R.2Q(8.1z,C);8.6.E.x=8.2O+(5.1O.3m?0:5.1O.2P?-2p.l:2p.l);8.6.E.y=8.2X+(5.1O.3m?0:5.1O.2P?-2p.t:2p.t);5(8.1z).1M(5.j.D.G(0))}9(8.6.M){5.j.41(8);8.6.1y.M=5.j.4h}9(8.6.1X){5.3z.4N(8)}17.1g=8.6.E.x-8.6.30+\'19\';17.1h=8.6.E.y-8.6.31+\'19\';17.23=8.6.E.10+\'19\';17.27=8.6.E.T+\'19\';5.j.7.6.37=g;9(8.6.2g){8.6.1y.1K=5.j.4f}9(8.6.2i!=g){5.j.D.H(\'2i\',8.6.2i)}9(8.6.14){5.j.D.H(\'14\',8.6.14);9(2c.38){5.j.D.H(\'44\',\'45(14=\'+8.6.14*46+\')\')}}9(8.6.18==g){2T.Q=\'15\'}9(5.v&&5.v.2w>0){5.v.49(8)}B g},41:z(8){9(8.6.M.1b==4A){9(8.6.M==\'3e\'){8.6.11=5.1u({x:0,y:0},5.R.1D(8.1z));2r=5.R.2Q(8.1z,C);8.6.11.w=8.6.11.10-2r.l-2r.r;8.6.11.h=8.6.11.T-2r.t-2r.b}S 9(8.6.M==\'I\'){3p=5.R.43();8.6.11={x:0,y:0,w:3p.w,h:3p.h}}}S 9(8.6.M.1b==4B){8.6.11={x:F(8.6.M[0])||0,y:F(8.6.M[1])||0,w:F(8.6.M[2])||0,h:F(8.6.M[3])||0}}8.6.11.O=8.6.11.x-8.6.E.x;8.6.11.P=8.6.11.y-8.6.E.y},2V:z(7){9(7.6.2u||7.6.M==\'3e\'){5(\'12\',I).1M(5.j.D.G(0))}5.j.D.3q().4O().H(\'14\',1);9(2c.38){5.j.D.H(\'44\',\'45(14=46)\')}},2S:z(e){5(I).3s(\'47\',5.j.3w).3s(\'48\',5.j.2S);9(5.j.7==N){B}7=5.j.7;5.j.7=N;9(7.6.2Z==g){B g}9(7.6.1T==C){5(7).H(\'1k\',7.6.2j)}2T=7.K;9(7.1X){5.j.D.H(\'4p\',\'4q\')}9(7.6.1R==g){9(7.6.L>0){9(!7.6.W||7.6.W==\'2L\'){x=4b 5.L(7,7.6.L,\'1g\');x.4c(7.6.Z.x,7.6.2o)}9(!7.6.W||7.6.W==\'2D\'){y=4b 5.L(7,7.6.L,\'1h\');y.4c(7.6.Z.y,7.6.2y)}}S{9(!7.6.W||7.6.W==\'2L\')7.K.1g=7.6.2o+\'19\';9(!7.6.W||7.6.W==\'2D\')7.K.1h=7.6.2y+\'19\'}5.j.2V(7);9(7.6.18==g){5(7).H(\'Q\',7.6.21)}}S 9(7.6.L>0){7.6.37=C;9(5.v&&5.v.1j&&5.u){28=5.R.1N(5.u.D.G(0))}S{28=g}5.j.D.4P({1g:28?28.x:7.6.E.x,1h:28?28.y:7.6.E.y},7.6.L,z(){7.6.37=g;9(7.6.18==g){7.K.Q=7.6.21}5.j.2V(7)})}S{5.j.2V(7);9(7.6.18==g){5(7).H(\'Q\',7.6.21)}}9(5.v&&5.v.2w>0){5.v.4v(7)}9(5.u&&5.v.1j){5.u.4e(7)}9(7.6.1s&&(7.6.2o!=7.6.Z.x||7.6.2y!=7.6.Z.y)){7.6.1s.1E(7,7.6.4Q||[0,0,7.6.2o,7.6.2y])}9(7.6.1a)7.6.1a.1E(7);B g},4f:z(x,y,O,P){9(O!=0)O=F((O+(k.6.2g*O/1q.4g(O))/2)/k.6.2g)*k.6.2g;9(P!=0)P=F((P+(k.6.2M*P/1q.4g(P))/2)/k.6.2M)*k.6.2M;B{O:O,P:P,x:0,y:0}},4h:z(x,y,O,P){O=1q.4i(1q.4j(O,k.6.11.O),k.6.11.w+k.6.11.O-k.6.E.10);P=1q.4i(1q.4j(P,k.6.11.P),k.6.11.h+k.6.11.P-k.6.E.T);B{O:O,P:P,x:0,y:0}},3w:z(e){9(5.j.7==N||5.j.7.6.37==C){B}J 7=5.j.7;7.6.1d=5.R.3x(e);9(7.6.2Z==g){4l=1q.4S(1q.4k(7.6.X.x-7.6.1d.x,2)+1q.4k(7.6.X.y-7.6.1d.y,2));9(4l<7.6.1W){B}S{5.j.3y(e)}}O=7.6.1d.x-7.6.X.x;P=7.6.1d.y-7.6.X.y;1B(i 1L 7.6.1y){1Q=7.6.1y[i].1E(7,[7.6.Z.x+O,7.6.Z.y+P,O,P]);9(1Q&&1Q.1b==4T){O=i!=\'3M\'?1Q.O:(1Q.x-7.6.Z.x);P=i!=\'3M\'?1Q.P:(1Q.y-7.6.Z.y)}}7.6.1t=7.6.E.x+O-7.6.30;7.6.1l=7.6.E.y+P-7.6.31;9(7.6.1X&&(7.6.2b||7.6.1s)){5.3z.2b(7,7.6.1t,7.6.1l)}9(!7.6.W||7.6.W==\'2L\'){7.6.2o=7.6.Z.x+O;5.j.D.G(0).K.1g=7.6.1t+\'19\'}9(!7.6.W||7.6.W==\'2D\'){7.6.2y=7.6.Z.y+P;5.j.D.G(0).K.1h=7.6.1l+\'19\'}9(5.v&&5.v.2w>0){5.v.33(7,1e)}B g},2a:z(o){9(!5.j.D){5(\'12\',I).1M(\'<34 U="4o"></34>\');5.j.D=5(\'#4o\');A=5.j.D.G(0);1G=A.K;1G.1k=\'36\';1G.Q=\'15\';1G.4p=\'4q\';1G.4V=\'15\';1G.4W=\'3B\';9(2c.38){A.4t=z(){B g};A.4u=z(){B g}}S{1G.4X=\'15\';1G.4Y=\'15\'}}9(!o){o={}}B k.1r(z(){9(k.3i||!5.R)B;9(2c.38){k.4t=z(){B g};k.4u=z(){B g}}J 3l=o.1F?5(k).52(o.1F):5(k);k.6={1R:o.1R?C:g,18:o.18?C:g,1T:o.1T?o.1T:g,1X:o.1X?o.1X:g,2u:o.2u?o.2u:g,2i:o.2i?F(o.2i)||0:g,14:o.14?3J(o.14):g,L:F(o.L)||N,1U:o.1U?o.1U:g,1y:{},X:{},1p:o.1p&&o.1p.1b==1Z?o.1p:g,1a:o.1a&&o.1a.1b==1Z?o.1a:g,1s:o.1s&&o.1s.1b==1Z?o.1s:g,W:/2D|2L/.4w(o.W)?o.W:g,1W:o.1W?F(o.1W)||0:0,V:o.V?o.V:g,3K:o.3K?C:g};9(o.1y&&o.1y.1b==1Z)k.6.1y.3M=o.1y;9(o.M&&((o.M.1b==4A&&(o.M==\'3e\'||o.M==\'I\'))||(o.M.1b==4B&&o.M.1i==4))){k.6.M=o.M}9(o.3Q){k.6.3Q=o.3Q}9(o.1K){9(5n o.1K==\'5q\'){k.6.2g=F(o.1K)||1;k.6.2M=F(o.1K)||1}S 9(o.1K.1i==2){k.6.2g=F(o.1K[0])||1;k.6.2M=F(o.1K[1])||1}}9(o.2b&&o.2b.1b==1Z){k.6.2b=o.2b}k.3i=C;3l.G(0).2K=k;3l.3n(\'3Z\',5.j.40)})}};5.3A.1u({3O:5.j.3g,3o:5.j.2a});5.v={4r:z(1C,1v,25,26){B 1C<=5.j.7.6.1t&&(1C+25)>=(5.j.7.6.1t+5.j.7.6.E.w)&&1v<=5.j.7.6.1l&&(1v+26)>=(5.j.7.6.1l+5.j.7.6.E.h)?C:g},4s:z(1C,1v,25,26){B!(1C>(5.j.7.6.1t+5.j.7.6.E.w)||(1C+25)<5.j.7.6.1t||1v>(5.j.7.6.1l+5.j.7.6.E.h)||(1v+26)<5.j.7.6.1l)?C:g},X:z(1C,1v,25,26){B 1C<5.j.7.6.1d.x&&(1C+25)>5.j.7.6.1d.x&&1v<5.j.7.6.1d.y&&(1v+26)>5.j.7.6.1d.y?C:g},1j:g,13:{},2w:0,16:{},49:z(8){9(5.j.7==N){B}J i;5.v.13={};2Y=g;1B(i 1L 5.v.16){9(5.v.16[i]!=N){q=5.v.16[i].G(0);9(5.2z.3t(5.j.7,q.n.a)){9(q.n.m==g){q.n.p=5.1u(5.R.1N(q),5.R.1D(q));q.n.m=C}9(q.n.1A){5.v.16[i].2N(q.n.1A)}5.v.13[i]=5.v.16[i];9(5.u&&q.n.s==C){q.n.A=5(\'.\'+q.n.a,q);8.K.Q=\'15\';5.u.3v(q);8.K.Q=8.6.21;2Y=C}}}}9(2Y){5.u.4a()}},4H:z(){5.v.13={};1B(i 1L 5.v.16){9(5.v.16[i]!=N){q=5.v.16[i].G(0);9(5.2z.3t(5.j.7,q.n.a)){q.n.p=5.1u(5.R.1N(q),5.R.1D(q));9(q.n.1A){5.v.16[i].2N(q.n.1A)}5.v.13[i]=5.v.16[i];9(5.u&&q.n.s==C){q.n.A=5(\'.\'+q.n.a,q);8.K.Q=\'15\';5.u.3v(q);8.K.Q=8.6.21;2Y=C}}}}},33:z(e){9(5.j.7==N){B}5.v.1j=g;J i;3D=g;1B(i 1L 5.v.13){q=5.v.13[i].G(0);9(5.v.1j==g&&5.v[q.n.t](q.n.p.x,q.n.p.y,q.n.p.10,q.n.p.T)){9(q.n.1Y&&q.n.h==g){5.v.13[i].2F(q.n.1A);5.v.13[i].2N(q.n.1Y)}9(q.n.h==g&&q.n.29){3D=C}q.n.h=C;5.v.1j=q;9(5.u&&q.n.s==C){5.u.D.G(0).2z=q.n.4d;5.u.33(q)}}S{9(q.n.22&&q.n.h==C){q.n.22.1E(q,[e,1e,q.n.L])}9(q.n.1Y){5.v.13[i].2F(q.n.1Y);5.v.13[i].2N(q.n.1A)}q.n.h=g}}9(5.u&&5.v.1j==g){5.u.D.G(0).K.Q=\'15\';5(\'12\').1M(5.u.D.G(0))}9(3D){5.v.1j.n.29.1E(5.v.1j,[e,1e])}},4v:z(e){J i;1B(i 1L 5.v.13){q=5.v.13[i].G(0);9(q.n.1A){5.v.13[i].2F(q.n.1A)}9(q.n.1Y){5.v.13[i].2F(q.n.1Y)}9(q.n.s){5.u.2e[5.u.2e.1i]=i}9(q.n.2v&&q.n.h==C){q.n.h=g;q.n.2v.1E(q,[e,q.n.L])}q.n.m=g;q.n.h=g}5.v.13={}},3g:z(){B k.1r(z(){9(k.35){9(k.n.s){U=5.1x(k,\'U\');5.u.1w[U]=N;5(\'.\'+k.n.a,k).3O()}5.v.16[\'d\'+k.3r]=N;k.35=g;k.f=N}})},2a:z(o){B k.1r(z(){9(k.35==C||!o.1n||!5.R||!5.j){B}k.n={a:o.1n,1A:o.2R,1Y:o.2U,4d:o.1I,2v:o.4R||o.2v,29:o.29||o.4m,22:o.22||o.4n,t:o.1S&&(o.1S==\'4r\'||o.1S==\'4s\')?o.1S:\'X\',L:o.L?o.L:g,m:g,h:g};9(o.3L==C&&5.u){U=5.1x(k,\'U\');5.u.1w[U]=k.n.a;k.n.s=C;9(o.24){k.n.24=o.24;k.n.3u=5.u.2W(U).32}}k.35=C;k.3r=F(1q.5g()*5l);5.v.16[\'d\'+k.3r]=5(k);5.v.2w++})}};5.3A.1u({5e:5.v.3g,42:5.v.2a});5.4U=5.v.4H;',62,341,'|||||jQuery|dragCfg|dragged|elm|if|||||||false|||iDrag|this|||dropCfg|||iEL||||iSort|iDrop||||function|el|return|true|helper|oC|parseInt|get|css|document|var|style|fx|containment|null|dx|dy|display|iUtil|else|hb|id|cursorAt|axis|pointer|es|oR|wb|cont|body|highlighted|opacity|none|zones|dhs|ghosting|px|onStop|constructor|currentStyle|currentPointer|clonedEl|documentElement|left|top|length|overzone|position|ny|0px|accept|shs|onStart|Math|each|onChange|nx|extend|zoney|collected|attr|onDrag|parentNode|ac|for|zonex|getSize|apply|handle|els|cur|helperclass|scrollTop|grid|in|append|getPosition|browser|pos|newCoords|revert|tolerance|so|hpc|scrollLeft|snapDistance|si|hc|Function|block|oD|onOut|width|onchange|zonew|zoneh|height|dh|onHover|build|onSlide|window|visibility|changed|toInteger|gx|inFrontOf|zIndex|oP|sortCfg|cs|marginRight|marginLeft|nRx|parentBorders|de|contBorders|clientWidth|st|insideParent|onDrop|count|marginBottom|nRy|className|marginTop|fnc|sl|vertically|event|removeClass|margins|ser|ts|clientHeight|dragElem|horizontally|gy|addClass|offsetLeft|opera|getBorder|activeclass|dragstop|dEs|hoverclass|hidehelper|serialize|offsetTop|oneIsSortable|init|diffX|diffY|hash|checkhover|div|isDroppable|absolute|prot|ActiveXObject|oldPosition|oldVisibility|self|ih|borderLeftWidth|parent|borderTopWidth|destroy|iw|isDraggable|right|bottom|dhe|msie|bind|Draggable|clnt|empty|idsa|unbind|has|os|measure|dragmove|getPointer|dragstart|iSlider|fn|hidden|while|applyOnHover|offsetHeight|scrollWidth|scrollHeight|innerWidth|innerHeight|parseFloat|autoSize|sortable|user|restoreStyle|DraggableDestroy|drag|fractions|floats|offsetParent|parentPos|sliderSize|offsetWidth|sliderPos|initialPosition|oldBorder|mousedown|draginit|getContainment|Droppable|getClient|filter|alpha|100|mousemove|mouseup|highlight|start|new|custom|shc|check|snapToGrid|abs|fitToContainer|min|max|pow|distance|onhover|onout|dragHelper|cursor|move|fit|intersect|onselectstart|ondragstart|checkdrop|test|getPos|getMargins|size|String|Array|nbsp|sortHelper|after|zindex|3000|remeasure|SortableAddItem|addItem|relative|cloneNode|auto|modifyContainer|hide|animate|lastSi|ondrop|sqrt|Object|recallDroppables|listStyle|overflow|mozUserSelect|userSelect|sy|tagName|toLowerCase|find|getScroll|paddingTop|paddingRight|paddingBottom|paddingLeft|borderRightWidth|borderBottomWidth|pageX|clientX|pageY|clientY|DroppableDestroy|html|random|break|before|childNodes|fadeIn|10000|isSortable|typeof|Sortable|getPadding|number|SortSerialize|fromHandler|sx|dragmoveBy'.split('|'),0,{}))


/*
Description:

	Allows only valid characters to be entered into input boxes.
	Note: does not validate that the final text is a valid number
	  (that could be done by another script, or server-side)

Usage:

	$(window).load(
		function()
		{
			$(".numeric").numeric(); // or $(".numeric").numeric(",");
		}
	);

Parameters:

	decimal     : Decimal separator (e.g. '.' or ',' - default is '.')
	allowPaste  : let paste operations through (does nothing with IE - paste is always allowed)
*/
$.fn.numeric = function(decimal, allowPaste)
{
	this.keypress(
		function(e)
		{
			decimal = decimal || ".";
			allowPaste = allowPaste || true;
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			var allow = false;
			// allow Ctrl+A
			if((e.ctrlKey && key == 97 /* firefox */) || key == 65 /* opera */) allow = true;
			// allow Ctrl+X (cut)
			if((e.ctrlKey && key == 120 /* firefox */) || key == 88 /* opera */) allow = true;
			// allow Ctrl+C (copy)
			if((e.ctrlKey && key == 99 /* firefox */) || key == 67 /* opera */) allow = true;
			// allow Ctrl+Z (undo)
			if((e.ctrlKey && key == 122 /* firefox */) || key == 90 /* opera */) allow = true;
			// allow or deny Ctrl+V (paste)
			if((e.ctrlKey && key == 118 /* firefox */) || key == 86 /* opera */)
			{
				allow = allowPaste;
			}
			
			//Allow comma
			if(key == 44)
				allow = true;
			
			
			// if a number was not pressed
			if(key < 48 || key > 57)
			{
				/* '-' only allowed at start */
				if(key == 45 && this.value.length == 0) return true;
				/* only one decimal separator allowed */
				if(key == decimal.charCodeAt(0) && this.value.indexOf(decimal) != -1)
				{
					allow = false;
				}
				// check for other keys that have special purposes
				if(
					key != 8 /* backspace */ &&
					key != 9 /* tab */ &&
					key != 13 /* enter */ &&
					key != 35 /* end */ &&
					key != 36 /* home */ &&
					key != 37 /* left */ &&
					key != 39 /* right */ &&
					key != 46 /* del */ &&
					key != 44 /* comma */
				)
				{
					allow = false;
				}
				else
				{
					// for detecting special keys (listed above)
					// IE does not support 'charCode' and ignores them in keypress anyway
					if(typeof e.charCode != "undefined")
					{
						// special keys have 'keyCode' and 'which' the same (e.g. backspace)
						if(e.keyCode == e.which && e.which != 0)
						{
							allow = true;
						}
						// or keyCode != 0 and 'charCode'/'which' = 0
						else if(e.keyCode != 0 && e.charCode == 0 && e.which == 0)
						{
							allow = true;
						}
					}
				}
				// if key pressed is the decimal and it is not already in the field
				if(key == decimal.charCodeAt(0) && this.value.indexOf(decimal) == -1)
				{
					allow = true;
				}
			}
			else
			{
				allow = true;
			}
			return allow;
		}
	);
	return this;
}

$(document).ready(function()
{
	//Set any field with a class of numeric to only allow numric chars
	$('.numeric').numeric('.',true);
});


/*  Copyright Mihai Bazon, 2002-2005  |  www.bazon.net/mishoo
 * -----------------------------------------------------------
 *
 * The DHTML Calendar, version 1.0 "It is happening again"
 *
 * Details and latest version at:
 * www.dynarch.com/projects/calendar
 *
 * This script is developed by Dynarch.com.  Visit us at www.dynarch.com.
 *
 * This script is distributed under the GNU Lesser General Public License.
 * Read the entire license text here: http://www.gnu.org/licenses/lgpl.html
 */
 Calendar=function(firstDayOfWeek,dateStr,onSelected,onClose){this.activeDiv=null;this.currentDateEl=null;this.getDateStatus=null;this.getDateToolTip=null;this.getDateText=null;this.timeout=null;this.onSelected=onSelected||null;this.onClose=onClose||null;this.dragging=false;this.hidden=false;this.minYear=1970;this.maxYear=2050;this.dateFormat=Calendar._TT["DEF_DATE_FORMAT"];this.ttDateFormat=Calendar._TT["TT_DATE_FORMAT"];this.isPopup=true;this.weekNumbers=true;this.firstDayOfWeek=typeof firstDayOfWeek=="number"?firstDayOfWeek:Calendar._FD;this.showsOtherMonths=false;this.dateStr=dateStr;this.ar_days=null;this.showsTime=false;this.time24=true;this.yearStep=2;this.hiliteToday=true;this.multiple=null;this.table=null;this.element=null;this.tbody=null;this.firstdayname=null;this.monthsCombo=null;this.yearsCombo=null;this.hilitedMonth=null;this.activeMonth=null;this.hilitedYear=null;this.activeYear=null;this.dateClicked=false;if(typeof Calendar._SDN=="undefined"){if(typeof Calendar._SDN_len=="undefined")Calendar._SDN_len=3;var ar=new Array();for(var i=8;i>0;){ar[--i]=Calendar._DN[i].substr(0,Calendar._SDN_len);}Calendar._SDN=ar;if(typeof Calendar._SMN_len=="undefined")Calendar._SMN_len=3;ar=new Array();for(var i=12;i>0;){ar[--i]=Calendar._MN[i].substr(0,Calendar._SMN_len);}Calendar._SMN=ar;}};Calendar._C=null;Calendar.is_ie=(/msie/i.test(navigator.userAgent)&&!/opera/i.test(navigator.userAgent));Calendar.is_ie5=(Calendar.is_ie&&/msie 5\.0/i.test(navigator.userAgent));Calendar.is_opera=/opera/i.test(navigator.userAgent);Calendar.is_khtml=/Konqueror|Safari|KHTML/i.test(navigator.userAgent);Calendar.getAbsolutePos=function(el){var SL=0,ST=0;var is_div=/^div$/i.test(el.tagName);if(is_div&&el.scrollLeft)SL=el.scrollLeft;if(is_div&&el.scrollTop)ST=el.scrollTop;var r={x:el.offsetLeft-SL,y:el.offsetTop-ST};if(el.offsetParent){var tmp=this.getAbsolutePos(el.offsetParent);r.x+=tmp.x;r.y+=tmp.y;}return r;};Calendar.isRelated=function(el,evt){var related=evt.relatedTarget;if(!related){var type=evt.type;if(type=="mouseover"){related=evt.fromElement;}else if(type=="mouseout"){related=evt.toElement;}}while(related){if(related==el){return true;}related=related.parentNode;}return false;};Calendar.removeClass=function(el,className){if(!(el&&el.className)){return;}var cls=el.className.split(" ");var ar=new Array();for(var i=cls.length;i>0;){if(cls[--i]!=className){ar[ar.length]=cls[i];}}el.className=ar.join(" ");};Calendar.addClass=function(el,className){Calendar.removeClass(el,className);el.className+=" "+className;};Calendar.getElement=function(ev){var f=Calendar.is_ie?window.event.srcElement:ev.currentTarget;while(f.nodeType!=1||/^div$/i.test(f.tagName))f=f.parentNode;return f;};Calendar.getTargetElement=function(ev){var f=Calendar.is_ie?window.event.srcElement:ev.target;while(f.nodeType!=1)f=f.parentNode;return f;};Calendar.stopEvent=function(ev){ev||(ev=window.event);if(Calendar.is_ie){ev.cancelBubble=true;ev.returnValue=false;}else{ev.preventDefault();ev.stopPropagation();}return false;};Calendar.addEvent=function(el,evname,func){if(el.attachEvent){el.attachEvent("on"+evname,func);}else if(el.addEventListener){el.addEventListener(evname,func,true);}else{el["on"+evname]=func;}};Calendar.removeEvent=function(el,evname,func){if(el.detachEvent){el.detachEvent("on"+evname,func);}else if(el.removeEventListener){el.removeEventListener(evname,func,true);}else{el["on"+evname]=null;}};Calendar.createElement=function(type,parent){var el=null;if(document.createElementNS){el=document.createElementNS("http://www.w3.org/1999/xhtml",type);}else{el=document.createElement(type);}if(typeof parent!="undefined"){parent.appendChild(el);}return el;};Calendar._add_evs=function(el){with(Calendar){addEvent(el,"mouseover",dayMouseOver);addEvent(el,"mousedown",dayMouseDown);addEvent(el,"mouseout",dayMouseOut);if(is_ie){addEvent(el,"dblclick",dayMouseDblClick);el.setAttribute("unselectable",true);}}};Calendar.findMonth=function(el){if(typeof el.month!="undefined"){return el;}else if(typeof el.parentNode.month!="undefined"){return el.parentNode;}return null;};Calendar.findYear=function(el){if(typeof el.year!="undefined"){return el;}else if(typeof el.parentNode.year!="undefined"){return el.parentNode;}return null;};Calendar.showMonthsCombo=function(){var cal=Calendar._C;if(!cal){return false;}var cal=cal;var cd=cal.activeDiv;var mc=cal.monthsCombo;if(cal.hilitedMonth){Calendar.removeClass(cal.hilitedMonth,"hilite");}if(cal.activeMonth){Calendar.removeClass(cal.activeMonth,"active");}var mon=cal.monthsCombo.getElementsByTagName("div")[cal.date.getMonth()];Calendar.addClass(mon,"active");cal.activeMonth=mon;var s=mc.style;s.display="block";if(cd.navtype<0)s.left=cd.offsetLeft+"px";else{var mcw=mc.offsetWidth;if(typeof mcw=="undefined")mcw=50;s.left=(cd.offsetLeft+cd.offsetWidth-mcw)+"px";}s.top=(cd.offsetTop+cd.offsetHeight)+"px";};Calendar.showYearsCombo=function(fwd){var cal=Calendar._C;if(!cal){return false;}var cal=cal;var cd=cal.activeDiv;var yc=cal.yearsCombo;if(cal.hilitedYear){Calendar.removeClass(cal.hilitedYear,"hilite");}if(cal.activeYear){Calendar.removeClass(cal.activeYear,"active");}cal.activeYear=null;var Y=cal.date.getFullYear()+(fwd?1:-1);var yr=yc.firstChild;var show=false;for(var i=12;i>0;--i){if(Y>=cal.minYear&&Y<=cal.maxYear){yr.innerHTML=Y;yr.year=Y;yr.style.display="block";show=true;}else{yr.style.display="none";}yr=yr.nextSibling;Y+=fwd?cal.yearStep:-cal.yearStep;}if(show){var s=yc.style;s.display="block";if(cd.navtype<0)s.left=cd.offsetLeft+"px";else{var ycw=yc.offsetWidth;if(typeof ycw=="undefined")ycw=50;s.left=(cd.offsetLeft+cd.offsetWidth-ycw)+"px";}s.top=(cd.offsetTop+cd.offsetHeight)+"px";}};Calendar.tableMouseUp=function(ev){var cal=Calendar._C;if(!cal){return false;}if(cal.timeout){clearTimeout(cal.timeout);}var el=cal.activeDiv;if(!el){return false;}var target=Calendar.getTargetElement(ev);ev||(ev=window.event);Calendar.removeClass(el,"active");if(target==el||target.parentNode==el){Calendar.cellClick(el,ev);}var mon=Calendar.findMonth(target);var date=null;if(mon){date=new Date(cal.date);if(mon.month!=date.getMonth()){date.setMonth(mon.month);cal.setDate(date);cal.dateClicked=false;cal.callHandler();}}else{var year=Calendar.findYear(target);if(year){date=new Date(cal.date);if(year.year!=date.getFullYear()){date.setFullYear(year.year);cal.setDate(date);cal.dateClicked=false;cal.callHandler();}}}with(Calendar){removeEvent(document,"mouseup",tableMouseUp);removeEvent(document,"mouseover",tableMouseOver);removeEvent(document,"mousemove",tableMouseOver);cal._hideCombos();_C=null;return stopEvent(ev);}};Calendar.tableMouseOver=function(ev){var cal=Calendar._C;if(!cal){return;}var el=cal.activeDiv;var target=Calendar.getTargetElement(ev);if(target==el||target.parentNode==el){Calendar.addClass(el,"hilite active");Calendar.addClass(el.parentNode,"rowhilite");}else{if(typeof el.navtype=="undefined"||(el.navtype!=50&&(el.navtype==0||Math.abs(el.navtype)>2)))Calendar.removeClass(el,"active");Calendar.removeClass(el,"hilite");Calendar.removeClass(el.parentNode,"rowhilite");}ev||(ev=window.event);if(el.navtype==50&&target!=el){var pos=Calendar.getAbsolutePos(el);var w=el.offsetWidth;var x=ev.clientX;var dx;var decrease=true;if(x>pos.x+w){dx=x-pos.x-w;decrease=false;}else dx=pos.x-x;if(dx<0)dx=0;var range=el._range;var current=el._current;var count=Math.floor(dx/10)%range.length;for(var i=range.length;--i>=0;)if(range[i]==current)break;while(count-->0)if(decrease){if(--i<0)i=range.length-1;}else if(++i>=range.length)i=0;var newval=range[i];el.innerHTML=newval;cal.onUpdateTime();}var mon=Calendar.findMonth(target);if(mon){if(mon.month!=cal.date.getMonth()){if(cal.hilitedMonth){Calendar.removeClass(cal.hilitedMonth,"hilite");}Calendar.addClass(mon,"hilite");cal.hilitedMonth=mon;}else if(cal.hilitedMonth){Calendar.removeClass(cal.hilitedMonth,"hilite");}}else{if(cal.hilitedMonth){Calendar.removeClass(cal.hilitedMonth,"hilite");}var year=Calendar.findYear(target);if(year){if(year.year!=cal.date.getFullYear()){if(cal.hilitedYear){Calendar.removeClass(cal.hilitedYear,"hilite");}Calendar.addClass(year,"hilite");cal.hilitedYear=year;}else if(cal.hilitedYear){Calendar.removeClass(cal.hilitedYear,"hilite");}}else if(cal.hilitedYear){Calendar.removeClass(cal.hilitedYear,"hilite");}}return Calendar.stopEvent(ev);};Calendar.tableMouseDown=function(ev){if(Calendar.getTargetElement(ev)==Calendar.getElement(ev)){return Calendar.stopEvent(ev);}};Calendar.calDragIt=function(ev){var cal=Calendar._C;if(!(cal&&cal.dragging)){return false;}var posX;var posY;if(Calendar.is_ie){posY=window.event.clientY+document.body.scrollTop;posX=window.event.clientX+document.body.scrollLeft;}else{posX=ev.pageX;posY=ev.pageY;}cal.hideShowCovered();var st=cal.element.style;st.left=(posX-cal.xOffs)+"px";st.top=(posY-cal.yOffs)+"px";return Calendar.stopEvent(ev);};Calendar.calDragEnd=function(ev){var cal=Calendar._C;if(!cal){return false;}cal.dragging=false;with(Calendar){removeEvent(document,"mousemove",calDragIt);removeEvent(document,"mouseup",calDragEnd);tableMouseUp(ev);}cal.hideShowCovered();};Calendar.dayMouseDown=function(ev){var el=Calendar.getElement(ev);if(el.disabled){return false;}var cal=el.calendar;cal.activeDiv=el;Calendar._C=cal;if(el.navtype!=300)with(Calendar){if(el.navtype==50){el._current=el.innerHTML;addEvent(document,"mousemove",tableMouseOver);}else addEvent(document,Calendar.is_ie5?"mousemove":"mouseover",tableMouseOver);addClass(el,"hilite active");addEvent(document,"mouseup",tableMouseUp);}else if(cal.isPopup){cal._dragStart(ev);}if(el.navtype==-1||el.navtype==1){if(cal.timeout)clearTimeout(cal.timeout);cal.timeout=setTimeout("Calendar.showMonthsCombo()",250);}else if(el.navtype==-2||el.navtype==2){if(cal.timeout)clearTimeout(cal.timeout);cal.timeout=setTimeout((el.navtype>0)?"Calendar.showYearsCombo(true)":"Calendar.showYearsCombo(false)",250);}else{cal.timeout=null;}return Calendar.stopEvent(ev);};Calendar.dayMouseDblClick=function(ev){Calendar.cellClick(Calendar.getElement(ev),ev||window.event);if(Calendar.is_ie){document.selection.empty();}};Calendar.dayMouseOver=function(ev){var el=Calendar.getElement(ev);if(Calendar.isRelated(el,ev)||Calendar._C||el.disabled){return false;}if(el.ttip){if(el.ttip.substr(0,1)=="_"){el.ttip=el.caldate.print(el.calendar.ttDateFormat)+el.ttip.substr(1);}el.calendar.tooltips.innerHTML=el.ttip;}if(el.navtype!=300){Calendar.addClass(el,"hilite");if(el.caldate){Calendar.addClass(el.parentNode,"rowhilite");}}return Calendar.stopEvent(ev);};Calendar.dayMouseOut=function(ev){with(Calendar){var el=getElement(ev);if(isRelated(el,ev)||_C||el.disabled)return false;removeClass(el,"hilite");if(el.caldate)removeClass(el.parentNode,"rowhilite");if(el.calendar)el.calendar.tooltips.innerHTML=_TT["SEL_DATE"];return stopEvent(ev);}};Calendar.cellClick=function(el,ev){var cal=el.calendar;var closing=false;var newdate=false;var date=null;if(typeof el.navtype=="undefined"){if(cal.currentDateEl){Calendar.removeClass(cal.currentDateEl,"selected");Calendar.addClass(el,"selected");closing=(cal.currentDateEl==el);if(!closing){cal.currentDateEl=el;}}cal.date.setDateOnly(el.caldate);date=cal.date;var other_month=!(cal.dateClicked=!el.otherMonth);if(!other_month&&!cal.currentDateEl)cal._toggleMultipleDate(new Date(date));else newdate=!el.disabled;if(other_month)cal._init(cal.firstDayOfWeek,date);}else{if(el.navtype==200){Calendar.removeClass(el,"hilite");cal.callCloseHandler();return;}date=new Date(cal.date);if(el.navtype==0)date.setDateOnly(new Date());cal.dateClicked=false;var year=date.getFullYear();var mon=date.getMonth();function setMonth(m){var day=date.getDate();var max=date.getMonthDays(m);if(day>max){date.setDate(max);}date.setMonth(m);};switch(el.navtype){case 400:Calendar.removeClass(el,"hilite");var text=Calendar._TT["ABOUT"];if(typeof text!="undefined"){text+=cal.showsTime?Calendar._TT["ABOUT_TIME"]:"";}else{text="Help and about box text is not translated into this language.\n"+"If you know this language and you feel generous please update\n"+"the corresponding file in \"lang\" subdir to match calendar-en.js\n"+"and send it back to <mihai_bazon@yahoo.com> to get it into the distribution  ;-)\n\n"+"Thank you!\n"+"http://dynarch.com/mishoo/calendar.epl\n";}alert(text);return;case-2:if(year>cal.minYear){date.setFullYear(year-1);}break;case-1:if(mon>0){setMonth(mon-1);}else if(year-->cal.minYear){date.setFullYear(year);setMonth(11);}break;case 1:if(mon<11){setMonth(mon+1);}else if(year<cal.maxYear){date.setFullYear(year+1);setMonth(0);}break;case 2:if(year<cal.maxYear){date.setFullYear(year+1);}break;case 100:cal.setFirstDayOfWeek(el.fdow);return;case 50:var range=el._range;var current=el.innerHTML;for(var i=range.length;--i>=0;)if(range[i]==current)break;if(ev&&ev.shiftKey){if(--i<0)i=range.length-1;}else if(++i>=range.length)i=0;var newval=range[i];el.innerHTML=newval;cal.onUpdateTime();return;case 0:if((typeof cal.getDateStatus=="function")&&cal.getDateStatus(date,date.getFullYear(),date.getMonth(),date.getDate())){return false;}break;}if(!date.equalsTo(cal.date)){cal.setDate(date);newdate=true;}else if(el.navtype==0)newdate=closing=true;}if(newdate){ev&&cal.callHandler();}if(closing){Calendar.removeClass(el,"hilite");ev&&cal.callCloseHandler();}};Calendar.prototype.create=function(_par){var parent=null;if(!_par){parent=document.getElementsByTagName("body")[0];this.isPopup=true;}else{parent=_par;this.isPopup=false;}this.date=this.dateStr?new Date(this.dateStr):new Date();var table=Calendar.createElement("table");this.table=table;table.cellSpacing=0;table.cellPadding=0;table.calendar=this;Calendar.addEvent(table,"mousedown",Calendar.tableMouseDown);var div=Calendar.createElement("div");this.element=div;div.className="calendar";if(this.isPopup){div.style.position="absolute";div.style.display="none";}div.appendChild(table);var thead=Calendar.createElement("thead",table);var cell=null;var row=null;var cal=this;var hh=function(text,cs,navtype){cell=Calendar.createElement("td",row);cell.colSpan=cs;cell.className="button";if(navtype!=0&&Math.abs(navtype)<=2)cell.className+=" nav";Calendar._add_evs(cell);cell.calendar=cal;cell.navtype=navtype;cell.innerHTML="<div unselectable='on'>"+text+"</div>";return cell;};row=Calendar.createElement("tr",thead);var title_length=6;(this.isPopup)&&--title_length;(this.weekNumbers)&&++title_length;hh("?",1,400).ttip=Calendar._TT["INFO"];this.title=hh("",title_length,300);this.title.className="title";if(this.isPopup){this.title.ttip=Calendar._TT["DRAG_TO_MOVE"];this.title.style.cursor="move";hh("&#x00d7;",1,200).ttip=Calendar._TT["CLOSE"];}row=Calendar.createElement("tr",thead);row.className="headrow";this._nav_py=hh("&#x00ab;",1,-2);this._nav_py.ttip=Calendar._TT["PREV_YEAR"];this._nav_pm=hh("&#x2039;",1,-1);this._nav_pm.ttip=Calendar._TT["PREV_MONTH"];this._nav_now=hh(Calendar._TT["TODAY"],this.weekNumbers?4:3,0);this._nav_now.ttip=Calendar._TT["GO_TODAY"];this._nav_nm=hh("&#x203a;",1,1);this._nav_nm.ttip=Calendar._TT["NEXT_MONTH"];this._nav_ny=hh("&#x00bb;",1,2);this._nav_ny.ttip=Calendar._TT["NEXT_YEAR"];row=Calendar.createElement("tr",thead);row.className="daynames";if(this.weekNumbers){cell=Calendar.createElement("td",row);cell.className="name wn";cell.innerHTML=Calendar._TT["WK"];}for(var i=7;i>0;--i){cell=Calendar.createElement("td",row);if(!i){cell.navtype=100;cell.calendar=this;Calendar._add_evs(cell);}}this.firstdayname=(this.weekNumbers)?row.firstChild.nextSibling:row.firstChild;this._displayWeekdays();var tbody=Calendar.createElement("tbody",table);this.tbody=tbody;for(i=6;i>0;--i){row=Calendar.createElement("tr",tbody);if(this.weekNumbers){cell=Calendar.createElement("td",row);}for(var j=7;j>0;--j){cell=Calendar.createElement("td",row);cell.calendar=this;Calendar._add_evs(cell);}}if(this.showsTime){row=Calendar.createElement("tr",tbody);row.className="time";cell=Calendar.createElement("td",row);cell.className="time";cell.colSpan=2;cell.innerHTML=Calendar._TT["TIME"]||"&nbsp;";cell=Calendar.createElement("td",row);cell.className="time";cell.colSpan=this.weekNumbers?4:3;(function(){function makeTimePart(className,init,range_start,range_end){var part=Calendar.createElement("span",cell);part.className=className;part.innerHTML=init;part.calendar=cal;part.ttip=Calendar._TT["TIME_PART"];part.navtype=50;part._range=[];if(typeof range_start!="number")part._range=range_start;else{for(var i=range_start;i<=range_end;++i){var txt;if(i<10&&range_end>=10)txt='0'+i;else txt=''+i;part._range[part._range.length]=txt;}}Calendar._add_evs(part);return part;};var hrs=cal.date.getHours();var mins=cal.date.getMinutes();var t12=!cal.time24;var pm=(hrs>12);if(t12&&pm)hrs-=12;var H=makeTimePart("hour",hrs,t12?1:0,t12?12:23);var span=Calendar.createElement("span",cell);span.innerHTML=":";span.className="colon";var M=makeTimePart("minute",mins,0,59);var AP=null;cell=Calendar.createElement("td",row);cell.className="time";cell.colSpan=2;if(t12)AP=makeTimePart("ampm",pm?"pm":"am",["am","pm"]);else cell.innerHTML="&nbsp;";cal.onSetTime=function(){var pm,hrs=this.date.getHours(),mins=this.date.getMinutes();if(t12){pm=(hrs>=12);if(pm)hrs-=12;if(hrs==0)hrs=12;AP.innerHTML=pm?"pm":"am";}H.innerHTML=(hrs<10)?("0"+hrs):hrs;M.innerHTML=(mins<10)?("0"+mins):mins;};cal.onUpdateTime=function(){var date=this.date;var h=parseInt(H.innerHTML,10);if(t12){if(/pm/i.test(AP.innerHTML)&&h<12)h+=12;else if(/am/i.test(AP.innerHTML)&&h==12)h=0;}var d=date.getDate();var m=date.getMonth();var y=date.getFullYear();date.setHours(h);date.setMinutes(parseInt(M.innerHTML,10));date.setFullYear(y);date.setMonth(m);date.setDate(d);this.dateClicked=false;this.callHandler();};})();}else{this.onSetTime=this.onUpdateTime=function(){};}var tfoot=Calendar.createElement("tfoot",table);row=Calendar.createElement("tr",tfoot);row.className="footrow";cell=hh(Calendar._TT["SEL_DATE"],this.weekNumbers?8:7,300);cell.className="ttip";if(this.isPopup){cell.ttip=Calendar._TT["DRAG_TO_MOVE"];cell.style.cursor="move";}this.tooltips=cell;div=Calendar.createElement("div",this.element);this.monthsCombo=div;div.className="combo";for(i=0;i<Calendar._MN.length;++i){var mn=Calendar.createElement("div");mn.className=Calendar.is_ie?"label-IEfix":"label";mn.month=i;mn.innerHTML=Calendar._SMN[i];div.appendChild(mn);}div=Calendar.createElement("div",this.element);this.yearsCombo=div;div.className="combo";for(i=12;i>0;--i){var yr=Calendar.createElement("div");yr.className=Calendar.is_ie?"label-IEfix":"label";div.appendChild(yr);}this._init(this.firstDayOfWeek,this.date);parent.appendChild(this.element);};Calendar._keyEvent=function(ev){var cal=window._dynarch_popupCalendar;if(!cal||cal.multiple)return false;(Calendar.is_ie)&&(ev=window.event);var act=(Calendar.is_ie||ev.type=="keypress"),K=ev.keyCode;if(ev.ctrlKey){switch(K){case 37:act&&Calendar.cellClick(cal._nav_pm);break;case 38:act&&Calendar.cellClick(cal._nav_py);break;case 39:act&&Calendar.cellClick(cal._nav_nm);break;case 40:act&&Calendar.cellClick(cal._nav_ny);break;default:return false;}}else switch(K){case 32:Calendar.cellClick(cal._nav_now);break;case 27:act&&cal.callCloseHandler();break;case 37:case 38:case 39:case 40:if(act){var prev,x,y,ne,el,step;prev=K==37||K==38;step=(K==37||K==39)?1:7;function setVars(){el=cal.currentDateEl;var p=el.pos;x=p&15;y=p>>4;ne=cal.ar_days[y][x];};setVars();function prevMonth(){var date=new Date(cal.date);date.setDate(date.getDate()-step);cal.setDate(date);};function nextMonth(){var date=new Date(cal.date);date.setDate(date.getDate()+step);cal.setDate(date);};while(1){switch(K){case 37:if(--x>=0)ne=cal.ar_days[y][x];else{x=6;K=38;continue;}break;case 38:if(--y>=0)ne=cal.ar_days[y][x];else{prevMonth();setVars();}break;case 39:if(++x<7)ne=cal.ar_days[y][x];else{x=0;K=40;continue;}break;case 40:if(++y<cal.ar_days.length)ne=cal.ar_days[y][x];else{nextMonth();setVars();}break;}break;}if(ne){if(!ne.disabled)Calendar.cellClick(ne);else if(prev)prevMonth();else nextMonth();}}break;case 13:if(act)Calendar.cellClick(cal.currentDateEl,ev);break;default:return false;}return Calendar.stopEvent(ev);};Calendar.prototype._init=function(firstDayOfWeek,date){var today=new Date(),TY=today.getFullYear(),TM=today.getMonth(),TD=today.getDate();this.table.style.visibility="hidden";var year=date.getFullYear();if(year<this.minYear){year=this.minYear;date.setFullYear(year);}else if(year>this.maxYear){year=this.maxYear;date.setFullYear(year);}this.firstDayOfWeek=firstDayOfWeek;this.date=new Date(date);var month=date.getMonth();var mday=date.getDate();var no_days=date.getMonthDays();date.setDate(1);var day1=(date.getDay()-this.firstDayOfWeek)%7;if(day1<0)day1+=7;date.setDate(-day1);date.setDate(date.getDate()+1);var row=this.tbody.firstChild;var MN=Calendar._SMN[month];var ar_days=this.ar_days=new Array();var weekend=Calendar._TT["WEEKEND"];var dates=this.multiple?(this.datesCells={}):null;for(var i=0;i<6;++i,row=row.nextSibling){var cell=row.firstChild;if(this.weekNumbers){cell.className="day wn";cell.innerHTML=date.getWeekNumber();cell=cell.nextSibling;}row.className="daysrow";var hasdays=false,iday,dpos=ar_days[i]=[];for(var j=0;j<7;++j,cell=cell.nextSibling,date.setDate(iday+1)){iday=date.getDate();var wday=date.getDay();cell.className="day";cell.pos=i<<4|j;dpos[j]=cell;var current_month=(date.getMonth()==month);if(!current_month){if(this.showsOtherMonths){cell.className+=" othermonth";cell.otherMonth=true;}else{cell.className="emptycell";cell.innerHTML="&nbsp;";cell.disabled=true;continue;}}else{cell.otherMonth=false;hasdays=true;}cell.disabled=false;cell.innerHTML=this.getDateText?this.getDateText(date,iday):iday;if(dates)dates[date.print("%Y%m%d")]=cell;if(this.getDateStatus){var status=this.getDateStatus(date,year,month,iday);if(this.getDateToolTip){var toolTip=this.getDateToolTip(date,year,month,iday);if(toolTip)cell.title=toolTip;}if(status===true){cell.className+=" disabled";cell.disabled=true;}else{if(/disabled/i.test(status))cell.disabled=true;cell.className+=" "+status;}}if(!cell.disabled){cell.caldate=new Date(date);cell.ttip="_";if(!this.multiple&&current_month&&iday==mday&&this.hiliteToday){cell.className+=" selected";this.currentDateEl=cell;}if(date.getFullYear()==TY&&date.getMonth()==TM&&iday==TD){cell.className+=" today";cell.ttip+=Calendar._TT["PART_TODAY"];}if(weekend.indexOf(wday.toString())!=-1)cell.className+=cell.otherMonth?" oweekend":" weekend";}}if(!(hasdays||this.showsOtherMonths))row.className="emptyrow";}this.title.innerHTML=Calendar._MN[month]+", "+year;this.onSetTime();this.table.style.visibility="visible";this._initMultipleDates();};Calendar.prototype._initMultipleDates=function(){if(this.multiple){for(var i in this.multiple){var cell=this.datesCells[i];var d=this.multiple[i];if(!d)continue;if(cell)cell.className+=" selected";}}};Calendar.prototype._toggleMultipleDate=function(date){if(this.multiple){var ds=date.print("%Y%m%d");var cell=this.datesCells[ds];if(cell){var d=this.multiple[ds];if(!d){Calendar.addClass(cell,"selected");this.multiple[ds]=date;}else{Calendar.removeClass(cell,"selected");delete this.multiple[ds];}}}};Calendar.prototype.setDateToolTipHandler=function(unaryFunction){this.getDateToolTip=unaryFunction;};Calendar.prototype.setDate=function(date){if(!date.equalsTo(this.date)){this._init(this.firstDayOfWeek,date);}};Calendar.prototype.refresh=function(){this._init(this.firstDayOfWeek,this.date);};Calendar.prototype.setFirstDayOfWeek=function(firstDayOfWeek){this._init(firstDayOfWeek,this.date);this._displayWeekdays();};Calendar.prototype.setDateStatusHandler=Calendar.prototype.setDisabledHandler=function(unaryFunction){this.getDateStatus=unaryFunction;};Calendar.prototype.setRange=function(a,z){this.minYear=a;this.maxYear=z;};Calendar.prototype.callHandler=function(){if(this.onSelected){this.onSelected(this,this.date.print(this.dateFormat));}};Calendar.prototype.callCloseHandler=function(){if(this.onClose){this.onClose(this);}this.hideShowCovered();};Calendar.prototype.destroy=function(){var el=this.element.parentNode;el.removeChild(this.element);Calendar._C=null;window._dynarch_popupCalendar=null;};Calendar.prototype.reparent=function(new_parent){var el=this.element;el.parentNode.removeChild(el);new_parent.appendChild(el);};Calendar._checkCalendar=function(ev){var calendar=window._dynarch_popupCalendar;if(!calendar){return false;}var el=Calendar.is_ie?Calendar.getElement(ev):Calendar.getTargetElement(ev);for(;el!=null&&el!=calendar.element;el=el.parentNode);if(el==null){window._dynarch_popupCalendar.callCloseHandler();return Calendar.stopEvent(ev);}};Calendar.prototype.show=function(){var rows=this.table.getElementsByTagName("tr");for(var i=rows.length;i>0;){var row=rows[--i];Calendar.removeClass(row,"rowhilite");var cells=row.getElementsByTagName("td");for(var j=cells.length;j>0;){var cell=cells[--j];Calendar.removeClass(cell,"hilite");Calendar.removeClass(cell,"active");}}this.element.style.display="block";this.hidden=false;if(this.isPopup){window._dynarch_popupCalendar=this;Calendar.addEvent(document,"keydown",Calendar._keyEvent);Calendar.addEvent(document,"keypress",Calendar._keyEvent);Calendar.addEvent(document,"mousedown",Calendar._checkCalendar);}this.hideShowCovered();};Calendar.prototype.hide=function(){if(this.isPopup){Calendar.removeEvent(document,"keydown",Calendar._keyEvent);Calendar.removeEvent(document,"keypress",Calendar._keyEvent);Calendar.removeEvent(document,"mousedown",Calendar._checkCalendar);}this.element.style.display="none";this.hidden=true;this.hideShowCovered();};Calendar.prototype.showAt=function(x,y){var s=this.element.style;s.left=x+"px";s.top=y+"px";this.show();};Calendar.prototype.showAtElement=function(el,opts){var self=this;var p=Calendar.getAbsolutePos(el);if(!opts||typeof opts!="string"){this.showAt(p.x,p.y+el.offsetHeight);return true;}function fixPosition(box){if(box.x<0)box.x=0;if(box.y<0)box.y=0;var cp=document.createElement("div");var s=cp.style;s.position="absolute";s.right=s.bottom=s.width=s.height="0px";document.body.appendChild(cp);var br=Calendar.getAbsolutePos(cp);document.body.removeChild(cp);if(Calendar.is_ie){br.y+=document.body.scrollTop;br.x+=document.body.scrollLeft;}else{br.y+=window.scrollY;br.x+=window.scrollX;}var tmp=box.x+box.width-br.x;if(tmp>0)box.x-=tmp;tmp=box.y+box.height-br.y;if(tmp>0)box.y-=tmp;};this.element.style.display="block";Calendar.continuation_for_the_fucking_khtml_browser=function(){var w=self.element.offsetWidth;var h=self.element.offsetHeight;self.element.style.display="none";var valign=opts.substr(0,1);var halign="l";if(opts.length>1){halign=opts.substr(1,1);}switch(valign){case "T":p.y-=h;break;case "B":p.y+=el.offsetHeight;break;case "C":p.y+=(el.offsetHeight-h)/2;break;case "t":p.y+=el.offsetHeight-h;break;case "b":break;}switch(halign){case "L":p.x-=w;break;case "R":p.x+=el.offsetWidth;break;case "C":p.x+=(el.offsetWidth-w)/2;break;case "l":p.x+=el.offsetWidth-w;break;case "r":break;}p.width=w;p.height=h+40;self.monthsCombo.style.display="none";fixPosition(p);self.showAt(p.x,p.y);};if(Calendar.is_khtml)setTimeout("Calendar.continuation_for_the_fucking_khtml_browser()",10);else Calendar.continuation_for_the_fucking_khtml_browser();};Calendar.prototype.setDateFormat=function(str){this.dateFormat=str;};Calendar.prototype.setTtDateFormat=function(str){this.ttDateFormat=str;};Calendar.prototype.parseDate=function(str,fmt){if(!fmt)fmt=this.dateFormat;this.setDate(Date.parseDate(str,fmt));};Calendar.prototype.hideShowCovered=function(){if(!Calendar.is_ie&&!Calendar.is_opera)return;function getVisib(obj){var value=obj.style.visibility;if(!value){if(document.defaultView&&typeof(document.defaultView.getComputedStyle)=="function"){if(!Calendar.is_khtml)value=document.defaultView. getComputedStyle(obj,"").getPropertyValue("visibility");else value='';}else if(obj.currentStyle){value=obj.currentStyle.visibility;}else value='';}return value;};var tags=new Array("applet","iframe","select");var el=this.element;var p=Calendar.getAbsolutePos(el);var EX1=p.x;var EX2=el.offsetWidth+EX1;var EY1=p.y;var EY2=el.offsetHeight+EY1;for(var k=tags.length;k>0;){var ar=document.getElementsByTagName(tags[--k]);var cc=null;for(var i=ar.length;i>0;){cc=ar[--i];p=Calendar.getAbsolutePos(cc);var CX1=p.x;var CX2=cc.offsetWidth+CX1;var CY1=p.y;var CY2=cc.offsetHeight+CY1;if(this.hidden||(CX1>EX2)||(CX2<EX1)||(CY1>EY2)||(CY2<EY1)){if(!cc.__msh_save_visibility){cc.__msh_save_visibility=getVisib(cc);}cc.style.visibility=cc.__msh_save_visibility;}else{if(!cc.__msh_save_visibility){cc.__msh_save_visibility=getVisib(cc);}cc.style.visibility="hidden";}}}};Calendar.prototype._displayWeekdays=function(){var fdow=this.firstDayOfWeek;var cell=this.firstdayname;var weekend=Calendar._TT["WEEKEND"];for(var i=0;i<7;++i){cell.className="day name";var realday=(i+fdow)%7;if(i){cell.ttip=Calendar._TT["DAY_FIRST"].replace("%s",Calendar._DN[realday]);cell.navtype=100;cell.calendar=this;cell.fdow=realday;Calendar._add_evs(cell);}if(weekend.indexOf(realday.toString())!=-1){Calendar.addClass(cell,"weekend");}cell.innerHTML=Calendar._SDN[(i+fdow)%7];cell=cell.nextSibling;}};Calendar.prototype._hideCombos=function(){this.monthsCombo.style.display="none";this.yearsCombo.style.display="none";};Calendar.prototype._dragStart=function(ev){if(this.dragging){return;}this.dragging=true;var posX;var posY;if(Calendar.is_ie){posY=window.event.clientY+document.body.scrollTop;posX=window.event.clientX+document.body.scrollLeft;}else{posY=ev.clientY+window.scrollY;posX=ev.clientX+window.scrollX;}var st=this.element.style;this.xOffs=posX-parseInt(st.left);this.yOffs=posY-parseInt(st.top);with(Calendar){addEvent(document,"mousemove",calDragIt);addEvent(document,"mouseup",calDragEnd);}};Date._MD=new Array(31,28,31,30,31,30,31,31,30,31,30,31);Date.SECOND=1000;Date.MINUTE=60*Date.SECOND;Date.HOUR=60*Date.MINUTE;Date.DAY=24*Date.HOUR;Date.WEEK=7*Date.DAY;Date.parseDate=function(str,fmt){var today=new Date();var y=0;var m=-1;var d=0;var a=str.split(/\W+/);var b=fmt.match(/%./g);var i=0,j=0;var hr=0;var min=0;for(i=0;i<a.length;++i){if(!a[i])continue;switch(b[i]){case "%d":case "%e":d=parseInt(a[i],10);break;case "%m":m=parseInt(a[i],10)-1;break;case "%Y":case "%y":y=parseInt(a[i],10);(y<100)&&(y+=(y>29)?1900:2000);break;case "%b":case "%B":for(j=0;j<12;++j){if(Calendar._MN[j].substr(0,a[i].length).toLowerCase()==a[i].toLowerCase()){m=j;break;}}break;case "%H":case "%I":case "%k":case "%l":hr=parseInt(a[i],10);break;case "%P":case "%p":if(/pm/i.test(a[i])&&hr<12)hr+=12;else if(/am/i.test(a[i])&&hr>=12)hr-=12;break;case "%M":min=parseInt(a[i],10);break;}}if(isNaN(y))y=today.getFullYear();if(isNaN(m))m=today.getMonth();if(isNaN(d))d=today.getDate();if(isNaN(hr))hr=today.getHours();if(isNaN(min))min=today.getMinutes();if(y!=0&&m!=-1&&d!=0)return new Date(y,m,d,hr,min,0);y=0;m=-1;d=0;for(i=0;i<a.length;++i){if(a[i].search(/[a-zA-Z]+/)!=-1){var t=-1;for(j=0;j<12;++j){if(Calendar._MN[j].substr(0,a[i].length).toLowerCase()==a[i].toLowerCase()){t=j;break;}}if(t!=-1){if(m!=-1){d=m+1;}m=t;}}else if(parseInt(a[i],10)<=12&&m==-1){m=a[i]-1;}else if(parseInt(a[i],10)>31&&y==0){y=parseInt(a[i],10);(y<100)&&(y+=(y>29)?1900:2000);}else if(d==0){d=a[i];}}if(y==0)y=today.getFullYear();if(m!=-1&&d!=0)return new Date(y,m,d,hr,min,0);return today;};Date.prototype.getMonthDays=function(month){var year=this.getFullYear();if(typeof month=="undefined"){month=this.getMonth();}if(((0==(year%4))&&((0!=(year%100))||(0==(year%400))))&&month==1){return 29;}else{return Date._MD[month];}};Date.prototype.getDayOfYear=function(){var now=new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0);var then=new Date(this.getFullYear(),0,0,0,0,0);var time=now-then;return Math.floor(time/Date.DAY);};Date.prototype.getWeekNumber=function(){var d=new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0);var DoW=d.getDay();d.setDate(d.getDate()-(DoW+6)%7+3);var ms=d.valueOf();d.setMonth(0);d.setDate(4);return Math.round((ms-d.valueOf())/(7*864e5))+1;};Date.prototype.equalsTo=function(date){return((this.getFullYear()==date.getFullYear())&&(this.getMonth()==date.getMonth())&&(this.getDate()==date.getDate())&&(this.getHours()==date.getHours())&&(this.getMinutes()==date.getMinutes()));};Date.prototype.setDateOnly=function(date){var tmp=new Date(date);this.setDate(1);this.setFullYear(tmp.getFullYear());this.setMonth(tmp.getMonth());this.setDate(tmp.getDate());};Date.prototype.print=function(str){var m=this.getMonth();var d=this.getDate();var y=this.getFullYear();var wn=this.getWeekNumber();var w=this.getDay();var s={};var hr=this.getHours();var pm=(hr>=12);var ir=(pm)?(hr-12):hr;var dy=this.getDayOfYear();if(ir==0)ir=12;var min=this.getMinutes();var sec=this.getSeconds();s["%a"]=Calendar._SDN[w];s["%A"]=Calendar._DN[w];s["%b"]=Calendar._SMN[m];s["%B"]=Calendar._MN[m];s["%C"]=1+Math.floor(y/100);s["%d"]=(d<10)?("0"+d):d;s["%e"]=d;s["%H"]=(hr<10)?("0"+hr):hr;s["%I"]=(ir<10)?("0"+ir):ir;s["%j"]=(dy<100)?((dy<10)?("00"+dy):("0"+dy)):dy;s["%k"]=hr;s["%l"]=ir;s["%m"]=(m<9)?("0"+(1+m)):(1+m);s["%M"]=(min<10)?("0"+min):min;s["%n"]="\n";s["%p"]=pm?"PM":"AM";s["%P"]=pm?"pm":"am";s["%s"]=Math.floor(this.getTime()/1000);s["%S"]=(sec<10)?("0"+sec):sec;s["%t"]="\t";s["%U"]=s["%W"]=s["%V"]=(wn<10)?("0"+wn):wn;s["%u"]=w+1;s["%w"]=w;s["%y"]=(''+y).substr(2,2);s["%Y"]=y;s["%%"]="%";var re=/%./g;if(!Calendar.is_ie5&&!Calendar.is_khtml)return str.replace(re,function(par){return s[par]||par;});var a=str.match(re);for(var i=0;i<a.length;i++){var tmp=s[a[i]];if(tmp){re=new RegExp(a[i],'g');str=str.replace(re,tmp);}}return str;};Date.prototype.__msh_oldSetFullYear=Date.prototype.setFullYear;Date.prototype.setFullYear=function(y){var d=new Date(this);d.__msh_oldSetFullYear(y);if(d.getMonth()!=this.getMonth())this.setDate(28);this.__msh_oldSetFullYear(y);};window._dynarch_popupCalendar=null;
  
 //Setup defaults
 Calendar.setup=function(params){function param_default(pname,def){if(typeof params[pname]=="undefined"){params[pname]=def;}};param_default("inputField",null);param_default("displayArea",null);param_default("button",null);param_default("eventName","click");param_default("ifFormat","%Y/%m/%d");param_default("daFormat","%Y/%m/%d");param_default("singleClick",true);param_default("disableFunc",null);param_default("dateStatusFunc",params["disableFunc"]);param_default("dateText",null);param_default("firstDay",null);param_default("align","Br");param_default("range",[1900,2999]);param_default("weekNumbers",true);param_default("flat",null);param_default("flatCallback",null);param_default("onSelect",null);param_default("onClose",null);param_default("onUpdate",null);param_default("date",null);param_default("showsTime",false);param_default("timeFormat","24");param_default("electric",true);param_default("step",2);param_default("position",null);param_default("cache",false);param_default("showOthers",false);param_default("multiple",null);var tmp=["inputField","displayArea","button"];for(var i in tmp){if(typeof params[tmp[i]]=="string"){params[tmp[i]]=document.getElementById(params[tmp[i]]);}}if(!(params.flat||params.multiple||params.inputField||params.displayArea||params.button)){alert("Calendar.setup:\n  Nothing to setup (no fields found).  Please check your code");return false;}function onSelect(cal){var p=cal.params;var update=(cal.dateClicked||p.electric);if(update&&p.inputField){p.inputField.value=cal.date.print(p.ifFormat);if(typeof p.inputField.onchange=="function")p.inputField.onchange();}if(update&&p.displayArea)p.displayArea.innerHTML=cal.date.print(p.daFormat);if(update&&typeof p.onUpdate=="function")p.onUpdate(cal);if(update&&p.flat){if(typeof p.flatCallback=="function")p.flatCallback(cal);}if(update&&p.singleClick&&cal.dateClicked)cal.callCloseHandler();};if(params.flat!=null){if(typeof params.flat=="string")params.flat=document.getElementById(params.flat);if(!params.flat){alert("Calendar.setup:\n  Flat specified but can't find parent.");return false;}var cal=new Calendar(params.firstDay,params.date,params.onSelect||onSelect);cal.showsOtherMonths=params.showOthers;cal.showsTime=params.showsTime;cal.time24=(params.timeFormat=="24");cal.params=params;cal.weekNumbers=params.weekNumbers;cal.setRange(params.range[0],params.range[1]);cal.setDateStatusHandler(params.dateStatusFunc);cal.getDateText=params.dateText;if(params.ifFormat){cal.setDateFormat(params.ifFormat);}if(params.inputField&&typeof params.inputField.value=="string"){cal.parseDate(params.inputField.value);}cal.create(params.flat);cal.show();return false;}var triggerEl=params.button||params.displayArea||params.inputField;triggerEl["on"+params.eventName]=function(){var dateEl=params.inputField||params.displayArea;var dateFmt=params.inputField?params.ifFormat:params.daFormat;var mustCreate=false;var cal=window.calendar;if(dateEl)params.date=Date.parseDate(dateEl.value||dateEl.innerHTML,dateFmt);if(!(cal&&params.cache)){window.calendar=cal=new Calendar(params.firstDay,params.date,params.onSelect||onSelect,params.onClose||function(cal){cal.hide();});cal.showsTime=params.showsTime;cal.time24=(params.timeFormat=="24");cal.weekNumbers=params.weekNumbers;mustCreate=true;}else{if(params.date)cal.setDate(params.date);cal.hide();}if(params.multiple){cal.multiple={};for(var i=params.multiple.length;--i>=0;){var d=params.multiple[i];var ds=d.print("%Y%m%d");cal.multiple[ds]=d;}}cal.showsOtherMonths=params.showOthers;cal.yearStep=params.step;cal.setRange(params.range[0],params.range[1]);cal.params=params;cal.setDateStatusHandler(params.dateStatusFunc);cal.getDateText=params.dateText;cal.setDateFormat(dateFmt);if(mustCreate)cal.create();cal.refresh();if(!params.position)cal.showAtElement(params.button||params.displayArea||params.inputField,params.align);else cal.showAt(params.position[0],params.position[1]);return false;};return cal;};
 
//English translations
Calendar._DN = new Array
("Sunday",
 "Monday",
 "Tuesday",
 "Wednesday",
 "Thursday",
 "Friday",
 "Saturday",
 "Sunday");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("Sun",
 "Mon",
 "Tue",
 "Wed",
 "Thu",
 "Fri",
 "Sat",
 "Sun");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// full month names
Calendar._MN = new Array
("January",
 "February",
 "March",
 "April",
 "May",
 "June",
 "July",
 "August",
 "September",
 "October",
 "November",
 "December");

// short month names
Calendar._SMN = new Array
("Jan",
 "Feb",
 "Mar",
 "Apr",
 "May",
 "Jun",
 "Jul",
 "Aug",
 "Sep",
 "Oct",
 "Nov",
 "Dec");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "About the calendar";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

Calendar._TT["PREV_YEAR"] = "Prev. year (hold for menu)";
Calendar._TT["PREV_MONTH"] = "Prev. month (hold for menu)";
Calendar._TT["GO_TODAY"] = "Go Today";
Calendar._TT["NEXT_MONTH"] = "Next month (hold for menu)";
Calendar._TT["NEXT_YEAR"] = "Next year (hold for menu)";
Calendar._TT["SEL_DATE"] = "Select date";
Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
Calendar._TT["PART_TODAY"] = " (today)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Display %s first";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Close";
Calendar._TT["TODAY"] = "Today";
Calendar._TT["TIME_PART"] = "(Shift-)Click or drag to change value";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "wk";
Calendar._TT["TIME"] = "Time:";
 