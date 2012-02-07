<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
#

if ($_QUERY['cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_foto_cat` WHERE `kode`='{$_QUERY['cat']}'");
    $_SESSION['foto_cat']=$CATTOP['kode'];
}
if (!$_SESSION['foto_cat']){
    $CATTOP=$db->sql("SELECT * FROM `"._dbp."_foto_cat` ORDER BY `pos` ASC, `id` ASC");
    $_SESSION['foto_cat']=$CATTOP['kode'];
}
unset($CATTOP);
$KAT=$_SESSION['foto_cat'];
define('_MODULE_TITLE', "Album Foto");
define('_TABLE_NAME',   _dbp."_foto_album");
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
$_SERVER['show_header']=($mod!='fotoaction')&&($mod!='getimg');
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
  case 'fotoaction':
        fotoaction();
        break;
  default:
        do_list();
}

//###
//#
//# Handler Functions
//#
//###
//---------------------------------- FOTO ACTION -----------------------------------------------
function fotoaction(){
	global $id,$db,$_QUERY;
	
	$albumdata=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}'  AND `cat`='"._KATEGORI."'");
	if (!$albumdata){
		echo '<script type="text/javascript">alert(\'Error\\nInternal Server Error...\');</script>';
		exit();
	}
	
	if (($_QUERY['fmod']=='up')||($_QUERY['fmod']=='dn')){
		$up  = ($_QUERY['fmod']=='up');
		$fid = (int) $_QUERY['fid'];
		$sql='';
	  $datapos=$db->sql("SELECT `id`,`pos` FROM `"._dbp."_foto` WHERE `id`='{$fid}' AND `contentid`='{$id}'");
	  if (!$up){
	    $sql=$db->sql("SELECT * FROM `"._dbp."_foto` WHERE `pos`>'{$datapos['pos']}' AND `contentid`='{$id}' ".
	                  "ORDER BY `pos` ASC");
	  }
	  else{
	    $sql=$db->sql("SELECT * FROM `"._dbp."_foto` WHERE `pos`<'{$datapos['pos']}' AND `contentid`='{$id}' ".
	                  "ORDER BY `pos` DESC");
	  }
	  if ($sql){
	    $db->query("UPDATE `"._dbp."_foto` SET `pos`='{$datapos['pos']}' WHERE `id`='{$sql['id']}' AND `contentid`='{$id}'");
	    $db->query("UPDATE `"._dbp."_foto` SET `pos`='{$sql['pos']}' WHERE `id`='{$datapos['id']}' AND `contentid`='{$id}'");
	  }
	  echo '<script type="text/javascript">parent.save_foto_uploaded(true);</script>';
	  exit();
	}
	elseif ($_QUERY['fmod']=='setcover'){
		$fid = (int) $_QUERY['fid'];
		$db->query("UPDATE `"._dbp."_foto` SET `cover`='0' WHERE `contentid`='{$id}'");
		$db->query("UPDATE `"._dbp."_foto` SET `cover`='1' WHERE `id`='{$fid}' AND `contentid`='{$id}'");
		echo '<script type="text/javascript">parent.save_foto_uploaded(true);</script>';
	  exit();
	}
	elseif ($_QUERY['fmod']=='delimg'){
		$fid = (int) $_QUERY['fid'];
		$db->query("DELETE FROM `"._dbp."_foto` WHERE `id`='{$fid}' AND `contentid`='{$id}'");
		$db->query("OPTIMIZE TABLE `"._dbp."_foto`");
		
		if (!$db->sql("SELECT `id` FROM `"._dbp."_foto` WHERE `cover`='1' AND `contentid`='{$id}'")){
			$first=$db->sql("SELECT `id` FROM `"._dbp."_foto` WHERE `contentid`='{$id}' ORDER BY `pos` ASC");
			if ($first){
				$db->query("UPDATE `"._dbp."_foto` SET `cover`='1' WHERE `id`='{$first['id']}' AND `contentid`='{$id}'");
			}
		}
		
		echo '<script type="text/javascript">parent.save_foto_uploaded(true);</script>';
	  exit();
	}
	elseif ($_QUERY['fmod']=='getdata'){
		$datafoto=$db->all("SELECT `id`, `deskripsi`, `orientation`, `pos`, `cover`, `modifier` FROM `"._dbp."_foto` WHERE `contentid`='{$id}' ORDER BY `pos` ASC");
		
		$max=$db->sql("SELECT `pos` FROM `"._dbp."_foto` ORDER BY `pos` DESC");
    $min=$db->sql("SELECT `pos` FROM `"._dbp."_foto` ORDER BY `pos` ASC");
    
    unset($ret);
    $ret['posmax']=(int) $max['pos'];
    $ret['posmin']=(int) $min['pos'];
    $ret['d']=$datafoto;
		echo jsencode($ret);
		exit();
	}
	elseif ($_QUERY['fmod']=='upload'){
		$IS_ERROR  = false;
		$ORIENT_SIZE = array(array(640,480),array(480,640),array(640,640));
		$ORIENTASI = 2;
		$IMG_TXT	 = '';
		if (($_FILES['newimg_file']['tmp_name'])&&($_FILES['newimg_file']['error']==UPLOAD_ERR_OK)){
      $im=new_class("autoimg",$_FILES['newimg_file']['tmp_name']);
      if ($im->im){
      	if (($im->w)>($im->h))
      		$ORIENTASI=0;
      	elseif (($im->w)<($im->h))
      		$ORIENTASI=1;
      	$im->resize($ORIENT_SIZE[$ORIENTASI][0],$ORIENT_SIZE[$ORIENTASI][1],1);
        $IMG_TXT=base64_encode($im->buf());
      }
      else{
      	$IS_ERROR=true;
      }
    }
    else{
    	$IS_ERROR=true;
    }
    if (!$_POST['newimg_desc']) $IS_ERROR=true;
    if ($IS_ERROR){
    	if (!$_POST['newimg_desc']){
    		echo '<script type="text/javascript">parent.save_foto_uploaded(false,\'Deskripsi Harus Diisi...\');</script>';
    	}
    	else{
    		echo '<script type="text/javascript">parent.save_foto_uploaded(false,\'Gambar Harus Diisi dengan format yang tepat (PNG atau JPG)...\');</script>';
    	}
    	exit();
    }
    else{
			$position=$db->sql("SELECT `pos` FROM `"._dbp."_foto` WHERE `contentid`='{$id}' ORDER BY `pos` DESC");
			$iscover = ((((int) $position['pos'])==0)?'1':'0');
			$position=((int) $position['pos'])+1;
			$_POST['newimg_desc'] = clean_substr($_POST['newimg_desc'],300);
    	$status_query=$db->query("INSERT INTO `"._dbp."_foto` VALUES (".
    						"NULL,'{$id}','{$_POST['newimg_desc']}','{$IMG_TXT}','{$ORIENTASI}','{$position}','{$iscover}','{$_SESSION['loginid']}'".
    					")");
    	echo '<script type="text/javascript">parent.save_foto_uploaded('.($status_query?'true':'false,\'Database Error : '.addslashes(mysql_error()).'\'').');</script>';
    	exit();
    }
	}
	
	exit();
}

