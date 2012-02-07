<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
#

if ($_QUERY['cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_video_cat` WHERE `kode`='{$_QUERY['cat']}'");
    $_SESSION['video_cat']=$CATTOP['kode'];
}
if (!$_SESSION['video_cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_video_cat` ORDER BY `pos` ASC, `id` ASC");
    $_SESSION['video_cat']=$CATTOP['kode'];
}
unset($CATTOP);
$KAT=$_SESSION['video_cat'];
$_SERVER['IS_PORTRAIT']	=$_SESSION['video_gbr'];
define('_MODULE_TITLE', "Video");
define('_TABLE_NAME',   _dbp."_video");
define('_KATEGORI',     $KAT);

//###
//#
//# Register Variables
//#
//###
$mod        = $_QUERY['mod'];
$id         = $_QUERY['id'];
$start      = (int) $_QUERY['s'];
$list_show  = 20;

//###
//#
//# Page Mode Action
//#
//###
header_tools($mod);
$_TPL->heading(_MODULE_TITLE);
switch ($mod){
  case 'save':
        do_save();
        break;
  case 'delete':
        do_delete();
        break;
  case 'edit':
        do_edit();
        break;
  default:
        do_list();
}

//###
//#
//# Handler Functions
//#
//###
//---------------------------------- HAPUS DATA -----------------------------------------------
//<!-- Delete Data
function do_delete(){
  global $id,$db;
  $sql="DELETE FROM `"._TABLE_NAME."` WHERE `id`='{$id}' AND `cat`='"._KATEGORI."'";
  $db->query($sql);

  // OPTIMIZED:
  $db->query("OPTIMIZE TABLE `"._TABLE_NAME."`");

  header("location:".make_link('/'));
  exit();
}
// Delete Data -->


//---------------------------------- SIMPAN DATA -----------------------------------------------
//<!-- Save Data
function do_save(){
  global $id,$db;
  
  $_POST['keyword']=txtfriendly($_POST['keyword'],',');
  if (!$_POST['deskripsi']){
  	$_POST['deskripsi'] = htmlspecialchars_decode(str_replace("  ",' ',str_replace("  ",' ',str_replace("\t",' ',str_replace("\r",'',str_replace("\n",' ',strip_tags($_POST['isi'])))))));
  }
  $_POST['deskripsi'] = clean_substr($_POST['deskripsi'],300);
  
  // `id`, `waktu`, `youtube_url`, `youtube_id`, `youtube_thumb`, `youtube_durasi`, 
  // `youtube_embed`, `judul`, `keyword`, `deskripsi`, `isi`, `modifier`
  if (($id=='new')||(!$id)){
    $sql="INSERT INTO `"._TABLE_NAME."` VALUES (".
      "NULL,".
      "'"._KATEGORI."',".
      "'".time()."',".
      	
      	"'{$_POST['youtube_url']}',".
      	"'{$_POST['youtube_id']}',".
      	"'{$_POST['youtube_thumb']}',".
      	"'{$_POST['youtube_durasi']}',".
      	"'{$_POST['youtube_embed']}',".
      	
      "'{$_POST['judul']}',".
      "'{$_POST['sumber']}',".
      "'{$_POST['keyword']}',".
      "'{$_POST['deskripsi']}',".
      "'{$_POST['isi']}'".
    ",'{$_SESSION['loginid']}')";
  }
  else{
    $sql="UPDATE `"._TABLE_NAME."` SET ".
      (($_POST['waktu'])?("`waktu`='".time()."',"):"").
      
      	"`youtube_url`='{$_POST['youtube_url']}',".
      	"`youtube_id`='{$_POST['youtube_id']}',".
      	"`youtube_thumb`='{$_POST['youtube_thumb']}',".
      	"`youtube_durasi`='{$_POST['youtube_durasi']}',".
      	"`youtube_embed`='{$_POST['youtube_embed']}',".
      	
      "`judul`='{$_POST['judul']}',".
      "`sumber`='{$_POST['sumber']}',".
      "`keyword`='{$_POST['keyword']}',".
      "`deskripsi`='{$_POST['deskripsi']}',".
      "`isi`='{$_POST['isi']}'".
    ",modifier='{$_SESSION['loginid']}' WHERE `id`='{$id}' AND `cat`='"._KATEGORI."'";
    $redir=make_link("/mod/edit/id/{$id}/msg/saved/");
  }

  if ($db->query($sql)){
    if (!$redir){
      $did=$db->sql("SELECT MAX(`id`) AS `id` FROM `"._TABLE_NAME."` WHERE `cat`='"._KATEGORI."'");
      $redir=make_link("/mod/edit/id/{$did['id']}/msg/saved/");
    }
    header("location:{$redir}");
    exit();
  }
  else if($id=='new'){
    header_tools('edit');
    do_edit();
  }
  else{
    ?>
    <script type="text/javascript">
      alert('Data gagal dimasukkan...');
      history.go(-1);
    </script>
    <?php
    exit();
  }
}
// Save Data -->



//---------------------------------- EDIT DATA -----------------------------------------------

//<!-- Edit
function do_edit(){
  global $db,$mod,$id,$_QUERY,$_TPL;
  $isOnEdit=(($id!='new')&&$id);

	$_TPL->js(_shr.'/youtube.js');

  echo '<fieldset><legend>'.($isOnEdit?'Edit Data':'Tambah Data').'</legend>';

  if ($_QUERY['msg']=='saved')
    echo '<div class="box-success">Simpan Data Berhasil...</div>';

  //-- Get Data:
  if ($isOnEdit)
    $row=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'  AND `cat`='"._KATEGORI."'");
  //-- Get Error Insert
  else if ($mod=='save'){
    $row=$_POST;
    echo '<div class="box-error">Data gagal dimasukkan...</div>';
  }

  echo '<form method="post" enctype="multipart/form-data" id="form-edit" action="'.make_link("/mod/save/id/{$id}/").'">';
//##<!-- Form Design
?>
<div class="row">
  <div class="label">
    Sumber Video
  </div>
  <div class="val">
    <input maxlength="128" type="text" name="sumber" size="30" class="inputbox" value="<?php echo ($row['sumber']?htmlspecialchars($row['sumber']):_default_source); ?>" />
  </div>
  <div class="label">
    Tanggal Video
  </div>
  <div class="val">
    <?php
      if (!$row['waktu']){
        echo "<b>".formatTanggal(time())."</b>";
      }
      else{
        echo "<b>".formatTanggal($row['waktu'])."</b>";
        echo "<br /><input type=\"checkbox\" name=\"waktu\" value=\"1\" /> Update Tanggal";
      }
    ?>
  </div>
</div>
<div class="row">&nbsp;</div>

<div class="header">Data Video di Youtube</div>
<div class="row">
  <div class="label">
    URL Video
  </div>
  <div class="vals">
    <input onchange="youtube_getdata(this);" maxlength="300" onfocus="this.select();" type="text" name="youtube_url" class="inputbox" value="<?php echo htmlspecialchars($row['youtube_url']?$row['youtube_url']:"http://www.youtube.com/watch?v="); ?>" style=";font-size:22px;width:820px;font-weight:bold;background:#ffc" />
    <div style="color:#666;display:none" id="youtube_loading_div"><img src="<?php echo _shr; ?>/ico/loading.gif" style="float:left;padding-right:5px" /> Silahkan Tunggu... Sedang mengakses data dari Youtube...</div>
    <em>Masukan URL Watch Video di Youtube. (Contoh: http://www.youtube.com/watch?v=LCcunMDs9YU )</em>
  </div>
</div>
<div class="row">
  <div class="label">
    &nbsp;
  </div>
  <div class="vals" style="line-height:14px">
  	<fieldset><legend>Data dari Youtube</legend>
  		<div class="row">
  			<div class="label">Video ID</div>
  			<div class="val" style="width:240px"><input id="youtubefield_id" value="<?php echo htmlspecialchars($row['youtube_id']); ?>" name="youtube_id" type="text" readonly="readonly" class="inputbox" style="background:#ddd;width:200px" /></div>
  			<div class="label">Video Thumbnail URL</div>
  			<div class="val" style="width:240px"><input id="youtubefield_thumb" value="<?php echo htmlspecialchars($row['youtube_thumb']); ?>" name="youtube_thumb" type="text" readonly="readonly" class="inputbox" style="background:#ddd;width:200px" /></div>
  		</div>
  		<div class="row">
  			<div class="label">Durasi (Detik)</div>
  			<div class="val" style="width:240px"><input id="youtubefield_duration" value="<?php echo htmlspecialchars($row['youtube_durasi']); ?>" name="youtube_durasi" type="text" readonly="readonly" class="inputbox" style="background:#ddd;width:200px" /></div>
  			<div class="label">Video Embed URL</div>
  			<div class="val" style="width:240px"><input id="youtubefield_swf" value="<?php echo htmlspecialchars($row['youtube_embed']); ?>" name="youtube_embed" type="text" readonly="readonly" class="inputbox" style="background:#ddd;width:200px" /></div>
  		</div>
  		<div class="row" style="text-align:center">
  			<em>Semua data di atas akan otomatis terisi setelah Anda memasukan URL Video Youtube</em>
  		</div>
  	</fieldset>
  </div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
  <div class="label">
    Judul Video
  </div>
  <div class="vals">
    <input maxlength="200" id="youtubefield_title" onfocus="this.select();" type="text" name="judul" class="inputbox" value="<?php echo htmlspecialchars($row['judul']); ?>" style="width:820px" />
  </div>
</div>
<div class="row">
  <div class="label">
    Kata Kunci Terkait
  </div>
  <div class="val">
  	<input maxlength="128" onfocus="this.select();" type="text" name="keyword" class="inputbox" style="width:324px" value="<?php echo htmlspecialchars($row['keyword']); ?>" /><br />
    <em>Gunakan Koma untuk memisahkan kata kunci.</em><br />
    <b>Deskripsi Singkat:</b><br />
    <textarea id="youtubefield_desc" name="deskripsi" onfocus="this.select();" class="inputbox" style="width:324px" rows="8"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
    <em>Akan tampil pada listing video. ( Max: 300 Karakter )</em>
  </div>
  <div class="label">
    Preview Video
  </div>
  <div class="val">
  	<div style="border:1px solid #aaa;padding:2px;" id="youtube_preview"><?php
  		
  		if ($row['youtube_embed']){
  			echo '<script type="text/javascript">drawFlash(false,\''.($row['youtube_embed']).'\',334,210);</script>';
  		}
  		else{
  			echo '<div style="padding-top:80px;padding-bottom:80px;font-size:10px;color:#666;text-align:center">Preview akan otomatis tampil setelah<br />Anda memasukan URL Video Youtube</div>';
  		}
  		
  	?></div>
  </div>
</div>

<div class="header">Artikel Video</div>
<div class="row">
  <div class="label">
    Artikel Video
  </div>
  <div class="vals">
    <textarea name="isi" id="isi" style="width:820px;height:400px"><?php echo trimEditor($row['isi']); ?></textarea>
    <script type="text/javascript">load_editor("isi",true,200);</script>
    </div>
</div>


<?php
if ($row['modifier']){
	echo '<div class="mod">Update terakhir oleh : '.($row['modifier']).'</div>';
}
else{
	echo '<div class="row">&nbsp;</div>';
}

//##--- Form Design -->
  echo '</form></fieldset>';
  header_tools($mod,true);
}
// Edit -->



//---------------------------------- LISTING DATA -----------------------------------------------
//<!-- Listing Data
function do_list(){
  global $db, $start,$list_show;
  list($order_by, $order_as, $filter, $filterby)=init_do_list();

  if (!$order_by||!$order_as){
    //--- NORMAL SORTING
    $order_by='waktu';
    $order_as='DESC';
  }
  $ORDER_SQL="ORDER BY `$order_by` $order_as";

  $WHERE_SQL="WHERE `cat`='"._KATEGORI."' ";
  if ($filter&&$filterby){
    $WHERE_SQL.="AND (`{$filterby}` like '%".addslashes($filter)."%')";
  }
  $filter_cat=array(
    array('Judul',           'judul'),
    array('Deskripsi Singkat','deskripsi'),
    array('Isi Video',      'isi'),
    array('Kata Kunci',      'keyword'),
    array('Youtube ID',      'youtube_id'),
    array('Sumber',          'sumber')
  );

  echo '<fieldset><legend>Listing Data</legend>';
  make_filter($filter_cat);

  $start=($start>0)?$start:0;
  $sql="SELECT * FROM `"._TABLE_NAME."` {$WHERE_SQL}";

  $num=$db->num($sql);
  if (!$num){
    echo '<div class="box-message">Tidak ada data yang dimaksud...</div>';
  }
  else{

    $rows=$db->all($sql." {$ORDER_SQL} LIMIT {$start},{$list_show}");

    //## Header List:
    ?>
    <table class="table-list" cellspacing="1" width="100%">
    <tr>
      <th><div style="width:44px">&nbsp;</div></th>
      <th width="15%"><?php make_sorthead("Dikirim","waktu"); ?></th>
      <th>Thumbnail</th>
      <th width="40%"><?php make_sorthead("Judul",  "judul"); ?></th>
      <th width="15%"><?php make_sorthead("Youtube ID", "youtube_id"); ?></th>
      <th width="15%"><?php make_sorthead("Durasi", "youtube_durasi"); ?></th>
      <th width="15%"><?php make_sorthead("Penginput", "sumber"); ?></th>
    </tr>
    <?php

    //## List Data:
    for ($i=0;$i<count($rows);$i++){
      $row=$rows[$i];
      echo '<tr>';

      //-- Tools
      echo '<td class="tools">';
      echo '<a href="'.make_link("/mod/edit/id/{$row['id']}/").'" class="tool-edit"><span>Edit</span></a>';
      echo '<a href="'.make_link("/mod/delete/id/{$row['id']}/").'" onclick="return msg_delete()" class="tool-delete"><span>Delete</span></a>';
      echo '</td>';

      //-- Data
      echo "<td align=\"center\">".shortTanggal($row['waktu'])."</td>";
      
      echo "<td align=\"center\">";
      if ($row['youtube_thumb']){
		    echo '<img style="border:1px solid #aaa" src="'.htmlspecialchars($row['youtube_thumb']).'" ';
	    	echo 'width="120" height="90" ';
		    echo 'alt="Preview" />';
  		}
  		else{
  			echo "-";
  		}
      echo "</td>";
      echo "<td><h3>".htmlspecialchars($row['judul'])."</h3>".htmlspecialchars($row['deskripsi'])."</td>";
      
      echo "<td align=\"center\">".htmlspecialchars($row['youtube_id'])."</td>";
      echo "<td align=\"center\">".htmlspecialchars($row['youtube_durasi'])." Detik</td>";      
      echo "<td align=\"center\">".htmlspecialchars($row['modifier'])."</td>";
      echo '</tr>';
    }

    //## End Table:
    echo '</table>';

    //-- Navigator
    echo '<table class="box-nav"><tr><td width="100%">';
    $ditampilkan=($num>($start+$list_show))?($start+$list_show):$num;
    echo "Data : <b>".($start+1)." - {$ditampilkan}</b> &bull; Total : <b>{$num}</b> &bull; Halaman : ";
    echo '</td><td><select onchange="location=\''.make_link('/s/').'\'+this.value;">';
    $n=1;
    for ($i=0;$i<$num;$i+=$list_show){
      echo "<option value=\"{$i}\"".(($start==$i)?' selected="selected"':'').">{$n}</option>";
      $n++;
    }
    echo '</select>';
    echo '</td></tr></table>';

  }
  echo '</fieldset>';
	header_tools($mod,true);
  //-- OPTIMIZED
  $db->query("OPTIMIZE TABLE `"._TABLE_NAME."`");
}
// Listing Data -->



//###----------------------------------------------------------------------------------------
//#
//# Create Main Tools
//#
//###
function header_tools($mod,$hidecat=false){
  global $id,$_QUERY,$db;
  if ($mod=='edit'){
    $tools[]  =array("back",  "Kembali", "/");
    if ($id!='new')
      $tools[]=array("new",   "Tambah Video",  "/mod/edit/id/new/");

    $tools[]  =array("save",  "Simpan",  "if (getID('youtubefield_id').value==''){ alert('Silahkan isi Data Video Terlebih Dahulu...'); return false; }else{getID('form-edit').submit();}", true);
  }
  elseif (!$mod)
    $tools[]  =array("new",   "Tambah Video",  "/mod/edit/id/new/");

  if ($tools)
    make_head(_MODULE_TITLE,$tools);

  if (($mod!='getimg')&&!$hidecat){
    $rows=$db->all("SELECT * FROM `"._dbp."_video_cat` ORDER BY `pos` ASC, `id` ASC");
    echo '<div class="head_data"><div class="inner_head_data"><b>Ubah Kategori :&nbsp;</b>';
    for ($i=0;$i<count($rows);$i++){
      $row=$rows[$i];
      if ($row['kode']==_KATEGORI)
      	echo '<span>';
      else
      	echo '<a href="'.make_link("/cat/{$row['kode']}/").'">';
      	
      echo htmlspecialchars($row['nama']);
      
      if ($row['kode']==_KATEGORI)
      	echo '</span> ';
      else
      	echo '</a> ';
      	
      if ($i<count($rows)-1){
      	echo '&bull; ';
      }
    }
    echo '</div></div>';
  }
}


?>