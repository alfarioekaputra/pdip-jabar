<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
define('_TABLE_NAME',   _dbp."_statis");
define('_TABLE_CAT',   	_dbp."_statis_cat");

$_CAT = txtfriendly($__PATH[1]);
$_PAGE= txtfriendly($__PATH[2]);

$data_cat = $db->sql("SELECT * FROM `"._TABLE_CAT."` WHERE `kode`='{$_CAT}' LIMIT 1");
if (($_PAGE)&&($data_cat))
	$data_page= $db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `cat`='{$_CAT}' AND `kode`='{$_PAGE}' LIMIT 1");

if (!$data_cat){
	$data_cat = $db->sql("SELECT * FROM `"._TABLE_CAT."` ORDER BY `pos` ASC, `id` ASC LIMIT 1");
	header('location:'._net.'/page/'.($data_cat['kode']).'/');
	exit();
}

if (!$data_page)
	$data_page= $db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `cat`='{$data_cat['kode']}' ORDER BY `pos` ASC, `id` ASC LIMIT 1");

if ($data_cat['kode']=='struktural')
	$_SERVER['MAINMENU_SEL']='struktural';
elseif ($data_cat['kode']=='redaksi')
	$_SERVER['MAINMENU_SEL']='redaksi';
	
## SIDE CONTENT
echo '<div class="tph_left" style="width:250px">';

## HALAMAN DALAM KATEGORI
echo '<div class="boxtitle">'.htmlspecialchars($data_cat['nama']).'</div>';
$rows=$db->all("SELECT `kode`,`judul` FROM `"._TABLE_NAME."` WHERE `cat`='{$data_cat['kode']}' ORDER BY `pos` ASC, `id` ASC");
echo '<ul class="newslist_small">';
for ($i=0;$i<count($rows);$i++){
	$row=$rows[$i];
	echo '<li>';
	if ($row['kode']==$data_page['kode']){
		echo '<b class="actived">'.htmlspecialchars($row['judul']).'</b>';
	}
	else{
		echo '<a href="'._net.'/page/'.($data_cat['kode']).'/'.($row['kode']).'">'.htmlspecialchars($row['judul']).'</a>';
	}
	echo '</li>';
}
echo '</ul>';

## KATEGORI HALAMAN
echo '<div class="boxtitle">Internal Lainnya</div>';
$rows=$db->all("SELECT `kode`,`nama` FROM `"._TABLE_CAT."` WHERE `kode`!='{$data_cat['kode']}' ORDER BY `pos` ASC, `id` ASC");
echo '<ul class="newscatlist">';
for ($i=0;$i<count($rows);$i++){
	$row=$rows[$i];
	echo '<li>';
	echo '<a href="'._net.'/page/'.($row['kode']).'/">'.htmlspecialchars($row['nama']).'</a>';
	echo '</li>';
}
echo '</ul>';

## NEW NEWS
echo '<div class="boxtitle">Berita Terbaru</div>';
draw_news(5,false,0,true);

echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';
echo '<div style="padding-bottom:5px">'.getbanner(234,60).'</div>';

## END OF SIDE
echo '</div>';

## MAIN CONTENT
echo '<div class="tph_bigcenter" style="width:744px">';

echo '<div class="boxtitle" style="margin-bottom:5px">';
echo '<a href="'._relative.'">Home</a> / ';
echo '<a href="'._net.'/page/'.($data_cat['kode']).'/">'.htmlspecialchars($data_cat['nama']).'</a>';
echo '</div>';

echo getbanner(728,90);
?>
<div class="addthis_toolbox addthis_default_style" style="float:right"><a class="addthis_counter"></a></div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4cdcfff56e7171cd"></script>
<?php
echo '<h1 class="newsview_title">'.htmlspecialchars($data_page['judul']).'</h1>';
echo '<div class="newsview_content">'.($data_page['isi']).'</div>';
// print_r($data_page);

echo '</div>';
?>