<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require_once 'config.php';
$response = array();
include 'base.php';
if($_POST["action"] == "auth"){
	include "auth.php";
}
/*
if($_GET["name"] == "get_tasks"){
	include "get_tasks.php";
}elseif($_GET["name"] == "add_task"){
	include "add_task.php";
}elseif($_GET["name"] == "remove_tasks"){
	include "remove_tasks.php";
}elseif($_GET["name"] == "edit_task"){
	include "edit_task.php";
}*/
echo json_encode($response);
?>