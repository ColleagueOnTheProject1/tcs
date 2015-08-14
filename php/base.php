<?php
//подключаемся к хосту, затем к базе, или возвращаем ошибки
//подключаемся к хосту
function hostConnect(){
	global $response;
	global $config;
	$connect = mysql_connect($config['host'], $config['user'], $config['pass']);
	if(!$connect){
		$response['action'] = 'connect_error';
		$response['error_text'] = mysql_error($connect);
		exit();
	}
}
//подключаемся к базе
function baseConnect() {
	if(!mysql_select_db($config['base'])) {
		$response['action'] = 'base_error';
		$response['error_text'] = mysql_error();
		exit();
	}//else mysql_query("SET NAMES 'utf8'");
}
//создаем таблицу пользователей
function userCreate(){
	$query = 'CREATE TABLE `$users_table` (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`login` VARCHAR(32) NOT NULL,
		`password` VARCHAR(32) NOT NULL,
		`type` TINYINT(4) NOT NULL,
		`tasks` VARCHAR(255) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE INDEX `login` (`login`));';
	mysql_query($query);
}
//создаем таблицу задач
/*
function tasksCreate(){
	$query = 'CREATE TABLE `$tasks_table` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`owner_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`title` VARCHAR(64) NOT NULL,
	`text` TEXT NOT NULL,
	`priority` TINYINT(3) UNSIGNED NOT NULL,
	`images` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`));';
	mysql_query($query);
}*/
//создаем базу
function baseCreate(){
		$connect = mysql_connect($config['host'], $config['user'], $config['pass']);
		$query = "CREATE DATABASE `".$config['base']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$result = mysql_query($query);
		mysql_select_db($config['base']);
		userCreate();
		tasksCreate();
}
function getTasks(){
	$result = mysql_query("SELECT * FROM $tasks_table");
	global $response;
	while($data = mysql_fetch_assoc($result)){
		$response[] = $data;
	}
}
function getUsers(){
	$result = mysql_query("SELECT * FROM $users_table");
	global $response;
	while($data = mysql_fetch_assoc($result)){
		$response[] = $data;
	}
}
$tasks_table = "tasks";
$users_table = "users";

//получаем задачи
if($_GET['action']=='get_tasks'){
	hostConnect();
	baseConnect();
	getTasks();
//получаем данные пользователей
}elseif($_GET['action']=='get_users'){
	hostConnect();
	baseConnect();
	getUsers();
}
/*создаем базу и парочку таблиц*/
elseif($_GET["action"] == "base_create")
	baseCreate();
?>