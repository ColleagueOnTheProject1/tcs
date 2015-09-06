<?php
function saveImages(){
	var $t = time();
	var $s = '';
	for($i = 0; $i < count($_FILES['uploadfile']); $i++){
		$s .= $t.'_'.$i.';';
		copy($_FILES['uploadfile'][$i]['tmp_name'],"images/".$t.'_'.$i);
	}
	echo $s;
}
?>