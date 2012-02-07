<?php
	$dirname 	= dirname($_SERVER["PHP_SELF"]);
	$path			= substr($_SERVER["REQUEST_URI"],strlen($dirname)+1);
	if (file_exists($path)){
		$isi			= file_get_contents($path);
		
		if (eregi("cedit/",$path)){
			header('content-type:text/javascript; charset="iso-8859-1"');
	  	header('content-length:'.strlen($isi));
	  	echo $isi;
	  	exit();
		}
		
		require_once 'packer.php';
		$packer = new JavaScriptPacker($isi,95,true,true);
  	$isi_pack = $packer->pack();
  	if (strlen($isi_pack)>strlen($isi)){
  		$packer2 	= new JavaScriptPacker($isi,0,true,true);
  		$isi_pack = $packer2->pack();
  	}
  	$isi = $isi_pack;
  	header('content-type:text/javascript; charset="iso-8859-1"');
  	header('content-length:'.strlen($isi));
  	echo $isi;
	}
	else{
		header("HTTP/1.1 403 Forbidden");
		exit();
	}
?>