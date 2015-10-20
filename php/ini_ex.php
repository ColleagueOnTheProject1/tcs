<?
	function change_ini_file($file, $data){
		$array=file( $file);
		for($i = 0; $i < count($array); $i++){
			foreach($data as $key=>$value)
			if(strpos($array[$i], $key.'=') === 0){
				$array[$i] = $key.'='.$value."\r\n";
				break;
			}
		}
		file_put_contents($file, $array);
	}
?>