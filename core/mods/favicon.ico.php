<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }

$isi = file_get_contents(_dir_res.'/favicon.ico');
header('content-type:image/x-icon');
header('content-length:'.strlen($isi));
echo $isi;
exit();
?>