<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
$_SERVER['MAIN_MENU']=array(
  //    permission      file         title                      category
  array(    1,    'berita',             'Berita',             		1),
  array(    2,    'cat_berita',         'Kategori Berita',        1),
  
  array(    3,    'halaman',            'Halaman',        	  			2),
  array(    4,    'cat_halaman',         'Kategori Halaman',  			2),
  
  array(    5,    'foto',               'Foto',            				3),
  array(    6,    'cat_foto',           'Kategori Foto',          3),

	array(    7,    'video',             	'Video',               		4),
	array(    8,    'cat_video',         	'Kategori Video',      		4),
	
/*  array(    9,    'agenda',             'Agenda',               	5),*/
  array(   9,    'banner',             'Banner',                 5),  
  array(   10,    'user',               'User Admin',      				5)
);

for ($i=0;$i<count($_SERVER['MAIN_MENU']);$i++){
  $mm=$_SERVER['MAIN_MENU'][$i];
  $_SERVER['MODULE_PERMISSION'][$mm[1]]=$mm[0];
}


$_ADMIN_DATA=$db->sql("SELECT * FROM `"._dbp."_admin` WHERE `user`='{$_SESSION['loginid']}'");

?>
