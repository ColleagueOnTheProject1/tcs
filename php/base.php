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
function userTableCreate(){
	global $config;
	$query = "CREATE TABLE `".$config['users_table']."` (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`login` VARCHAR(32) NOT NULL,
		`password` VARCHAR(32) NOT NULL,
		`type` TINYINT(4) NOT NULL,
		`tasks` VARCHAR(255) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE INDEX `login` (`login`));";
	mysql_query($query);
	//создаем тестовых пользователей
	if($config['test_users']==1){
		$query = "INSERT INTO `".$config['users_table']."` (`id`, `login`, `password`, `type`, `tasks`) VALUES (1, 'vladimir', 'pas1', 1, '1,2,3');";
		mysql_query($query);

	}
}
//создаем таблицу групп
function groupTableCreate(){
	global $config;
	$query="CREATE TABLE `".$config['groups_table']."` (
	`id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(64) NULL DEFAULT NULL,
	`description` VARCHAR(255) NULL DEFAULT NULL,
	`users` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`));";
	mysql_query($query);
}
//
function tasksTableCreate(){
	global $config;
	$query = "CREATE TABLE `".$config['tasks_table']."` (
	`id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(64) NULL DEFAULT NULL,
	`text` TEXT NULL,
	`owner` INT(11) UNSIGNED DEFAULT '0',
	`images` VARCHAR(255) NULL DEFAULT NULL,
	`comment` TEXT NULL,
	`lead_time` TIME NULL DEFAULT NULL,
	`priority` INT(11) UNSIGNED DEFAULT '0',
	`state` INT(11) UNSIGNED DEFAULT '0',
	PRIMARY KEY (`id`))";
	mysql_query($query);
	if($config['test_tasks']==1){
		$query="";
		mysql_query($query);
	}
}
//создаем базу
function baseCreate(){
		global $config;
		$connect = mysql_connect($config['host'], $config['user'], $config['pass']);
		$query = "CREATE DATABASE `".$config['base']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$result = mysql_query($query);
		mysql_select_db($config['base']);
		userTableCreate();
		groupTableCreate();
		tasksTableCreate();
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
?>