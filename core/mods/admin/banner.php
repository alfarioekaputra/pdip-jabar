<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
#

define('_MODULE_TITLE', "Pengaturan Banner");
define('_TABLE_NAME',   _p."_banner");

$_SERVER['banner_unit']=array(
			array(234,60,'Half Banner','Kontent berita dan foto pada bagian Kiri'),
      array(392,70,'Full Banner','Halaman Home pada bagian Tengah'),
      array(728,90,'Leaderboard','Kontent berita dan foto pada bagian Utama'),
      array(300,250,'Medium Rectangle','Halaman Home bagian Kanan Atas')
);
$_SERVER['banner_type']=array(
	array('Upload Gambar','Gambar di upload ke server. <b>Upload Image atau Upload Flash</b> pada Data Banner harus diisi.'),
	array('Gambar External','Gambar berada pada server lain. <b>External Image</b> pada Data Banner harus diisi.'),
	array('Script','Banner berupa script HTML dan Javascript seperti Google AdSense, dll. <b>Script</b> pada Data Banner harus diisi.'),
	array('Upload Flash','Upload Flash ke server. <b>Upload Image atau Upload Flash</b> pada Data Banner harus diisi.')
);
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
  case 'getimg':
        getimg();
        exit();
        break;
  default:
        do_edit();
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
//---------------------------------- AMBIL GAMBAR -----------------------------------------------

//<!-- AMBIL GAMBAR
function getimg(){
  global $id,$db;
  $did=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'");

  if ($did['type']==3)
    header("content-type:application/x-shockwave-flash");
  else
    header("content-type:image/jpeg");

  echo base64_decode($did['data']);
  exit();
}
// AMBIL GAMBAR -->

