<?php
	include_once('connect.php');
	
	$ct1 ='create table Roles(
		id int not null auto_increment primary key,
		role varchar(32) unique	
	)default charset="utf8"';
		
	$ct2 ='create table Users(
		id int not null auto_increment primary key,		
		email varchar(128) unique,
		pass varchar(1024),
		name varchar(64),
		lastname varchar(64),
		address varchar(256),
		imgpath varchar(256),
		lastdate datetime,
		roleid int,
		foreign key(roleid) references Roles(id) on delete cascade
	)default charset="utf8"';
	
	connect();
	mysql_query($ct2);
	$err = mysql_errno();
	if($err){
		echo "Error: ".$err.'</br>';
	}


?>