<?php
//подключаемся к хосту, затем к базе, или возвращаем ошибки
//подключаемся к хосту
function hostConnect(){
	global $response;
	global $config;
	$connect = mysql_connect($config['host'], $config['user'], $config['pass']);
	if(!$connect){
		$response['action'] = 'connect';
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
	}else mysql_query("SET NAMES 'utf8'");
}
//создаем таблицу пользователей
function usersTableCreate(){
	global $config;
	$query = "CREATE TABLE `".$config['users_table']."` (
		`id` INT(11) UNSIGNED NOT NULL,
		`login` VARCHAR(32) NOT NULL,
		`password` VARCHAR(32) NOT NULL,
		`type` TINYINT(4) NOT NULL,
		`tasks` VARCHAR(255) NOT NULL,
		`finished` INT(11) UNSIGNED DEFAULT '0',
		PRIMARY KEY (`id`),
		UNIQUE INDEX `login` (`login`));";
	mysql_query($query);
	//создаем тестовых пользователей
	if($config['test_users']==1){
		$query = "INSERT INTO `".$config['users_table']."` (`id`, `login`, `password`, `type`, `tasks`) VALUES
		(0, 'admin', 'admin', 0, ''),
		(1, 'leader', '1234', 1, '1,2,3'),
		(2, 'executor1', '1234', 2, '1,2,3'),
		(3, 'executor2', '1234', 2, '1,2,3');";
		mysql_query($query);
	}
}
//создаем таблицу групп
function groupsTableCreate(){
	global $config;
	$query="CREATE TABLE `".$config['groups_table']."` (
	`id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(64) NULL DEFAULT NULL,
	`description` VARCHAR(255) NULL DEFAULT NULL,
	`users` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`));";
	mysql_query($query);
	if($config['test_groups']==1){
		$query = "INSERT INTO `".$config['groups_table']."` (`id`, `title`, `description`, `users`) VALUES
		(1, 'сайтостроение', 'все задачи по сайтам', '1,2,3'),
		(2, 'другие', 'остальные задачи', '1,2');";
		mysql_query($query);
	}
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
		$query="INSERT INTO `".$config['tasks_table']."` (`id`, `title`, `text`, `owner`) VALUES
		(1, 'задача 1', 'описание задачи 1', 0),
		(2, 'задача 2', 'описание задачи 2', 1),
		(3, 'задача 3', 'описание задачи 3', 0);";
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
		usersTableCreate();
		groupsTableCreate();
		tasksTableCreate();
}
//получаем массив задач
function getTasks($ids=null){
	global $response, $user, $config;
	if(!$ids)
		$ids = $user['id'];
	$query = "SELECT * FROM ".$config['tasks_table']."";
	$result = mysql_query($query);
	$data = array();
	while($row = mysql_fetch_assoc($result)){
		$data[] = $row;
	}
	$response['data'] = $data;
}
//получаем группы
function getGroups(){
	global $response, $user, $config;
	$query = "SELECT * FROM ".$config['groups_table'].";";
	$result = mysql_query($query);
	$data = array();
	while($row = mysql_fetch_assoc($result)){
		$data[] = $row;
	}
	$response['data'] = $data;
}
function getUsers(){
	global $response, $user, $config;
	$query = "SELECT * FROM ".$config['users_table'].";";
	$result = mysql_query($query);
	$data = array();
	while($row = mysql_fetch_assoc($result)){
		$data[] = $row;
	}
	$response['data'] = $data;
}
//подключаемся к хосту и к бузу
function connect(){
	hostConnect();
	baseConnect();
}
//получает информацию о текущем пользователе для последующей обработки
function init(){
	global $user, $config, $login, $password;
	$query = "SELECT * FROM ".$config['users_table']." WHERE `login`='$login' AND `password`='$password';";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0){
		$user = mysql_fetch_assoc($result);
	}
	else exit(mysql_num_rows($result));
}
?>