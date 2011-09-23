<html>
	<head>
		<title>{TITLE} - Access Levels</title>
	</head>
	<body>
		<table>
		<th>Access Name</th><th>Access?</th><th>Level Needed</th>
		<?php global $q;
		while($val = mysql_fetch_assoc($q)) { ?>
		<tr><td><?= $val['aname'];?></td><td><?= (($s->isAllowed($val['alevel'])) ? "Yes" : "No") ?></td><td><?= $val['alevel'];?></td></tr>
		<?php } ?>
		</table>
	</body>
</html>