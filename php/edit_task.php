<?php
$query = "UPDATE `main` SET `caption` = '".$_GET["caption"]."',`priority` = '".$_GET["priority"]."', `description` = '".$_GET["description"]."' WHERE `id` = ".$_GET["id"];
$result = mysql_query($query);
if($result)
	echo "complete";
else echo mysql_error();
?>