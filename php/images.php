<?php
function saveImages(){
	global $response;
	$t = time();
	$s = '';
	if(!isset($_FILES['uploadfile'][0])){
		$ext = substr($_FILES['uploadfile']['name'], -4);
		$s = $t.$ext;
		copy($_FILES['uploadfile']['tmp_name'],"../images/".$s);

	}else
	for($i = 0; $i < count($_FILES['uploadfile']); $i++){
		$s .= $t.'_'.$i.';';
		copy($_FILES['uploadfile'][$i]['tmp_name'],"images/".$t.'_'.$i);
	}
	$response['data'] = $s;
}
?>