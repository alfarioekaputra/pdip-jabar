<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
# WEBCORE (c)
#

### BANNER
function getbanner($width,$height=0){
  global $db;
  if (count($_SERVER['reged_banner'])>0){
    $WHEREFILTER=" AND (";
    foreach($_SERVER['reged_banner'] as $key=>$val){
      $WHEREFILTER.="`id`!='{$key}' AND ";
    }
    $WHEREFILTER.="1=1)";
  }
  $row=$db->sql("SELECT * FROM `"._p."_banner` WHERE `data`!='' AND `width`='{$width}' AND `height`='{$height}' AND (`credit`='0' OR `credit`>`show`) {$WHEREFILTER} ORDER BY RAND()");
  if ((!$row)&&(_banner_duplicate==true))
    $row=$db->sql("SELECT * FROM `"._p."_banner` WHERE `image`!='' AND `width`='{$width}' AND `height`='{$height}' AND (`credit`='0' OR `credit`>`show`) ORDER BY RAND()");
  $html_b='<div style="overflow:hidden;width:'.$width.'px;height:'.$height.'px;margin:auto">';
  if (!$row){
    $html_b.='<table cellpadding="0" cellspacing="0" style="text-align:center;height:'.$height.'px;width:'.$width.'px;overflow:hidden;background:#fcc"><tr><td><a href="'._net.'/ads/" style="display:block;color:#444">Pasang Iklan Pada<br />Banner Space ('.$width.'x'.$height.') Ini</a></td></tr></table>';
  }
  else if ($row['type']==0){
    //## UPLOADED IMAGE
    $_SERVER['reged_banner'][$row['id']]=true;
    $html_b.='<a href="'.htmlspecialchars($row['url']).'" title="Advertisement: '.htmlspecialchars($row['title']).'" onclick="return popUp(this)">';
    $html_b.='<img src="'._net.'/ads/img/'.($row['id']).'/banner_'.soefriendly($row['title'],'gif').'" alt="'.htmlspecialchars($row['title']).'" style="margin:auto;display:block;border:0" width="'.($row['width']).'" height="'.($row['height']).'" />';
    $html_b.='</a>';
  }
  else if ($row['type']==3){
    //## UPLOADED IMAGE
    $_SERVER['reged_banner'][$row['id']]=true;
    $html_b.='<div id="flashbanners_'.($row['id']).'"></div><script type="text/javascript">drawFlash(getID(\'flashbanners_'.($row['id']).'\'),"'._net.'/ads/img/'.($row['id']).'/banner_'.soefriendly($row['title'],'swf').'","'.($row['width']).'","'.($row['height']).'");</script>';
  }
  else if ($row['type']==1){
    //## EXTERNAL IMAGE
    $_SERVER['reged_banner'][$row['id']]=true;
    $html_b.='<a href="'.htmlspecialchars($row['url']).'" title="Advertisement: '.htmlspecialchars($row['title']).'" onclick="return popUp(this)">';
    $html_b.='<img src="'.($row['data']).'" alt="'.htmlspecialchars($row['title']).'" style="margin:auto;display:block;border:0" width="'.($row['width']).'" height="'.($row['height']).'" />';
    $html_b.='</a>';
  }
  else if ($row['type']==2){
    //## SCRIPTS
    $_SERVER['reged_banner'][$row['id']]=true;
    $html_b.=$row['data'];
  }
  if ($row){
    $row=$db->sql("UPDATE `"._dbp."_banner` SET `show`=`show`+1 WHERE `id`='{$row['id']}'");
  }
  $html_b.='</div>';
  return $html_b;
}

### NEWS
function draw_news_sql($sql,$label='',$moreurl='',$onlyfirst=false,$listonly=false){
	global $db;
	$rows=$db->all($sql);	
	$orient = array(array(80,60),array(60,80));	
	$is_not_first = false;
	if (($rows)&&(count($rows)>0)){
		for ($i=0;$i<count($rows);$i++){
			$row=$rows[$i];
			$ckat=$db->sql("SELECT * FROM `"._dbp."_berita_cat` WHERE `kode`='{$row['cat']}'");
			
			if (($onlyfirst&&($i>0))||($listonly)){
				if (!$is_not_first){
					$is_not_first = true;
					echo '<ul class="newslist_small">';
				}
				echo '<li><a href="'._net.'/berita/view/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']);
				echo '<span>'.getRanahWaktu($row['waktu']).'</span>';
				echo '</a></li>';
			}
			else{
				echo '<div class="newslist">';
				if ($row['img']) echo '<div class="newslist_img"><img src="'._net.'/berita/thmb/'.soefriendly($row['judul'],id2base($row['id']).".jpg",30).'" /></div>';
				echo '<h2><a href="'._net.'/berita/view/'.soefriendly($row['judul'],id2base($row['id']).".html",30).'">'.htmlspecialchars($row['judul']).'</a></h2>';
				echo '<p><span>'.getRanahWaktu($row['waktu']).'</span> &middot; '.htmlspecialchars($row['deskripsi']).'</p>';
				echo '<div class="clears">&nbsp;</div></div>';
			}		
		}
		if ($is_not_first)
			echo '</ul>';
		if ($label&&$moreurl)
			echo '<div class="morenews"><a href="'.htmlspecialchars($moreurl).'">'.htmlspecialchars($label).' Lainnya</a></div>';
	}
	else{
		echo '<div class="newsnodata">Tidak terdapat Berita</div>';
	}
}

function draw_news($limit=5,$cat='',$start=0,$onlyfirst=false,$listonly=false,$label='Berita'){
	$where_sql='';
	if ($cat) $where_sql=" AND `cat`='{$cat}' ";
	draw_news_sql(
		"SELECT `id`,`waktu`,`cat`,`judul`,`sumber`,`deskripsi`,IF(`image`='',0,1) AS `img` ".
	  	"FROM `"._p."_berita` WHERE 1=1 {$where_sql} ORDER BY `waktu` DESC LIMIT {$start},{$limit}",
	  $label,
	  _net.'/berita/'.($cat?"cat/{$cat}/":''),
	  $onlyfirst, $listonly
	);
}
?>