//---------------------------------- SIMPAN DATA -----------------------------------------------
//<!-- Save Data
function do_save(){
  global $id,$db;

  $width =$_SERVER['banner_unit'][$_POST['unit']][0];
  $height=$_SERVER['banner_unit'][$_POST['unit']][1];
  $data='';
  if (($_POST['type']==0)||($_POST['type']==3)){
      $changedata=false;
      $data='';
      if ($_POST['delimg']){
        $changedata=true;
        $$data='';
      }
      else if ($_FILES['image']){
        if ($_FILES['image']['error']==UPLOAD_ERR_OK){
          $fp=fopen($_FILES['image']['tmp_name'],"r+");
          if ($fp){
            $data=fread($fp,filesize($_FILES['image']['tmp_name']));
            if ($data){
              $data=base64_encode($data);
              $changedata=true;
            }
            fclose($fp);
          }
        }
      }
    }
    else if ($_POST['type']==1){
        $changedata=true;
        $data=$_POST['imglocation'];
    }
    else if ($_POST['type']==2){
        $changedata=true;
        $data=$_POST['bannerscript'];
    }

  if (($id=='new')||(!$id)){
    $sql="INSERT INTO `"._TABLE_NAME."` VALUES (".
      "NULL,".
      "'{$width}',".
      "'{$height}',".
      "'{$_POST['type']}','{$_POST['title']}',".
      "'{$_POST['credit']}',".
      ($_POST['showupdate']?"'{$_POST['show']}',":"'0',").
      "'{$data}',".
      "'{$_POST['url']}',".
      "'{$_POST['ket']}'".
      ",'{$_SESSION['loginid']}')";
  }
  else{
    $sql="UPDATE `"._TABLE_NAME."` SET ".
      "`width`  ='{$width}',".
      "`height` ='{$height}',".
      "`type`       ='{$_POST['type']}',".
      "`title`  ='{$_POST['title']}',".
      "`credit` ='{$_POST['credit']}',".
      ($_POST['showupdate']?"`show`='{$_POST['show']}',":"").
      ($changedata?("`data`='{$data}',"):"").
      "`url`='{$_POST['url']}',".

      "`ket`='{$_POST['ket']}'".
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
    $row=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'");
  //-- Get Error Insert
  else if ($mod=='save'){
    $row=$_POST;
    echo '<div class="box-error">Data gagal dimasukkan...</div>';
  }

  echo '<form method="post" enctype="multipart/form-data" id="form-edit" action="'.make_link("/mod/save/id/{$id}/").'">';
//##<!-- Form Design
    if ($row){
        if (($row['type']==0)||($row['type']==3)){
            $row['image']=$row['data'];
        }
        else if ($row['type']==1){
            $row['imglocation']=$row['data'];
        }
        else if ($row['type']==2){
            $row['bannerscript']=$row['data'];
        }
    }

?>
<div class="row">
  <div class="label">
    Ditampilkan
  </div>
  <div class="val">
    <input type="text" name="show" style="background:#fffdee" class="inputbox" onchange="getID('show_update').checked=true;" value="<?php echo htmlspecialchars($row['show']?$row['show']:"0"); ?>" size="12" /> <input type="checkbox" value="1" name="showupdate" id="show_update" /> Update<br />
    <em>Jumlah berapa kali banner ini telah ditampilkan</em>
  </div>

  <div class="label">
    Jumlah Kredit
  </div>
  <div class="val">
    <input type="text" name="credit" style="background:#fffdee" class="inputbox" value="<?php echo htmlspecialchars($row['credit']?$row['credit']:"0"); ?>" size="12" /><br />
    <em>0: Tidak terbatas, -1 Tidak Aktif</em>
  </div>
</div>
<div class="row">
  <div class="label">
    Judul Banner
  </div>
  <div class="vals">
    <input maxlength="200" type="text" name="title" class="inputbox" value="<?php echo htmlspecialchars($row['title']); ?>" size="80" style="width:820px" /><br />
    <em>Hanya sebagai tanda untuk pengaturan</em>
  </div>
</div>
<div class="row">
  <div class="label">
    Target URL
  </div>
  <div class="vals">
    <input maxlength="300" type="text" name="url" class="inputbox" value="<?php echo htmlspecialchars($row['url']?$row['url']:"http://"); ?>" size="80" style="width:820px" /><br />
    <em>Alamat web tujuan ketika user melakukan klik pada banner ( tambahkan http:// )</em>
  </div>
</div>
<div class="row">
  <div class="label">
    Tipe Banner
  </div>
  <div class="vals" style="line-height:14px">
  	<table class="table-list" cellspacing="1" width="100%" cellpadding="2">
  		<tr>
  			<th>Pilih</th>
  			<th width="40%">Tipe Banner</th>
  			<th width="60%">Keterangan</th>
  		</tr>
<?php
		$typeSel=$row['type']?$row['type']:0;
    for ($i=0;$i<count($_SERVER['banner_type']);$i++){
    	echo '<tr>';
        echo '<td align="center"><input type="radio" name="type" ';
        if ($i==$typeSel){
            echo 'checked="checked" ';
        }
        echo 'value="'.$i.'" /></td>';
        echo '<td>'.htmlspecialchars($_SERVER['banner_type'][$i][0]).'</td>';
        echo '<td>'.($_SERVER['banner_type'][$i][1]).'</td>';
      echo '</tr>';
    }
?>
	</table>
  </div>
</div>
<div class="row">
  <div class="label">
    Unit
  </div>
  <div class="vals" style="line-height:14px">
  	<table class="table-list" cellspacing="1" width="100%" cellpadding="2">
  		<tr>
  			<th>Pilih</th>
  			<th width="20%">Ukuran</th>
  			<th width="20%">Nama Ukuran</th>
  			<th width="60%">Letak Banner</th>
  		</tr>
<?php
    for ($i=0;$i<count($_SERVER['banner_unit']);$i++){
    	echo '<tr>';
        echo '<td align="center"><input type="radio" name="unit" ';
        if (($_SERVER['banner_unit'][$i][0]==$row['width'])&&($_SERVER['banner_unit'][$i][1]==$row['height'])){
            echo 'checked="checked" ';
        }
        echo 'value="'.$i.'" /></td>';
        echo '<td align="center">'.htmlspecialchars($_SERVER['banner_unit'][$i][0]).'x'.htmlspecialchars($_SERVER['banner_unit'][$i][1]).'px</td>';
        echo '<td align="center">'.htmlspecialchars($_SERVER['banner_unit'][$i][2]).'</td>';
        echo '<td>'.htmlspecialchars($_SERVER['banner_unit'][$i][3]).'</td>';
      echo '</tr>';
    }
?>
	</table>
  </div>
</div>
<div class="row">
  <div class="label">
    Data Banner
  </div>
  <div class="vals" style="line-height:14px">
    <table class="table-list" cellspacing="1" cellpadding="2" width="100%">
        <tr>
        	<th width="30%">Upload Image atau Upload Flash</th>
        	<td width="70%"><?php
              if ($row['image']){
                $w=$_SERVER['banner_unit'][$row['unit']][0];
                    $h=$_SERVER['banner_unit'][$row['unit']][1];
                if ($w>500) {$w=$w/2; $h=$h/2; }

                if ($row['type']==3){
                    $imurl=make_link("/mod/getimg/id/{$row['id']}/".md5($row['image']));
?>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" name="base" height="<?php echo $h; ?>" width="<?php echo $w; ?>">
<param name="movie" value="<?php echo $imurl; ?>" /><param name="quality" value="high" /><param name="menu" value="false" /><param name="bgcolor" value="#000000" />
<embed src="<?php echo $imurl; ?>" quality="high" menu="false" type="application/x-shockwave-flash" height="<?php echo $h; ?>" width="<?php echo $w; ?>" />
</object><br />Update :
<?php
                }
                else
                    echo '<img style="cursor:pointer;border:1px solid #000" onclick="window.open(this.src,\'\',\'\');" src="'.make_link("/mod/getimg/id/{$row['id']}/".md5($row['image'])).'" width="'.($w).'" height="'.($h).'" alt="Gambar Banner" /><br />Update : ';
              }
            ?><input type="file" name="image" />
            <?php
                if ($row['image']) echo '<input type="checkbox" name="delimg" value="1" /> Delete';
            ?><br />
            <em>Format : SWF, PNG, JPG atau GIF. Resolution must be same with selected unit.</em>
        </td>
       </tr>
        <tr>
        	<th>External Image</th>
            <td>
            <input maxlength="300" style="width:600px" type="text" name="imglocation" class="inputbox" value="<?php echo htmlspecialchars($row['imglocation']?$row['imglocation']:"http://"); ?>" size="60" />
        </td></tr>
        <tr><th>Script</th>
            <td>
            <textarea style="width:600px" name="bannerscript" class="inputbox" style="width:440px;height:100px"><?php echo htmlspecialchars($row['bannerscript']); ?></textarea>
        </td></tr>
    </table>
  </div>
</div>
<div class="row">
  <div class="label">
    Catatan
  </div>
  <div class="vals">
    <textarea name="ket" class="inputbox" style="width:820px;height:60px"><?php echo htmlspecialchars($row['ket']); ?></textarea>
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
  do_list();
  header_tools($mod);
}
// Edit -->



//---------------------------------- LISTING DATA -----------------------------------------------
//<!-- Listing Data
function do_list(){
  global $db, $start,$list_show;
  list($order_by, $order_as, $filter, $filterby)=init_do_list();

  if (!$order_by||!$order_as){
    //--- NORMAL SORTING
    $order_by='title';
    $order_as='ASC';
  }
  $ORDER_SQL="ORDER BY `$order_by` $order_as";

  if ($filter&&$filterby){
    $WHERE_SQL="WHERE (`{$filterby}` like '%".addslashes($filter)."%')";
  }
  $filter_cat=array(
    array('Judul Banner',           'title')
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
      <th width="50%"><?php make_sorthead("Judul","title"); ?></th>
      <th width="20%"><?php make_sorthead("Tipe", "type"  );?></th>
      <th width="15%"><?php make_sorthead("Ukuran", "unit"  );?></th>
      <th width="15%"><?php make_sorthead("Ditampilkan", "show"  );?></th>
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
      echo "<td>".htmlspecialchars($row['title'])."</td>";
      echo "<td align=\"center\">".htmlspecialchars($_SERVER['banner_type'][$row['type']][0])."</td>";
      echo "<td align=\"center\">{$row['width']}x{$row['height']}</td>";
      echo "<td align=\"right\">{$row['show']}</td>";

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
  if ($mod!='getimg'){
      if ($_QUERY['id']){
        $tools[]=array("new",   "Tambah Banner",  "/mod/edit/id/new/");
      }
      $tools[]  =array("save",  "Simpan",  "getID('form-edit').submit();", true);

      if ($tools)
        make_head(_MODULE_TITLE,$tools);
    }
}


?>
