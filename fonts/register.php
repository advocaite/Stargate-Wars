<?php
//page title
$pagetitle="Register";
//end title

require("db_connect.php");
require("functions/register2.php");

if( isset($_POST['register']) ){
	//Set vars
	$username = trim($_POST['username']);
	$email = trim($_POST['email']);
	$pass = trim($_POST['password']);
	$passconfirm = trim($_POST['passconfirm']);
	
	//check for input errors
	if( empty($username) ){
		$message = "You did not choose a username";
	} else if( mysql_num_rows(mysql_query("SELECT userid FROM user_users WHERE username='$username'")) > 0 ){
		$message = "Someone already has that username";
	} else if( empty($email) ){
		$message = "You did not give an email";
	} else if( !validateEmail($email) ){
		$message = "You did not give an email";
	} else if( empty($pass) ){
		$message = "You did not enter a password";
	} else if( empty($passconfirm) ){
		$message = "You must confirm your password";
	} else if( $pass != $passconfirm ){
		$message = "Your passwords do not match";
	}else if($_POST['tos']!=1){
		$message = "Please Agree To The Terms Of Service";
	}else if($_POST['rules']!=1){
		$message = "Please Agree To The Rules";
	}else if($_POST['cheat']!=1){
		$message = "Please Agree To Not Cheat";
	}else if($_POST['account']!=1){
		$message = "Please Agree This Account Is Not A Multi";
	}
     else {
		//we made it this far, everything is spot on.
		$encpass = md5($pass);
		$timenow = time();
		$rid=$_POST['race'];
		$sql = mysql_query("INSERT INTO `user_users` SET username='$username',password='$encpass',email='$email',datejoined='$timenow', `raceID`='$rid'");
		
		if( $sql ){
		  $to = $email;
   $from = "admin@war-of-ages.com";
   $title = "Welcome To War of Ages Wars";
$body = "
Dear ".$username.",

This email confirms your signup at War of Ages
below is your login information. if you have
any questions or comments please email us at
Admin@war-of-ages.com.

Username: ".$username."
Password: ".$pass."
E-Mail:   ".$email."

Thank you,
WOA Admin Team

";
   $success = mail($to,$title,$body,
              "From:$from\r\nReply-To:Admin@war-of-ages.com");
			header("Location: index.php?strErr=Your Account Has Successfully Been Created. An Email Has Been Sent To The Email Adress Provided.Please Read It for further instructions");
			
		} else {
			$message=mysql_error();
		}
	}
}
include("includes/header.php");
?>
<html>
<head>
</head>
<body>
<form action="register.php" method="post">
<table width="50%" border="0" cellspacing="1" cellpadding="5" class="table_lines">
  <tr>
    
    <td colspan=2 align=center class="header"><strong>Registration</strong></td>
  </tr>
  <?php if($message!=""){ ?><tr><td align=center colspan=2><font color=red><?php echo($message); ?></font></td></tr><?php } ?>
  <tr>
    <td><div align="left">Username:</div></td>
    <td><input type="text" name="username" value="<?php echo $_POST['username'];?>"></td>
  </tr>
  <tr>
    <td><div align="left">Email:</div></td>
    <td><input type="text" name="email" value="<?php echo $_POST['email'];?>"></td>
  </tr>
  <tr>
    <td><div align="left">Password:</div></td>
    <td><input type="password" name="password"></td>
  </tr>
  <tr>
    <td><div align="left">Confirm Password: </div></td>
    <td><input type="password" name="passconfirm"></td>
  </tr>
  <tr>
    <td><div align="left">Race:</div></td>
    <td><label>
      <select name="race">
	  <?php
	  $races=getRaces();
	  echo($races);
	  ?>
      </select>
    </label></td>
  </tr>
  <tr>
    <td><div align="left">Commander</div></td>
    <td>none [this is changeable ingame] </td>
  </tr>
  <tr>
    <td colspan=2><table width="100%" align="center" cellpadding="0" cellspacing="0" class="table_lines">
      <tr>
        <td colspan="2"><input name="tos" value="1" type="checkbox">
          I have read and agree to comply with the <a href="http://www.ultimate-battle-online.com/tos.php" target="_new">terms of service</a> </td>
      </tr>
      <tr>
        <td colspan="2"><input name="rules" value="1" type="checkbox">
          I have read and agree to comply with the <a href="http://www.ultimate-battle-online.com/help.php#rules" target="_new">rules</a> </td>
      </tr>
      <tr>
        <td colspan="2"><input name="cheat" value="1" type="checkbox">
          I promise not to try to gain an unfair            advantage by breaking the rules </td>
      </tr>
      <tr>
        <td colspan="2"><input name="account" value="1" type="checkbox">
          This is my <strong>ONLY</strong> game account</td>
      </tr>
    </table>    </td>
  </tr>
  <tr>
  <Td colspan=2><div align="center">
    <input type="submit" name="register" value="Register">  
  </div></Td>
  </tr>
</table>
</form>

<br>
<?php include("includes/bottom.php"); ?>

</body>
</html>