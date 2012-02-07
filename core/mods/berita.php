<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

$thmbstr  = '';
$thmbtype = 0;
if ($_QUERY['thmb']){
	$thmbstr=$_QUERY['thmb'];
}
elseif ($_QUERY['img']){
	$thmbstr=$_QUERY['img'];
	$thmbtype = 1;
}
if ($thmbstr){
	$thmbvar=explode(".",$thmbstr);
	if ($thmbvar[2]=='jpg'){
		$id=(int) base2id($thmbvar[1]);
		$did=$db->sql("SELECT * FROM `"._dbp."_berita` WHERE `id`='{$id}'");
		if ($did){
		  header('content-type:image/jpeg');
		  
		  if ($thmbtype==1)
		  	$orient = array(array(400,300),array(300,400));
		  else
		  	$orient = array(array(80,60),array(60,80));
		  
		  $ckat=$db->sql("SELECT * FROM `"._dbp."_berita_cat` WHERE `kode`='{$did['cat']}'");
		  $im=new_class("autoimg",base64_decode($did['image']),true);
		  if ($im->im){
		  	$im->resize($orient[$ckat['portrait']][0],$orient[$ckat['portrait']][1],1);
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
elseif($viewstr=$_QUERY['view']){
	$viewvar=explode(".",$viewstr);
	if ($viewvar[2]=='html'){
		$id = (int) base2id($viewvar[1]);
		require_once _dir_mods.'/berita/view.php';
		return;
	}
	header('location:'._net.'/berita/');
	exit();
}
elseif($viewstr=$_QUERY['print']){
	$viewvar=explode(".",$viewstr);
	if ($viewvar[2]=='html'){
		$id = (int) base2id($viewvar[1]);
		require_once _dir_mods.'/berita/print.php';
		return;
	}
	header('location:'._net.'/berita/');
	exit();
}
else{
	require_once _dir_mods.'/berita/list.php';
	return;
}

?>
