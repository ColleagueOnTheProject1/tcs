<?php
setcookie('test', 'test', time()+30);
echo $_COOKIE['test'];
?>