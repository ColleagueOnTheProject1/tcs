<?php
$caption = $_GET["caption"];
$ids = $_GET["ids"];
$result = mysql_query("DELETE FROM `main` WHERE id IN ($ids)");
if($result)
	echo "complete DELETE FROM `main` WHERE id IN ($ids)";
else echo mysql_error();
?>