<?php
error_reporting(0);
require_once('fns_db.php');
$dbp=isset($_CONFIG['dbprefix'])?$_CONFIG['dbprefix']:'mgnx_';
require_once('fns_output.php');
header('Content-type: text/html; charset=utf-8');
db_connect();
$sql="SHOW TABLES LIKE '".$dbp."users';";
$res=db_query($sql);
if(db_num_rows()>0){
	$sql="SELECT `id` FROM `".$dbp."users` WHERE `rights`=2147483647";
	$res=db_query($sql);
	if(db_num_rows()>0){
		echo $sql.'<br />';
		die('Пользователь с правами администратора уже был создан ранее. <a href="../index.php">Авторизация</a>');
	}else{
		if(isset($_POST['user_login']))			
		if(strlen($_POST['user_login'])>=4 && strlen($_POST['user_password'])>=4){
			if ($_POST['user_password']==$_POST['user_password2']){
				$sql="INSERT INTO  `".$dbp."users` (`id` ,`login`,`password`,`rights`,`name`,`email`) VALUES ('', '$_POST[user_login]', MD5('$_POST[user_password]' ) , '$_POST[user_rights]', '$_POST[user_name]', '$_POST[user_email]');";
				db_query($sql);
				header('Location: ../index.php');
			} else $error='Пароль подтвержден неверно';
		} else {
			$error='Логин и пароль должны быт не менее 4х символов в длину';
		}
		echo form_adduser(Array(Array('name'=>'Администратор','rights'=>'2147483647')),$error);
	}
} else{
	$sql='CREATE TABLE IF NOT EXISTS `'.$dbp.'users` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`login` varchar(30) NOT NULL,
		`password` varchar(32) NOT NULL,
		`rights` int(11) NOT NULL,
		`name` varchar(30) NOT NULL,
		`email` varchar(40) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
	db_query($sql);
	$sql='CREATE TABLE IF NOT EXISTS `'.$dbp.'plugins` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) NOT NULL,
		  `options` int(11) NOT NULL DEFAULT "0",
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
	db_query($sql);
	$sql="SHOW TABLES LIKE '".$dbp."users';";
	$res=db_query($sql);
	if(db_num_rows()>0)
		header('Location: install.php');
	die('Ошибка создания таблицы пользователей<br />');
}
?>
