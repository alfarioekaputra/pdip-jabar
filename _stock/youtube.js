/*<![CDATA[
 *
 * YouTUBE Fetcher 1.00
 *	(c) 2010 amarullz@yahoo.com
 *			http://blog.unikom.ac.id/amarullz/
 *
 * This SCRIPT was Free to Use, but please always include
 * this main coder comment information in production.
 *
 * USAGE:
 * ======
 * youtubeFetcher.getData('http://www.youtube.com/watch?v=qCOfgNXHe4k&feature=youtube_gdata',callbackFunc);
 *
 * CALLBACK FUNCTION:
 * ==================
 * function(isSuccess,data){
 *   ...
 * }
 *
 * SUCCESS DATA STRUCTURE:
 * =======================
 * data['player']]      = View URL in YouTUBE
 * data['thumbnail']    = Thumbnail Image for Video
 * data['title']        = Video Title
 * data['duration']     = Duration in seconds
 * data['rating']       = User Rating for Video
 * data['description']  = Video Descriptions
 * data['id']           = Video ID
 * data['swf']          = SWF URL to embed in your HTML
 *
 *******************************************************/

var youtubeFetcher={
	lastError:'',
	onloadCallback:null,
	
	/* Creating Script for Data Stream */
	createScript:function(uri){
		if (!youtubeFetcher.getID('youtubeFetcherRootScript')){
			var d = document.createElement('div');
			d.setAttribute('id','youtubeFetcherRootScript');
			d.style.display='none';
			document.body.appendChild(d);
		}
		youtubeFetcher.getID('youtubeFetcherRootScript').innerHTML = '';
		var s = document.createElement('script');
		s.setAttribute('src',uri);
		s.setAttribute('type','text/javascript');
		s.setAttribute('async','true');
		youtubeFetcher.getID('youtubeFetcherRootScript').appendChild(s);
	},
	
	/* Get Element By ID */
	getID:function(n){
		return document.getElementById?document.getElementById(n):document.all(n);
	},
	
	/* Parse URL - from http://phpjs.org/ */
	parse_url:function(str) {
    var  o   = {
        strictMode: false,
        key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],        q:   {
            name:   "queryKey",
            parser: /(?:^|&)([^&=]*)=?([^&]*)/g
        },
        parser: {            strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
            loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-protocol to catch file:/// (should restrict this)
        }
    };
    var m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),uri = {},i= 14;
    while (i--) {uri[o.key[i]] = m[i] || "";}
    uri[o.q.name] = {};
    uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
    if ($1) uri[o.q.name][$1] = $2;
    });        return uri;
    var retArr = {};
    if (uri.protocol !== '') {retArr.scheme=uri.protocol;}
    if (uri.host !== '') {retArr.host=uri.host;}
    if (uri.port !== '') {retArr.port=uri.port;}            if (uri.user !== '') {retArr.user=uri.user;}
    if (uri.password !== '') {retArr.pass=uri.password;}
    if (uri.path !== '') {retArr.path=uri.path;}
    if (uri.query !== '') {retArr.query=uri.query;}
    if (uri.anchor !== '') {retArr.fragment=uri.anchor;}            
    return retArr;
	},
	
	/* URL Query parser */
	get_query_val:function(str,queryname){
		try{
			var bv=str.split('&');
			for (var i=0;i<bv.length;i++){
				var bk = bv[i];
				bk=bk.split('=');
				if (bk[0]==queryname){
					return decodeURIComponent(bk[1].replace(/\+/g,'%20'));
				}
			}
		}
		catch(ee){};
		return false;
	},
	
	/* Get Data From Youtube Server */
	getData:function(youtube_url,onload_callback){
		/* Reset Error Message */
		youtubeFetcher.lastError='';
		
		/* Onload Script Callback */
		if (onload_callback)
			youtubeFetcher.onloadCallback=onload_callback;
		else
			youtubeFetcher.onloadCallback=null;
		
		/* Parsing URL */
		var url_yt	=	youtubeFetcher.parse_url(youtube_url);
		var vQuery	= url_yt.query;
		var vDomain	= url_yt.host;
		var vPath		= url_yt.path;
		
		/* Youtube Video ID */
		var videoID	= '';
		
		/* Validate URL */
		if (vDomain=='www.youtube.com'){
			if (vPath=='/watch'){
				if (vQuery)
					videoID=youtubeFetcher.get_query_val(vQuery,'v');
			}
		}
		if (!videoID){
			/* Error URL */
			youtubeFetcher.lastError='URL Watch YouTUBE tidak Valid!';
			if (youtubeFetcher.onloadCallback){
				youtubeFetcher.onloadCallback(false,false);
			}
			return false;
		}
		else{
			/* Start Fetching Data */
			youtubeFetcher.createScript('http://gdata.youtube.com/feeds/api/videos/'+videoID+'?v=2&alt=jsonc&callback=youtubeFetcher.callback');
		}
		return true;
	},
	
	/* YouTUBE Data Stream Callback */
	callback:function(d){
		/* Reset Error Message */
		youtubeFetcher.lastError='';
		
		var errorMSG = ['OK','Player Video Tidak Ditemukan','Thumbnail Video Tidak Ditemukan','Title Video tidak Ditemukan','ID Video tidak Valid'];
		
		/* Return Value */
		var retval = new Array();
		
		/* Return Status */
		var retstat= false;
		
		/* YouTUBE Data Object */
		var r = d.data;
		
		/* Error Variable */
		var f = 1;
		
		/* Error Info */
		var errinfo='';
		
		if (d.error)
			/* Stream is Error */
			errinfo=d.error.message;
		else if (r){
			/* Get Player URL */
			if (r.player){
				if (r.player.default){
					f=false;
					retval['player']=r.player.default;
				}
			}
			/* Get Thumbnail URL */
			if (!f){
				f=2;
				if (r.thumbnail){
					if (r.thumbnail.sqDefault){
						retval['thumbnail']=r.thumbnail.sqDefault;
						f=false;
					}
				}
			}
			/* Get Video Title */
			if (!f){
				f=3;
				if (r.title){
					retval['title']=r.title;
					f=false;
				}
			}
			/* Get Duration */
			if (!f){
				f=4;
				if (r.duration){
					retval['duration']=r.duration;
					f=false;
				}
			}
			if (!f){
				f=5;
				retval['rating']= "0.00";
				retval['description']= "...";
				retval['id']= "";
				retval['swf']= "";
				
				/* Get Video Rating */
				if (r.rating)
					retval['rating']=r.rating;
				
				/* Get Video Description */
				if (r.description)
					retval['description']=r.description;
				
				/* Get Video ID & SWF URL to Embed */
				if (r.id){
					retval['id']=r.id;
					retval['swf']='http://www.youtube.com/v/'+retval['id'];
					f=false;
				}
			}
		}
		if (f){
			retstat = false;
			if (errinfo){
				youtubeFetcher.lastError = 'Error Message : '+errinfo;
			}
			else{
				youtubeFetcher.lastError = 'Error Message : ('+f+') '+errorMSG[f];
				retval=null;
			}
		}
		else{
			retstat=true;
		}
		if (youtubeFetcher.onloadCallback){
			youtubeFetcher.onloadCallback(retstat,retval);
		}
	}
};
	

