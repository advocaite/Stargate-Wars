<?php
include("config.php");
$s = new Game();
if($_GET['val'])
{
	$query = "SELECT users.uname, userdata.uid, race.r_name as race, rank.overall as rank 
	          FROM users, race, userdata, rank 
	          WHERE uname LIKE ? 
	          AND userdata.uid = users.uid 
	          AND race.rid = userdata.rid 
	          AND rank.uid = userdata.uid 
	          ORDER BY rank";
	$stmt = $s->db_link->prepare($query);
	$searchVal = $_GET['val'] . '%';
	$stmt->bind_param("s", $searchVal);
	$stmt->execute();
	$q = $stmt->get_result();
	$str = "{ \"result\": [";
	while ($data = $q->fetch_object())
	{
		$str .= "[\"$data->uname\", \"$data->race\", \"$data->rank\",\"$data->uid\" ],";
	}
	$str .= "[]]}";
	echo $str;
}
?>