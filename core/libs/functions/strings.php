<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
# WEBCORE (c)
#
function clean_substr($str,$length,$add_postfix='..'){
	if (strlen($str)>$length){
		$str = substr($str,0,$length);
  	$dsc = explode(' ',$str);
  	if (count($dsc)>1)
			unset($dsc[count($dsc)-1]);
		$dsc = implode(' ',$dsc);
		return trim($dsc).$add_postfix;
	}
	return $str;
}

function trimAllWhiteSpace($str){
	return str_replace("  "," ",
    					str_replace("  "," ",
    					str_replace("   "," ",
    					str_replace("\t"," ",
    					str_replace("\n"," ",
    					str_replace("\r","",
    						$str
    						))))));
}
function jsencode($obj,$json=false){
	if (function_exists('json_encode')){
		return json_encode($obj);
	}
	switch(gettype($obj)){
		case 'array':
		case 'object':
			$code = array();
			if( array_keys($obj) !== range(0, count($obj) - 1) ){
				foreach( $obj as $key => $val ){
					$code []= $json ?
						'"' . $key . '":' . jsencode( $val ) :
						$key . ':' . jsencode( $val );
				}
				$code = '{' . implode( ',', $code ) . '}';
			} else {
				foreach( $obj as $val ){
					$code []= jsencode( $val );
				}
				$code = '[' . implode( ',', $code ) . ']';
			}
			return $code;
			break;
		case 'boolean':
			return $obj?'true':'false';
			break;
		case 'integer':
		case 'double':
			return floatVal( $obj );
			break;
		case 'NULL':
		case 'resource':
		case 'unknown':
			return 'null';
			break;
		default:
			return '"'.addslashes($obj).'"';
	}
}
function soefriendly($title,$prefix="html",$maxsize=0){
	if ($maxsize) $title=clean_substr($title,$maxsize);
	$title=strtolower($title);
  return str_replace(" ","-",trim(str_replace("  "," ",str_replace("  "," ",eregi_replace("[^a-zA-Z0-9]"," ",$title))))).".{$prefix}";
}
function filenameFriendly($title){
	return strtolower(str_replace(" ","-",trim(str_replace("  "," ",str_replace("  "," ",eregi_replace("[^a-zA-Z0-9\\.]"," ",$title))))));
}
function txtfriendly($title,$separator='-'){
    return strtolower(str_replace(" ",$separator,trim(str_replace("  "," ",str_replace("  "," ",eregi_replace("[^a-zA-Z0-9]"," ",$title))))));
}
function id2base($num){
	return strtolower(base_convert($num,10,36));
}
function base2id($num){
	return base_convert($num,36,10);
}
function b64e($txt){
	$ret = base64_encode($txt);
	$ret = str_replace('A','!',$ret);
	$ret = str_replace('d','@',$ret);
	$ret = str_replace('H','#',$ret);
	$ret = str_replace('l','$',$ret);
	$ret = str_replace('P','%',$ret);
	$ret = str_replace('t','^',$ret);
	$ret = str_replace('S','&',$ret);
	$ret = str_replace('X','*',$ret);
	
	$ret = str_replace('/','A',$ret);
	$ret = str_replace('=','X',$ret);
	
	$ret = str_replace('!','-',$ret);
	$ret = str_replace('*','_',$ret);
	
	$ret = str_replace('@','S',$ret);
	$ret = str_replace('#','t',$ret);
	$ret = str_replace('$','P',$ret);
	$ret = str_replace('%','l',$ret);
	$ret = str_replace('^','H',$ret);
	$ret = str_replace('&','d',$ret);
	
	return $ret;
}
function b64d($txt){
	$ret = str_replace('S','@',$txt);
	$ret = str_replace('t','#',$ret);
	$ret = str_replace('P','$',$ret);
	$ret = str_replace('l','%',$ret);
	$ret = str_replace('H','^',$ret);
	$ret = str_replace('d','&',$ret);
	
	$ret = str_replace('-','!',$ret);
	$ret = str_replace('_','*',$ret);
	
	$ret = str_replace('A','/',$ret);
	$ret = str_replace('X','=',$ret);
	
	$ret = str_replace('!','A',$ret);
	$ret = str_replace('@','d',$ret);
	$ret = str_replace('#','H',$ret);
	$ret = str_replace('$','l',$ret);
	$ret = str_replace('%','P',$ret);
	$ret = str_replace('^','t',$ret);
	$ret = str_replace('&','S',$ret);
	$ret = str_replace('*','X',$ret);
	
	$ret = base64_decode($ret);
	return $ret;
}




?>