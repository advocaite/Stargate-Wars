<?php
include("../config.php");

$pagegen = new page_gen();
$pagegen->round_to = 4;
$pagegen->start();
$s = new Game();
$s->updatePower($_SESSION['userid']); 

if ($_GET['id'] && $_GET['atype'] != "Send") {
    $query = "SELECT `uname` FROM `users` WHERE uid = ? LIMIT 1";
    $stmt = $s->db_link->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_object();
    $name = $data->uname;
    $id = $_GET['id'];
}

if ($_REQUEST['atype'] == "Send") {
    if ($s->sendMessage($_GET['id'], $_REQUEST['subject'], $_REQUEST['message'])) {
        echo "Message Sent to " . htmlspecialchars($_REQUEST['toUser'], ENT_QUOTES, 'UTF-8');
    } else {
        echo "Error Sending Message Contact Admin";
    }
}
?>
<form action="javascript:void(0)" onSubmit="submit.value='Sending Message'; submit.disabled=true; sendData('sendmessage','post',userID.value,'Send');">
<center>
Sending to: <input type="text" disabled="disabled" name="toUser" id="toUser" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" onkeyup="autocomplete(this,event);">
<input type="hidden" id="userID" name="userID" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
<table width="100%" border="0">
  <tr>
    <td align="left" valign="top">Subject:</td>
    <td colspan="3" align="left" valign="top"><input type="text" name="subject"></td>
  </tr>
  <tr>
    <td align="left" valign="top">Message:</td>
    <td colspan="3" align="left" valign="top">
      <p>:
        <textarea name="message" cols="100" rows="20" wrap="virtual"></textarea>
        <br>
      </p>
    </td>
  </tr>
</table>
<input type="submit" name="submit" id="submit" value="Send Message">
</center>
</form>
<?php
echo "Query Count: " . $s->queryCount . "<br>";
$pagegen->stop();
print('page generation time: ' . $pagegen->gen());
?>