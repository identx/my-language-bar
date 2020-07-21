<?php
	header('Content-Type: text/html; charset=utf-8');
	include_once("../include/fns_mailer.php");	
	error_reporting(0);
	$MAILLIST = array('super.rain-gain@yandex.ru');
	
	$res=array('status'=>0);
	$a=$_POST;

	$ob=array('name', 'fname', 'age', 'language', 'level', 'decs', 'phone', 'skype', 'email');

	foreach($ob as $o) if(!isset($a[$o])||$a[$o]=='') $err='заполните все обязательные поля';

	if(strlen($err)>0)
		$res['error']=$err;
	else {		
		$head='Форма обратной связи на сайте mylanguagebar';
		$text='
		<h3>Форма обратной связи</h3>
		<p>
		Имя: <b>'.strip_tags($a['name']).'</b><br />
		Фамилия: <b>'.strip_tags($a['fname']).'</b><br />
		Возраст: <b>'.strip_tags($a['age']).'</b><br />
		Язык: <b>'.strip_tags($a['language']).'</b><br />
		Уровень владения языком: <b>'.strip_tags($a['level']).'</b><br />';
		if(isset($a['course'])||$a['course'] !='') {
			$text.='Курс: <b>'.strip_tags($a['course']).'</b><br />';
		}
		$text .= '
		Описание: <b>'.strip_tags($a['decs']).'</b><br />
		Телефон: <b>'.strip_tags($a['phone']).'</b><br />
		Скайп: <b>'.strip_tags($a['name']).'</b><br />
		Почта: <b>'.strip_tags($a['name']).'</b><br />
		</p>';
		foreach($MAILLIST as $mail)
		$res['status']=sendmailer($head,$text,$mail);
	};
	exit(json_encode($res));
?>
