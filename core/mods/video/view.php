<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

$content = $db->sql("SELECT * FROM `"._p."_video` WHERE `id`='{$id}'");
if (!$content){ header('location:'._net.'/video/'); exit(); }
$kategori= $db->sql("SELECT * FROM `"._p."_video_cat` WHERE `kode`='{$content['cat']}'");
if (!$kategori){ header('location:'._net.'/video/'); exit(); }

$_TPL->heading($content['judul']);


## GET KEYWORDS
$kwd=explode(",",$content['keyword']);
unset($where_kwd);
unset($keywd);
for ($i=0;$i<count($kwd);$i++){
  $key=trim($kwd[$i]);
  if ($key){
    $where_kwd[]="(`keyword` LIKE '%{$key}%')";
    $keywd[]=$key;
  }
}
	
?>
<div class="tph_left" style="width:250px">
<?php
echo '<div class="boxtitle">Kategori Video Lainnya</div>';
$rows=$db->all("SELECT * FROM `"._dbp."_video_cat` WHERE `kode`!='{$kategori['kode']}' ORDER BY `pos` ASC, `id` ASC");
echo '<ul class="newscatlist">';
for ($i=0;$i<count($rows);$i++){
	echo '<li><a href="'._net.'/video/cat/'.($rows[$i]['kode']).'/">'.htmlspecialchars($rows[$i]['nama']);
	echo '</a></li>';
}
echo '</ul>';

if (count($where_kwd)>0){
	$foto_where=implode(" OR ",$where_kwd);
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi`,`youtube_thumb` FROM `"._dbp."_video` WHERE `id`!='{$id}' AND ({$foto_where}) ORDER BY `waktu` DESC LIMIT 0,2";
	$videos=$db->all($sql);
	if ($videos){
		echo '<div class="boxtitle">Video Terkait</div>';
		
		for ($i=0;$i<count($videos);$i++){
			$row=$videos[$i];
				echo '<div class="newslist">';
				echo '<div class="newslist_img"><img src="'.htmlspecialchars($row['youtube_thumb']).'" width="80" height="60" /></div>';		
				echo '<h2><a href="'._net.'/video/watch/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'</a></h2>';
				echo '<p><span>'.getRanahWaktu($row['waktu']).'</span> &middot; '.htmlspecialchars($row['deskripsi']).'</p>';
				echo '<div class="clears">&nbsp;</div></div>';
		}
		echo '<div class="morenews"><a href="'._net.'/video/">Video Lainnya</a></div>';
	}


	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi` FROM `"._dbp."_foto_album` WHERE `aktif`='1' AND ({$foto_where}) ORDER BY `waktu` DESC LIMIT 0,4";
	$fotos=$db->all($sql);
	if ($fotos){
		echo '<div class="boxtitle">Foto Terkait</div>';
		echo '<ul class="newslist_small foto">';
		for ($i=0;$i<count($fotos);$i++){
			$row=$fotos[$i];
			$coverimg=$db->sql("SELECT `id`,`deskripsi` FROM `"._dbp."_foto` WHERE `contentid`='{$row['id']}' AND `cover`='1'");
			if ($coverimg){
				echo '<li><a href="'._net.'/foto/album/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'<span>'.getRanahWaktu($row['waktu']).'</span></a></li>';
			}
		}
		echo '</ul>';
		echo '<div class="morenews"><a href="'._net.'/foto/">Foto Lainnya</a></div>';
	}
}

## RELATED NEWS
echo '<div class="boxtitle">Berita Terkait</div>';
if (count($where_kwd)>0){
	$where_kwd=implode(" OR ",$where_kwd);
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`sumber`,`deskripsi`,IF(`image`='',0,1) AS `img` ".
				 	"FROM `"._dbp."_berita` WHERE (`id`!='{$id}') AND ({$where_kwd}) ORDER BY `waktu` DESC LIMIT 0,4";
	draw_news_sql($sql,false,false,true,false);
}
else{
	echo '<div class="newsnodata">Tidak terdapat Berita Terkait</div>';
}
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';


?>
</div>
<div class="tph_bigcenter" style="width:744px">
	<div class="boxtitle" style="margin-bottom:5px">
		<div style="float:right;padding-right:10px"><?php echo formatTanggal($content['waktu']); ?></div>
		<a href="<?php echo _relative; ?>">Home</a> / 
		<a href="<?php echo _net; ?>/video/">Video</a> / 
		<a href="<?php echo _net; ?>/video/cat/<?php echo $kategori['kode']; ?>/"><?php echo htmlspecialchars($kategori['nama']); ?></a> /
		sumber: <?php echo htmlspecialchars($content['sumber']); ?>
	</div>
	<?php echo getbanner(728,90); ?>
