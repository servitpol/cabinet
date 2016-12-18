<?php
/* старт сессии; проверка авторизирован ли пользователь, если нет - отправляет на страницу авторизации */
session_start();
$iduser = $_SESSION['iduser'];

/* подключение к БД и получение всех полей пользователя */
include_once('connect.php'); 
connect();
$result = mysql_query('SELECT * FROM Users WHERE id = '.$iduser);
$row = mysql_fetch_array($result);

if (($_POST['pass1']) == ($_POST['pass2']) && md5($_POST['oldpass']) == $row['pass']) {
	
	$password = $_POST['pass1'];
	$newpass = md5($password);
	$result = mysql_query('UPDATE Users SET pass = "'.$newpass.'" WHERE id = '.$iduser);
	
	/* формируем письмо для отправки */
	$to = $row['email'];
	$subject = 'Замена пароля на тестовом сайте'; 					//загаловок письма
	$message = '
			<html>
				<head>
					<title>'.$subject.'</title>
				</head>
				<body>
					<p>Вы изменили пароль на сайте localhost//cabinet</p>
					<p>Ваш логин(e-mail): '.$row['email'].'</p>
					<p>Новый пароль: '.$password.'</p>                        
				</body>
			</html>'; 												//текст сообщения
			
	$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 		//кодировка письма
	$headers .= "From: Отправитель <smartinetseo@gmail.com>\r\n"; 	//наименование и почта отправителя
	mail($to, $subject, $message, $headers); 						//отправка письма с помощью функции mail

}

/* обработчик сброса пароля */
if(isset($_POST['reset_email'])) {
	
	$to = trim(htmlspecialchars($_POST['reset_email']));
	
	/* генерируем новый пароль */
	$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; 
	$max = 10; 
	$size= StrLen($chars) - 1; 
	$password = null; 
	while($max--){
		$password .= $chars[rand(0,$size)];
	}
	$passmd5 = md5($password); 										//хешируем перед записью в БД
	echo $passmd5;
	/* обновляем пароль пользователя в бд и отправляем ему письмо с новым паролем */
	connect();
	$result = mysql_query('UPDATE Users SET pass = "'.$passmd5.'" WHERE email = "'.$_POST['reset_email'].'"');
	
	$subject = 'Сброс пароля на тестовом сайте'; 					
	$message = '
			<html>
				<head>
					<title>'.$subject.'</title>
				</head>
				<body>
					<p>Вы запросили сброс пароля на сайте localhost//cabinet</p>
					<p>Ваш логин(e-mail): '.$to.'</p>
					<p>Новый пароль: '.$password.'</p>                        
				</body>
			</html>'; 
	$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 
	$headers .= "From: Отправитель <smartinetseo@gmail.com>\r\n";
	mail($to, $subject, $message, $headers);
}
?>