<?php 
/* старт сессии; проверка авторизирован ли пользователь, если нет - отправляет на страницу авторизации */
session_start();
$iduser = $_SESSION['iduser'];
if($iduser == NULL) {
echo '<script>document.location="index.php"</script>';
exit;
}

/* подключение к БД и запись в переменные значений всех полей */
include_once('includes/connect.php'); 
connect();
$result = mysql_query('UPDATE Users SET lastdate = NOW() WHERE id = '.$iduser);
$result = mysql_query('SELECT * FROM Users WHERE id = '.$iduser);
$row = mysql_fetch_array($result);
$email = $row['email'];
$url = $row['imgpath'];
$roleid = $row['roleid'];
$name = $row['name'];
$lname = $row['lastname'];
$address = $row['address'];
$lastdate = $row['lastdate'];

/* действия при нажатии на "exit" */
if (isset($_GET['exits'])) {
session_destroy();
echo '<script>document.location="index.php"</script>'; 
exit;
}

/* обработка запроса на смену данных пользователя */
if(isset($_POST['change'])){

	if($_POST['email'] != NULL && $_POST['email'] != $email) {
	$email = trim(htmlspecialchars($_POST['email']));
	}
	if($_POST['address'] != NULL && $_POST['address'] != $address) {
	$address = trim(htmlspecialchars($_POST['address']));
	}
	if($_POST['lname'] != NULL && $_POST['lname'] != $lname) {
	$lname = trim(htmlspecialchars($_POST['lname']));
	}
	if($_POST['name'] != NULL && $_POST['name'] != $name) {
	$name = trim(htmlspecialchars($_POST['name']));
	}

	connect();
	$result = mysql_query('UPDATE Users SET name = "'.$name.'", lastname = "'.$lname.'", address = "'.$address.'", 
	email = "'.$email.'" WHERE id = '.$iduser);

}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Тестовое задание</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="style/style.css">
</head>

<body>
<div class="container">
	<div class="row">
	<div class="wrapper">
		
		<header class="col-lg-12">
		<div class="col-lg-2"><a href="?exits"> Exit </a></div>
		<div class="col-lg-10">Last connection: <?php echo $lastdate; ?></div>
		</header>
		
		<div class="col-lg-4">

<?php
/* обработка и добавление аватара пользователя на сервер */
if(isset($_POST['upfile'])) {
	
	if(is_uploaded_file($_FILES['fn']['tmp_name'])){
	move_uploaded_file($_FILES['fn']['tmp_name'], "img/".$_FILES['fn']['name']);
	$url = 'img/'.$_FILES['fn']['name'];
	
	connect();
	$result = mysql_query('UPDATE Users SET imgpath = "'.$url.'" WHERE id = '.$iduser);
	}
}

/* картинка по умолчанию */
if($url == NULL) {
	echo '<img src="avatar.gif" width="200" />';
	} else {
	echo '<img src="'.$url.'" width="200" />';
}
?>
		<form action="profile.php" method="POST" enctype="multipart/form-data">
			<div class="form-group">
			<label for="fn">Select photo:</label>
			<input type="file" name="fn" accept="image/jpeg, image/png, image/gif">
			</div>	
			<button type="submit" name="upfile">Send</button>
		</form>
		</div>
		
		<div class="col-lg-8">
		<form id ="profile" method="post" action="profile.php" class="form-horizontal">
			
			<div class="form-group">
			<label class="col-sm-2 control-label">Name</label>
			<div class="col-xs-4">
			<input type="text" name="name" class="form-control" value="<?php echo $name; ?>" />
			</div>
			</div>
			
			<div class="form-group">
			<label class="col-sm-2 control-label">Last name</label>
			<div class="col-xs-4">
			<input type="text" name="lname" class="form-control" value="<?php echo $lname; ?>" />
			</div>
			</div>
			
			<div class="form-group">
			<label class="col-sm-2 control-label">Address</label>
			<div class="col-xs-4">
			<input type="text" name="address" class="form-control" value="<?php echo $address; ?>" />
			</div>
			</div>
<?php
if($roleid == 1) {
echo '<div class="form-group">';
echo '<label class="col-sm-2 control-label">E-mail</label>';
echo '<div class="col-xs-4">';
echo '<input type="text" name="email" class="form-control" size="10" placeholder="E-mail" value="'.$row['email'].'" />';
echo '</div>';
echo '</div>';
}
?>
		</form>
		<a id="link1" href="#">Change password</a>
		<div id="content1" style="display: none;">
		<form method="post" id="form" class="form-horizontal">
			<div class="form-group">
			<label class="col-sm-2 control-label">Old password</label>
			<div class="col-xs-4">
			<input type="password" name="oldpass" class="form-control" size="10" required/>
			</div>
			</div>
			
			<div class="form-group">
			<label class="col-sm-2 control-label">New pass</label>
			<div class="col-xs-4">
			<input type="password" name="pass1" class="form-control" size="10"  required/>
			</div>
			</div>
			
			<div class="form-group">
			<label class="col-sm-2 control-label">Confirm new pass</label>
			<div class="col-xs-4">
			<input type="password" name="pass2" class="form-control" size="10" required />
			</div>
			</div>
			
			<div class="form-group">
			<div class="col-sm-offset-2 col-xs-4">
			<button type="submit" class="btn" >Change</button>
			<div id="information"></div>
			</div>
			</div>
		</form>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-2 col-xs-4">
			<input type="submit" name="change" form="profile" value="Save" class="btn btn-primary" />
			</div>
		</div>
		</div><!-- end <div class="col-lg-8"> -->
		
<!-- изменение пароля через ajax -->
<script type="text/javascript">
function funcBefore(){							//функция ожидания ответа от обработчика
$("#information").html('<span class="label label-warning">Идет обработка запроса</span>');
}

function funcSucces(response) {                 //функция вывода результата обработчика
$("#information").html('<span class="label label-success">Пароль успешно изменен и выслан Вам на почту</span>');				
}

$(document).ready( function(){  				//привязка к событию на submit формы
	$("#form").submit(function() {
	var form_data = $(this).serialize();
		$.ajax({
		url: "includes/changepass.php",         //обработчик
		type: "POST",                                
		data: form_data,            			//что посылаем через ajax                        
		beforeSend: funcBefore,                	//вызов ф-ции ожидания ответа    
		success: funcSucces                		//вызов ф-ции вывода результата
		});

	return false;								//возвращает false чтоб не перегружалась страница и был виден результат
	});                                
}); 
</script>

<!-- реализация всплывающего diva с формой смены пароля, взята из интернета-->
<script type="text/javascript">
$(document).ready(function () {
	$('a#link1').click(function (e) {
	$(this).toggleClass('active');
	$('#content1').toggle();

	e.stopPropagation();
	});

	$('#content1').click(function (e) {
	e.stopPropagation();
	});

	$('body').click(function () {
	var link = $('a#link1');
	if (link.hasClass('active')) {
	link.click();
	}
	});
});
</script>

</div><!-- wrapper -->
</div><!-- row -->
</div><!-- container -->
</body>
</html>	