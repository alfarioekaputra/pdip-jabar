<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }

$_MOD			= 'Video';
$_TIT			= $_MOD;
$_WHR			= '';
$_TAG			= str_replace('-','',txtfriendly($_QUERY['tag'],'-'));
$_CAT			= $_QUERY['cat'];

## TAG
if ($_TAG){
	$_TIT		= "Tag {$_MOD} : {$_TAG}";
	$_WHR   = "(`keyword` LIKE '%{$_TAG}%')";
}
## KATEGORI
elseif ($_CAT){
	$_WHR   = "(`cat`='{$_CAT}')";
	$kategori = $db->sql("SELECT * FROM `"._p."_video_cat` WHERE `kode`='{$_CAT}'");
	if (!$kategori){ header('location:'._net.'/video/'); exit(); }
	$_TIT		= "Kategori {$_MOD} : ".htmlspecialchars($kategori['nama']);
}

## PAGINATION SET
$num_show	= 10;
if (!((int) $_QUERY['page'])) $_QUERY['page']=1;
$halaman  = (int) $_QUERY['page'];
$s			  = (((int) $_QUERY['page'])-1)*$num_show;
if (!$s) $s = 0;
if ($halaman>1){
	$_TIT.=" ({$halaman})";
}

$_TPL->heading($_TIT);

## LIST QUERY
$sql	= "SELECT * FROM `"._p."_video` ".($_WHR?"WHERE {$_WHR}":"")." ORDER BY `waktu` DESC";
$num  = $db->num($sql);
$sql .= " LIMIT {$s}, {$num_show}";
$rows = $db->all($sql);


## SIDE CONTENT
echo '<div class="tph_left" style="width:250px">';

## KATEGORI video
echo '<div class="boxtitle">Kategori '.$_MOD.($_CAT?' Lainnya':'').'</div>';
$bcats=$db->all("SELECT * FROM `"._dbp."_video_cat` ".($_CAT?"WHERE `kode`!='{$_CAT}' ":"")."ORDER BY `pos` ASC, `id` ASC");
echo '<ul class="newscatlist">';
for ($i=0;$i<count($bcats);$i++){
	echo '<li><a href="'._net.'/video/cat/'.($bcats[$i]['kode']).'/">'.htmlspecialchars($bcats[$i]['nama']);
	echo '</a></li>';
}
echo '</ul>';
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';

## NEW NEWS
echo '<div class="boxtitle">Berita Terbaru</div>';
draw_news(5,false,0,true);
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';

## NEW FOTO
$fotosql = "SELECT `id`,`waktu`,`cat`,`judul`,`deskripsi` FROM `"._dbp."_foto_album` WHERE `aktif`='1' ORDER BY `waktu` DESC LIMIT 0,2";
$fotos=$db->all($fotosql);
if ($fotos){
	echo '<div class="boxtitle">Foto Terbaru</div>';
	for ($i=0;$i<count($fotos);$i++){
		$foto=$fotos[$i];
		$coverimg=$db->sql("SELECT `id`,`deskripsi` FROM `"._dbp."_foto` WHERE `contentid`='{$foto['id']}' AND `cover`='1'");
		if ($coverimg){
			echo '<div class="newslist">';
			echo '<div class="newslist_img"><img src="'._net.'/foto/thmb/'.soefriendly($coverimg['deskripsi'],id2base($coverimg['id']).".jpg",20).'" /></div>';
			echo '<h2><a href="'._net.'/foto/album/'.soefriendly($foto['judul'],id2base($foto['id']).".html",30).'">'.htmlspecialchars($foto['judul']).'</a></h2>';
			echo '<p><span>'.getRanahWaktu($foto['waktu']).'</span> &middot; '.htmlspecialchars($foto['deskripsi']).'</p>';
			echo '<div class="clears">&nbsp;</div></div>';
		}
	}
	echo '<div class="morenews"><a href="'._net.'/foto/">Foto Lainnya</a></div>';
	echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';
}



## END OF SIDE
echo '</div>';

## MAIN CONTENT
echo '<div class="tph_bigcenter" style="width:744px">';

echo '<div class="boxtitle" style="margin-bottom:5px">';
echo '<a href="'._relative.'">Home</a> / ';
if ($_CAT||$_TAG){
	echo '<a href="'._net.'/video/">Video</a> / ';
}
else{
	echo 'Video';
}
if ($_CAT){
	echo htmlspecialchars($kategori['nama']);
}
elseif($_TAG){
	echo "Tag : {$_TAG}";
}
echo '</div>';

echo getbanner(728,90);

if ($num<1){
	echo 'TIDAK ADA';
}
else{
	for ($i=0;$i<count($rows);$i++){
		$row=$rows[$i];
		$ckat=$db->sql("SELECT * FROM `"._dbp."_video_cat` WHERE `kode`='{$row['cat']}'");			
		echo '<div class="newslist" style="border-bottom:1px solid #eee">';
		if ($row['youtube_thumb']) echo '<div class="newslist_img"><img src="'.($row['youtube_thumb']).'" width="80" height="60" /></div>';
		echo '<h2><a href="'._net.'/video/watch/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'</a></h2>';
		echo '<p><span>'.getRanahWaktu($row['waktu']).'</span> &middot; '.htmlspecialchars($row['deskripsi']).'<br />';
		
		echo '<em>Durasi : <span>'.htmlspecialchars($row['youtube_durasi']).' detik</span></em>';
		
		if (!$_CAT){
			echo '<em>Dalam Kategori : <a href="'._net.'/video/cat/'.($ckat['kode']).'/">'.htmlspecialchars($ckat['nama']).'</a></em>';
		}		
		$kwd=explode(",",$row['keyword']);
		unset($keywd);
		for ($j=0;$j<count($kwd);$j++){
		  $key=trim($kwd[$j]);
		  if ($key) $keywd[]=$key;
		}
		if (count($keywd)>0)
		{
			echo '<em>Tag : ';
			for ($j=0;$j<count($keywd);$j++){
				echo '<a href="'._net.'/video/tag/'.($keywd[$j]).'/">'.($keywd[$j]).'</a>';
				if ($j<count($keywd)-1) echo ', ';
			}
			echo '</em>';
		}
		
		echo '</p>';
		echo '<div class="clears" style="height:5px">&nbsp;</div></div>';
		
		if ($i==4)
			echo getbanner(728,90);
	}
}

if ($num>$num_show){
	echo '<div class="pagination">';
	$paging = &new_class('pager');
	if ($_TAG)
		$paging->set('urlscheme',_net.'/video/tag/'.($_TAG).'/page/%page%/');
	elseif($_CAT)
		$paging->set('urlscheme',_net.'/video/cat/'.($_CAT).'/page/%page%/');
	else
		$paging->set('urlscheme',_net.'/video/page/%page%/');

	$paging->set('perpage',$num_show);
	$paging->set('page',$_QUERY['page']);
	$paging->set('total',$num);
	
	$paging->set('focusedclass','actived');
	$paging->set('normalclass','');
	$paging->set('delimiter','');
	$paging->set('numlinks',9);
	$paging->display();	
	echo '</div>';
}

echo '</div>';

?>