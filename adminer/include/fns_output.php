<?php
// @session_start();

function check_user(){
	global $dbp;
		if (isset($_POST['logout'])){
			session_destroy();
		}
		$sql="SELECT * FROM `".$dbp."users` WHERE `rights`=2147483647;";
		$res=db_query($sql);

		if(is_numeric(db_num_rows()) && (db_num_rows()>0)){
			if(isset($_POST['login'])){
				$sql="SELECT * FROM `".$dbp."users` WHERE `login`='$_POST[user_login]' && `password`=MD5('$_POST[user_password]') LIMIT 1";
				$res=db_getItem($sql);
				
				if (db_num_rows()>0){
				/////////////
					$_SESSION["m_user"]["id"]=$res['id'];
					$_SESSION["m_user"]["rights"]=$res['rights'];
					$_SESSION["m_user"]["name"]=$res['name'];
					
					$_SESSION['KCFINDER'] = array();
					$_SESSION['KCFINDER']['disabled'] = false;
				/////////////
				}else {
					echo form_login('Неверный логин или пароль');
					exit();
				}
			} else {
				echo form_login();
				exit();
			}
		} else {
			header('Location: include/install.php');
		}
}

function form_login($error=''){
	// header('Content-type: text/html; charset=utf-8');
	$res='<!DOCTYPE html><html>
	<head>
		<link type="text/css" href="css/style.css" media="screen"  rel="stylesheet"/>
		<title>Авторизация</title>
	</head>
	<body style="background:#fafafa;">
		<form action="" id="head_form" method="POST">
			<a href="http://magneex.ru" target="_blank"><img src="images/logo.png" /></a>
			<div>
				<span class="adduser_error">'.$error.'</span><br />
				<label>Логин</label><input type="text" name="user_login" class="inputs" id="login" value="" /><br />
				<label>Пароль</label><input type="password" name="user_password" class="inputs" id="pass" value="" /><br /><input type="submit" name="login" value="Вход">
			</div>
		</form>
	</body></html>';
	return $res;
}

function form_adduser($rights,$error=''){
	header('Content-type: text/html; charset=utf-8');
	$res='<!DOCTYPE html><html>
	<head>
		<link type="text/css" href="../css/style.css" media="screen"  rel="stylesheet"/>
		<title>Создание пользователя</title>
	</head>
	<body style="background:#fafafa;">
		<form action="" id="head_form" method="post">
			<div><span class="adduser_error">'.$error.'</span><br />
				<label>Логин</label><input type="text" name="user_login" value="'.$_POST[user_login].'" /><br />
				<label>Пароль</label><input type="password" name="user_password" value="" /><br />
				<label>Еще раз</label><input type="password" name="user_password2" class="inputs" id="pass" value="" /><br />
				<label>Имя</label><input type="text" name="user_name" class="inputs" value="'.$_POST[user_name].'" /><br />
				<label>E-mail</label><input type="text" name="user_email" class="inputs" value="'.$_POST[user_email].'" /><br />
				<label>Права</label><select name="user_rights">';
				foreach($rights as $one){
					$res.='<option value="'.$one[rights].'">'.$one[name].'</option>';
				}
				$res.='</select><input type="submit" name="login" value="Создать">
			</div>
		</form>
	</body></html>';
	return $res;
}

function output_header($plugins){
	header('Content-type: text/html; charset=utf-8');	
	$res.='
<!DOCTYPE html>
<html>
<head>
	<title>'.$_SESSION['madmin']['title'].'</title>
	<meta name="author" content="Magneex.ru" />
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Language" content="ru" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link type="text/css" href="sel2/css/select2.min.css" media="screen"  rel="stylesheet"/>
	<link type="text/css" href="css/style.css" media="screen"  rel="stylesheet"/>
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.mousewheel-3.0.6.pack.js"></script>
	<script type="text/javascript" src="sel2/js/select2.full.min.js"></script>
	';
	$ui='
	<script type="text/javascript" src="js/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.datepicker-ru.min.js"></script>
		 <link type="text/css" href="css/jquery-ui-1.9.2.custom.css" rel="stylesheet" />';
	$wysiwyg='
		<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="ckeditor/adapters/jquery.js"></script>
	';
	$res.=($_SESSION['user']['ui']==true)?$ui:'';
	$res.=($_SESSION['user']['wysiwyg']==true)?$wysiwyg:'';
	$res.=$_SESSION['madmin']['js'].'
</head>
<body>
<table id="site">
<tr>
	<td id="head" colspan="2">
		<a href="index.php"><img src="images/logo.png" /></a>
		<div id="form_logout"><form action="logout.php" method="POST"><b>'.$_SESSION["user"]["name"].'</b> <input type="submit" name="logout" value="Выход"></form></div>
	</td>
</tr>
<tr id="content">
	<td id="menu" >';
	foreach($plugins as $one)
		$res.='<a href="index.php?plugin='.$one['dir'].'">'.$one['name'].'</a>';	
	$res.='</td>
	<td id="cont">';
	// $_SESSION['madmin']['title']='';
	// $_SESSION['madmin']['js']='';
	return $res;
};

function output_footer(){
	$res.='
	</td>
</tr>
</table>

</body>
</html>
	';
	return $res;
};

?>
