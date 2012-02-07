<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
#

if (($_QUERY['in'])&&(!$_SESSION['loginid'])){
	?>
<div style="text-align:center;font-size:12px;padding:40px;padding-top:240px;background:url('<?php echo _shr; ?>/img/admin/home.jpg') no-repeat center 30px">
		<b style="font-size:16px">Login Administrator</b><br />
    Untuk mengakses halaman Administrator, silahkan lakukan Login terlebih dahulu pada formulir berikut ini:
    
    <form method="post" action="<?php echo _net; ?>/sign/">
			&nbsp;<br />
			<table cellspacing="0" cellpadding="4" style="margin:auto;text-align:left">
				<tr>
					<td>Username</td>
					<td><input size="50" id="loginusername" name="username" type="text" class="inputbox" value="" /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input size="50" name="password" type="password" class="inputbox" value="" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Login" name="s1" class="button" /></td>
				</tr>
			</table>		
		</form>
		&nbsp;<br />
		Bila Anda bukan Administrator, silahkan <a href="<?php echo _relative; ?>">Klik di Sini</a>
</div>
<script type="text/javascript">
	setOnload(function(){ getID('loginusername').focus(); });
</script>
<?php
	return;
}
elseif (!$_SESSION['loginid']){
        $nameurl=parse_url($_SERVER["HTTP_REFERER"]);
      if ($_POST['username']&&$_POST['password']){
          $u=strtolower($_POST['username']);
          $p=md5($_POST['password']);
          $row=$db->sql("SELECT * FROM `"._p."_user` WHERE `username`='{$u}' AND `password`='{$p}'");
          if ($row){
            $_SESSION['loginid']=$row['username'];
            $_SESSION['loginidnomor']=$row['id'];
            $_SESSION['loginname']=$row['name'];
            $_SESSION['logindata']=$row;
            $_SESSION['loginadmin']=$row['admin'];
            header("location:"._net."/admin/");
            exit();
          }
          else{
            ?>
            <script type="text/javascript">
              alert('Username dan Password tidak Cocok!');
              location='<?php echo _net; ?>/sign/in/admin';
            </script>
            <?php
              exit();
          }
      }
}
else{
  unset($_SESSION['loginid']);
  unset($_SESSION['loginidnomor']);
  unset($_SESSION['loginname']);
  unset($_SESSION['logindata']);
  unset($_SESSION['loginadmin']);
  $db->query("DELETE FROM `"._p."_cart` WHERE `sessid`='".session_id()."'");
  session_destroy();
  $nameurl=parse_url($_SERVER["HTTP_REFERER"]);
    header("location:"._relative);
  exit();
}

header('location:'._relative);
exit();
?>
