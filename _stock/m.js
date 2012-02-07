/*<![CDATA[
#(c)XAXMXAXRXUXLXLXZX
*/
function getMeta(mn){
  var m = document.getElementsByTagName('meta'); 
  for(var i=0;i<m.length;i++){ 
   if(m[i].name == mn){ 
     return m[i].content; 
   } 
  }
}
function getID(field){
    return document.getElementById ? document.getElementById(field) : document.all(field);
}
function isFunction(x) { 
    return Object.prototype.toString.call(x) === "[object Function]";
}

/* ONLOAD FUNCTION */
var mouseX, mouseY;
var Element_Hovered= null;
var doconload_vars=new Array();
var doconload_n=0;
var windowOnMouseMove_Actions=new Array();
var windowOnMouseMove_Actions_n=0;
var windowOnMouseUp_Actions=new Array();
var windowOnMouseUp_Actions_n=0;
var unique_id_cnt = 0;
var net	= getMeta('net');
var shr = getMeta('shr');
var is_editor_loaded=0;
var editor_instance=new Array();

document.write('<link href="'+shr+'/mx.css" rel="stylesheet" type="text/css" />');

function setOnload(callback){
	if (isFunction(callback)){
		doconload_n++;
		doconload_vars[doconload_n]=callback;
		return doconload_n;
	}
	return false;
}
function clearOnload(moveID){
	if (doconload_vars[moveID])
		delete doconload_vars[moveID];
}
function setMouseMove(callback){
	if (isFunction(callback)){
		windowOnMouseMove_Actions_n++;
		windowOnMouseMove_Actions[windowOnMouseMove_Actions_n]=callback;
		return windowOnMouseMove_Actions_n;
	}
	return false;
}
function clearMouseMove(moveID){
	if (windowOnMouseMove_Actions[moveID]){
		delete windowOnMouseMove_Actions[moveID];
	}
}
function setMouseUp(callback){
	if (isFunction(callback)){
		windowOnMouseUp_Actions_n++;
		windowOnMouseUp_Actions[windowOnMouseUp_Actions_n]=callback;
		return windowOnMouseUp_Actions_n;
	}
	return false;
}
function clearMouseUp(moveID){
	if (windowOnMouseUp_Actions[moveID]){
		delete windowOnMouseUp_Actions[moveID];
	}
}
function windowOnMouseUp(e){
	try{
		for (var n in windowOnMouseUp_Actions){
			if (isFunction(windowOnMouseUp_Actions[n])){
				windowOnMouseUp_Actions[n]();
			}
			else{
				clearMouseUp(n);
			}
		}
	}
	catch(er){}
}
function windowOnMouseMove(e){
	if (e)
		Element_Hovered=e.target;
	else
		Element_Hovered=window.event.srcElement;
			
	if (!e)
		e = window.event;
	if (e.pageX||e.pageY)
	{
	  mouseX = e.pageX;
	  mouseY = e.pageY;
	}
	else if (e.clientX||e.clientY)
	{
	  mouseX = e.clientX + document.body.scrollLeft;
	  mouseY = e.clientY + document.body.scrollTop;
	}
	try{
		for (var n in windowOnMouseMove_Actions){
			if (isFunction(windowOnMouseMove_Actions[n])){
				windowOnMouseMove_Actions[n]();
			}
			else{
				clearMouseMove(n);
			}
		}
	}
	catch(er){}
}
function doconload(){
	try{
		for (var n in doconload_vars){
			if (isFunction(doconload_vars[n])){
				doconload_vars[n]();
			}
			else{
				clearOnload(n);
			}
		}
	}
	catch(er){}
}
onload=doconload;
document.onmousemove=windowOnMouseMove;
document.onmouseup=windowOnMouseUp;