//---------------------------------- HAPUS DATA -----------------------------------------------
//<!-- Delete Data
function do_delete(){
  global $id,$db;
  $sql="DELETE FROM `"._TABLE_NAME."` WHERE `id`='{$id}' AND `cat`='"._KATEGORI."'";
  $db->query($sql);
  
  $sql="DELETE FROM `"._dbp."_foto` WHERE `contentid`='{$id}'";
  $db->query($sql);

  // OPTIMIZED:
  $db->query("OPTIMIZE TABLE `"._TABLE_NAME."`");
  $db->query("OPTIMIZE TABLE `"._dbp."_foto`");

  header("location:".make_link('/'));
  exit();
}
// Delete Data -->

//---------------------------------- AMBIL GAMBAR -----------------------------------------------
//<!-- AMBIL GAMBAR
function getimg(){
  global $id,$db;
  $did=$db->sql("SELECT * FROM `"._dbp."_foto` WHERE `id`='{$id}'");
  header('content-type:image/jpeg');
 
  $im=new_class("autoimg",base64_decode($did['image']),true);
  if ($im->im){
  	if ($_QUERY['sz']=='small'){
  		if ($did['orientation']==1)
	  		$im->resize(75,100,1);
	  	elseif ($did['orientation']==2)
	    	$im->resize(100,100,1);
	  	else
	    	$im->resize(100,75,1);
  	}
  	else{
  		if ($did['orientation']==1)
	  		$im->resize(176,220,1);
	  	elseif ($did['orientation']==2)
	    	$im->resize(220,220,1);
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
      "'{$_POST['aktif']}'".
    ",'{$_SESSION['loginid']}')";
  }
  else{
    $sql="UPDATE `"._TABLE_NAME."` SET ".
      (($_POST['waktu'])?("`waktu`='".time()."',"):"").
      "`judul`='{$_POST['judul']}',".
      "`sumber`='{$_POST['sumber']}',".
      "`keyword`='{$_POST['keyword']}',".
      "`deskripsi`='{$_POST['deskripsi']}',".
      "`aktif`='{$_POST['aktif']}',".
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
  global $db,$mod,$id,$_QUERY;
  $isOnEdit=(($id!='new')&&$id);

  echo '<fieldset><legend>'.($isOnEdit?'Edit Album Foto':'Tambah Album Foto').'</legend>';

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
    Sumber Foto
  </div>
  <div class="val">
    <input maxlength="128" type="text" name="sumber" size="30" class="inputbox" value="<?php echo ($row['sumber']?htmlspecialchars($row['sumber']):_default_source); ?>" />
  </div>
  <div class="label">
    Tanggal Album
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
    Judul Album
  </div>
  <div class="vals">
    <input maxlength="200" type="text" name="judul" class="inputbox" value="<?php echo htmlspecialchars($row['judul']); ?>" style="width:820px" />
  </div>
</div>
<div class="row">
  <div class="label">
    Keterangan Album
  </div>
  <div class="vals">
    <textarea name="isi" id="isi" style="width:820px;height:400px"><?php echo trimEditor($row['isi']); ?></textarea>
    <script type="text/javascript">load_editor("isi",true,180);</script>
    </div>
</div>
<div class="row">
  <div class="label">
    Kata Kunci Terkait
  </div>
  <div class="val">
  	<input maxlength="128" type="text" name="keyword" class="inputbox" style="width:324px" value="<?php echo htmlspecialchars($row['keyword']); ?>" /><br />
    <em>Gunakan Koma untuk memisahkan kata kunci.</em>
    <fieldset><legend>Status Album</legend>
    	<label for="field_aktif" style="display:block;font-weight:bold"><input id="field_aktif" type="checkbox" name="aktif" value="1" <?php echo ($row['aktif']?'checked="checked"':''); ?> /> Aktif</label>
    	<em>Album ini akan segera tampil di galeri foto bila Aktif.</em>
    </fieldset>
  </div>
  <div class="label">
    Deskripsi Singkat:
  </div>
  <div class="val">
    <textarea name="deskripsi" class="inputbox" style="width:324px" rows="6"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
    <em>Akan tampil pada listing album. ( Max: 300 Karakter )</em>
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
  echo '<fieldset><legend>Atur Foto</legend>';
  if ($isOnEdit){
?>
<iframe style="width:1px;height:1px;display:none" id="frame_save_foto" name="frame_save_foto" src="about:blank"></iframe>
<form onsubmit="getID('save_foto_submit').disabled='disabled';" method="post" target="frame_save_foto" enctype="multipart/form-data" id="form_save_foto" action="<?php echo make_link("/mod/fotoaction/id/{$id}/fmod/upload/"); ?>">
	<table class="table-list" cellspacing="1" width="100%">
  <tr>
    <th><div style="width:66px">&nbsp;</div></th>
    <th>Gambar</th>
    <th width="55%">Deskripsi</th>
    <th width="25%">Orientasi</th>
    <th width="25%">Cover</th>
  </tr>
  <tbody id="foto_list_table">
  </tbody>
  <tfoot>
  	<tr style="background:#ffd">
  		<td>Tambah:</td>
  		<td><div class="button" style="overflow:hidden;width:220px;height:40px;position:relative;text-align:center"><input 
  			onchange="if (this.value){getID('filenamecontainer').innerHTML=this.value;}else{getID('filenamecontainer').innerHTML='Silahkan pilih file terlebih dahulu';}" id="newimg_file"
  			style="cursor:pointer;position:absolute;opacity:0;font-size:500px;top:-20px;right:-20px" type="file" name="newimg_file" />
  			<div style="padding-top:7px">Pilih Gambar<br />
  			<span id="filenamecontainer" style="font-weight:bold">Silahkan pilih file terlebih dahulu</div>
	  		</div>
  		</div></td>
  		<td align="center"><textarea name="newimg_desc" id="newimg_desc" class="inputbox" style="width:450px;height:40px;" rows="2"></textarea></td>
  		<td align="center">
  			<input style="height:45px" id="save_foto_submit" type="submit" class="button" value="Upload Foto Ini" />
  		</td>
  		<td align="center">
  			<input style="height:45px" type="button" class="button" value="Reset" onclick="save_foto_reset()" />
  		</td>
  	</tr>
  </tfoot>
	</table>
</form>
<script type="text/javascript">
/*<![CDATA[*/
function foto_list_update_cb(txt,js){
	eval('var r='+txt+';');
	var d=r.d;
	var pmax = r.posmax;
	var pmin = r.posmin;
    
	getID('foto_list_table').innerHTML = '';
	if (d.length>0){
		var va = '<?php echo make_link("/mod/fotoaction/id/{$row['id']}"); ?>';
		var vi = '<?php echo make_link("/"); ?>';
		var h  = '';
		var ori= ['Landscape','Portrait','Kotak'];
		var ors= [[100,75],[75,100],[100,100]];
		for (var i=0;i<d.length;i++){
			h+= '<tr>';
      h+= '<td class="tools">';
      h+= '<a target="frame_save_foto" href="'+va+'/fmod/delimg/fid/'+(d[i].id)+'/" onclick="return msg_delete()" class="tool-delete"><span>Delete</span></a>';
      
      if (d[i].pos!=pmin)
        h+= '<a target="frame_save_foto" href="'+va+'/fmod/up/fid/'+(d[i].id)+'/" class="tool-up"><span>Pindah Ke Atas</span></a>';
      else
        h+= '<a href="#" onclick="return false;" style="visibility:hidden"></a>';
      
      if (d[i].pos!=pmax)
      	h+= '<a target="frame_save_foto" href="'+va+'/fmod/dn/fid/'+(d[i].id)+'/" class="tool-down"><span>Pindah Ke Bawah</span></a>';
      
      h+= '</td>';
      h+= "<td align=\"center\">";
	    h+= '<img width="'+(ors[d[i].orientation][0])+'" height="'+(ors[d[i].orientation][1])+'" style="border:1px solid #aaa" src="'+vi+'mod/getimg/sz/small/id/'+(d[i].id)+'/" alt="Preview" />';  		
      h+= "</td>";
      h+= "<td>"+nl2br(htmlspecialchars(d[i].deskripsi))+"<br /><b>Upload Oleh :</b>"+(d[i].modifier)+"</td>";
      h+= "<td align=\"center\">"+(ori[d[i].orientation])+"</td>";
      h+= "<td align=\"center\">";
      if (d[i].cover==0){
      	h+='<a target="frame_save_foto" href="'+va+'/fmod/setcover/fid/'+(d[i].id)+'/">Set</a>';
      }
      else{
      	h+='<b>Cover</b>';
      }
      h+= "</td>";      
      h+= '</tr>';      
		}
		getID('foto_list_table').innerHTML = h;
	}
	else{
		getID('foto_list_table').innerHTML = '<tr><td align="center" colspan="5">Belum Terdapat Foto pada Album Ini<br />Silahkan untuk melakukan upload Foto terlebih dahulu...</td></tr>';
	}
}
function foto_list_update(){
	var uri='<?php echo make_link("/mod/fotoaction/id/{$id}/fmod/getdata/"); ?>';
	a2jaxget(uri,'foto_list_update_cb');
}
function save_foto_reset(){
	getID('form_save_foto').reset();
	getID('newimg_desc').value="";
	getID('filenamecontainer').innerHTML='Silahkan pilih file terlebih dahulu';
	
}
function save_foto_uploaded(statusx,errorstr){
	if (!statusx){
		alert('UPLOAD ERROR!!!\n'+errorstr);
	}
	else{
		save_foto_reset();
	}
	getID('save_foto_submit').disabled='';
	foto_list_update();
}
foto_list_update();
setOpacity(getID('newimg_file'),0);
/*]]>*/
</script>
<?php
  }
  else{
  	echo '<div style="text-align:center;padding:20px;color:#666;font-size:10px;font-weight:bold">Silahkan Simpan Album Foto Terlebih Dahulu<br />Sebelum Anda dapat melakukan upload Foto pada Album ini</div>';
  }
  echo '</fieldset>';
  
  
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
    array('Deskripsi Singkat',       'deskripsi'),
    array('Keterangan Album',      'isi'),
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
      <th>Cover</th>
      <th width="55%"><?php make_sorthead("Judul",  "judul"); ?></th>
      <th width="10%"><?php make_sorthead("Penginput", "sumber"); ?></th>
      <th width="5%"><?php make_sorthead("Status", "aktif"); ?></th>
      <th width="10%">Jumlah Gambar</th>
    </tr>
    <?php
    
    $ors = array(array(100,75),array(75,100),array(100,100));

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
      $coverimg=$db->sql("SELECT `id`,`orientation` FROM `"._dbp."_foto` WHERE `contentid`='{$row['id']}' AND `cover`='1'");
      if ($coverimg){
		    echo '<img style="border:1px solid #aaa" src="'.make_link("/mod/getimg/id/{$coverimg['id']}/sz/small/").'" ';
	    	echo 'height="'.($ors[$coverimg['orientation']][1]).'" width="'.($ors[$coverimg['orientation']][0]).'" ';
		    echo 'alt="Preview" />';
  		}
  		else{
  			echo "-";
  		}
  		echo "</td>";
      
      echo "<td><h3>".htmlspecialchars($row['judul'])."</h3>".htmlspecialchars($row['deskripsi'])."</td>";
      echo "<td align=\"center\">".htmlspecialchars($row['modifier'])."</td>";
      echo "<td align=\"center\">".($row['aktif']?'Aktif':'-')."</td>";
      echo "<td align=\"center\">";
      $fotocnt=$db->sql("SELECT COUNT(*) AS `cnt` FROM `"._dbp."_foto` WHERE `contentid`='{$row['id']}'");
      echo ((int) $fotocnt['cnt']);
      echo "</td>";
      
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
      $tools[]=array("new",   "Tambah Album Foto",  "/mod/edit/id/new/");

    $tools[]  =array("save",  "Simpan",  "getID('form-edit').submit();", true);
  }
  elseif (!$mod)
    $tools[]  =array("new",   "Tambah Album Foto",  "/mod/edit/id/new/");

  if ($tools)
    make_head(_MODULE_TITLE,$tools);

  if (($_SERVER['show_header'])&&!$hidecat){
    $rows=$db->all("SELECT * FROM `"._dbp."_foto_cat` ORDER BY `pos` ASC, `id` ASC");
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