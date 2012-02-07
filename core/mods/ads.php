<?php
# Security
if (!defined('_IN_PHP')){ header('location:/'); exit(); }
#
#
# WEBCORE (c) 2007
#
# Halaman : STATIC CONTENTS
#
define('_TABLE_NAME',   _p."_banner");
if ($id=(int) $_QUERY['img']){
  $did=$db->sql("SELECT * FROM `"._TABLE_NAME."` WHERE `id`='{$id}' AND (`type`='0' OR `type`='3')");
  if ($did['type']==3)
    header("content-type:application/x-shockwave-flash");
  echo base64_decode($did['data']);
  exit();
}

header('location:'._net.'/page/redaksi/ads');
exit();

?>