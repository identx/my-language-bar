<?php
function engine_mail($subject,$body,$email,$from){
	$headers="MIME-Version: 1.0\r\n";
	$headers.="Content-type: text/html; charset=utf-8\r\n";
	$headers.="From: ".iconv("UTF-8","WINDOWS-1251","«Автосалон19.рф»")." <".$from.">\r\n";
	mail($email,$subject,$body,$headers);
};

require("../include/phpmailer/class.phpmailer.php"); // укажите путь к файлу class.phpmailer.php
require("../include/phpmailer/class.smtp.php"); // укажите путь к файлу class.phpmailer.php


function sendmail_ ($subject, $body, $email) {
// function sendmail_ ($subject, $body, $email='badmkl@yandex.ru') {
		$from="avtolombard19@magneex.ru";	
		$mail = new PHPMailer();
		$mail->IsSMTP(); // отсылать используя SMTP
		// $mail->Host     = "smtp.mail.ru"; // SMTP сервер
		$mail->Host     = "smtp.yandex.ru"; // SMTP сервер
		$mail->SMTPAuth = true;     // включить SMTP аутентификацию
		$mail->Username = "avtolombard19@magneex.ru";  // SMTP username
		$mail->Password = "GRU54rvu123tut"; // SMTP password
		$mail->CharSet = "utf-8";
		$mail->SMTPSecure = "ssl"; 
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->Port = 465; 
		$mail->From     = $from; // укажите от кого письмо
		$mail->FromName = $from; // имя отправителя
		$mail->AddAddress("$email",""); // е-мэил кому отправлять
		$mail->AddReplyTo($from,"Info"); // е-мэил того кому прейдет ответ на ваше письмо
		$mail->WordWrap = 50;// set word wrap
		$mail->IsHTML(true);// отправить в html формате
		$mail->Subject  =  "$subject"; // тема письма
		$mail->Body     =  "$body"; // тело письма в html формате
		$mail->AltBody  =  ""; // тело письма текстовое

		if(!$mail->Send())
		{
		echo "Mailer Error: " . $mail->ErrorInfo;
		return false;
		} else
		return true;
}

?>