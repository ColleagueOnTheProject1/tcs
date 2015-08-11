<?php
$result = mysql_query("SELECT * FROM $users_table");
$arr = array();
while($data = mysql_fetch_assoc($result)){
	$arr[] = $data;
}
$response['auth'] = json_encode($arr);
?>