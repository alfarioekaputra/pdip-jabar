<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007 e-Natives Technology
#             All Rights Reserverd
#
#

define('_MODULE_TITLE', "Statistik Trafik");
define('_TABLE_NAME',   _dbprefix."_statistik");

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
do_list();

//###
//#
//# Handler Functions
//#
//###
//---------------------------------- LISTING DATA -----------------------------------------------
//<!-- Listing Data
function do_list(){
  global $db, $start,$list_show;
  list($order_by, $order_as, $filter, $filterby)=init_do_list();

  if ($_POST)
    $_SESSION['STATISTIK']=$_POST;
  if (!$order_by||!$order_as){
    //--- NORMAL SORTING
    $order_by='waktu';
    $order_as='DESC';
  }
  $ORDER_SQL="ORDER BY `$order_by` $order_as";

  $WHERE_SQL='';
  if ($_SESSION['STATISTIK']['jenis']){
    $WHERE_SQL="WHERE `new_session` = '1'";
  }
  if (($_SESSION['STATISTIK']['only_tgl'])&&($_SESSION['STATISTIK']['tgl1'])){
    if ($WHERE_SQL)
      $WHERE_SQL.=" AND `tgl` = '".($_SESSION['STATISTIK']['tgl1'])."'";
    else
      $WHERE_SQL="WHERE `tgl` = '".($_SESSION['STATISTIK']['tgl1'])."'";
  }
 
  echo '<fieldset><legend>Listing Data</legend>';
  //make_filter($filter_cat);
  ?>
  <form method="post" action="<?php echo make_link('/'); ?>">
	<table class="box-kit">
	<tr>
		<td width="100%">&nbsp;</td>
		<td><input type="checkbox" name="only_tgl" value="1" <?php echo (($_SESSION['STATISTIK']['only_tgl'])?'checked="checked"':''); ?> /></td>
		<td>Hanya Tanggal :</td>
		<td><?php
				      make_calendar('tgl1',(($_SESSION['STATISTIK']['tgl1'])?($_SESSION['STATISTIK']['tgl1']):(date("Y-m-d"))));
				?></td>
		<td>
			<input type="radio" name="jenis" <?php echo ((!$_SESSION['STATISTIK']['jenis'])?'checked="checked"':''); ?> value="0" /> Semua
		</td>
		<td>
			<input type="radio" name="jenis" <?php echo (($_SESSION['STATISTIK']['jenis'])?'checked="checked"':''); ?> value="1" /> First Visit
		</td>
		<td>
			<input type="submit" value="Update" class="button" name="s1" />
		</td>
	</tr>
</table></form>
<?php

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
      <th width="40%"><?php make_sorthead("IP Address","ip"); ?></th>
      <th width="30%"><?php make_sorthead("Jam","waktu"); ?></th>
      <th width="30%"><?php make_sorthead("First Visit","new_session"); ?></th>
    </tr>
    <?php

    //## List Data:
    for ($i=0;$i<count($rows);$i++){
      $row=$rows[$i];
      echo '<tr>';
      
      //-- Data
      echo "<td>".htmlspecialchars($row['ip'])."</td>";
      echo "<td>".date("H:i:s",$row['waktu'])."</td>";
      echo "<td>".(($row['new_session'])?'YA':'')."</td>"; 
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
  $tools[]  =array("update",  "Refresh", "/");
    
  if ($tools)
    make_head(_MODULE_TITLE,$tools);
}


?>
