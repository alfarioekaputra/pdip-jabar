//-- TinyMCE Plugins
(function() {
	tinymce.create('tinymce.plugins.epictures', {
		init : function(ed, url) {
			ed.addButton('epictures', {
				title : 'epicTures - Insert Picture',
				image : url + '/ico.gif',
				onclick : function() {
					epictures_OpenWindow(ed,url);
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "epicTures TinyMCE Plugin",
				author : 'e-Natives Techmology - Ahmad Amarullah',
				authorurl : 'http://www.e-natives.com/',
				infourl : 'http://www.e-natives.com/',
				version : "1.0.0"
			};
		}
	});
	tinymce.PluginManager.add('epictures', tinymce.plugins.epictures);
})();

//-- Global Script
var epictures_Editor=null;
function epictures_OpenWindow(ed,uri){
	epictures_Editor=ed;
	window.open(uri,'epicTures','width=520,height=360');
}