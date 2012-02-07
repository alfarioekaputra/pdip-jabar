<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

$content = $db->sql("SELECT * FROM `"._p."_berita` WHERE `id`='{$id}'");
if (!$content){ header('location:'._net.'/berita/'); exit(); }
$kategori= $db->sql("SELECT * FROM `"._p."_berita_cat` WHERE `kode`='{$content['cat']}'");
if (!$kategori){ header('location:'._net.'/berita/'); exit(); }

if ($content['image']){
	$content['image']=_net.'/berita/img/'.soefriendly($content['judul'],id2base($content['id']).".jpg",30);
}
$orient 					= array(array(400,300),array(300,400));
$content['width'] = $orient[$kategori['portrait']][0];
$content['height']= $orient[$kategori['portrait']][1];

## HEADER
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>'.htmlspecialchars($content['judul']).'</title>';
## STYLES
echo '<style type="text/css">/*<![CDATA[*/body{ font-family:Arial,Verdana,sans-serif; font-size:14px; padding:20px; }h2{ font-size:22px !important; } h3{ font-size:18px !important;}.clears{ clear:both; height:0px; overflow:hidden;}blockquote{border-left:4px solid #ccc;padding:5px;margin:5px;margin-left:20px;}/*]]>*/</style>';

## BODY & HOLDER
echo '</head><body><div>';

	echo '<h1 style="text-align:center">'.htmlspecialchars($content['judul']).'</h1>';

## CONTENT
	if ($content['image']){
		echo '<div style="text-align:center"><img width="'.($content['width']).'" height="'.($content['height']).'" src="'.($content['image']).'" alt="'.htmlspecialchars($content['judul']).'" /><br />';
		if ($content['judulgbr']){
			echo '<div style="text-align:center;padding:5px;font-weight:bold">'.htmlspecialchars($content['judulgbr']).'</div>';
		}
		echo '</div>';
	}
	echo '<div style="padding:10px">'.($content['isi']).'</div>';


## INFO
	echo '<div style="text-align:center;padding:10px;border-top:1px solid #aaa;margin-top:10px">';
	echo '<b>'.htmlspecialchars($content['sumber']).'</b> pada <b>'.formatTanggal($content['waktu']).'</b><br />';
	$berita_uri  = '/berita/view/'.soefriendly($content['judul'],id2base($content['id']).".html",30);
	$current_url = _www_net.$berita_uri;
	echo '<a href="'.htmlspecialchars($current_url).'">'.htmlspecialchars($current_url).'</a>';
	echo '</div>';

	echo '</div></body></html>';	
	exit();

?>