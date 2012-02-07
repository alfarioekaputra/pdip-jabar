<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
#

if ($_QUERY['cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_berita_cat` WHERE `kode`='{$_QUERY['cat']}'");
    $_SESSION['berita_cat']=$CATTOP['kode'];
    $_SESSION['berita_gbr']=$CATTOP['portrait'];
}
if (!$_SESSION['berita_cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_berita_cat` ORDER BY `pos` ASC, `id` ASC");
    $_SESSION['berita_cat']=$CATTOP['kode'];
    $_SESSION['berita_gbr']=$CATTOP['portrait'];
}
unset($CATTOP);
$KAT=$_SESSION['berita_cat'];
$_SERVER['IS_PORTRAIT']	=$_SESSION['berita_gbr'];
define('_MODULE_TITLE', "Berita");
define('_TABLE_NAME',   _dbp."_berita");
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
  case 'getimg':
        getimg();
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

//---------------------------------- AMBIL GAMBAR -----------------------------------------------
//<!-- AMBIL GAMBAR
function getimg(){
  global $id,$db;
  $did=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'");
  header('content-type:image/jpeg');
 
  $im=new_class("autoimg",base64_decode($did['image']),true);
  if ($im->im){
  	if ($_QUERY['sz']=='small'){
  		if ($_SERVER['IS_PORTRAIT'])
	  		$im->resize(75,100,1);
	  	else
	    	$im->resize(100,75,1);
  	}
  	else{
	  	if ($_SERVER['IS_PORTRAIT'])
	  		$im->resize(176,220,1);
	  	else
	    	$im->resize(220,176,1);
	  }
    echo $im->buf();
  }
  else{
  	echo base64_decode($did['image']);
  }
  exit();
}
// AMBIL GAMBAR -->

//---------------------------------- SIMPAN DATA -----------------------------------------------
//<!-- Save Data
function do_save(){
  global $id,$db;
  
  $_POST['keyword']=txtfriendly($_POST['keyword'],',');
  if (!$_POST['deskripsi']){
  	$_POST['deskripsi'] = htmlspecialchars_decode(str_replace("  ",' ',str_replace("  ",' ',str_replace("\t",' ',str_replace("\r",'',str_replace("\n",' ',strip_tags($_POST['isi'])))))));
  }
  $_POST['deskripsi'] = clean_substr($_POST['deskripsi'],300);
  $changeimg=false;
  $imgtxt='';
  if ($_POST['delimg']){
    $changeimg=true;
    $imgtxt='';
  }
  else if ($_FILES['image']){
    if ($_FILES['image']['error']==UPLOAD_ERR_OK){
      $im=new_class("autoimg",$_FILES['image']['tmp_name']);
      if ($im->im){
      	if ($_SERVER['IS_PORTRAIT'])
      		$im->resize(300,400,1);
      	else
        	$im->resize(400,300,1);
        $imgtxt=$im->buf();
        if ($imgtxt){
          $imgtxt=base64_encode($imgtxt);
          $changeimg=true;
        }
      }
    }
  }
  if (($id=='new')||(!$id)){
    $sql="INSERT INTO `"._TABLE_NAME."` VALUES (".
      "NULL,".
      "'"._KATEGORI."',".
      "'".time()."',".
      "'{$_POST['judul']}',".
      "'{$_POST['sumber']}',".
      "'{$_POST['keyword']}',".
      "'{$_POST['deskripsi']}',".
      "'{$_POST['isi']}',".
      "'{$imgtxt}','{$_POST['judulgbr']}'".
    ",'{$_SESSION['loginid']}')";
  }
  else{
    $sql="UPDATE `"._TABLE_NAME."` SET ".
      (($_POST['waktu'])?("`waktu`='".time()."',"):"").
      "`judul`='{$_POST['judul']}',".
      "`flag`='{$_POST['flag']}',".
      "`judulgbr`='{$_POST['judulgbr']}',".
      "`sumber`='{$_POST['sumber']}',".
      "`keyword`='{$_POST['keyword']}',".
      "`deskripsi`='{$_POST['deskripsi']}',".
      "`isi`='{$_POST['isi']}'".
      ($changeimg?",`image`='{$imgtxt}'":"").
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
    echo $sql;
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
  global $db,$mod,$id,$_QUERY;
  $isOnEdit=(($id!='new')&&$id);

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
    Flag
  </div>
  <div class="val">
    <input type="checkbox" name="flag" value="1" />
  </div>
  <div class="label">
    Sumber berita
  </div>
  <div class="val">
    <input maxlength="128" type="text" name="sumber" size="30" class="inputbox" value="<?php echo ($row['sumber']?htmlspecialchars($row['sumber']):_default_source); ?>" />
  </div>
  <div class="label">
    Tanggal Berita
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
<div class="row">
  <div class="label">
    Judul
  </div>
  <div class="vals">
    <input maxlength="200" type="text" name="judul" class="inputbox" value="<?php echo htmlspecialchars($row['judul']); ?>" style="width:820px" />
  </div>
</div>
<div class="row">
  <div class="label">
    Isi Berita
  </div>
  <div class="vals">
    <textarea name="isi" id="isi" style="width:820px;height:400px"><?php echo trimEditor($row['isi']); ?></textarea>
    <script type="text/javascript">load_editor("isi",true);</script>
    </div>
</div>
<div class="row">
  <div class="label">
    Kata Kunci Terkait
  </div>
  <div class="val">
  	<input maxlength="128" type="text" name="keyword" class="inputbox" style="width:324px" value="<?php echo htmlspecialchars($row['keyword']); ?>" /><br />
    <em>Gunakan Koma untuk memisahkan kata kunci.</em><br />
    <b>Deskripsi Singkat:</b><br />
    <textarea name="deskripsi" class="inputbox" style="width:324px" rows="8"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
    <em>Akan tampil pada listing berita. ( Max: 300 Karakter )</em>
  </div>
  <div class="label">
    <?php if ($row['image']){ echo 'Update Gambar'; } else { echo 'Tambah Gambar'; } ?>
  </div>
  <div class="val">
<?php
	echo '<input type="file" name="image" /><br />';
	echo '<em>Format : PNG atau JPG. Resolution ( ';
  if ($_SERVER['IS_PORTRAIT'])
		echo "300x400 Portrait";
	else
  	echo "400x300 Landscape";
  echo ' )</em>';
	
	if ($row['image']){
    echo '<img style="border:1px solid #aaa" src="'.make_link("/mod/getimg/id/{$row['id']}/").'" ';
    if ($_SERVER['IS_PORTRAIT'])
    	echo 'height="220" width="176" ';
    else
    	echo 'width="220" height="176" ';
    echo 'alt="Preview" />';
    
    echo '<label for="delimg_box" title="Hapus Gambar yang sudah dimasukan..." style="cursor:pointer;display:block;line-height:30px;margin-top:-32px;border-top:1px solid #aaa;margin-left:1px;height:30px;background:rgba(255,255,255,0.8);width:'.($_SERVER['IS_PORTRAIT']?'176':'220').'px;position:absolute;text-shadow:1px 1px 1px #fff">';
    echo '<input type="checkbox" name="delimg" value="1" id="delimg_box" style="top:3px;position:relative" /> Hapus Image';
    echo '</label>';
  }
  else{
  	echo '<div style="border:1px solid #aaa;color:#444;text-align:center;font-size:10px;';
    if ($_SERVER['IS_PORTRAIT'])
    	echo 'height:220px;width:176px;line-height:220px';
    else
    	echo 'width:220px;height:176px;line-height:176px';
    echo '">Tidak ada gambar</div>';
  }
	
?>    
  </div>
</div>
<div class="row">
	<div class="label">&nbsp;</div><div class="val">&nbsp;</div>
  <div class="label">
  	Judul Gambar
  </div>
  <div class="val">
    <input maxlength="200" type="text" name="judulgbr" class="inputbox" value="<?php echo htmlspecialchars($row['judulgbr']); ?>" style="width:324px" />
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
    array('Isi berita',      'isi'),
    array('Kata Kunci',      'keyword'),
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
      <th width="20%"><?php make_sorthead("Dikirim","waktu"); ?></th>
      <th>Gambar</th>
      <th width="60%"><?php make_sorthead("Judul",  "judul"); ?></th>
      <th width="10%"><?php make_sorthead("Sumber", "sumber"); ?></th>
      <th width="10%"><?php make_sorthead("Penginput", "sumber"); ?></th>
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
      if ($row['image']){
		    echo '<img style="border:1px solid #aaa" src="'.make_link("/mod/getimg/id/{$row['id']}/sz/small/").'" ';
		    if ($_SERVER['IS_PORTRAIT'])
		    	echo 'height="100" width="75" ';
		    else
		    	echo 'width="100" height="75" ';
		    echo 'alt="Preview" />';
  		}
  		else{
  			echo "-";
  		}
      echo "</td>";
      echo "<td><h3>".htmlspecialchars($row['judul'])."</h3>".htmlspecialchars($row['deskripsi'])."</td>";
      echo "<td align=\"center\">".htmlspecialchars($row['sumber'])."</td>";
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
      $tools[]=array("new",   "Tambah Berita",  "/mod/edit/id/new/");

    $tools[]  =array("save",  "Simpan",  "getID('form-edit').submit();", true);
  }
  elseif (!$mod)
    $tools[]  =array("new",   "Tambah Berita",  "/mod/edit/id/new/");

  if ($tools)
    make_head(_MODULE_TITLE,$tools);

  if (($mod!='getimg')&&!$hidecat){
    $rows=$db->all("SELECT * FROM `"._dbp."_berita_cat` ORDER BY `pos` ASC, `id` ASC");
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