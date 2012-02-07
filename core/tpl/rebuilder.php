<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
#
## Rebuild HTML Here...
$MainMenu_Token = array(
	'home',
	'jabar',
	'nasional',
	'amanatpartai',
	'struktural',
	'arsip',
	'foto',
	'redaksi'
);
for ($i=0;$i<count($MainMenu_Token);$i++){
	if ($_SERVER['MAINMENU_SEL']==$MainMenu_Token[$i]){
		$_TPL->token("mainmenu_{$MainMenu_Token[$i]}",'class="actived" ');
	}
	else{
		$_TPL->token("mainmenu_{$MainMenu_Token[$i]}","");
	}
}

if ($_SESSION['loginadmin']){
	if ($_SERVER['MAINMENU_SEL']=='admin')
		$_TPL->token('admin_link','<a class="actived" href="'._net.'/admin/">ADMIN</a>');
	else
		$_TPL->token('admin_link','<a href="'._net.'/admin/">ADMIN</a>');
}
else
	$_TPL->token('admin_link','');
?>