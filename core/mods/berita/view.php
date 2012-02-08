<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

$content = $db->sql("SELECT * FROM `"._p."_berita` WHERE `id`='{$id}'");
if (!$content){ header('location:'._net.'/berita/'); exit(); }
$kategori= $db->sql("SELECT * FROM `"._p."_berita_cat` WHERE `kode`='{$content['cat']}'");
if (!$kategori){ header('location:'._net.'/berita/'); exit(); }

$_TPL->heading($content['judul']);
if ($content['image']){
	$content['image']=_net.'/berita/img/'.soefriendly($content['judul'],id2base($content['id']).".jpg",30);
}
$orient 					= array(array(400,300),array(300,400));
$orient_small			= array(array(220,176),array(176,220));
$content['width'] = $orient[$kategori['portrait']][0];
$content['height']= $orient[$kategori['portrait']][1];
$content['swidth'] = $orient_small[$kategori['portrait']][0];
$content['sheight']= $orient_small[$kategori['portrait']][1];
echo $content['dilihat'];
switch($kategori['kode']){
	case 'nasional':
	case 'jabar':
	case 'amanatpartai':
		$_SERVER['MAINMENU_SEL']=$kategori['kode'];
	break;
}

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
$dilihat = "UPDATE `"._p."_berita` set `dilihat`= (`dilihat`+1) WHERE `id` = '{$id}'"; //counter berita
$db->query($dilihat);
//echo "<script>alert('$db->query($dilihat)');</script>";
?>
<div class="tph_left" style="width:250px">
<?php
if (count($where_kwd)>0){
	$foto_where=implode(" OR ",$where_kwd);
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi` FROM `"._dbp."_foto_album` WHERE `aktif`='1' AND ({$foto_where}) ORDER BY `waktu` DESC LIMIT 0,2";
	$fotos=$db->all($sql);
	if ($fotos){
		echo '<div class="boxtitle">Foto Terkait</div>';
		
		for ($i=0;$i<count($fotos);$i++){
			$row=$fotos[$i];
			$coverimg=$db->sql("SELECT `id`,`deskripsi` FROM `"._dbp."_foto` WHERE `contentid`='{$row['id']}' AND `cover`='1'");
			if ($coverimg){
				echo '<div class="newslist">';
				echo '<div class="newslist_img"><img src="'._net.'/foto/thmb/'.soefriendly($coverimg['deskripsi'],id2base($coverimg['id']).".jpg",20).'" /></div>';		
				echo '<h2><a href="'._net.'/foto/album/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'</a></h2>';
				echo '<p><span>'.getRanahWaktu($row['waktu']).'</span> &middot; '.htmlspecialchars($row['deskripsi']).'</p>';
				echo '<div class="clears">&nbsp;</div></div>';
			}
		}
		
		echo '<div class="morenews"><a href="'._net.'/foto/">Foto Lainnya</a></div>';
		echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';
	}
}

## RELATED NEWS
echo '<div class="boxtitle">Berita Terkait</div>';
if (count($where_kwd)>0){
	$where_kwd=implode(" OR ",$where_kwd);
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`sumber`,`deskripsi`,IF(`image`='',0,1) AS `img` ".
				 	"FROM `"._dbp."_berita` WHERE (`id`!='{$id}') AND ({$where_kwd}) ORDER BY `waktu` DESC LIMIT 0,5";
	draw_news_sql($sql,false,false,false,false);
}
else{
	echo '<div class="newsnodata">Tidak terdapat Berita Terkait</div>';
}
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';

	
echo '<div class="boxtitle">Kategori Berita Lainnya</div>';
$rows=$db->all("SELECT * FROM `"._dbp."_berita_cat` WHERE `kode`!='{$kategori['kode']}' ORDER BY `pos` ASC, `id` ASC");
echo '<ul class="newscatlist">';
for ($i=0;$i<count($rows);$i++){
	echo '<li><a href="'._net.'/berita/cat/'.($rows[$i]['kode']).'/">'.htmlspecialchars($rows[$i]['nama']);
	echo '</a></li>';
}
echo '</ul>';
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';

?>
</div>
<div class="tph_bigcenter" style="width:744px">
	<div class="boxtitle" style="margin-bottom:5px">
		<div style="float:right;padding-right:10px"><?php echo formatTanggal($content['waktu']); ?></div>
		<a href="<?php echo _relative; ?>">Home</a> / 
		<a href="<?php echo _net; ?>/berita/">Berita</a> / 
		<a href="<?php echo _net; ?>/berita/cat/<?php echo $kategori['kode']; ?>/"><?php echo htmlspecialchars($kategori['nama']); ?></a> /
		sumber: <?php echo htmlspecialchars($content['sumber']); ?>
	</div>
	<?php echo getbanner(728,90); ?><div class="addthis_toolbox addthis_default_style" style="float:right"><a class="addthis_counter"></a></div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4cdcfff56e7171cd"></script>
	<h1 class="newsview_title"><?php echo htmlspecialchars($content['judul']); ?></h1>
<?php
	if ($content['image']){
		echo '<div onclick="news_imgExpand(this,'.($content['width']).','.($content['height']).');" title="Klik di sini untuk memperbesar" class="newsview_image"><img src="'.($content['image']).'" width="'.($content['swidth']).'" height="'.($content['sheight']).'" alt="'.htmlspecialchars($content['judul']).'" />';
		if ($content['judulgbr']){
			echo '<div style="text-align:center;padding:5px;font-weight:bold">'.htmlspecialchars($content['judulgbr']).'</div>';
		}
		echo '</div>';
	}
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

	echo '<span class="newsview_info_label">Dibaca :</span><span class="newsview_info_val">';
	echo htmlspecialchars($content['dilihat'])."&nbsp;Kali";
	echo '</span>';
	
	$berita_uri  = '/berita/print/'.soefriendly($content['judul'],id2base($content['id']).".html",30);
	$current_url = _www_net.$berita_uri;
	$goodname		 = soefriendly($content['judul'],id2base($content['id']).".pdf",30);
	$pdfurl = "http://pdfmyurl.com/?url=".urlencode($current_url)."&-O=Portrait&--filename=".urlencode($goodname);	
	
	echo '<span class="newsview_info_label">Format Lain :</span><span class="newsview_info_val">';
	$print_url 	= _net.'/berita/print/'.soefriendly($content['judul'],id2base($content['id']).".html",30);
	$pdf_url 	= htmlspecialchars($pdfurl);
	echo '<a class="print" href="'.$print_url.'">Print Friendly</a>';
	echo '<a class="pdf" href="'.$pdf_url.'">PDF</a>';
	echo '</span>';
	
	if (count($keywd)>0)
	{
		echo '<span class="newsview_info_label">Tag :</span><span class="newsview_info_val tag">';
		for ($i=0;$i<count($keywd);$i++){
			echo '<a href="'._net.'/berita/tag/'.($keywd[$i]).'/">'.($keywd[$i]).'</a>';
			if ($i<count($keywd)-1) echo ', ';
		}
		echo '</span><br />';
	}
	
	$berita_uri  = '/berita/view/'.soefriendly($content['judul'],id2base($content['id']).".html",30);
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
			<div class="boxtitle"><?php echo htmlspecialchars($kategori['nama']); ?></div><?php
	## Other News In Category 
	draw_news_sql(
			"SELECT `id`,`waktu`,`cat`,`judul`,`sumber`,`deskripsi`,IF(`image`='',0,1) AS `img` ".
		  	"FROM `"._p."_berita` WHERE `id`!='{$id}' AND `cat`='{$kategori['kode']}' ORDER BY `waktu` DESC LIMIT 0,5",
		  $kategori['nama'],
		  _net."/berita/cat/{$kategori['kode']}/",
		  true, false
	);
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