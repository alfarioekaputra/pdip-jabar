<?php
	$dirname 	= dirname($_SERVER["PHP_SELF"]);
	$path			= substr($_SERVER["REQUEST_URI"],strlen($dirname)+1);
	if (file_exists($path)){
		$isi			= file_get_contents($path);
		$isi = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $isi);
	  $isi = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $isi);
  	header('content-type:text/css');
  	header('content-length:'.strlen($isi));
  	echo $isi;
	}
	else{
		header("HTTP/1.1 403 Forbidden");
		exit();
	}
?>