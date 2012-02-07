<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#

$_SERVER['MAINMENU_SEL'] = 'admin';
if (!$_SESSION['loginadmin']){
    header("location:"._net."/sign/in/admin/");
    exit();
}

include_once _dir_libs."/functions/admin.php";
include_once _dir_mods.'/admin/variables.php';

## Check Module Name:
if ($_QUERY['m']){
    $_ADMIN_MODULE_FILENAME=_dir_mods."/admin/{$_QUERY['m']}.php";
}
else{
    $_QUERY['m']='index';
    $_ADMIN_MODULE_FILENAME=_dir_mods."/admin/{$_QUERY['m']}.php";
}

## Check Exists Module File:
if (!file_exists($_ADMIN_MODULE_FILENAME)){
    header('location:'._net.'/admin/');
    exit();
}

## Check Permission:
$MOD_PERM=$_SERVER['MODULE_PERMISSION'][$_SERVER['MODULE']];
if (($MOD_PERM>0)&&(!$_SESSION['logindata']['permission']{$MOD_PERM})){
  header('location:'._net.'/admin/');
  exit();
}

$_SERVER['MODULE_ADMIN']=$_QUERY['m'];

## Load Admin Module
ob_start();
	include_once $_ADMIN_MODULE_FILENAME;
	$___ADMIN_FLUSH=ob_get_contents();
ob_end_clean();

$_TPL->css(_shr.'/admin.css');

# ADMIN MENU
echo '<div>';
  echo '<div class="box_admin_menu"><span>Menu Admin :</span>';

		$PREV_CAT_NUM=1;
		for ($i=0;$i<count($_SERVER['MAIN_MENU']);$i++){
		    $CCT=($PREV_CAT_NUM!=$_SERVER['MAIN_MENU'][$i+1][3])&&($PREV_CAT_NUM<5);
		    if ($_SESSION['logindata']['permission']{$_SERVER['MAIN_MENU'][$i][0]-1}){
		        $CCS=($_QUERY['m']==$_SERVER['MAIN_MENU'][$i][1]);
		        
            echo "<a".($CCS?' class="actived"':'')." href=\""._net."/admin/m/".($_SERVER['MAIN_MENU'][$i][1])."/\">";
            echo htmlspecialchars($_SERVER['MAIN_MENU'][$i][2]);
            echo "</a>";
            
            if ($CCT){
            	echo '<span>|</span>';
            }
            
		        $PREV_CAT_NUM=$_SERVER['MAIN_MENU'][$i+1][3];
		    }
		}
	echo '<a style="float:right" href="'._net,'/sign/">Logout Administrator</a>';
	echo '</div>';
echo '</div>';

## FLUSH MODULE BUFFER
echo '<div>';
	echo $___ADMIN_FLUSH;
echo '</div>';

?>