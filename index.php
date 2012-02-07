<?php 
#
#----------- MAIN INDEX ----------------
#
	$_SERVER["PATH_INFO"]="/index"; 
	if (ereg("^/index",$_SERVER["REQUEST_URI"]))
		header("HTTP/1.1 404 Not Found");
	else
		include_once "main.php";
?>