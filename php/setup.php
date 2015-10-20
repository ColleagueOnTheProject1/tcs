<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require_once 'config.php';
$response = array();
include 'base.php';
if($config['isset'] == 0){
	baseCreate();
	$config['isset'] = 1;
	updateConfig();
	echo "база с таблицами создана!";
}
?>