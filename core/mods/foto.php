<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
$thmbstr  = '';
$thmbtype = 0;
if ($_QUERY['ssimg']){
	$thmbstr=$_QUERY['ssimg'];
}
elseif ($_QUERY['img']){
	$thmbstr=$_QUERY['img'];
	$thmbtype = 1;
}
elseif ($_QUERY['thmb']){
	$thmbstr=$_QUERY['thmb'];
	$thmbtype = 2;
}
elseif ($_QUERY['ath']){
	$thmbstr=$_QUERY['ath'];
	$thmbtype = 3;
}
if ($thmbstr){
	$thmbvar=explode(".",$thmbstr);
	if ($thmbvar[2]=='jpg'){
		$id=(int) base2id($thmbvar[1]);
		$did=$db->sql("SELECT * FROM `"._dbp."_foto` WHERE `id`='{$id}'");
		if ($did){
		  header('content-type:image/jpeg');
		 	
		 	if ($thmbtype==1)
		  	$orient = array(array(500,375),array(375,500),array(500,500));
		  elseif ($thmbtype==2)
		  	$orient = array(array(80,60),array(60,80),array(80,80));
		  elseif ($thmbtype==3)
		  	$orient = array(array(50,50),array(50,50),array(50,50));
		  else
		  	$orient = array(array(468,250),array(468,250),array(468,250));

		  $im=new_class("autoimg",base64_decode($did['image']),true);
		  if ($im->im){
		  	$im->resize($orient[$did['orientation']][0],$orient[$did['orientation']][1],1);
		    $ret=$im->buf();
		    header('content-length:'.strlen($ret));
		    echo $ret;
		    exit();
		  }
		}
	}
	header("HTTP/1.1 404 Not Found");
	exit();
}
elseif($viewstr=$_QUERY['album']){
	$viewvar=explode(".",$viewstr);
	if ($viewvar[2]=='html'){
		$id = (int) base2id($viewvar[1]);
		require_once _dir_mods.'/foto/album.php';
		return;
	}
	header('location:'._net.'/foto/');
	exit();
}
else{
	require_once _dir_mods.'/foto/list.php';
	return;
}


?>