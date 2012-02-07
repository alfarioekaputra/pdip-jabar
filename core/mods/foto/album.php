<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

$content = $db->sql("SELECT * FROM `"._p."_foto_album` WHERE `id`='{$id}'");
if (!$content){ header('location:'._net.'/foto/'); exit(); }
$kategori= $db->sql("SELECT * FROM `"._p."_foto_cat` WHERE `kode`='{$content['cat']}'");
if (!$kategori){ header('location:'._net.'/foto/'); exit(); }

$_TPL->heading($content['judul']);
$orient = array(array(400,300),array(300,400),array(400,400));
$orients= array(array(80,60),array(60,80),array(80,80));
$_SERVER['MAINMENU_SEL']='foto';

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
if (count($where_kwd)>0){
	$foto_where=implode(" OR ",$where_kwd);
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi` FROM `"._dbp."_foto_album` WHERE (`id`!='{$id}') AND `aktif`='1' AND ({$foto_where}) ORDER BY `waktu` DESC LIMIT 0,2";
	$fotos=$db->all($sql);
	echo '<div class="boxtitle">Foto Terkait</div>';
	if ($fotos){		
		$foto_is_exists=false;
		for ($i=0;$i<count($fotos);$i++){
			$row=$fotos[$i];
			$coverimg=$db->sql("SELECT `id`,`deskripsi` FROM `"._dbp."_foto` WHERE `contentid`='{$row['id']}' AND `cover`='1'");
			if ($coverimg){
				$foto_is_exists=true;
				echo '<div class="newslist">';
				echo '<div class="newslist_img"><img src="'._net.'/foto/thmb/'.soefriendly($coverimg['deskripsi'],id2base($coverimg['id']).".jpg",20).'" /></div>';		
				echo '<h2><a href="'._net.'/foto/album/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'</a></h2>';
				echo '<p><span>'.getRanahWaktu($row['waktu']).'</span> &middot; '.htmlspecialchars($row['deskripsi']).'</p>';
				echo '<div class="clears">&nbsp;</div></div>';
			}
		}
		if ($foto_is_exists){
			echo '<div class="morenews"><a href="'._net.'/foto/">Foto Lainnya</a></div>';
			echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';
		}
		else{
			echo '<div class="newsnodata">Tidak terdapat Foto Terkait</div>';
		}
	}
	else{
		echo '<div class="newsnodata">Tidak terdapat Foto Terkait</div>';
	}
}

## RELATED NEWS
echo '<div class="boxtitle">Berita Terkait</div>';
if (count($where_kwd)>0){
	$where_kwd=implode(" OR ",$where_kwd);
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`sumber`,`deskripsi`,IF(`image`='',0,1) AS `img` ".
				 	"FROM `"._dbp."_berita` WHERE ({$where_kwd}) ORDER BY `waktu` DESC LIMIT 0,5";
	draw_news_sql($sql,false,false,false,false);
}
else{
	echo '<div class="newsnodata">Tidak terdapat Berita Terkait</div>';
}
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';

	echo '<div class="boxtitle">Kategori Lainnya</div>';
	$rows=$db->all("SELECT * FROM `"._dbp."_foto_cat` WHERE `kode`!='{$kategori['kode']}' ORDER BY `pos` ASC, `id` ASC");
	echo '<ul class="newscatlist">';
	for ($i=0;$i<count($rows);$i++){
		echo '<li><a href="'._net.'/foto/cat/'.($rows[$i]['kode']).'/">'.htmlspecialchars($rows[$i]['nama']);
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
		<a href="<?php echo _net; ?>/foto/">Galeri Foto</a> / 
		<a href="<?php echo _net; ?>/foto/cat/<?php echo $kategori['kode']; ?>/"><?php echo htmlspecialchars($kategori['nama']); ?></a> /
		sumber: <?php echo htmlspecialchars($content['sumber']); ?>
	</div>
	<?php echo getbanner(728,90); ?>
<div class="addthis_toolbox addthis_default_style" style="float:right"><a class="addthis_counter"></a></div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4cdcfff56e7171cd"></script>
	<h1 class="newsview_title"><?php echo htmlspecialchars($content['judul']); ?></h1>
<?php

echo '<div id="gallery_holder" style="display:none"><div id="gallery_contents" style="position:relative;text-align:center">';
$rows=$db->all("SELECT `id`,`deskripsi` FROM `"._dbp."_foto` WHERE `contentid`='{$id}' ORDER BY `pos` ASC");
unset($rowsa);
for ($i=0;$i<count($rows);$i++){
	$row=$rows[$i];
	$rowsa[]=array(
			'judul'=>$row['deskripsi'],
			'img'=>_net.'/foto/img/'.soefriendly($row['deskripsi'],id2base($row['id']).".jpg",20),
			'low'=>_net.'/foto/ath/'.soefriendly($row['deskripsi'],id2base($row['id']).".jpg",20)
	);
}
for ($i=0;$i<count($rowsa);$i++){
	$row=$rowsa[$i];
	if ($i==count($rowsa)-1)
	echo '<div>';
	else
	echo '<div style="display:none">';
		echo '<img src="'.($row['img']).'" />';
		echo '<h1>'.nl2br(htmlspecialchars($row['judul'])).'</h1>';
	echo '</div>';
}

echo '</div><div class="clears" id="gallery_clears">&nbsp;</div></div>';

echo '<div id="gallery_thumbnails" style="display:none">';
for ($i=0;$i<count($rowsa);$i++){
	$row=$rowsa[$i];
	echo '<img src="'.($row['low']).'" '.(($i==count($rowsa)-1)?'class="actived" ':'').'/>';
}
echo '</div>';
?>
<div id="gallery_wait">
	<img src="<?php echo _shr; ?>/ico/load.gif" /><br />
	Memuat Album Foto<br />
	Silahkan Tunggu...
</div>
<script type="text/javascript">
var gallery_n, gallery_v, gallery_a, gallery_t;
function nextGallery(prev_n){
	var cur = gallery_v[prev_n];
	cur.style.display='none';
}
function updateGallery(next_n){
	if (gallery_n<=1) return;
	if (next_n) { if (next_n==gallery_a+1) return; }
	var curn= gallery_a;
	var cur = gallery_v[gallery_a];
	gallery_t[gallery_a].className='';
	if (next_n){
		gallery_a=next_n-1;
	}
	else{
		gallery_a++; if (gallery_a>=gallery_n) gallery_a=0;
	}
	var nex = gallery_v[gallery_a];
	gallery_t[gallery_a].className='actived';
	
	cur.style.position='absolute';
	cur.style.left='0px';
	cur.style.top='0px';
	nex.style.position='';
	nex.style.width=nex.parentNode.offsetWidth+'px';
	setOpacity(nex,0);
	nex.style.display='';
	
	aniOpacity(cur,0,5);
	aniOpacity(nex,100,5,'nextGallery('+curn+')');
	aniDivHeight(getID('gallery_holder'),getID('gallery_clears'),'relative',40,10);
	
}
function startGallery(){
	getID('gallery_wait').style.display='none';
	getID('gallery_holder').style.display='';
	getID('gallery_thumbnails').style.display='';
	updateGallery();
}
function initGallery(){
	gallery_v = new Array();
	gallery_t = new Array();
	gallery_n = 0;
	var contents = getID('gallery_contents');
	var thumbs = getID('gallery_thumbnails');
	var chld = contents.firstChild;
	var chlt = thumbs.firstChild;
	do{
		if (chld.tagName.toLowerCase()=='div'){
			gallery_t[gallery_n]=chlt;
			chlt.setAttribute('gallertyn',gallery_n+1);
			chlt.onclick=function(){
				updateGallery(parseInt(this.getAttribute('gallertyn')));
			};
			gallery_v[gallery_n++]=chld;
		}
		chlt=chlt.nextSibling;
		chld=chld.nextSibling;
	}
	while(chld);	

	gallery_a = gallery_n-1;
	setOnload(startGallery);
}
initGallery();
</script>
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
	
	if (count($keywd)>0)
	{
		echo '<span class="newsview_info_label">Tag :</span><span class="newsview_info_val tag">';
		for ($i=0;$i<count($keywd);$i++){
			echo '<a href="'._net.'/foto/tag/'.($keywd[$i]).'/">'.($keywd[$i]).'</a>';
			if ($i<count($keywd)-1) echo ', ';
		}
		echo '</span><br />';
	}
	
	$berita_uri  = '/foto/album/'.soefriendly($content['judul'],id2base($content['id']).".html",30);
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
			<div class="boxtitle">Foto : <?php echo htmlspecialchars($kategori['nama']); ?></div><?php
	## Other News In Category 
	$sql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi` FROM `"._dbp."_foto_album` WHERE `aktif`='1' AND `cat`='{$kategori['kode']}' AND `id`!='{$id}' ORDER BY `waktu` DESC LIMIT 0,3";
	$fotos=$db->all($sql);
	if ($fotos){
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
		echo '<div class="morenews"><a href="'._net.'/foto/cat/'.($kategori['kode']).'/">'.htmlspecialchars($kategori['nama']).' Lainnya</a></div>';
	}
	else{
		echo '<div class="newsnodata">Tidak terdapat Album Foto Lain</div>';
	}
	
		?></div>
	</div>
	<div class="tph_center" style="width:365px">
		<div class="boxtitle">Berita Terbaru</div><?php draw_news(5,false,0,true); ?>
	</div>
	<div class="clears">&nbsp;</div>
</div>
</div>