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
		exit(json_encode($response));
	}
}
//подключаемся к базе
function baseConnect() {
	global $config;
	if(!mysql_select_db($config['base'])) {
		$response['action'] = 'base_error';
		$response['error_text'] = mysql_error();
		exit(json_encode($response));
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
//создает таблицы, если их нет
function tablesCreate(){

}
//получает информацию о пользователе, если таблиц нет, создает их
function getInfo(){
	global $response;
	$query = 'SELECT * FROM `$users_table` WHERE `login` = "'.$_POST['login'].'";';	
	$result = mysql_query($query);	
	if (mysql_num_rows($result) == 0) {
		$response['action'] = 'auth_error';
		$response['error_text'] = mysql_error($connect);
	} else
		$response[] = mysql_fetch_assoc($result);
}
$tasks_table = "tasks";
$users_table = "users";
$response['status'] = "ok";
hostConnect();
baseConnect();

//получаем задачи
if($_POST['action']=='get_info'){	
	getInfo();	
}
//получаем задачи
elseif($_POST['action']=='get_tasks'){
	getTasks();
//получаем данные пользователей
}elseif($_POST['action']=='get_users'){
	getUsers();
}
/*создаем базу и парочку таблиц*/
elseif($_POST["action"] == "base_create")
	baseCreate();
?>