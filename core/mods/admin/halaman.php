<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
#

if ($_QUERY['cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_statis_cat` WHERE `kode`='{$_QUERY['cat']}'");
    $_SESSION['statis_cat']=$CATTOP['kode'];
}
if (!$_SESSION['statis_cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_statis_cat` ORDER BY `pos` ASC, `id` ASC");
    $_SESSION['statis_cat']=$CATTOP['kode'];
}
unset($CATTOP);
$KAT=$_SESSION['statis_cat'];
define('_MODULE_TITLE', "Halaman Internal");
define('_TABLE_NAME',   _dbp."_statis");
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
$_TPL->heading(_MODULE_TITLE);
switch ($mod){
  case 'save':
        do_save();
        break;
  case 'delete':
        do_delete();
        break;
  case 'edit':
  			header_tools($mod);
        do_edit();
        break;
  case 'up':
        do_pos(true);
        break;
  case 'down':
        do_pos(false);
        break;
  default:
  			header_tools($mod);
        do_list();
}

function do_pos($up){
  global $id,$db;
  $sql='';
  $datapos=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `cat`='"._KATEGORI."' AND `id`='{$id}'");
  if (!$up){
    $sql=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `pos`>'{$datapos['pos']}' AND `cat`='"._KATEGORI."'".
                  "ORDER BY `pos` ASC");
  }
  else{
    $sql=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `pos`<'{$datapos['pos']}' AND `cat`='"._KATEGORI."'".
                  "ORDER BY `pos` DESC");
  }
  if ($sql){
    $db->query("UPDATE `"._TABLE_NAME."` SET `pos`='{$datapos['pos']}' WHERE `id`='{$sql['id']}' AND `cat`='"._KATEGORI."'");
    $db->query("UPDATE `"._TABLE_NAME."` SET `pos`='{$sql['pos']}' WHERE `id`='{$datapos['id']}' AND `cat`='"._KATEGORI."'");
  }
  $redir=make_link("/");
  header("location:{$redir}");
  exit();
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
  
  $_POST['kode']=txtfriendly($_POST['kode']);
  if ($_POST['kode']){
	  if (($id=='new')||(!$id)){
	  	$row=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `cat`='"._KATEGORI."' ORDER BY `pos` DESC");
	    $row=$row['pos']+1;
	    
	    $sql="INSERT INTO `"._TABLE_NAME."` VALUES (".
	      "NULL,".
	      "'"._KATEGORI."','{$_POST['kode']}','{$row}',".
	      "'{$_POST['judul']}',".
	      "'{$_POST['isi']}'".
	    ",'{$_SESSION['loginid']}')";
	  }
	  else{
	    $sql="UPDATE `"._TABLE_NAME."` SET ".
	      "`judul`='{$_POST['judul']}',".
	      "`kode`='{$_POST['kode']}',".
	      "`isi`='{$_POST['isi']}'".
	    ",modifier='{$_SESSION['loginid']}' WHERE `id`='{$id}' AND `cat`='"._KATEGORI."'";
	    $redir=make_link("/mod/edit/id/{$id}/msg/saved/");
	  }
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
      alert('ERROR!\nData gagal dimasukkan...\n\n  Tips: Kode harus di isi dengan kode yang unik dan belum\n        digunakan pada halaman lain.');
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
    echo '<div class="box-error"><b>ERROR: Data gagal dimasukkan...</b><br /><br />';
    echo '<b>Tips:</b> <u>Kode</u> harus di isi dengan kode yang unik dan belum digunakan pada halaman lain.</div>';
  }

  echo '<form method="post" enctype="multipart/form-data" id="form-edit" action="'.make_link("/mod/save/id/{$id}/").'">';
//##<!-- Form Design
?>
<div class="row">
  <div class="label">
    Kode Halaman
  </div>
  <div class="vals">
    <input size="32" maxlength="32" type="text" name="kode" class="inputbox" value="<?php echo htmlspecialchars($row['kode']); ?>" />
    <em>Kode ini akan digunakan pada URL, harus berbeda dengan kode halaman lain</em>
  </div>
</div>
<div class="row">
  <div class="label">
    Judul Halaman
  </div>
  <div class="vals">
    <input maxlength="200" type="text" name="judul" class="inputbox" value="<?php echo htmlspecialchars($row['judul']); ?>" style="width:820px" />
  </div>
</div>
<div class="row">
  <div class="label">
    Isi Halaman
  </div>
  <div class="vals">
    <textarea name="isi" id="isi" style="width:820px;height:400px"><?php echo trimEditor($row['isi']); ?></textarea>
    <script type="text/javascript">load_editor("isi",true,450);</script>
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
  $ORDER_SQL="ORDER BY `pos` ASC, `id` ASC";

  $WHERE_SQL="WHERE `cat`='"._KATEGORI."'";
  if ($filter&&$filterby){
    $WHERE_SQL.="AND (`{$filterby}` like '%".addslashes($filter)."%')";
  }
  $filter_cat=array(
    array('Judul Halaman',    'judul'),
    array('Kode Halaman',      'kode'),
    array('Isi Halaman',      'isi')
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

  	$max=$db->sql("SELECT `pos` FROM `"._TABLE_NAME."` WHERE `cat`='"._KATEGORI."' ORDER BY `pos` DESC");
    $min=$db->sql("SELECT `pos` FROM `"._TABLE_NAME."` WHERE `cat`='"._KATEGORI."' ORDER BY `pos` ASC");

    $rows=$db->all($sql." {$ORDER_SQL} LIMIT {$start},{$list_show}");

    //## Header List:
    ?>
    <table class="table-list" cellspacing="1" width="100%">
    <tr>
      <th><div style="width:88px">&nbsp;</div></th>
      <th width="30%">Kode</th>
      <th width="50%">Judul Halaman</th>
      <th width="20%">Penginput</th>
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

      if ($row['pos']!=$min['pos'])
        echo '<a href="'.make_link("/mod/up/id/{$row['id']}/").'" class="tool-up"><span>Pindah Ke Atas</span></a>';
      else
        echo '<a href="#" style="visibility:hidden"></a>';
      if ($row['pos']!=$max['pos'])
      	echo '<a href="'.make_link("/mod/down/id/{$row['id']}/").'" class="tool-down"><span>Pindah Ke Bawah</span></a>';
      
      echo '</td>';

      //-- Data
      echo "<td align=\"center\">".htmlspecialchars($row['kode'])."</td>";
      echo "<td>".htmlspecialchars($row['judul'])."</td>";
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
      $tools[]=array("new",   "Tambah Halaman",  "/mod/edit/id/new/");

    $tools[]  =array("save",  "Simpan",  "getID('form-edit').submit();", true);
  }
  elseif (!$mod)
    $tools[]  =array("new",   "Tambah Halaman",  "/mod/edit/id/new/");

  if ($tools)
    make_head(_MODULE_TITLE,$tools);

  if (!$hidecat){
    $rows=$db->all("SELECT * FROM `"._dbp."_statis_cat` ORDER BY `pos` ASC, `id` ASC");
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