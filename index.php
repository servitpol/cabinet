<?php 
/* старт сессии; подключение к бд */
session_start();
include_once('includes/connect.php'); 
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
	<div class="wrapper center">
		
		<!-- вкладки форм -->
		<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#panel1">Authorization</a></li>
		<li><a data-toggle="tab" href="#panel2">Registration</a></li>
		<li><a data-toggle="tab" href="#panel3">Reset your password</a></li>
		</ul>
		
		<div class="tab-content">
			<!-- авторизация -->
			<div id="panel1" class="tab-pane fade in active">
			<form method="post" action="index.php">
			<div class="input-group">
			<span class="input-group-addon "><span class="glyphicon glyphicon-user"></span></span>
			<input type="email" name="auth_email" class="form-control" placeholder="e-mail" required autofocus />
			</div>
			
			<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
			<input type="password" name="auth_pass" class="form-control" placeholder="Pass" required />
			</div>
			
			<button type="submit" class="btn btn-labeled btn-success"> 
			<span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>Sign in</button>
			</form>
			</div>
			
			<!-- регистрация -->
			<div id="panel2" class="tab-pane fade">
			<form method="post" action="index.php">
			<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-gift"></span></span>
			<input type="email" name="reg_email" class="form-control" placeholder="e-mail" required />
			</div>
			
			<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
			<input type="password" name="reg_pass" class="form-control" placeholder="Password" required />
			</div>
			
			<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
			<input type="password" name="reg_pass_r" class="form-control" placeholder="Confirm Password" required />
			</div>
			
			<button type="submit" name="registration" class="btn btn-labeled btn-primary">
			<span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>Registration</button>
			</form>
			</div>
			
			<!-- сброс пароля -->
			<div id="panel3" class="tab-pane fade">
			<form method="post" id="form" class="form-horizontal">
			<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-gift"></span></span>
			<input type="email" name="reset_email" class="form-control" placeholder="e-mail" required />
			</div>
			
			<button type="submit" class="btn btn-labeled btn-success"> 
			<span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>Forgot your password?</button>
			<div id="reset"></div>
			</form>
			</div>
		</div>
	</div><!-- wrapper -->
	</div><!-- row -->
</div><!-- container -->

<!-- сброс пароля через ajax -->
<script type="text/javascript">
function funcBefore(){								//функция ожидания ответа от обработчика
$("#reset").html('<span class="label label-warning">Processing request...</span>');
}

function funcSucces(response) {                     //функция вывода результата обработчика
$("#reset").html('<span class="label label-success">Your password has been sent to your primary email address.</span>');				
}

$(document).ready( function(){  					//привязка к событию на submit формы
$("#form").submit(function() {
var form_data = $(this).serialize();
$.ajax({
url: "includes/changepass.php",                     //обработчик
type: "POST",                                
data: form_data,            						//что посылаем через ajax                        
beforeSend: funcBefore,                      		//вызов ф-ции ожидания ответа    
success: funcSucces                          		//вызов ф-ции вывода результата
});

return false;										//возвращает false чтоб не перегружалась страница и был виден результат
});                                
}); 

</script>

<?php
/* авторизация */
if (isset($_POST['auth_email']) && isset($_POST['auth_pass'])){

	$email = trim(htmlspecialchars($_POST['auth_email']));
	$pass = md5($_POST['auth_pass']);

	connect();
	$result = mysql_query("SELECT * FROM Users WHERE `email` = '$email' AND `pass` = '$pass'");
	$row = mysql_fetch_array($result);

	if ($row[0] == ''){
		echo '<div class="request">';	
		echo "Error!";
		echo '</div>';
	} else {        
		$_SESSION['iduser'] = $row['id'];            
		echo '<script>document.location="profile.php"</script>';
	}
}

/* регистрация */
if (isset($_POST['reg_email']) && (($_POST['reg_pass']) == ($_POST['reg_pass_r']))){

	$email = trim(htmlspecialchars($_POST['reg_email']));
	$pass = md5($_POST['reg_pass']);

	connect();
	$result = mysql_query("INSERT INTO Users (email,pass,roleid) VALUES ('$email','$pass', '2')");
	$result = mysql_query("SELECT id FROM Users WHERE `email` = '$email'"); //вытаскиваем id зарегистрированного пользователя чтоб передать его в сессии
	$row = mysql_fetch_array($result);

	if ($result = 'true'){
		$_SESSION['iduser'] = $row['id'];            
		echo '<script>document.location="profile.php"</script>';
	}else{
		echo '<div class="request">'; 
		echo "Error!";
		echo '</div>'; 
	}
}
?>
</body>
</html>	
