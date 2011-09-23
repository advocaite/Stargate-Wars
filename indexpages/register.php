<?
include_once("../config.php");
$s = new Game();

if (!$s->loggedIn)
{
?>
<form method="post" action="index.php">
<table border="0">
 <tr>
  <td align="center" valign="middle"><font color="black">Username:</font></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="text" name="user" /></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><font color="black">Home Planet Name:</font></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="text" name="hpname" /></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><font color="black">Password</font></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="password" name="pass" /></td>
 </tr>
  <tr>
  <td align="center" valign="middle"><font color="black">E-mail Address:</font></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><input type="text" name="email" /></td>
 </tr>
  <tr>
  <td align="center" valign="middle"><font color="black">Race</font></td>
 </tr>
 <tr>
  <td align="center" valign="middle"><select name="rid">
    <?
		$list = $s->getRaces();
		for ($x = 0; $x < count($list); $x++)
		{
			echo "<option value='".$list[$x]["id"]."'>".$list[$x]["name"]."</option>\r\n";
		}
	?>
  </select></td>
 </tr><tr><Td>
 <strong><font>
Please enter the string shown in the image in the form.<br> The possible characters are letters from A to Z in capitalized form and the numbers from 0 to 9.
</font></strong></td></tr><tr><td align="center" colspan=2><input name="number" type="text" id=\"number\"></td></tr>
<tr><td colspan=2 align="center"><img src="image.php?mt=<?= microtime();?>"></td></tr>
 <tr>
  <td align="center" valign="middle"><input type="submit" name="submit" value="Register" /></td>
 </tr>
</table>
<?
}
else
{
	echo "You are Logged in, You can register another account its against rules";
}

?>