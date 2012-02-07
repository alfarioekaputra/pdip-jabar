<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
# WEBCORE (c)
#
# Ranah Waktu
$_SERVER['LIST_BULAN']=array(
	'Januari',
	'Februari',
	'Maret',
	'April',
	'Mei',
	'Juni',
	'Juli',
	'Agustus',
	'September',
	'Oktober',
	'November',
	'Desember'
);
$_SERVER['LIST_HARI']=array(
	'Sun'=>'Minggu',
	'Mon'=>'Senin',
	'Tue'=>'Selasa',
	'Wed'=>'Rabu',
	'Thu'=>'Kamis',
	'Fri'=>'Jumat',
	'Sat'=>'Sabtu'
);

function date_format_do($format,$time){
    $hari=$_SERVER['LIST_HARI'][date("D",$time)];
    $bulan=$_SERVER['LIST_BULAN'][date('n',$time)-1];
    $dt = explode("##",$format);
    for ($i=0;$i<count($dt);$i++){
        if ($i%2==1){
            $dt[$i]=date($dt[$i],$time);
        }
    }
    $tout=implode("",$dt);
    $out =  str_replace("[DAY]",$hari,
    				str_replace("[DDD]",substr($hari,0,3),
            str_replace("[MONTH]",$bulan,
            str_replace("[MMM]",substr($bulan,0,3), $tout)))
          );
    return $out;
}
function shortTanggal($time){
    return date_format_do("[DDD], ##d## [MMM] ##y## - ##H:i##",$time);
}
function tglTanggal($time){
    return date_format_do("[DAY], ##d## [MONTH] ##Y##",$time);
}
function formatTanggal($time){
    return date_format_do("[DAY], ##d## [MONTH] ##Y## ##H:i## WIB",$time);
}
function getRanahWaktu($time){
    return date_format_do("[DAY], ##d## [MMM] ##Y## ##H:i## WIB",$time);
}
function tglberita($time){
    return date_format_do("##d## [MONTH] ##Y##",$time);
}
function tgl2normal($str){
  return $str{8}.$str{9}."/".$str{5}.$str{6}."/".substr($str,0,4);
}
function tgl2unixtime($str){
    $d=(int) $str{8}.$str{9};
    $m=(int) $str{5}.$str{6};
    $y=(int) substr($str,0,4);
  return mktime(0,0,0,$m,$d,$y);
}
/*function getRanahWaktu($time){
	$ret = '';
	if ($time>(time()-(60*60))){
		$waktu = (int) floor((time()-$time)/60);
		if ($waktu==0){
			$ret = "Beberapa waktu lalu";
		}
		else{
			$ret = "{$waktu} Menit yang lalu";
		}
	}
	elseif ($time>(time()-(60*60*24))){
		$waktu = (int) floor((time()-$time)/(60*60));
		$ret = "{$waktu} Jam yang lalu";
	}
	elseif ($time>(time()-(60*60*24*7))){
		$waktu = (int) floor((time()-$time)/(60*60*24));
		if ($waktu==1)
			$ret = "Kemarin Jam ".date("H:i",$time);
		else
			$ret = "{$waktu} Hari yang lalu";
	}
	else{
		$ret = formatTanggal($time);
	}
	return $ret;
}*/

function getCalendar($tagName,$firstval,$alignright=false){
  $buf='<div id="'.$tagName.'_div" style="line-height:14px">';
  $buf.='<img src="about:blank" alt="" class="hideObj" onerror="calender_write('.
       "'{$tagName}_div','{$tagName}',";
        if ($firstval)
          $buf.= str_replace('-',',',$firstval);
        else
          $buf.= '0,0,0';
  $buf.=",'input1','width:80px;text-align:center;'".($alignright?",1":",0").");".
       '" width="0" height="0" /></div>';
  return $buf;
}
# Calendar Builder
function printCalendar($tagName,$firstval,$alignright=false){
  echo getCalendar($tagName,$firstval,$alignright);
}


?>