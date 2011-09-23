
<center><table width="510" border="0">
  <tr>
    <td align="center">Enlightenment Progress:<br /> <font>This Shows You Your Current Progress When you reach on of the markers this shows you can ascend to the next level with the ascend button. </td>
  </tr>
  <tr>
    <td align="center"><img src="progress/prog.gif" width="480" height="50"><img src="modules/progress.php?prog=<?= $_SESSION["progress"]; ?>" width="480" height="11" /> </td><td align="left" valign="bottom"><font><?= (float)($_SESSION["progress"]/2)."%"; ?></font></td>
  </tr>
</table>
</center>
