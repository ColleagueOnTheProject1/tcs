<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require_once 'config.php';
$login = '';
$password = '';
//если пришел логин с паролем, берем их, иначе берем из cookie
if(isset($_POST['login']) && isset($_POST['password'])){
	$login = $_POST['login'];
	$password = $_POST['password'];
	setcookie('login', $_POST['login'], time() + 2629743,'/');//срок хранения 1 месяц
	setcookie('password', $_POST['password'], time() + 2629743,'/');//срок хранения 1 месяц
}elseif(isset($_COOKIE['login']) && isset($_COOKIE['password'])){
	$login = $_COOKIE['login'];
	$password = $_COOKIE['password'];
}else{
	exit(json_encode(array('action' => 'connect')));
}
$response = array();
include 'base.php';
$user;
connect();
init();
//не найден пользователь - уходим отсюда
if(!isset($user['id']) || !isset($user['type']))
	exit(json_encode(array('action' => 'auth', 'user_id'=>$user['id'], 'user_type'=>$user['type'])));

if($_POST["action"] == "auth"){
	$response['action'] = 'get_tasks';
	getTasks();
}elseif($_POST["action"] == "get_tasks"){
	$response['action'] = 'get_tasks';
	getTasks();
}elseif($_POST["action"] == "get_users"){
	$response['action'] = 'get_users';
	getUsers();
}

/*
if(defined('JSON_NUMERIC_CHECK'))
	echo json_encode($response, JSON_NUMERIC_CHECK);
else*/
	echo json_encode($response);
?>