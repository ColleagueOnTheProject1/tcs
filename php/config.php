<?php
//читаем файл конфигурации
$config_file = '../config.ini';
$config = parse_ini_file($config_file);
//пишем в файл конфигурации
if($_GET['name'] == "set_config"){
	foreach($_GET as $key=>$value){
		if(isset($config[$key]))
			$config[$key] = $value;
	}
	include "ini_ex.php";
	change_ini_file($config_file, $config);// скидываем измененную информацию в ini файл
}
?>