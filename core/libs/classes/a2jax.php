<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
# A2JAX - Library...
#
# WEBCORE (c) 2006 Ahmad Amarullah <amarullz@yahoo.com>
#

// {{{ Class: a2jax

class a2jax
{
	var $isAjax;
	var $headScript='';
	var $isJS=false;
	
	function a2jax(){
		$this->isAjax=($_SERVER[HTTP_AGENT_TYPE]=='a2jax');
		$this->isJS=false;
		if ($this->isAjax){
			header('a2jax-uri: '.($_SERVER["REQUEST_URI"]),true);
		}
	}
	function writeJS($strss){
		$this->headScript.=$strss;
		header('a2jax-script: '.($this->headScript),true);
	}
	function js_escape($txt){
	    return str_replace("<","<'+'",str_replace("'","\\'",str_replace("\r","",str_replace("\n","\\n",str_replace("\\","\\\\",$txt)))));
	}
	function setTitle($txt){
		$s="aTL('".($this->js_escape($txt))."');";
    if ($this->isJS)
    	echo $s;
    else
    	$this->writeJS($s);
	}
	function alert($txt){
		$s="alert('".($this->js_escape($txt))."');";
    if ($this->isJS)
    	echo $s;
    else
    	$this->writeJS($s);
	}
	function write($txt){
		$s="document.write('".($this->js_escape($txt))."');";
	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function js($txt){
		$s=$txt;
	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function location($uri){
		$s="location='".($this->js_escape($uri))."';";
	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function windowOpen($uri,$win_id=false,$win_conf=false){
	    $s="window.open('".($this->js_escape($uri))."'".
	            ($win_id?",'{$winid}'":"").
	            ($win_conf?
	                ((!$win_id?",''":"").",'{$win_conf}'"):"").
	         ");";

	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function style($div,$element,$value){
		$s="getID('{$div}').style.{$element}='".($this->js_escape($value))."';";
	    if ($this->isJS) 
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function getID($div,$element,$txt){
		$s="getID('{$div}').{$element}='".($this->js_escape($txt))."';";
	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function innerHTML($div,$txt){
		$s="getID('{$div}').innerHTML='".($this->js_escape($txt))."';";
	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function onload($txt){
		$s='a2jax_onload(function(){ '.$txt.' });';
	    if ($this->isJS)
	    	echo $s;
	    else
	    	$this->writeJS($s);
	}
	function addScriptFile($file,$ondiv){
		$this->js("ajs('{$file}','{$ondiv}');");
	}
	function addCssFile($file,$ondiv){
		$this->js("acs('{$file}','{$ondiv}');");
	}
	function setJS(){
		if ($this->isAjax){
		    if (!$this->isJS)
			    header("a2jax-type: script");

			$this->isJS=true;
			return true;
		}
		else
			return false;
	}
}
?>