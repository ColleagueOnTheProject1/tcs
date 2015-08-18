<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require_once 'config.php';
//нет локина или пароля - уходим отсюда
if(!isset($_POST['login']) || !isset($_POST['password']))
	exit(json_encode(array('auth_error' => 'auth_error')));
$response = array();
include 'base.php';
$user;
init();
//не найден пользователь - уходим отсюда
if(!isset($user['id']) || !isset($user['type']))
	exit(json_encode(array('auth_error' => 'auth_error')));
if($_POST["action"] == "auth"){
	$response['action'] = 'get_tasks';
	getTasks();
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