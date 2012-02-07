<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
	$_SERVER['MAINMENU_SEL'] = 'home';
	$_SERVER['TRIM_OUTPUT'] = true;
?>
<div id="tph_left" class="tph_left">
	<div class="boxtitle">Berita Terbaru</div>
	<?php draw_news(8,false,0,true); ?>
	
	<div class="boxtitle" style="margin-top:5px">Suara Kader</div>

<div id="tph_scroll_suarakader" style="position:relative;overflow:hidden;height:0px;margin-left:5px;margin-right:5px"><div id="suarakader_scroll" style="display:none;position:absolute">
<?php draw_news(8,'suarakader',0,false,false,'Suara Kader'); ?>
</div></div>

<?php
	
## KATEGORI HALAMAN
echo '<div class="boxtitle">Internal</div>';
$rows=$db->all("SELECT `kode`,`nama` FROM `"._p."_statis_cat` ORDER BY `pos` ASC, `id` ASC");
echo '<ul class="newscatlist">';
for ($i=0;$i<count($rows);$i++){
	$row=$rows[$i];
	echo '<li>';
	echo '<a href="'._net.'/page/'.($row['kode']).'/">'.htmlspecialchars($row['nama']).'</a>';
	echo '</li>';
}
echo '</ul>';
	
	?><div class="boxtitle">Facebook</div>
	<div style="padding:0;position:relative;width:220px;height:235px;overflow:hidden"><iframe src="http://www.facebook.com/plugins/likebox.php?href=<?php echo urlencode(_facebook_fan_url); ?>&amp;width=240&amp;colorscheme=light&amp;connections=8&amp;stream=false&amp;header=false&amp;height=275" scrolling="no" frameborder="0" style="margin-top:-9px;margin-left:-9px;border:none;overflow:hidden;width:240px;height:275px;" allowTransparency="true"></iframe></div>
</div>
<div id="tph_right" class="tph_right"><?php
	echo getbanner(300,250);

## VIDEO
$rows=$db->all("SELECT `id`,`waktu`,`judul`,`youtube_thumb`,`youtube_embed` FROM `"._p."_video` ORDER BY `waktu` DESC LIMIT 0,4");
if (count($rows)>0){
	echo '<div class="boxtitle" style="margin-top:5px">Video</div>';
	
	echo '<div style="padding-top:4px;padding-bottom:4px"><script type="text/javascript">/*<![CDATA[*/';
	echo 'drawFlash(false,"'.($rows[0]['youtube_embed']).'",300,225);';
	echo '/*]]>*/</script></div>';
	
	echo '<ul class="newslist_small video">';
	for ($i=0;$i<count($rows);$i++){
		$row=$rows[$i];
		echo '<li><a href="'._net.'/video/watch/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']);
		echo '<span>'.getRanahWaktu($row['waktu']).'</span>';
		echo '</a></li>';
	}
	echo '</ul>';
	echo '<div class="morenews"><a href="'._net.'/video/">Video Lainnya</a></div>';
}

?>
	<div class="boxtitle" id="amanatpartai_title" style="margin-top:5px">Amanat Partai</div>
	<?php draw_news(4,'amanatpartai',0,true,true,'Amanat Partai'); ?>
	
	<div id="twitter_holder" class="twitter_holder"><script type="text/javascript" src="http://widgets.twimg.com/j/2/widget.js"></script>
<script type="text/javascript">/*<![CDATA[*/
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 4,
  interval: 6000,
  width: 'auto',
  height: 280,
  theme: {
    shell: {
      background: '#A90802',
      color: '#FFFFFF'
    },
    tweets: {
      background: '#fff5f5',
      color: '#000000',
      links: '#880000'
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    hashtags: true,
    timestamp: true,
    avatars: false,
    behavior: 'all'
  }
}).render().setUser('<?php echo _twitter_username; ?>').start();
/*]]>*/
</script></div>
	
</div>
<div id="tph_center" class="tph_center">
<?php
	require_once _dir_mods.'/index/slideshow.php';
?>
	<div class="boxtitle">Lintas Jabar</div>
	<?php draw_news(10,'jabar',0,true); ?>
	
	<?php echo getbanner(392,70); ?>
	
	<div class="boxtitle" style="margin-top:5px">Nasional</div>
	<?php draw_news(10,'nasional',0,true); ?>
	
	<?php echo getbanner(392,70); ?>
	
</div>
<div class="clears" style="height:5px">&nbsp;</div>
<script type="text/javascript">/*<![CDATA[*/
getID('tph_scroll_suarakader').style.height = (getID('tph_center').offsetHeight-getID('tph_left').offsetHeight)+'px';
function on_home_load(){ getID('amanatpartai_title').style.marginTop = 5+(getID('tph_center').offsetHeight-getID('tph_right').offsetHeight)+'px'; }
setOnload(on_home_load);
/*]]>*/</script>
<div class="home_bottom">
	<div class="tph_left">
		<div style="padding-left:4px">
			<div class="boxtitle">Agenda</div>
			<?php draw_news(5,'agenda',0,true,true,'Agenda'); ?>
		</div>
	</div>
	<div class="tph_right">
		<div style="padding-right:4px">
			<div class="boxtitle">Profil</div>
			<?php draw_news(3,'profil',0,true,false,'Profil'); ?>
		</div>
	</div>
	<div class="tph_center">
		<div class="boxtitle">Kolom</div>
		<?php draw_news(3,'kolom',0,true,false,'Kolom'); ?>
	</div>
	<div class="clears">&nbsp;</div>
</div>