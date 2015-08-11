<?php
//ini_set('display_errors','On');
//error_reporting(E_ALL);
//подключаемся к хосту, затем к базе, или возвращаем ошибки
function dataBaseConnect() {
	global $config;
	$connect = mysql_connect($config['host'], $config['user'], $config['pass']);
	if(!$connect){
		$config['error'] = 1;
		$config['error_text'] = mysql_error($connect);
		exit(json_encode($config));
	}
	if(!mysql_select_db($config['base'])) {
		$config['error'] = 2;
		$config['error_text'] = mysql_error();
		exit(json_encode($config));
	}else mysql_query("SET NAMES 'utf8'");
}
//читаем файл конфигурации
$config_file = '../config.ini';
$config = parse_ini_file($config_file);
//пишем в файл конфигурации
if($_GET['name'] == "config"){
	foreach($_GET as $key=>$value){
		if(isset($config[$key]))
			$config[$key] = $value;
	}
	include "ini_ex.php";
	change_ini_file($config_file, $config);// скидываем измененную информацию в ini файл
	exit();
}
/*создаем базу и парочку таблиц*/
if($_GET["name"] == "create_base"){
	$connect = mysql_connect($config['host'], $config['user'], $config['pass']);
	$query = "CREATE DATABASE `".$config['base']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result = mysql_query($query);
	mysql_select_db($config['base']);
	mysql_query("CREATE TABLE Users (id INT AUTO_INCREMENT, login CHAR, pass CHAR, PRIMARY KEY(id))");
	exit();
}

dataBaseConnect();
$table = "main";
$users_table = "users";
$response = array();
if($_GET["name"] == "auth"){
	include "auth.php";
}
if($_GET["name"] == "get_tasks"){
	include "get_tasks.php";
}elseif($_GET["name"] == "add_task"){
	include "add_task.php";
}elseif($_GET["name"] == "remove_tasks"){
	include "remove_tasks.php";
}elseif($_GET["name"] == "edit_task"){
	include "edit_task.php";
}
else echo '{error:"error"}';
?>