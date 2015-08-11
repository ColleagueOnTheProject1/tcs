<?php
copy($_FILES['uploadfile']['tmp_name'],"images/".basename($_FILES['uploadfile']['name']));
echo basename($_FILES['uploadfile']['name']);
?>