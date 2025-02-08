<?php
include_once("../config.php");
$s = new Game();

if (!$s->loggedIn)
{
?>
<form method="post" action="index.php">
<table border="0">
 <tr>
  <td align="center" valign="middle"><label for="username"><font color="black">Username:</font></label></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="text" name="user" id="username" required /></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><label for="hpname"><font color="black">Home Planet Name:</font></label></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="text" name="hpname" id="hpname" required /></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><label for="password"><font color="black">Password:</font></label></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="password" name="pass" id="password" required /></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><label for="email"><font color="black">E-mail Address:</font></label></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="email" name="email" id="email" required /></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><label for="race"><font color="black">Race:</font></label></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><select name="rid" id="race" required>
    <?php
		$list = $s->getRaces();
		for ($x = 0; $x < count($list); $x++)
		{
			echo "<option value='".$list[$x]["id"]."'>".$list[$x]["name"]."</option>\r\n";
		}
	?>
  </select></td>
 </tr>
 <tr>
  <td>
    <strong><font>
      Please enter the string shown in the image in the form.<br> The possible characters are letters from A to Z in capitalized form and the numbers from 0 to 9.
    </font></strong>
  </td>
 </tr>
 <tr>
  <td align="center" colspan="2"><input name="number" type="text" id="number" required></td>
 </tr>
 <tr>
  <td colspan="2" align="center"><img src="image.php?mt=<?= microtime();?>"></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="submit" name="submit" value="Register" /></td>
 </tr>
</table>
</form>
<?php
}
else
{
	echo "You are Logged in, You cannot register another account as it's against the rules.";
}
?>