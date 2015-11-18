<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require_once 'config.php';

$login = '';
$password = '';
if(isset($_POST['host'])&&isset($_POST['user'])&&isset($_POST['password'])&&isset($_POST['base'])){
	$config['host'] = $_POST['host'];
	$config['user'] = $_POST['user'];
	$config['password'] = $_POST['password'];
	$config['base'] = $_POST['base'];
	updateConfig();
}
//если пришел логин с паролем, берем их, иначе берем из cookie
if(isset($_POST['login']) && isset($_POST['password'])&& $_POST['action']=="auth"){
	$login = $_POST['login'];
	$password = $_POST['password'];
	setcookie('login', $_POST['login'], time() + 2629743,'/');//срок хранения 1 месяц
	setcookie('password', $_POST['password'], time() + 2629743,'/');//срок хранения 1 месяц
}elseif(isset($_COOKIE['login']) && isset($_COOKIE['password'])){//если пароль с логином в кукисах
	$login = $_COOKIE['login'];
	$password = $_COOKIE['password'];
}else{
	include 'base.php';
	connect();
	exit(json_encode(array('action' => 'auth')));
}
include 'base.php';
connect();
$response = array();
$user=null;
init();
//не найден пользователь - уходим отсюда
if(!isset($user['id']) || !isset($user['type']))
	exit(json_encode(array('action' => 'auth', 'user_id'=>$user['id'], 'user_type'=>$user['type'])));
if($_POST["action"] == "auth" || $_POST["action"] == "connect"){
	$response['action'] = 'get_tasks';
	getInfo();
	getTasks();
}elseif($_POST["action"] == "get_tasks"){
	$response['action'] = 'get_tasks';
	getInfo();
	getTasks();
}elseif($_POST["action"] == "get_users"){
	$response['action'] = 'get_users';
	getUsers();
}elseif($_POST["action"] == "save_images"){
	include 'images.php';
	$response['action'] = 'task_image';
	saveImages();
}elseif($_POST["action"] == "save_task"){
	$response['action'] = 'get_tasks';
	saveTask();
	getTasks();
}
elseif($_POST["action"] == "get_info"){
	$response['action'] = 'get_info';
	getInfo();
}elseif($_POST["action"] == "add_task"){
	$response['action'] = 'get_tasks';
	addTask();
	getTasks();
}elseif($_POST["action"] == "add_user"){
	$response['action'] = 'get_users';
	userAdd();
	getUsers();
}elseif($_POST["action"] == "remove_users"){
	$response['action'] = 'get_users';
	usersRemove();
	getUsers();
}elseif($_POST["action"] == "get_complete_tasks"){
	$response['action'] = 'get_complete_tasks';
	getCompleteTasks();
}elseif($_POST["action"] == "get_groups"){
	$response['action'] = 'get_groups';
	getGroups();
}elseif($_POST["action"] == "add_group"){
	$response['action'] = 'get_groups';
	groupAdd();
	getGroups();
}
/*
if(defined('JSON_NUMERIC_CHECK'))
	echo json_encode($response, JSON_NUMERIC_CHECK);
else*/
	echo json_encode($response);
?>