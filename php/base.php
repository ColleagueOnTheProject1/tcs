﻿<?php
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
		`type` TINYINT(4) NOT NULL DEFAULT '2',
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
	`users` VARCHAR(255) NOT NULL DEFAULT '',
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
	`owner_task` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`title` VARCHAR(64) NULL DEFAULT 'новая задача',
	`text` TEXT NULL,
	`owner` INT(11) UNSIGNED DEFAULT '0',
	`assigned` VARCHAR(32) NOT NULL DEFAULT '',
	`images` VARCHAR(255) NULL DEFAULT NULL,
	`comment` TEXT NULL,
	`start_time` INT(10) UNSIGNED DEFAULT '0',
	`end_time` INT(10) UNSIGNED DEFAULT '0',
	`lead_time` TIME NULL DEFAULT '0',
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
	$query = "SELECT * FROM `".$config['tasks_table']."` WHERE `state` != 5";
	$result = mysql_query($query);
	$data = array();
	while($row = mysql_fetch_assoc($result)){
		$data[] = $row;
	}
	$response['data'] = $data;
}
//получаем список завершенных задач
function getCompleteTasks(){
	global $response, $user, $config;
	$query = "SELECT * FROM `".$config['tasks_table']."` WHERE state=5";
	$result = mysql_query($query);
	$data = array();
	while($row = mysql_fetch_assoc($result)){
		$data[] = $row;
	}
	$response['data'] = $data;
}
//добавить задачу
function addTask(){
	global $config;
	$query = "INSERT INTO `".$config['tasks_table']."` (`id`) VALUES (".time().");";
	$result = mysql_query($query);
}
//сохранить задачу
function saveTask(){
	global $config;
	$fields = Array(0=>'title', 1=>'text',2=>'priority',3=>'images',4=>'assigned',5=>'state',6=>'comment');
	//если приоритет наивысший, то переписать наивысший приоритет другого задания на высокий.
	if($_POST['last_comment'])
		$_POST['comment'] = '\n'.date('d.m.y в H:i').'\n'.$_POST['last_comment'].'\n'.$_POST['comment'];
	if($_POST['priority'] == 3){
		$query = "UPDATE `".$config['tasks_table']."` SET `priority`=2 WHERE `priority`=3 LIMIT 1;";
		$result = mysql_query($query);
	}

	$query = "UPDATE ".$config['tasks_table']." SET ";
	for($i = 0; $i < count($fields); $i++){
		$query.=$fields[$i]."='".$_POST[$fields[$i]]."',";
	}
	if($_POST['state'] == 1){//состояние-начать
		$query.= "start_time=".time().",";
	}
	elseif($_POST['state'] == 2 || ($_POST['state'] == 3 && $_POST['old_state'] == 1)){//состояние-остановить или вернуть владельцу
		//$query.="lead_time=lead_time + ".time()." - start_time,";
		$query.="lead_time=ADDTIME(lead_time, SEC_TO_TIME(".time()." - start_time)),";
	}
	elseif($_POST['state'] == 5 && $_POST['id']){//состояние-завершить
		//увеличиваем количесво завершенных задач пользователя на 1
		$query = "UPDATE `".$config['users_table']."` SET finished=finished+1 WHERE `login`='".$_POST['assigned']."';";
		$result = mysql_query($query);
		//$query = "DELETE FROM `".$config['tasks_table']."` WHERE  `id`=".$_POST['id'].";";
		$query = "UPDATE `".$config['tasks_table']."` SET `state`=5, `end_time`=".time()." WHERE `id`=".$_POST['id'].";";
		$result = mysql_query($query);
		return;
	}
	$query = substr($query,0,-1)." WHERE id=".$_POST['id'].";";
	$result = mysql_query($query);
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
//добавляем группу
function groupAdd(){
	global $response, $user, $config;
	$query = "INSERT INTO `".$config['groups_table']."` (`id`,`title`,`description`) VALUES (".time().", '".$_POST['title']."','".$_POST['description']."');";
	$result = mysql_query($query);
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
//добавить пользователя
function userAdd(){
	global $response, $user, $config;
	$query = "INSERT INTO `".$config['users_table']."` (`id`,`login`,`password`) VALUES (".time().", '".$_POST['login']."','".$_POST['password']."');";
	$result = mysql_query($query);
}
//
function usersRemove(){
	global $response, $user, $config;
	$query = "DELETE FROM `".$config['users_table']."` WHERE  `login` IN(".$_POST['users'].");";
	$result = mysql_query($query);
}
//возвращает информацию список пользователей, задач и логинов
function getInfo(){
	global $response, $user, $config;
	$data = array();
	$query = "SELECT `id` FROM ".$config['tasks_table'].";";
	$result = mysql_query($query);
	$s = '';
	while($row = mysql_fetch_assoc($result)){
		$s .= $row['id'].',';
	}
	$data['tasks'] = substr($s, 0, - 1);
	$query = "SELECT `login` FROM ".$config['users_table'].";";
	$result = mysql_query($query);
	$s = '';
	while($row = mysql_fetch_assoc($result)){
		$s .= $row['login'].',';
	}
	$data['users'] = substr($s, 0, - 1);
	$query = "SELECT `id` FROM ".$config['groups_table'].";";
	$result = mysql_query($query);
	$s = '';
	while($row = mysql_fetch_assoc($result)){
		$s .= $row['id'].',';
	}
	$data['groups'] = substr($s, 0, - 1);
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