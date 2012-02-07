<?php
	include './config.php';
  $dirpos=stripslashes($_GET['d']);
  if (substr($dirpos,0,5)!=">root") $dirpos=">root";  
  if ($_GET['m']){
  	$godir=stripslashes($_GET['m']);
  	if ($godir=='..'){
  		if ($dirpos!='>root')
  			$dirpos=dirname($dirpos);
  	}
  	else
  		$dirpos.="/{$godir}";
  }
  $curl="./?d=".urlencode($dirpos);
  $dirpath=_dir_save.substr($dirpos,5);
  $urlpath=_url_save.substr($dirpos,5);
  
  //-- UPLOAD
  if ($_FILES['image']){
    if ($_FILES['image']['error']==UPLOAD_ERR_OK){
    	$sz=getimagesize($_FILES['image']['tmp_name']);
    	$fname=$_FILES['image']['name'];
    	$pi=pathinfo($fname);
    	$pi=$pi['extension'];
    	$fnametmp=substr($fname,0,strlen($fname)-strlen($pi));
      if ($sz){
      	$dest=$dirpath."/{$fname}";
      	$ori_n=1;
      	while (file_exists($dest)){
      		$fname="{$fnametmp}{$ori_n}.{$pi}";
      		$ori_n++;
      		$dest=$dirpath."/{$fname}";
      	}
      	$fp=fopen($dest,"w+");
      	fwrite($fp,file_get_contents($_FILES['image']['tmp_name']));
      	fclose($fp);
      	header('Location:'.$curl.'&refresh='.time());
      	exit();
      }
      else{
      	?><script type="text/javascript">alert('Image Format Not Valid');history.go(-1);</script><?php
      	exit();
      }
    }
  }
  
  //-- RENAME
  if ($_GET['renn']&&$_GET['renf']){
  	$oldfile=$dirpath."/".stripslashes($_GET['renf']);
  	$newfile=$dirpath."/".stripslashes($_GET['renn']);
  	if (file_exists($oldfile)){
  		if (file_exists($newfile)){
  			?><script type="text/javascript">alert('New filename already exists...');history.go(-1);</script><?php
  			exit();
  		}
  		else{
  			rename($oldfile,$newfile);
  			header('Location:'.$curl.'&refresh='.time());
      	exit();
  		}
  	}
  	else{
  		?><script type="text/javascript">alert('File not found...');history.go(-1);</script><?php
  		exit();
  	}  	
  }
  
  //-- DELETE
  if ($_GET['delf']){
  	$delfile=$dirpath."/".stripslashes($_GET['delf']);
  	if (file_exists($delfile)){
  		if (filetype($delfile)=='dir'){
  			if (@rmdir($delfile)){
	  			header('Location:'.$curl.'&refresh='.time());
	      	exit();
	      }
	      else{
	      	?><script type="text/javascript">alert('Delete folder failed...\nPlease check if directory already empty...');history.go(-1);</script><?php
  				exit();
	      }
  		}
  		else{
  			if (@unlink($delfile)){
	  			header('Location:'.$curl.'&refresh='.time());
	      	exit();
	      }
	      else{
	      	?><script type="text/javascript">alert('Delete image failed...');history.go(-1);</script><?php
  				exit();
	      }
  		}
  	}
  	else{
  		?><script type="text/javascript">alert('File not found...');history.go(-1);</script><?php
  		exit();
  	}
  }
  
  //-- New Folder
  if ($_GET['nfolder']){
  	$newfolder=$dirpath."/".stripslashes($_GET['nfolder']);
  	if (!file_exists($newfolder)){
  		if (@mkdir($newfolder,0777)){
  			header('Location:'.$curl.'&refresh='.time());
      	exit();
      }
      else{
      	?><script type="text/javascript">alert('Create new folder error...');history.go(-1);</script><?php
				exit();
      }
  	}
  	else{
  		?><script type="text/javascript">alert('Folder name already exists...');history.go(-1);</script><?php
  		exit();
  	}  
	}
  
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>epicTures - Image Manager</title>
	<script type="text/javascript" src="./main.js"></script>
	<link href="./main.css" rel="stylesheet" type="text/css" />
</head>
<body onload="loadManager('<?php echo htmlspecialchars($dirpos); ?>','<?php echo htmlspecialchars($urlpath); ?>');">
	<div class="epic_nav">
		<input type="text" value="<?php echo (substr($dirpos,5)?htmlspecialchars(substr($dirpos,5)):"/"); ?>" style="width:350px" readonly="readonly" />		
		<input type="button" value="^" style="width:20px" title="Parent Directory" <?php
		if ($dirpos=='>root')
			echo 'disabled="disabled"';
		else
			echo 'onclick="location=\''.$curl.'&amp;m='.urlencode("..").'\';"';
		?>/>
		<input type="button" style="width:80px" onclick="popNewFolder();" value="New Folder" />
		<input type="button" style="width:50px" onclick="window.close();" value="Close" />
	</div>
	<div id="epic_detail">
		<div style="text-align:center;padding-top:50px">
			<b style="font-size:13px"><em style="color:#aa3300">epic</em><span style="color:#224499">Tures</span> Manager</b><br />
			<em style="color:#666666">The simplest and most easy to use image manager for TinyMCE</em><br />
			version 1.0.0<br /><br />
			&copy; 2008<br />e-Natives Technology<br />All rights reserved<br />&nbsp;
		</div>
		<a href="http://www.e-natives.com/" onclick="window.opener.window.open('http://www.e-natives.com/');return false;">&raquo; Go to Website</a>
		<a href="mailto:support@e-natives.com" onclick="alert('Please send your mail into: support@e-natives.com');return false;">&raquo; Support</a>
	</div>
	<div class="epic_content" id="epic_list">
		<?php
//-- LIST DIRECTORY:
if (is_dir($dirpath)) {
	unset($files);
	unset($dirs);
	if ($dh = opendir($dirpath)) {
	   while (($file = readdir($dh)) !== false) {
				if (($file!=".")&&($file!="..")){
					if (filetype($dirpath."/".$file)=='dir')
						$dirs[]=$file;
					else
						$files[]=$file;
	      }
	   }
	   closedir($dh);
	}
	$cn=0;
	for ($i=0;$i<count($dirs);$i++){
		$file=$dirs[$i];
  	echo '<a href="'.$curl.'&amp;m='.urlencode($file).'" onclick="return clickDir(this,\''.htmlspecialchars($file).'\');" ondblclick="location=this.href;" class="folder"><b>'.htmlspecialchars($file).'</b></a>';
  	$cn++;
	}
	for ($i=0;$i<count($files);$i++){
		$file=$files[$i];
		$sz=getimagesize($dirpath."/".$file);
		if ($sz){
			$cn++;
			echo '<a href="image:'.htmlspecialchars($file).'" onclick="return clickImg(this,'.($sz[0]).','.($sz[1]).');" ondblclick="pasteImage(this,'.($sz[0]).','.($sz[1]).')" class="file">&nbsp;</a>';
		}
	}
	if ($cn==0){
		echo '<em>No file in this folder...</em>';
	}
}
		?>
		<div style="clear:both">&nbsp;</div>
	</div>
	<form onsubmit="this.s1.disabled='disabled';" method="post" action="<?php echo $curl; ?>" enctype="multipart/form-data">
		<b>Upload Image:</b>
		<input type="file" name="image" /><input type="submit" name="s1" value="Upload" />
		<em>GIF, PNG, JPG</em>
	</form>
</body>
</html>