/***** MAIN BEHAVIOUR API *****/
function createUniqueId(field,prefix){
	if (field.id){
		return field.id;
	}
	else{
		if (!prefix) prefix = '_uniqueid';
		var unique_id = field.tagName+'_'+unique_id_cnt+'_'+prefix;
		field.setAttribute('id',unique_id);
		unique_id_cnt++;
		return unique_id;
	}
}
function setOpacity(field,val){
  if(val<0) val=0;
	if(val>100) val=100;
	field.style.opacity=val/100;
	if (val==100)
		field.style.filter="";
	else{
		field.style.filter="alpha(opacity="+val+")";
	}
	field.setAttribute('currentOpacity',val);
}
function ani_set_opacity(field){
	if (field){
		var val = parseInt(field.getAttribute('targetOpacity'));
		var inc = parseInt(field.getAttribute('incrementOpacity'));
		var cop = parseInt(field.getAttribute('currentOpacity'));
		if (cop-inc>val){
			setOpacity(field,cop-inc);
		}
		else if (cop+inc<val){
			setOpacity(field,cop+inc);		
		}
		else{
			setOpacity(field,val);
			var callback = field.getAttribute('callbackOpacity');
			if (callback) eval(callback);
			return;
		}
		setTimeout(function(){ani_set_opacity(field)},1);
	}
}
function aniOpacity(field,val,inc,callback){
	if (!inc) inc = 1;
	if (callback)
		field.setAttribute('callbackOpacity',callback);
	else
		field.setAttribute('callbackOpacity','');
	field.setAttribute('targetOpacity',val);
	field.setAttribute('incrementOpacity',inc);
	if (!field.id){
		createUniqueId(field,"aniop");
	}
	ani_set_opacity(field);
}
function xajs(uri){
	if (!getID('xajsHiddenScript')){
		var d = document.createElement('div');
		d.setAttribute('id','xajsHiddenScript');
		d.style.display='none';
		document.body.appendChild(d);
		d.innerHTML = '';
	}	
	var s = document.createElement('script');
	s.setAttribute('src',uri);
	s.setAttribute('type','text/javascript');
	s.setAttribute('async','true');
	getID('xajsHiddenScript').appendChild(s);
}
function init_ckeditor(elname){
	if (editor_instance[elname]){
		if (!editor_instance[elname][0]){
			if (editor_instance[elname][1]){
				editor_instance[elname][0]=CKEDITOR.replace(getID(elname),
					{
						height:editor_instance[elname][2],
						uiColor: '#eeeeee', language : 'en',
						enterMode	: Number(3), shiftEnterMode	: Number(2),
						toolbar : [ 
							[ 'Maximize','Source','-','PasteFromWord','PasteText','-','Undo','Redo','-', 'Link', 'Unlink', '-','Smiley','SpecialChar','myImage','-','NumberedList', 'BulletedList','-','Outdent','Indent','Blockquote','-','Find','Replace'], '/',
							[ 'Styles','Font','FontSize', '-', 'Bold', 'Italic', 'Underline', 'Strike','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','TextColor','BGColor']
						]
					}
				);
			}
			else{
				editor_instance[elname][0]=CKEDITOR.replace(getID(elname),
				{
					height:editor_instance[elname][2],
					uiColor: '#eeeeee', language : 'en',
					enterMode	: Number(2), shiftEnterMode	: Number(3),
					toolbar : [
							[ 'Bold', 'Italic', 'Underline', 'Strike','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList', 'BulletedList', 'Outdent','Indent', '-','Link', 'Unlink','-','TextColor','BGColor']
						]
				}
				);
			}
		}
	}
}
function editor_loaded(){
	is_editor_loaded=2;
	for (var elname in editor_instance){
		init_ckeditor(elname);
	}
}
function load_editor(elname,isadvance,height_editor){
	if (is_editor_loaded==0){
		is_editor_loaded=1;
		editor_instance[elname]=new Array(false,isadvance,(height_editor?height_editor:350));
		xajs(shr+'/cedit/ckeditor.js');
	}
	else if (is_editor_loaded==1){
		editor_instance[elname]=new Array(false,isadvance,(height_editor?height_editor:350));
	}
	else if (is_editor_loaded==2){
		editor_instance[elname]=new Array(false,isadvance,(height_editor?height_editor:350));
		init_ckeditor(elname);
	}	
}
function drawFlash(field,s,w,h){
    var d=  'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0';
    var h=  '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="'+d+'" name="base" height="'+h+'" width="'+w+'">'+
            '<param name="movie" value="'+s+'" /><param name="quality" value="high" /><param name="menu" value="false" /><param name="bgcolor" value="#000000" />'+
            '<embed src="'+s+'" quality="high" menu="false" type="application/x-shockwave-flash" height="'+h+'" width="'+w+'" />'+
            '</object>';
    if (field==false)
        document.write(h);
    else
        field.innerHTML=h;
}
function msg_delete(){
  return confirm("KONFIRMASI\nYakin akan menghapus data yang dimaksud?");
}

