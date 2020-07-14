<?php
	session_start(); 	
	header("Content-Type: text/html; charset=utf-8");
	include('../../../include/fns_db.php');
	include('../config.php');
	$d=(isset($_CONFIG['dir']))?$_CONFIG['dir']:'/';
	$_cp=explode($_CONFIG['dir'],__DIR__); $_cp=$_cp[count($_cp)-2];
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	db_connect();
	
	$sql="SELECT `id`,`name`,DATE_FORMAT(`date`,'%d.%m.%Y') as `date` FROM `$dbase` ORDER BY `id` DESC LIMIT 0, $conf[lastRecords];";
	$req=db_query($sql);
	if ($req!=false) {
		$response['status']='ok';
		$response['result']=$req;
	} else {
		$response['status']='false';
	}
	db_disconnect();
	echo json_encode($response);
	//ыыы
?>
