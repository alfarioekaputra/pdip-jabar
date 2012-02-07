function getID(field){
	return document.getElementById ? document.getElementById(field) : document.all(field);
}
var ed			= window.opener.epictures_Editor;
var currUrl	= null;
var currDir	= null;
function loadManager(dirpos,uri){
	currUrl=uri;
	currDir=dirpos;
	cEl=getID("epic_list");
	contentEl=cEl.childNodes;
	
	for (i=0;i<contentEl.length;i++){
		el=contentEl[i];
		if (el.nodeName.toLowerCase()=='a'){
			if (el.className=='file'){
				var nfile=el.href;
				el.style.backgroundImage='url(\'./thumb.php?d='+encodeURIComponent(dirpos)+'&f='+encodeURIComponent(nfile.substring(6))+'\')';
			}
		}
	}
	
}
function sureDelete(delname){
	return confirm('Are you sure\nYou want to delete "'+delname+'" ?');
}
function prompRen(field,renName){
	var newName=prompt("Insert new file or folder name:",renName);
	if ((newName)&&(newName!='NULL')){
		var newurl=(field.href+'&renn='+encodeURIComponent(newName));
		location=newurl;
		return false;
	}
	return false;
}
function popNewFolder(){
	var newName=prompt("Insert new folder name:","New Folder");
	if ((newName)&&(newName!='NULL')){
		var newurl='./?d='+encodeURIComponent(currDir)+'&nfolder='+encodeURIComponent(newName);
		location=newurl;
	}
}
function clickDir(field,dname){
	var dhtml='<b>Folder Name:</b><br /><input type="text" onfocus="this.select()" readonly="readonly" value="'+dname+'" style="width:125px" /><br /><br />';
	dhtml+='<a href="'+field.href+'">&raquo; Open</a>';
	dhtml+='<a href="./?d='+encodeURIComponent(currDir)+'&amp;delf='+encodeURIComponent(dname)+'" onclick="return sureDelete(\''+dname+'\');">&raquo; Delete</a>';
	dhtml+='<a href="./?d='+encodeURIComponent(currDir)+'&amp;renf='+encodeURIComponent(dname)+'" onclick="return prompRen(this,\''+dname+'\');">&raquo; Rename</a>';
	getID('epic_detail').innerHTML=dhtml;
	return false;
}
function clickImg(field,w,h){
	var dname=field.href.substring(6);
	var dhtml='<div class="detailPreview" style="background-image:url(\'./thumb.php?d='+encodeURIComponent(currDir)+'&f='+encodeURIComponent(dname)+'\');">&nbsp;</div>';
	dhtml+='<div style="text-align:center"><input type="text" onfocus="this.select()" readonly="readonly" value="'+dname+'" style="width:125px" /><br /><b>'+w+'x'+h+' pixels</b></div>';

	dhtml+='<br /><a href="image:'+dname+'" onclick="return pasteImage(this,'+w+','+h+');">&raquo; Insert Image</a>';
	dhtml+='<a href="./?d='+encodeURIComponent(currDir)+'&amp;delf='+encodeURIComponent(dname)+'" onclick="return sureDelete(\''+dname+'\');">&raquo; Delete</a>';
	dhtml+='<a href="./?d='+encodeURIComponent(currDir)+'&amp;renf='+encodeURIComponent(dname)+'" onclick="return prompRen(this,\''+dname+'\');">&raquo; Rename</a>';

	getID('epic_detail').innerHTML=dhtml;
	
	return false;
}
function pasteImage(field,w,h){
	var dname=field.href.substring(6);
	var imgHTML='<img src="'+encodeURI(currUrl+'/'+dname)+'" alt="'+dname+'" width="'+w+'" height="'+h+'" />';
	ed.fire('paste',{'html':imgHTML});
	window.close();
	return false;
}
//ed.execCommand('inserthtml', false, 'TEST <b>Hallo</b>');