/********** CALENDAR ***********/
/*cal*/
var calender_hidden_timer=new Array();
var calender_current_date=new Array();
function calender_visible(fieldname){
    clearTimeout(calender_hidden_timer[fieldname]);
    getID('cldr-'+fieldname+'-div').style.visibility='';
    getID('cldr-'+fieldname+'-btn').style.backgroundColor='#aaa';
}
function calender_hidden (fieldname){
    calender_hidden_timer[fieldname]=setTimeout("getID('cldr-"+fieldname+"-div').style.visibility='hidden';"+
        "getID('cldr-"+fieldname+"-btn').style.backgroundColor='#ccc';",200);
}
function calender_nBulan(t){
    var Kabisat  =(((2004-t+4)%4)==0)?29:28;
    return new Array(31,Kabisat,31,30,31,30,31,31,30,31,30,31);;
}
function calender_interval(b,t){
    var q=calender_nBulan(t);
    var r=0;
    if (2004<=t){
        for (var i=0;i<(b-1);i++)
            r+=q[i];
        for (var i=2004;i<t;i++)
            r+=(((2004-i)%4)==0)?366:365;
        r+=4;
        r=r%7;
    }
    else{
        for (var i=2003;i>=t;i--)
            r+=(((2004+2004-i)%4)==0)?366:365;
        var tt=0;
        for (var i=0;i<(b-1);i++)
            r-=q[i];
        r=6-((r+2)%7);
    }
    return r;
}
function calender_show(fieldname,currentDate){
    /******** aCalender Style Sheet ********/
    calender_current_date[fieldname]=currentDate;
    var sT='color:#fff;background:#000';
    var sD='color:#000;background:#fff';
    var styleDayT=new Array('color:#fff;background:#600',sT,sT,sT,sT,'color:#fff;background:#050',sT);
    var styleDays=new Array('color:#fff;background:#f00',sD,sD,sD,sD,'color:#030;background:#efe',sD);
    var styleDayNow='color:#004;background:#cdf';
    var styleBln ='background:#444';
    var styleList='font-family:arial,tahoma,verdana,sans-serif;font-size:11px';
    var styleCldr='background:#ddd;border:1px solid #444;padding:1px';
    var styleDay ='font-family:arial,tahoma,verdana,sans-serif;font-size:11px;padding:3px';
    var colorDayBorder          ='#ddd';
    var colorDayBorder_select   ='#009';
    var colorDayBorder_hover    ='#090';
    /***** end of aCalender Style Sheet *****/
    field=getID(fieldname);
    dfield=getID('cldr-'+fieldname+'-div');
    var m='';
    var t_s=currentDate.substring(0,4);
    var b_s=currentDate.substring(5,7);
    var h_s=currentDate.substring(8,10);
    var t=Number(t_s);
    var b=Number(b_s);
    var h=Number(h_s);
    var curval=field.value;
    var cv_t_s=curval.substring(0,4);
    var cv_b_s=curval.substring(5,7);
    var cv_h_s=curval.substring(8,10);
    var cv_t=Number(cv_t_s);
    var cv_b=Number(cv_b_s);
    var cv_h=Number(cv_h_s);
    var namaHari =new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
    var namaBulan=new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
    m+='<div style="'+styleCldr+'">';
    m+='<table cellspacing="0" cellpadding="2">';
    m+='<tr><td colspan="7" style="text-align:center;'+styleBln+'">';
    var blur_focus='onfocus="calender_visible(\''+fieldname+'\')" '+
                   'onblur="calender_hidden(\''+fieldname+'\')"';
    // Tulis Bulan
    m+='<select id="cldr-'+fieldname+'-bulan" '+blur_focus+' onchange="calender_show(\''+fieldname+'\',\''+t_s+'-\'+this.value+\'-'+h_s+'\')" style="margin-right:3px;'+styleList+'">';
    for (var i=1;i<=12;i++){
        var vb='0'+i; vb=vb.substring(vb.length-2,vb.length);
        m+='<option '+((i==b)?'selected="selected"':'')+' value="'+vb+'">'+namaBulan[i-1]+'</option>';
    }
    m+='</select>';
    // Tulis Tahun
    m+='<select '+blur_focus+' onchange="calender_show(\''+fieldname+'\',this.value+\'-'+b_s+'-'+h_s+'\')" style="'+styleList+'">';
    for (var i=1900;i<=2099;i++)
        m+='<option '+((i==t)?'selected="selected"':'')+' value="'+i+'">'+i+'</option>';
    m+="</select>";
    // Tulis Title Hari
    m+='</tr></tr><tr>';
    for (var i=0;i<7;i++){
        m+='<th style="text-align:center;cursor:help;font-weight:bold;border:1px solid '+colorDayBorder+';'+styleDay+';'+styleDayT[i]+'" title="'+namaHari[i]+'">'+namaHari[i].substring(0,1)+'</th>';
    }
    m+='</tr><tr>';
    var startDay=calender_interval(b,t);
    var jBulan  =calender_nBulan(t);
    // Tulis Tanggal
    if (startDay){
        m+='<td colspan="'+(startDay)+'"></td>';
    }
    var dtNew=new Date();
    var n_t=dtNew.getFullYear()+'-'+(dtNew.getMonth()+1)+'-'+dtNew.getDate();

    for (var i=1;i<=jBulan[b-1];i++){
        var c_t=t_s+'-'+b+'-'+i;
        var vb='0'+i; vb=vb.substring(vb.length-2,vb.length);
        var n=(i-1+startDay)%7;
        var bord=(c_t==cv_t_s+'-'+cv_b+'-'+cv_h)?colorDayBorder_select:colorDayBorder;
        if (n==0)
            m+='</tr><tr>';
        m+='<td '+
            'onmouseover="this.style.borderColor=\''+colorDayBorder_hover+'\';" '+
            'onmouseout="this.style.borderColor=\''+bord+'\';" '+
            'onmousedown="getID(\''+fieldname+'\').value=\''+t_s+'-'+b_s+'-'+vb+'\';" '+
            'style="text-align:right;cursor:pointer;border:1px solid '+
            bord+';'+styleDay+';'+((n_t==c_t)?styleDayNow:styleDays[n])+'">'+i+'</td>';
    }
    m+='</tr>';
    m+='</table>';
    m+='</div>';
    dfield.innerHTML=m;
    calender_visible(fieldname);
    getID('cldr-'+fieldname+'-bulan').focus();
}
function calender_write(field,fieldname,t,b,h,nameclass,inputstyle,alignright){
    field=getID(field);
    var appd='';
    var m='';
    d=new Date();
    t=(t?t:d.getFullYear());
    b=(b?b:d.getMonth()+1);
    h=(h?h:d.getDate());
    if (nameclass)
        appd+='class="'+nameclass+'" ';
    if (inputstyle)
        appd+='style="float:left;'+inputstyle+'" ';
    t=(t>75)?('19'+t):('20'+t);
    t=t.substring(t.length-4,t.length);
    b='0'+b; b=b.substring(b.length-2,b.length);
    h='0'+h; h=h.substring(h.length-2,h.length);
    m   +='<div id="cldr-'+fieldname+'-div" style="position:absolute;visibility:hidden;'+(alignright?'margin-left:-35px':'')+'">&nbsp;</div>';
    m   +='<div><input onfocus="getID(\'cldr-'+fieldname+'-btn\').focus();" readonly="readonly" type="text" name="'+fieldname+'" id="'+fieldname+'" value="'+t+'-'+b+'-'+h+'" '+appd+'/>';
    m   +='<input type="button" '+
            'onmouseover="if (this.style.backgroundColor!=\'#000\') this.style.color=\'#f00\';" onmouseout="this.style.color=\'#000\';" '+
            'onfocus="this.style.color=\'#000\';calender_show(\''+fieldname+'\',calender_current_date[\''+fieldname+'\'])" '+
            'value="&equiv;" id="cldr-'+fieldname+'-btn" title="Calender" style="float:left;cursor:pointer;padding:0px;margin:0px;border:none;background-color:#ccc;width:22px;margin-left:1px;font-weight:bold;font-size:14px;" /></div>';
    field.innerHTML=m;
    ifield=getID(fieldname);
    calender_current_date[fieldname]=ifield.value;
    bfield=getID('cldr-'+fieldname+'-btn');
    dfield=getID('cldr-'+fieldname+'-div');
    bfield.style.color='#000';
    dfield.style.marginTop=field.style.height=bfield.style.height=ifield.offsetHeight+'px';
    field.style.width=(bfield.offsetWidth+ifield.offsetWidth+1)+'px';
}

