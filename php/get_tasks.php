<?php
$result = mysql_query("SELECT * FROM $table");
$arr = array();
$i = 0;
while($data = mysql_fetch_assoc($result)){
	$arr[$i] = $data;
	$arr[$i]["num"] = $i + 1;
	$i++;
}
echo json_encode($arr);
?>