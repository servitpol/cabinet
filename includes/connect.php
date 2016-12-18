<?php
function connect($host = "servitsj.beget.tech", $user = "servitsj_cabinet", $pass = "123456", $dbname = "servitsj_cabinet") {
	
	$link = mysql_connect($host, $user, $pass) or die('Connection error');
	mysql_select_db($dbname) or die ('db select error');
	mysql_query('set names "utf8"');
}

?>