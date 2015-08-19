<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require_once 'config.php';
//нет локина или пароля - уходим отсюда
if(!isset($_POST['login']) || !isset($_POST['password'])){
	if(!$_COOKIE['login'] || !$_COOKIE['passsword'])
		exit(json_encode(array('action' => 'connect')));
}elseif(!$_COOKIE['login'] || !$_COOKIE['passsword']){
	setcookie('login', 'password', time() + 2629743);//срок хранения 1 месяц
}
//если пришел логин с паролем, берем их, иначе берем из cookie
if(isset($_POST['login']) && isset($_POST['password'])){
	$login = $_POST['login'];
	$password = $_POST['password'];
}elseif($_COOKIE['login'] && $_COOKIE['passsword']){
	$login = $_COOKIE['login'];
	$password = $_COOKIE['password'];
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