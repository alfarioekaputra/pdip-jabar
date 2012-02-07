var tss_contents = new Array();
var tss_content_n= 0;
var tss_pos			 = 0;
var tss_width		 = 0;
var tss_waiter	 = null;
var tss_cholder	 = null;
var tss_holder	 = null;
var tss_page		 = null;
var tss_n_ct		 = 0;
var tss_c_ct		 = 0;
var tss_onhover=false;

function tss_ani(){
	var bs		=	tss_c_ct;
	var bt		= tss_n_ct;
	if (bt<0){
		bt=0;
		tss_n_ct=0;
	}
	var ns    = bs;
	if (bs<bt)
		ns+= Math.floor((bt-bs)/4) + 1;
	else if (bs>bt)
		ns-= Math.floor((bs-bt)/4) + 1;
	if (bs!=bt){
		tss_c_ct = ns;
		tss_cholder.style.left="-"+ns+"px";	
	}	
	if (bt!=ns)
		setTimeout(tss_ani,25);
	else
		tss_wait_next();
}
function tss_next(){
	tss_pos++;
	if (tss_pos>=tss_content_n){
		tss_pos=0;
	}
	for (var i=0;i<tss_content_n;i++){
		var el = getID('tss_p'+i);
		if (i==tss_pos)
			el.className='tss_activepage';
		else
			el.className='';
	}
	tss_n_ct = tss_pos*tss_width;
	tss_ani();
}

function tss_wait_next(){
	if (!tss_onhover){
		clearTimeout(tss_waiter);
		tss_waiter = setTimeout(tss_next,3000);
	}
}
function tss_wait_continue(){
	tss_onhover=false;
	if (tss_waiter==null)
		tss_wait_next();
}
function tss_wait_pause(){
	tss_onhover=true;
	clearTimeout(tss_waiter);
	tss_waiter=null;
}
function tss_init(){
	var holder   =getID('tss_holder');
	var contents =getID('tss_contents');
	
	if (holder&&contents){
		tss_cholder = contents;
		tss_holder  = holder;
		holder.style.display='';
		
		var chld = contents.firstChild;
		do{
			if (chld.className=='tss_content'){
				tss_contents[tss_content_n++]=chld;
			}
			chld=chld.nextSibling;
		}
		while(chld);
		
		var w 						= holder.offsetWidth;
		var cw						= (w+20)*tss_content_n;
		tss_width 	= w;
		
		holder.style.width 	 = w+'px';
		contents.style.width = cw+'px';
		for (var i=0;i<tss_content_n;i++){
			tss_contents[i].style.width=w+'px';
		}
		var ss_page_w			 = tss_content_n*12;
		tss_page		 = document.createElement('DIV');
		tss_page.id	 = "tss_paging";
		tss_page.style.width	= (ss_page_w)+'px';
		for (var i=0;i<tss_content_n;i++){
			var pg 			 = document.createElement('DIV');
			pg.innerHTML = '&nbsp;';
			pg.id				 = 'tss_p'+i;
			pg.setAttribute('pageid',i);
			pg.onclick=function(){
				clearTimeout(tss_waiter);
				tss_waiter=null;
				tss_pos=parseInt(this.getAttribute('pageid')) - 1;
				tss_next();
			};
			if (i==0){
				pg.className='tss_activepage';
			}
			tss_page.appendChild(pg);
		}
		
		holder.appendChild(tss_page);
		
		tss_n_ct		 = 0;
		tss_c_ct		 = 0;
		tss_wait_next();
		
		tss_holder.onmouseover=tss_wait_pause;
		tss_holder.onmouseout=tss_wait_continue;
	}
}
setOnload(tss_init);