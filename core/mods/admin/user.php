<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007 e-Natives Technology
#             All Rights Reserverd
#
#

define('_MODULE_TITLE', "Atur User Administrator");
define('_TABLE_NAME',   _dbprefix."_user");

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
  $sql="DELETE FROM `"._TABLE_NAME."` WHERE `id`='{$id}'";
  $db->query($sql);

  // OPTIMIZED:
  $db->query("OPTIMIZE TABLE `"._TABLE_NAME."`");

  header("location:".make_link('/'));
  exit();
}
// Delete Data -->



//---------------------------------- SIMPAN DATA -----------------------------------------------
//<!-- Save Data
function validUsername($txt){
    $invalidchr='~`!#$%^&*()+=\\|][}{": ;/?><,\'';
    if (strlen($txt)<5)
        return false;
    for ($i=0;$i<strlen($txt);$i++){
    	if (strpos($txt{$i},$invalidchr)!==false)
          return false;
    }
    return true;
}
function do_save(){
  global $id,$db;
	
  $pass=md5(($_POST['password']));
  $user=strtolower($_POST['username']);
  $perm='';
  for ($i=0;$i<64;$i++){
    if ($_POST['perm'][$i+1])
      $perm.='1';
     else
      $perm.='0';
  }
  if (($id=='new')||(!$id)){
  	if (!validUsername($user)){
  		?>
	    <script type="text/javascript">
	      alert('ERROR!!\nUsername tidak Valid. Jangan gunakan karakter spesial\nMinimal 5 Karakter...');
	      history.go(-1);
	    </script>
	    <?php
	    exit();
  	}
    $sql="INSERT INTO `"._TABLE_NAME."` VALUES (".
      "NULL,'{$user}','{$pass}','{$_POST['fullname']}','{$_POST['name']}','{$_POST['email']}','{$_POST['website']}'".
      ",'0','{$perm}','{$_POST['admin']}','','{$_SERVER["REMOTE_ADDR"]}','{$_SERVER["REMOTE_ADDR"]}','".time()."','".time()."'".
    ")";
  }
  else{
    if ($_POST['password'])
      $passSQL="`password`='{$pass}',";
    $sql="UPDATE `"._TABLE_NAME."` SET {$passSQL}".
        "`fullname`='{$_POST['fullname']}',".
      "`name`='{$_POST['name']}',".
      "`email`='{$_POST['email']}',".
      "`website`='{$_POST['website']}',".
      "`permission`='{$perm}',".
      "`admin`='{$_POST['admin']}',".
      "`ket`=''".
    " WHERE `id`='{$id}'";
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
    $_POST['username']=$user;
    $_POST['permission']=$perm;
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

  echo '<form method="post" id="form-edit" action="'.make_link("/mod/save/id/{$id}/").'">';
//##<!-- Form Design
?>

<div class="header">
  Data Login
</div>

<div class="row">
  <div class="label">
    Username
  </div>
  <div class="val">
    <input maxlength="32" type="text" <?php if ($isOnEdit) { echo 'readonly="readonly"';} ?> name="username" class="inputbox" value="<?php echo htmlspecialchars($row['username']); ?>" />
  </div>
</div>

<div class="row">
  <div class="label">
    Password
  </div>
  <div class="vals">
    <input type="password" name="password" class="inputbox" />
    <?php
    if ($isOnEdit){
      echo '<em>Biarkan kosong bila tidak akan mengubah password</em>';
    }
    ?>
  </div>
</div>

<div class="row">
  <div class="label">
    Nama Lengkap
  </div>
  <div class="vals">
    <input type="text" name="fullname" class="inputbox" value="<?php echo htmlspecialchars($row['fullname']); ?>" size="50" />
  </div>
</div>

<div class="row">
  <div class="label">
    Nama Tampilan
  </div>
  <div class="vals">
    <input type="text" name="name" class="inputbox" value="<?php echo htmlspecialchars($row['name']); ?>" size="50" />
  </div>
</div>

<div class="row">
  <div class="label">
    Email
  </div>
  <div class="val">
    <input type="text" name="email" class="inputbox" value="<?php echo htmlspecialchars($row['email']); ?>" size="30" />
  </div>

  <div class="label">
    Website
  </div>
  <div class="val">
    <input type="text" name="website" class="inputbox" value="<?php echo htmlspecialchars($row['website']); ?>" size="30" />
  </div>
</div>

<div class="row">
  <div class="label">
    Register IP
  </div>
  <div class="val">
    <input type="text" name="r_ip" class="inputbox" value="<?php echo htmlspecialchars($row['reg_ip']); ?>" size="30" readonly="readonly" />
  </div>

  <div class="label">
    Last Login IP
  </div>
  <div class="val">
    <input type="text" name="l_ip" class="inputbox" value="<?php echo htmlspecialchars($row['last_ip']); ?>" size="30" readonly="readonly" />
  </div>
</div>

<div class="row">
  <div class="label">
    Admin
  </div>
  <div class="val">
    <input type="checkbox" value="1" name="admin" <?php echo ($row['admin']?'checked="checked"':''); ?>/>
  </div>
</div>

<div class="row">&nbsp;</div>
<div class="header">
  Hak Akses
</div>

<?php

for ($i=0;$i<strlen($row['permission']);$i++){
  $rowperm[$i+1]=$row['permission']{$i};
}

for ($i=0;$i<count($_SERVER['MAIN_MENU']);$i++){
  $mm=$_SERVER['MAIN_MENU'][$i];
  echo '<div style="border-bottom:1px solid #eeeeee;line-height:31px;"><input '.($rowperm[$mm[0]]?'checked="checked"':'').' name="perm['.($mm[0]).']" type="checkbox" value="1" /> '.htmlspecialchars($mm[2]).'</div>';
}

?>
<div class="row">&nbsp;</div>
<?php
//##--- Form Design -->
  echo '</form></fieldset>';
}
// Edit -->



//---------------------------------- LISTING DATA -----------------------------------------------
//<!-- Listing Data
function do_list(){
  global $db, $start,$list_show;
  list($order_by, $order_as, $filter, $filterby)=init_do_list();

  if (!$order_by||!$order_as){
    //--- NORMAL SORTING
    $order_by='username';
    $order_as='ASC';
  }
  $ORDER_SQL="ORDER BY `$order_by` $order_as";

  if ($filter&&$filterby){
    $WHERE_SQL="WHERE `{$filterby}` like '%".addslashes($filter)."%'";
  }
  $filter_cat=array(
    array('Username', 'username'),
    array('Name', 'name'),
  );

  echo '<fieldset><legend>List</legend>';
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
    <table class="table-list" cellspacing="1">
    <tr>
      <th><div style="width:44px">&nbsp;</div></th>
      <th width="30%"><?php make_sorthead("Username","username"); ?></th>
      <th width="60%"><?php make_sorthead("Nama","name"); ?></th>
      <th width="10%"><?php make_sorthead("Admin","admin"); ?></th>
    </tr>
    <?php

    //## List Data:
    for ($i=0;$i<count($rows);$i++){
      $row=$rows[$i];
      echo '<tr>';
      //-- Tools
      echo '<td class="tools">';
      echo '<a href="'.make_link("/mod/edit/id/{$row['id']}/").'" class="tool-edit"><span>Edit</span></a>';
      if ($_SESSION['loginidnomor']!=$row['id'])
        echo '<a href="'.make_link("/mod/delete/id/{$row['id']}/").'" onclick="return msg_delete()" class="tool-delete"><span>Hapus</span></a>';
      echo '</td>';
      //-- Data
      echo "<td>{$row['username']}</td>";
      echo "<td>".htmlspecialchars($row['name'])."</td>";
      echo "<td align=\"center\">".($row['admin']?'Yes':'-')."</td>";
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
  global $id;
  if ($mod=='edit'){
    $tools[]  =array("back",  "Kembali", "/");
    if ($id!='new')
      $tools[]=array("new",   "Tambah User",  "/mod/edit/id/new/");

    $tools[]  =array("save",  "Simpan",  "getID('form-edit').submit();", true);
  }
  elseif (!$mod)
    $tools[]  =array("new",   "Tambah User",  "/mod/edit/id/new/");
  if ($tools)
    make_head(_MODULE_TITLE,$tools);
}


?>