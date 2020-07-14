<?php
	session_start(); 	
	header("Content-Type: text/html; charset=utf-8");
	include('../../../include/fns_db.php');
	include('../config.php');
	$d=(isset($_CONFIG['dir']))?$_CONFIG['dir']:'/';
	$_cp=explode($_CONFIG['dir'],__DIR__); $_cp=$_cp[count($_cp)-2];
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	$conn=db_connect();
	
	$sql="SELECT * FROM `$dbase_cats` ORDER BY `name` DESC";
	$req=db_query($sql);
	if ($req!=false) {
		$response['status']='ok';
		$response['result']=$req;
		$response['sql']=$sql;
	} else {
		$response['status']='false';
		$response['sql']=$sql;
	}
	db_disconnect();
	echo json_encode($response);
	//ыыы
?>
