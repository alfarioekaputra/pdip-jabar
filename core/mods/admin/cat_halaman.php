<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
#

define('_MODULE_TITLE', "Kategori Halaman Internal");
define('_TABLE_NAME',   _dbp."_statis_cat");

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
  case 'up':
        do_pos(true);
        break;
  case 'down':
        do_pos(false);
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
  $sql="DELETE FROM `"._TABLE_NAME."` WHERE `id`='{$id}'";
  $db->query($sql);
  
  // OPTIMIZED:
  $db->query("OPTIMIZE TABLE `"._TABLE_NAME."`");
  
  header("location:".make_link('/'));
  exit();
}
// Delete Data -->

//###
//#
//# Handler Functions
//#
//###
//---------------------------------- PINDAH POSISI -----------------------------------------------
//<!-- Pindah Posisi
function do_pos($up){
  global $id,$db;
  $sql='';
  $datapos=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'");
  if (!$up){
    $sql=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `pos`>'{$datapos['pos']}' ".
                  "ORDER BY `pos` ASC");
  }
  else{
    $sql=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `pos`<'{$datapos['pos']}' ".
                  "ORDER BY `pos` DESC");
  }
  if ($sql){
    $db->query("UPDATE `"._TABLE_NAME."` SET `pos`='{$datapos['pos']}' WHERE `id`='{$sql['id']}'");
    $db->query("UPDATE `"._TABLE_NAME."` SET `pos`='{$sql['pos']}' WHERE `id`='{$datapos['id']}'");
  }
  
  $redir=make_link("/");
  header("location:{$redir}");
  exit();
}
// Pindah Posisi -->

//---------------------------------- SIMPAN DATA -----------------------------------------------
//<!-- Save Data
function do_save(){
  global $id,$db;
  
  $_POST['kode']=txtfriendly($_POST['kode']);
  if (($id=='new')||(!$id)){
     // `id`, `kode`, `pos`, `show_list`, `nama`, `modifier`
    $row=$db->sql("SELECT * FROM `"._TABLE_NAME."` ORDER BY `pos` DESC");
    $row=$row['pos']+1;
    $sql="INSERT INTO `"._TABLE_NAME."` VALUES (".
      "NULL,".
      "'{$_POST['kode']}',".
      "'{$_POST['nama']}','{$row}'".
    ",'{$_SESSION['loginid']}')";
  }
  else{
    $sql="UPDATE `"._TABLE_NAME."` SET ".
      "`kode`='{$_POST['kode']}',".
      "`nama`='{$_POST['nama']}'".
    ",modifier='{$_SESSION['loginid']}' WHERE `id`='{$id}'";
    $redir=make_link("/mod/edit/id/{$id}/msg/saved/");
  }
  
  if ($db->query($sql)){
    if (!$redir){
      $did=$db->sql("SELECT MAX(`id`) AS `id` FROM `"._TABLE_NAME."`");
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
    $row=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'");
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
    Kode
  </div>
  <div class="vals">
    <input maxlength="32" type="text" name="kode" class="inputbox" value="<?php echo htmlspecialchars($row['kode']); ?>" size="32" />
    <em>Kode ini harus berbeda dengan kode dari kategori lain</em>
  </div>
</div>
<div class="row">
  <div class="label">
    Nama Kategori
  </div>
  <div class="vals">
    <input maxlength="200" type="text" name="nama" class="inputbox" value="<?php echo htmlspecialchars($row['nama']); ?>" size="80" />
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
  if ($_QUERY['show']!='print')
  do_list();
}
// Edit -->



//---------------------------------- LISTING DATA -----------------------------------------------
//<!-- Listing Data
function do_list(){
  global $db, $start,$list_show;
  list($order_by, $order_as, $filter, $filterby)=init_do_list();
  
  //--- NORMAL SORTING
  $ORDER_SQL="ORDER BY `pos` ASC, `id` ASC";

  $WHERE_SQL="WHERE 1=1 ";
  if ($filter&&$filterby){
    $WHERE_SQL.="AND (`{$filterby}` like '%".addslashes($filter)."%')";
  }
  $filter_cat=array(
    array('Nama Kategori',           'nama'),
    array('Kode Kategori',           'kode')
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
    
    $max=$db->sql("SELECT `pos` FROM `"._TABLE_NAME."` ORDER BY `pos` DESC");
    $min=$db->sql("SELECT `pos` FROM `"._TABLE_NAME."` ORDER BY `pos` ASC");

    $rows=$db->all($sql." {$ORDER_SQL} LIMIT {$start},{$list_show}");
    
    //## Header List:
    ?>
    <table class="table-list" cellspacing="1" width="100%">
    <tr>
      <th><div style="width:88px">&nbsp;</div></th>
      <th width="30%">Kode</th>
      <th width="70%">Nama Kategori</th>
    </tr>
    <?php
    
    //## List Data:
    for ($i=0;$i<count($rows);$i++){
      $row=$rows[$i];
      echo '<tr>';
      
      //-- Tools
      echo '<td class="tools">';
      echo '<a href="'.make_link("/mod/edit/id/{$row['id']}/").'" class="tool-edit"><span>Edit</span></a>';
      echo '<a href="'.make_link("/mod/delete/id/{$row['id']}/").'" onclick="return msg_delete()" class="tool-delete"><span>Hapus</span></a>';
      
      
      if ($row['pos']!=$min['pos'])
        echo '<a href="'.make_link("/mod/up/id/{$row['id']}/").'" class="tool-up"><span>Pindah Ke Atas</span></a>';
      else
        echo '<a href="#" style="visibility:hidden"></a>';
      
      if ($row['pos']!=$max['pos'])
      echo '<a href="'.make_link("/mod/down/id/{$row['id']}/").'" class="tool-down"><span>Pindah Ke Bawah</span></a>';
      echo '</td>';
      
      //-- Data
      echo "<td align=\"center\">".htmlspecialchars($row['kode'])."</td>";
      echo "<td>".htmlspecialchars($row['nama'])."</td>";
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
  
  //-- OPTIMIZED
  $db->query("OPTIMIZE TABLE `"._TABLE_NAME."`");
}
// Listing Data -->



//###----------------------------------------------------------------------------------------
//#
//# Create Main Tools
//#
//###
function header_tools($mod){
  global $id,$_QUERY;
  if ($mod=='edit'){
    $tools[]  =array("back",  "Kembali", "/");
    if ($id!='new')
      $tools[]=array("new",   "Tambah Kategori",  "/mod/edit/id/new/");

    $tools[]  =array("save",  "Simpan",  "getID('form-edit').submit();", true);
  }
  elseif (!$mod)
    $tools[]  =array("new",   "Tambah Kategori",  "/mod/edit/id/new/");
    
  if ($tools)
    make_head(_MODULE_TITLE,$tools);
}


?>