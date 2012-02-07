<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

if($viewstr=$_QUERY['watch']){
	$viewvar=explode(".",$viewstr);
	if ($viewvar[2]=='html'){
		$id = (int) base2id($viewvar[1]);
		require_once _dir_mods.'/video/view.php';
		return;
	}
	header('location:'._net.'/video/');
	exit();
}
else{
	require_once _dir_mods.'/video/list.php';
	return;
}

?>
