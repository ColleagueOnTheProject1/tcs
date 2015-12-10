<?php
//подключаемся к хосту, затем к базе, или возвращаем ошибки
//подключаемся к хосту
function hostConnect(){
	global $config,$connect;
		$connect = @mysql_connect($config['host'], $config['user'], $config['password']);
	if(!$connect){
		return false;
	}
	return true;
}
//подключаемся к базе
function baseConnect($base = null) {
	global $config;
	if(!$base){
		$base = $config['base'];
	}
	if(!mysql_select_db($base)) {
		return false;
	}else {
		mysql_query("SET NAMES 'utf8'");
	}
	return true;
}
//создаем базу
function baseCreate(){
		global $config;
		$query = "CREATE DATABASE `".$config['base']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$result = mysql_query($query);
}
//создаем таблицы
function tablesCreate(){
	//если нет таблиц, то создаем их
	$query = "SHOW TABLES;";
	$result = mysql_query($query);
    if(mysql_num_rows($result) == 0){
		usersTableCreate();
		groupsTableCreate();
		tasksTableCreate();
	}
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
		(0, 'без категории', 'задачи которые не попали в другие группы', ''),
		(1, 'сайтостроение', 'все задачи по сайтам', '1,2,3')";
		mysql_query($query);
	}
}
//
function tasksTableCreate(){
	global $config;
	$query = "CREATE TABLE `".$config['tasks_table']."` (
	`id` INT(10) UNSIGNED NOT NULL,
	`type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 - новинка, 1- улучшение, 2 - ошибка, 3 - тест, 100 - другое',
	`group` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`title` VARCHAR(64) NULL DEFAULT 'новая задача',
	`text` TEXT NULL,
	`owner` INT(11) UNSIGNED DEFAULT '0',
	`assigned` VARCHAR(32) NOT NULL DEFAULT '',
	`images` VARCHAR(255) NULL DEFAULT NULL,
	`comment` TEXT NOT NULL,
	`last_comment` TEXT NOT NULL,
	`start_time` INT(10) UNSIGNED DEFAULT '0',
	`end_time` INT(10) UNSIGNED DEFAULT '0',
	`lead_time` TIME NULL DEFAULT '0',
	`priority` INT(11) UNSIGNED DEFAULT '0',
	`state` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '0 - не начата, 1 - начата, 2 - приостановлена, 3 - на проверке, 4 - переоткрыта, 5- закрыта',
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

//получаем массив задач
function getTasks($ids=null){
	global $response, $user, $config,$login;
	if(!$ids)
		$ids = $user['id'];
	$query = "SELECT * FROM `".$config['tasks_table']."` WHERE true";
	if(isset($_POST['filters'])){
		$filters = explode(',', $_POST['filters']);
		if($filters[0]!= 'all'){//пользователь
			$query .= " AND `assigned` = '".$filters[0]."'";
		}
		if($filters[1]!= 'all'){//группа
			$query .= " AND `group` = ".$filters[1];
		}
		if($filters[2]!= '9'){//состояние
			$query .= " AND `state` = '".$filters[2]."'";
		}else{
			$query .= " AND `state` != 5";
		}
		if($filters[3]!=127){//тип задачи
			$query .= " AND `type` = ".$filters[3];
		}
	}else $query .= " AND `state` != 5";
	$query .= " ORDER BY `id` DESC;";
	$result = mysql_query($query);
	$data = array();
	if($result){
		while($row = mysql_fetch_assoc($result)){
			$data[] = $row;
		}
	}
	$response['data'] = $data;
	$c_arr = Array();
	for($i = 0 ; $i < 6; $i++){
		$c_arr[$i] = 0;
	}
	$query = "SELECT `state`,COUNT(*) FROM `".$config['tasks_table']."` WHERE `assigned`='$login' GROUP BY `state`;";
	$result = mysql_query($query);
	while($row = mysql_fetch_row($result)){
		$c_arr[$row[0]] = $row[1];
	}
	$response['u_task_count'] = $c_arr;
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
	global $config, $login;
	if($_POST['assigned']==''){
		$_POST['assigned'] = $login;
	}

	$query = "INSERT INTO `".$config['tasks_table']."` (`id`,`group`,`assigned`) VALUES (".time().",".$_POST['group'].",'".$_POST['assigned']."');";
	$result = mysql_query($query);
}
//сохранить задачу
function saveTask(){
	global $config, $login;
	$fields = Array(0=>'title', 1=>'text',2=>'priority',3=>'images',4=>'assigned',5=>'state',6=>'type', 7=>'group');
	if($_POST['last_comment']){
		$_POST['last_comment'] = $login.'\n'.date('d.m.y в H:i').'\n'.$_POST['last_comment'].'\n';
	}
	//если приоритет наивысший, то переписать наивысший приоритет другого задания на высокий.
	if($_POST['priority'] == 3){
		$query = "UPDATE `".$config['tasks_table']."` SET `priority`=2 WHERE `priority`=3 AND `assigned`='".$_POST['assigned']."' LIMIT 1;";
		$result = mysql_query($query);
	}

	$query = "UPDATE ".$config['tasks_table']." SET ";
	for($i = 0; $i < count($fields); $i++){
		$query.="`".$fields[$i]."`='".$_POST[$fields[$i]]."',";
	}
	if($_POST['last_comment'] == '' && $_POST['state'] != 4){//сменили состояние задачи, но задача не была переоткрыта
		$query.= "`comment`=CONCAT(`last_comment`, `comment`), `last_comment`='',";
	}else{//если прислали комментарий
		$query.="`comment`='".$_POST['comment']."', `last_comment`=CONCAT('".$_POST['last_comment']."\n',`last_comment`),";
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
//сохранить данные группы
function saveGroup(){
	global $config;
	$fields=Array('title','description');
	$s = "";
	for($i=0;$i<count($fields);$i++){
		if(isset($_POST[$fields[$i]])){
			if($i > 0){
				$s .=",";
			}
			$s .="`".$fields[$i]."`='".$_POST[$fields[$i]]."'";
		}
	}
	$query = "UPDATE `".$config['groups_table']."` SET ".$s." WHERE id=".$_POST['id'].";";
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
	$response['groups'] = $data;
}
//добавляем группу
function groupAdd(){
	global $response, $user, $config;
	$query = "INSERT INTO `".$config['groups_table']."` (`id`,`title`,`description`) VALUES (".time().", '".$_POST['title']."','".$_POST['description']."');";
	$result = mysql_query($query);
}
function getUsers(){
	global $response, $user, $config, $user;
	$query = "SELECT * FROM ".$config['users_table'].";";
	$result = mysql_query($query);
	$data = array();
	while($row = mysql_fetch_assoc($result)){
		$data[] = $row;
	}
	if($user['type'] > 0){
		for($i = 0; $i < count($data); $i++){
			//unset($data[$i]['password']);
			$data[$i]['password'] = "";
		}
	}
	$response['data'] = $data;
}
//добавить пользователя
function userAdd(){
	global $response, $user, $config;
	if(preg_match("/[^A-Za-zА-Яа-я_]/",$_POST['login']) || strlen($_POST['login']) < 4 || strlen($_POST['login']) > 32){
		$response['error'] = 1;
		exit(json_encode($response));
	}

	$query = "INSERT INTO `".$config['users_table']."` (`id`,`login`,`password`) VALUES (".time().", '".$_POST['login']."','".$_POST['password']."');";
	$result = mysql_query($query);
	if(!$result){
		$response['error'] = 2;
		exit(json_encode($response));
	}
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
	//получаем пользователей
	$query = "SELECT `login` FROM ".$config['users_table'].";";
	$result = mysql_query($query);
	$s = '';
	while($row = mysql_fetch_assoc($result)){
		$s .= $row['login'].',';
	}
	$data['users'] = substr($s, 0, - 1);
	$response['get_info'] = $data;
}
//подключаемся к хосту и к базе
function connect(){
	global $response, $config;
	$data = Array();
	$data['host'] = $config['host'];
	$data['user'] = $config['user'];
	$data['password'] = $config['password'];
	$data['base'] = $config['base'];
	if(isset($_POST['create_base'])){
		$data['create_base'] = $_POST['create_base'];
	}else{
		$data['create_base'] = 0;//не создавать базу
	}
	$data['host_connect'] = 1;
	$data['base_connect'] = 1;
	//подключаемся к серверу
	if(!hostConnect()){
		$data['host_connect'] = 0;
	}else//подключаемся к базе
	if(!baseConnect()){
		if($data['create_base'] == 1){
			baseCreate();
			if(!baseConnect()){
				$data['host_connect'] = 1;
				$data['base_connect'] = 0;
			}else{
				tablesCreate();
			}
		}else{
			$data['base_connect'] = 0;
		}
	}else{
		$query = "SHOW TABLES;";
		$result = mysql_query($query);
		if(!mysql_num_rows($result)){
			tablesCreate();
		}
	}
	if($data['base_connect'] == 0 || $data['host_connect'] == 0) {
		$response['connect'] = $data;
		exit(json_encode($response));
	}
}
//получает информацию о текущем пользователе для последующей обработки
function init(){
	global $user, $config, $login,$u_info, $password;
	$query = "SELECT * FROM ".$config['users_table']." WHERE `login`='$login' AND `password`='$password';";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0){
		$user = mysql_fetch_assoc($result);
	}
	else exit(mysql_num_rows($result));
}
//экспорт базы в файл
function export(){
	global $response, $config, $connect;
	include 'export.php';
	$query = "SHOW TABLES;";
	$result = mysql_query($query);
	$tables=Array();
    if(mysql_num_rows($result) != 0){
		while($row = mysql_fetch_row($result)){
			$tables[] = $row[0];
		}
	}
	get_dump($connect, $tables);
	$response['export'] = 'dump.sql';
}
//импорт базы с другого сервера
function importFS(){
	global $config;
	if(!isset($_POST['i_host'])||!isset($_POST['i_user'])||!isset($_POST['i_password'])||!isset($_POST['i_base'])){
		return;
	}
	$t_names = Array(0=>$config['tasks_table'],1=>$config['users_table'],2=>$config['groups_table']);
	$f_names = Array();
	for($i=0;$i<$t_names;$i++){
		//получаем имена полей наших таблиц
		$query = "SELECT COLUMN_NAME
		FROM information_schema.COLUMNS
		WHERE TABLE_SCHEMA = DATABASE()
		  AND TABLE_NAME = '".$t_names[$i]."'";
		$result = mysql_query($query);
		$f_names[] = Array();
		while($row = mysql_fetch_row($result)){
			$f_names[$i][] = $row[0];
		}
	}
	if($_POST['i_host'] != $config['host'] || $_POST['i_user'] != $config['user'] || $_POST['i_password'] != $config['password']){
		$connect2 = @mysql_connect($_POST['i_host'], $_POST['i_user'], $_POST['i_password']);
	}else $connect2 = $connect;

}
?>