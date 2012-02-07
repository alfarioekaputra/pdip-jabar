<?php
	include './config.php';
	include './autoimg.php';
  
  $dirpos=stripslashes($_GET['d']);
  $file  =stripslashes($_GET['f']);
  if (substr($dirpos,0,5)!=">root") $dirpos=">root";
  $curl="./?d=".urlencode($dirpos);
  $dirpath=_dir_save.substr($dirpos,5);
  $urlpath=_url_save.substr($dirpos,5);
	$filepath=$dirpath.'/'.$file;
	
	$sz=getimagesize($filepath);
	if ($sz){
		$im=new autoimg(file_get_contents($filepath),true);
		$im->resize(108,108,true);
		header('content-type:image/png');
		echo $im->buf();
	  exit();
	}
?>