function setScrollToTop(){
	var holder=getID('back_to_top');
	if (holder){
		if (window.innerHeight<document.body.offsetHeight){
			holder.style.cssFloat='none';
			holder.style.styleFloat='none';
			holder.style.position='fixed';
			holder.style.marginTop='0';
			holder.style.bottom='5px';
			holder.style.right='10px';
			setOpacity(getID('back_to_top'),0);
			document.onscroll=function(){
				var scrtop=document.getElementsByTagName('html')[0].scrollTop;
				if (scrtop>0){
					aniOpacity(getID('back_to_top'),90,2);
				}
				else{
					aniOpacity(getID('back_to_top'),0,2);
				}
			};
			document.onscroll();
		}
	}
}
setOnload(setScrollToTop);


function a2jax(){
    this.conn=null;
    this.method='GET';
    this.postDATA='\n\n';
    this.argument=null;
    
    this.close=function(){
        this.conn.abort();
    };
    this.open=function(uri,nonblock){
    	this.method=this.method.toUpperCase();
        try{
            this.conn = new XMLHttpRequest();
        }
        catch (e){
					this.conn = null;
				}
		    if(!this.conn){
		    		var msxmlhttp = new Array('Msxml2.XMLHTTP.5.0','Msxml2.XMLHTTP.4.0','Msxml2.XMLHTTP.3.0','Msxml2.XMLHTTP','Microsoft.XMLHTTP');
		    		for (var i = 0; i < msxmlhttp.length; i++) {
		    			try{
		    				this.conn = new ActiveXObject(msxmlhttp[i]);
		    			}
		    			catch (e){
		    				this.conn = null;
		    			}
		    		}
			    }
				if (!this.conn)
				    return false;
		
				this.conn.open(this.method, uri, nonblock);
				
				//-- REPLACE HEADERS (MINIMUM)
				this.conn.setRequestHeader("agent-type", "a2jax");
				this.conn.setRequestHeader("user-agent", "a2jax");
				this.conn.setRequestHeader("accept", "*/*");
				this.conn.setRequestHeader("accept-language", "en-us");
				
				if (this.method=='POST'){
				    this.conn.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
						this.conn.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				}
				this.conn.send(this.postDATA);
				return true;
    };
    this.setPost=function(postBody){
        this.postDATA=postBody;
        this.method='POST';
    };
    
    this.get=function(uri,callback,argument,resend_uri){
    	if (callback)
    		this.open(uri,true);
    	else
	    	this.open(uri,false);
	    if (this.conn){
	    	if (callback){
		    		var x;
	          x=this.conn;
	          if (argument){
			    		x.argument		= argument;
			    		if (resend_uri)
			    			x.resend_uri	= resend_uri;
			    	}
	    	    x.onreadystatechange = function () {
	    		    if (x.readyState == 4){
	    		    		var txt=x.responseText;
	    		    		var js='';
	    		    		var s='';
	    		    		if (s=x.getResponseHeader('a2jax-script')){
	    		    			js = decodeURIComponent(s);
	    		    		}
	    		    		if (x.getResponseHeader('a2jax-type')=='script'){
	    		    			js += txt;
	    		    		}
	    		    		var nowuri = x.getResponseHeader('a2jax-uri');
	    		    		
	    		    		var callback_retval = false;
    		    			if (x.argument){
    		    				var argument_x=x.argument;
    		    				if (x.resend_uri){
    		    					var resend_uri_x = nowuri;
    		    					eval("callback_retval="+callback+"(txt,js,argument_x,resend_uri_x);");
    		    				}
    		    				else
    		    					eval("callback_retval="+callback+"(txt,js,argument_x);");
    		    			}
    		    			else{
    		        		eval("callback_retval="+callback+"(txt,js);");
    		        	}
    		        	
    		        	if (!callback_retval){
    		        		if (js){
		    		    			eval(js);
		    		    		}
    		        	}
	    		    }
	    	    };
	    	    return true;
    		}
    	    else {
	    		var txt=this.conn.responseText;
	    		if (s=this.conn.getResponseHeader('a2jax-script'))
	    			eval(decodeURIComponent(s));
	    		if (this.conn.getResponseHeader('a2jax-type')=='script')
			  		eval(txt);
		      return txt;
		    }
	    }
	    else
	        return false;
    };
    this.formAction=function(frm,callBack,postScript,argument,resend_uri){
				var txt="";
            	for (var frmpos=0;frmpos<frm.length;frmpos++) {
            		var inp=frm.elements[frmpos];
            		var send_it=true;
            		if (((inp.type=='radio')||(inp.type=='checkbox'))&&(!inp.checked))
            			send_it=false;
            		else if (inp.type=='submit') inp.style.visibility='hidden';
            		if (send_it){
            			if (txt!="") txt+='&';
            			txt+=encodeURIComponent(inp.name)+'='+encodeURIComponent(inp.value);
            		}
            	}
            	this.postDATA=txt;
				var uri;
				if (frm.method=='post'){
					uri=frm.action;
					this.setPost(this.postDATA);
				}
				else{
					uri=frm.action+"?"+this.postDATA;
					this.postDATA='\n\n';
				}
				if (postScript)
					eval(postScript);
					
				if (callBack){
					if (resend_uri)
						return this.get(uri,callBack,argument,resend_uri);
					else if (argument)
						return this.get(uri,callBack,argument);
					else
						return this.get(uri,callBack);
				}
				else
			  	return this.get(uri);
		};
}
function a2jaxget(uri,callback){
	var ht=new a2jax();
  return ht.get(uri,callback);
}
function trim(str, charlist) {
    str += '';
    var whitespace;
    if (!charlist) {
      	whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    } else {
        // preg_quote custom list
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    }
    
    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {            str = str.substring(i);
            break;
        }
    }
        l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;        }
    }
    
    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}