<div class="addthis_toolbox addthis_default_style" style="float:right"><a class="addthis_counter"></a></div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4cdcfff56e7171cd"></script>
	<h1 class="newsview_title"><?php echo htmlspecialchars($content['judul']); ?></h1>
<?php
	echo '<div style="padding-top:4px;padding-bottom:20px;width:728px;margin:auto"><script type="text/javascript">/*<![CDATA[*/';
	echo 'drawFlash(false,"'.($content['youtube_embed']).'",728,546);';
	echo '/*]]>*/</script></div>';
?>
	<div class="newsview_content"><?php echo $content['isi']; ?></div>
	
<?php
	echo getbanner(728,90);
	echo '<div class="newsview_info">';
	
	echo '<span class="newsview_info_label">Oleh :</span><span class="newsview_info_val">';
	$user=$db->sql("SELECT * FROM `"._p."_user` WHERE `username`='{$content['modifier']}'");
	echo htmlspecialchars($user['fullname']);
	echo ' <em style="color:#666">sumber</em> '.htmlspecialchars($content['sumber']);
	echo ' <em style="color:#666">pada</em> '.formatTanggal($content['waktu']);
	echo '</span>';
	
	echo '<span class="newsview_info_label">Durasi :</span><span class="newsview_info_val">';
	echo ($content['youtube_durasi']).' Detik';
	echo '</span>';
	
	echo '<span class="newsview_info_label">Youtube URL :</span><span class="newsview_info_val tag">';
	echo '<a href="'.htmlspecialchars($content['youtube_url']).'">'.htmlspecialchars($content['youtube_url']).'</a>';
	echo '</span>';
	
	if (count($keywd)>0)
	{
		echo '<span class="newsview_info_label">Tag :</span><span class="newsview_info_val tag">';
		for ($i=0;$i<count($keywd);$i++){
			echo '<a href="'._net.'/video/tag/'.($keywd[$i]).'/">'.($keywd[$i]).'</a>';
			if ($i<count($keywd)-1) echo ', ';
		}
		echo '</span><br />';
	}
	
	$berita_uri  = '/video/watch/'.soefriendly($content['judul'],id2base($content['id']).".html",30);
	$current_url = _www_net.$berita_uri;
	
	echo '<span class="newsview_info_label">Like This :</span><span class="newsview_info_val">';
	?><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo htmlspecialchars($current_url);?>&amp;layout=standard&amp;show_faces=true&amp;width=650&amp;action=like&amp;colorscheme=light&amp;height=24" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:650px; height:24px;" allowTransparency="true"></iframe><?php
	echo '</span>';
	
	echo '</div>';
?>
<div class="clears" style="height:5px">&nbsp;</div>
<div class="home_bottom">
	<div class="tph_right" style="width:365px">
		<div style="padding-right:4px">
			<div class="boxtitle">Video : <?php echo htmlspecialchars($kategori['nama']); ?></div><?php
	## Other News In Category 

	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi`,`youtube_thumb` FROM `"._dbp."_video` WHERE  `id`!='{$id}' AND `cat`='{$kategori['kode']}' AND `id`!='{$id}' ORDER BY `waktu` DESC LIMIT 0,3";
	$fotos=$db->all($sql);
	if ($fotos){
		for ($i=0;$i<count($fotos);$i++){
			$row=$fotos[$i];
			echo '<div class="newslist">';
			echo '<div class="newslist_img"><img src="'.($row['youtube_thumb']).'" width="80" height="60" /></div>';		
			echo '<h2><a href="'._net.'/video/watch/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'</a></h2>';
			echo '<p><span>'.getRanahWaktu($row['waktu']).'</span> &middot; '.htmlspecialchars($row['deskripsi']).'</p>';
			echo '<div class="clears">&nbsp;</div></div>';
		}
		echo '<div class="morenews"><a href="'._net.'/video/cat/'.($kategori['kode']).'/">'.htmlspecialchars($kategori['nama']).' Lainnya</a></div>';
	}
		?></div>
	</div>
	<div class="tph_center" style="width:365px">
		<div class="boxtitle">Berita Terbaru</div><?php 
		draw_news_sql(
			"SELECT `id`,`waktu`,`cat`,`judul`,`sumber`,`deskripsi`,IF(`image`='',0,1) AS `img` ".
		  	"FROM `"._p."_berita` WHERE `id`!='{$id}' ORDER BY `waktu` DESC LIMIT 0,5",
		  "Berita",
		  _net."/berita/",
		  true, false
		);
	?>
	</div>
	<div class="clears">&nbsp;</div>
</div>

</div>