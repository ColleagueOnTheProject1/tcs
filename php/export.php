<?php
function get_dump($db,$tables) {

	if(is_array($tables)) {
		$fp = fopen("../dump.sql","w");

		$text = "-- SQL Dump
-- my_ version: 1.234
--
-- База дынных: `Без имени`
--
-- ---------------------------------------------------
-- ---------------------------------------------------
";
		fwrite($fp,$text);

		foreach($tables as $item) {

				$text = "
--
-- Структура таблицы - ".$item."
--
";
		fwrite($fp,$text);


			$text = "";

			$sql = "SHOW CREATE TABLE ".$item;
			$result = mysql_query($sql);
			if(!$result) {
				exit(mysql_error($db));
			}
			$row = mysql_fetch_row($result);

			$text .= "\n".$row[1].";";
			fwrite($fp,$text);

			$text = "";
			$text .=
			"
--
-- Dump BD - tables :".$item."
--
			";

			$text .= "\nINSERT INTO `".$item."` VALUES";
			fwrite($fp,$text);

			$sql2 = "SELECT * FROM ".$item."`";
			$result2 = mysql_query($sql2);
			if(!$result2) {
				exit(mysql_error($db));
			}
			$text = "";

			for($i = 0; $i < mysql_num_rows($result2); $i++) {
				$row = mysql_fetch_row($result2);

				if($i == 0) $text .= "(";
					else  $text .= ",(";

				foreach($row as $v) {
					$text .= "\"".mysql_real_escape_string($v)."\",";
				}
				$text = rtrim($text,",");
				$text .= ")";

				if($i > 10) {
					fwrite($fp,$text);
					$text = "";
				}

			}
			$text .= ";\n";
			fwrite($fp,$text);


		}
		fclose($fp);
	}

}?>
