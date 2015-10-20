<?php
$caption = $_GET["caption"];
$priority = $_GET["priority"];
$result = mysql_query("INSERT INTO `main` (`caption`,`priority`) VALUES('$caption','$priority')");
if($result)
	echo "complete";
else echo mysql_error();
?>