function nl2br(str){
	return str.replace(/\n/g,'<br />');
}
function htmlspecialchars(str, quote_style, charset, double_encode) {
	if (!str) str="";
    var optTemp = 0, i = 0, noquotes= false;
    if (typeof quote_style === 'undefined' || quote_style === null) {
        quote_style = 2;
    }
    str = str.toString();
    if (double_encode !== false) { // Put this first to avoid double-encoding
        str = str.replace(/&/g, '&amp;');
    }
    str = str.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE' : 1,
        'ENT_HTML_QUOTE_DOUBLE' : 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE' : 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') {
        quote_style = [].concat(quote_style);
        for (i=0; i < quote_style.length; i++) {
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            }
            else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        str = str.replace(/'/g, '&#039;');
    }
    if (!noquotes) {
        str = str.replace(/"/g, '&quot;');
    }
    return str;
}
function news_imgExpand(field,w,h){
	field.setAttribute('title','');
	field.className='newsview_image_nofloat';
	var chld=field.getElementsByTagName('img')[0];
	chld.setAttribute('width',w);
	chld.setAttribute('height',h);
	field.onclick=function(){};
}
function aniDivHeight(field,clearsfield,positioning,divsize,interv){
	if (positioning) field.style.position=positioning;
	if (!divsize) divsize=40;
	if (!interv) interv=4;
	field.style.overflow='hidden';
	var start_height 	= field.offsetHeight;
	var end_height 		= clearsfield.offsetTop;
	var n_i = interv;
	var inc = (end_height>start_height)?((end_height - start_height) / divsize):((start_height - end_height) / divsize);
	for (var i=1;i<=divsize;i++){
		if (end_height>start_height)
			var y = start_height + (inc*i);
		else
			var y = parseInt(start_height - (inc*i));
		setTimeout("getID('"+(field.id)+"').style.height = '"+y+"px';",n_i);
		n_i+=interv;
	}
	setTimeout("getID('"+(field.id)+"').style.height = '"+end_height+"px';",n_i);
}


/* scroll */
function newscroll_interval(){
	var field = getID('suarakader_scroll');
	if (parseInt(field.getAttribute('status'))!=1){
		var pos = parseInt(field.getAttribute('pos'))-1;
		if (pos<0-field.offsetHeight)
			pos=field.parentNode.offsetHeight;
		field.setAttribute('pos',pos);
		field.style.top=pos+'px';
	}
}
function newscroll_init(){
	var field = getID('suarakader_scroll');
	if (field){
  	field.setAttribute('pos',field.parentNode.offsetHeight);
  	field.setAttribute('status',0);
  	field.onmouseover=function(){
  		this.setAttribute('status',1);
  	};
  	field.onmouseout=function(){
  		this.setAttribute('status',0);
  	};
  	field.style.display='';
  	field.style.top=field.parentNode.offsetHeight+'px';
  	setInterval(newscroll_interval,30);
  }
}
setOnload(newscroll_init);

//]]>