var youtube_url_field;
function youtube_callback(s,d){
  /* perinta-perintah */
  getID('youtube_loading_div').style.display='none';
	youtube_url_field.disabled='';
	youtube_url_field.style.background='#ffc';
	if (!s){
		alert('Akses data Video Error\n'+youtubeFetcher.lastError);
		youtube_url_field.focus();
		
		getID('youtube_preview').innerHTML = '<div style="padding-top:80px;padding-bottom:80px;font-size:10px;color:#666;text-align:center">Preview akan otomatis tampil setelah<br />Anda memasukan URL Video Youtube</div>';
		getID('youtubefield_id').value='';
		getID('youtubefield_thumb').value='';
		getID('youtubefield_duration').value='';
		getID('youtubefield_swf').value='';
	}
	else{
		getID('youtubefield_title').value=d['title'];
		getID('youtubefield_id').value=d['id'];
		getID('youtubefield_thumb').value=d['thumbnail'];
		getID('youtubefield_duration').value=d['duration'];
		getID('youtubefield_swf').value=d['swf'];
		getID('youtubefield_desc').value=d['description'];
		drawFlash(getID('youtube_preview'),d['swf'],334,210);
	}
}

function youtube_getdata(field){
	youtube_url_field=field;
	getID('youtube_loading_div').style.display='';
	field.disabled='disabled';
	field.style.background='#eee';
	
	var urlyt = field.value;
  youtubeFetcher.getData(urlyt,youtube_callback);
}

 


/*]]>*/