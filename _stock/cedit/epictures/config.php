<?php
	define("_IN_PHP",true);
	$_TMP_CURR_DIR=getcwd();
	chdir("../../../");
	include_once './configuration.php';
	
	define("_url_save",     _cfg_base_uri."/_stock/img_contents");
  define("_dir_save",     getcwd()."/_stock/img_contents");

	chdir($_TMP_CURR_DIR);
	session_name(_session_id);
  session_start();

  //-- Break jika belum login
  if (!$_SESSION['loginid']){
  	header("HTTP/1.1 403 Forbidden");
  	exit();
  }
?>