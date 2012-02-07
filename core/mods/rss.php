<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
# Halaman : NEWS
#
define('_TABLE_NAME',   _dbp."_berita");

// KATEGORI LAINNYA
$KATEGORI=$_SERVER['KATEGORI_BERITA'];

	//## 5 Hours Expire
	header("Expires: ".gmdate("D, d M Y H:i:s",time()+(3600*5))." GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT",time());
	header("content-type: text/xml");
	
	//## RSS HEADER:
	echo "<".'?xml version="1.0" encoding="ISO-8859-1"?'.">\r\n\r\n";
	echo '<rss version="0.91">'."\r\n\r\n<channel>\r\n\r\n";
	
	//## RSS INFO:
	echo "<title>"._web_title." - News</title>\r\n";
	echo "<link>"._www."</link>\r\n";
	echo "<description>"._web_title."</description>\r\n";
	echo "<language>id</language>\r\n\r\n\r\n";
	
	//## CONTENTS:
	$rows=$db->all("SELECT * FROM `"._dbp."_berita` ORDER BY `waktu` DESC LIMIT 0,15");
	for ($i=0;$i<count($rows);$i++){
		$row=$rows[$i];
		$URL="http://"._dns._net."/berita/view/".soefriendly($row['judul'],id2base($row['id']).".html",30);
		$JUDUL=htmlspecialchars($row['judul']);
		$DESKRIPSI=htmlspecialchars($row['deskripsi']);
		$DATE=date("r",$row['waktu']);
		echo	"<item>\r\n\t".
					"<title>{$JUDUL}</title>\r\n\t".
					"<link>{$URL}</link>\r\n\t".
					"<description>{$DESKRIPSI}</description>\r\n\t".
					"</item>\r\n\r\n";
	}
	
	//## RSS FOOTER:
	echo "\r\n\r\n</channel>\r\n</rss>";
exit();
?>