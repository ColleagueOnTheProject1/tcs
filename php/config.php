<?php
//читаем файл конфигурации
$config_file = '../config.ini';
$config = parse_ini_file($config_file);
//пишем в файл конфигурации
function updateConfig(){
	include "ini_ex.php";
	global $config, $config_file;
	change_ini_file($config_file, $config);// скидываем измененную информацию в ini файл
}
function postInConfig(){
	global $config, $config_file;
	foreach($_GET as $key=>$value){
		if(isset($config[$key]))
			$config[$key] = $value;
	}
	include "ini_ex.php";
	change_ini_file($config_file, $config);// скидываем измененную информацию в ini файл
}
?>