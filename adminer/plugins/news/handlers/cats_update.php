<?php
	session_start(); 	
	header("Content-Type: text/html; charset=utf-8");
	include('../../../include/fns_db.php');
	include('../config.php');
	$d=(isset($_CONFIG['dir']))?$_CONFIG['dir']:'/';
	$_cp=explode($_CONFIG['dir'],__DIR__); $_cp=$_cp[count($_cp)-2];
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	$conn=db_connect();
	
	$sql="UPDATE `$dbase_cats` SET `name`='".addslashes($_POST['name'])."' WHERE `id`='$_POST[id]'";
	$res=db_query($sql,$conn);
	if ($res) {
		$response['status']='ok';
		// $response['result']=$res;
	} else {
		$response['status']='false';
		$response['sql']=$sql;
	}
	db_disconnect($conn);
	echo json_encode($response);
	//ыыы

?>
