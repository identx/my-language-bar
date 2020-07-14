<?php
session_start(); $_cp=explode('/',__FILE__); 
$_cp=$_cp[count($_cp)-3];
if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
include('../config.php');
include('../../../include/fns_db.php');
header("Content-Type: text/html; charset=utf-8");
/*Получение префикса БД*/
global $_CONFIG;
$dbp=$_CONFIG['dbprefix'];
$dbase=$dbp.'users';
/**/
$conn=db_connect();
switch ($_POST['mode_h']){
	case "changereq":
		$response['status']=false;
		$name=$_POST['name'];
		$email=$_POST['mail'];
		$curpswd=$_POST['curpswd'];
		$newpswd=$_POST['newpswd'];
		$newpswd2=$_POST['newpswd2'];
		$response=Array();
		$user_id=$_SESSION['m_user']['id'];
		$SQL="SELECT `password` FROM `$dbase` WHERE `id`='$user_id'";
		$pass=current(db_query($SQL,$conn));
		if ($curpswd!=''){
			if(md5($curpswd)==$pass['password']){
				$response['status']=true;
			} else {
				$response['error']='Текущий пароль введен неверно!';
				echo json_encode($response);
				db_disconnect($conn);
				exit;
			}
		}
		if($newpswd!=$newpswd2){
			$response['status']=false;
			$response['error']='Введенные пароли не совпадают!';
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		$newpswd=(md5($curpswd)==$pass['password'])?",`password`='".md5($newpswd)."'":'';
		$SQL="UPDATE `$dbase` SET `name`='$name',`email`='$email'".$newpswd." WHERE `id` = '$user_id'";
		$quer=db_query($SQL,$conn);
		if($quer){
			$response['status']=true;
			$response['msg']="Данные обновлены";

		} else {
			$response['error']='Данные не обновлены';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		echo json_encode($response);
		db_disconnect($conn);
		exit;
	break;
	case "adduser":
		$response['status']=false;
		$newlogin=$_POST['newlogin'];
		$newpass=md5($_POST['newpass']);
		$newpass2=md5($_POST['newpass2']);
		$newname=$_POST['newname'];
		$newemail=$_POST['newemail'];
		$response=Array();
		$user_id=$_SESSION['m_user']['id'];
		if(($_POST['newlogin']=='')||($_POST['newpass']=='')||($_POST['newpass2']=='')){
			$response['error']='Введены не все данные';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		if($newpass!=$newpass2){
			$response['error']='Пароли не совпадают';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		$SQL="SELECT `login` FROM `$dbase` WHERE `login`='$newlogin'";
		$logcheck=db_query($SQL);
		if ($logcheck){
			$response['error']='Логин уже занят';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		$newname=($newname!='')?$newname:'';
		$newemail=($newemail!='')?$newemail:'';
		$SQL="INSERT INTO `$dbase` (`id`,`login`,`password`,`rights`,`name`,`email`) VALUES (NULL,'$newlogin','$newpass','1','$newname','$newemail')";
		$query=db_query($SQL);
		if($query){
			$response['status']=true;
			$response['msg']="Данные добавлены";

		} else {
			$response['error']='Ошибка добавления';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		echo json_encode($response);
		db_disconnect($conn);
		exit;
	break;
	case "show_user_par":
		$response['status']=false;
		$user_id=$_POST['id'];
		$SQL="SELECT * FROM `$dbase` WHERE `id`='$user_id'";
		$info=current(db_query($SQL));
		$rights=$info['rights'];
		$first=$rights&1;
		$response['first']=$first;
		$idsp=array();
		for ($i=0;($rights>0)&&($i<32);$i++){
			if($rights&1){
				$idsp[]=$i;
			}
			$rights=$rights>>1;
		}
		$response['idsp']=$idsp;
		if($info){
			$response['status']=true;
			$response['msg']="Пользователь загружен";
			$response['u_name']=$info['name'];
			$response['u_mail']=$info['email'];
		} else {
			$response['error']='Ошибка';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		
		echo json_encode($response);
		db_disconnect($conn);
		exit;
	break;
	case "change_user_req":
		$response['status']=false;
		$rednewpass=$_POST['rednewpass'];
		$rednewpass2=$_POST['rednewpass2'];
		$redname=$_POST['redname'];
		$redemail=$_POST['redemail'];
		$iduser=$_POST['userid'];
		if (isset($_POST['idsp'])){
			$idsp=$_POST['idsp'];
		}
		if (($rednewpass!='')||($rednewpass2!='')){
			if($rednewpass!=$rednewpass2){
				$response['status']=false;
				$response['error']='Введенные пароли не совпадают!';
				echo json_encode($response);
				db_disconnect($conn);
				exit;
			}
		}
		$redname=($redname!='')?$redname:'';
		$redemail=($redemail!='')?$redemail:'';
		$password=md5($rednewpass);
		$password=(($rednewpass!='')&&($rednewpass2!='')&&($rednewpass==$rednewpass2))?" ,`password`='$password'":"";
		$rights=$_POST['first']?0:1;
		if (isset($idsp)){
			foreach ($idsp as $one){
				$rights|=1<<$one;
			}
		}
		$SQL="UPDATE `$dbase` SET `name`='$redname', `email`='$redemail',`rights`='$rights' $password WHERE `id`='$iduser'";
		$quer=db_query($SQL);
		if($quer){
			$response['status']=true;
			$response['msg']="Данные обновлены";
		} else {
			$response['error']='Данные не обновлены';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		echo json_encode($response);
		db_disconnect($conn);
		exit;
	break;
	case "del_user_req":
		$response['status']=false;
		$iduser=$_POST['userid'];
		if($iduser=='1'){
			$response['error']='Нельзя удалить!';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		$SQL="DELETE FROM `$dbase` WHERE `id`='$iduser'";
		$quer=db_query($SQL);
		if($quer){
			$response['status']=true;
			$response['msg']="Пользователь удален";

		} else {
			$response['error']='Ошибка удаления';
			$response['status']=false;
			echo json_encode($response);
			db_disconnect($conn);
			exit;
		}
		echo json_encode($response);
		db_disconnect($conn);
		exit;
	break;
}


?>