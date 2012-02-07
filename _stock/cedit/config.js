CKEDITOR.editorConfig = function(config){
	config.extraPlugins = 'myImage';	
	config.stylesCombo_stylesSet = 'uc_styles';
};
CKEDITOR.plugins.add('myImage',
{
    init: function(editor)
    {
        editor.addCommand('myImage',{exec:function(ed){
        	epictures_Editor=ed;
					window.open(shr+'/cedit/epictures/','epicTures','width=530,height=360');
        }});
				editor.ui.addButton('myImage',
					{
						label : 'Masukan Gambar',
						command : 'myImage',
						icon : shr+'/ico/pic.png'
					}
				);
    }
});
CKEDITOR.addStylesSet( 'uc_styles',
[
    { name : 'Normal', 			element : 'div', styles : {}},
    { name : 'Paragraf', 		element : 'div', styles : {'padding-bottom':'10px'}},
    { name : 'Header', 			element : 'h2', styles : { 'border-bottom':'1px solid #aaa','font-size' : '18px', 'color':'#444','margin':'0px 0px 5px 0px','padding-bottom':'4px','padding-top':'8px' } },
    { name : 'Sub Header' , element : 'h3', styles : { 'border-bottom':'1px solid #ccc','font-size' : '14px', 'color':'#666','margin':'0px 0px 5px 0px','padding-bottom':'4px','padding-top':'8px' } },
    { name : 'Code' , element : 'div', styles : { 'font-family' : "'Courier New',courier,fixed", 'font-size':'12px','padding':'5px','background-color':'#f5f5f5','border-left':'10px solid #aaa','color':'#333' } },
    { name : 'Marker: Abu', element : 'span', styles : { 'background-color' : '#ccc' }},
    { name : 'Marker: Kuning', element : 'span', styles : { 'background-color' : '#ff0' }},
    { name : 'Marker: Merah', element : 'span', styles : { 'background-color' : '#faa' }},
    { name : 'Marker: Hijau', element : 'span', styles : { 'background-color' : '#afa' }},
    { name : 'Marker: Biru', element : 'span', styles : { 'background-color' : '#aaf' }}
]);