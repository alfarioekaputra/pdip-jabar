<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }

echo '<div id="tss_holder"><div id="tss_contents">';

$rows=$db->all("SELECT * FROM `"._dbp."_berita` WHERE `flag`='1' LIMIT 0,5");
unset($rowsa);
for ($i=0;$i<count($rows);$i++){
	$row=$rows[$i];
	//$coverimg=$db->sql("SELECT `id`,`deskripsi` FROM `"._dbp."_berita` WHERE `flag`='1'");
	//if ($coverimg){
		$rowsa[]=array(
				'url'=>_net.'/berita/view/'.soefriendly($row['judul'],id2base($row['id']).".html",30),
				'judul'=>$row['judul'],
				'img'=>_net.'/berita/img/'.soefriendly($row['judul'],id2base($row['id']).".jpg",30)
			);
	//}
}
for ($i=0;$i<count($rowsa);$i++){
	$row=$rowsa[$i];
	echo '<div class="tss_content" style="background-image:url(\''.($row['img']).'\')">';
		echo '<h1>';
		echo '<a href="'.htmlspecialchars($row['url']).'">'.htmlspecialchars($row['judul']).'</a>';
		echo '</h1>';
	echo '</div>';
}

echo '</div></div>';
echo '<script type="text/javascript" src="'._shr.'/ss/m.js"></script>';

?>