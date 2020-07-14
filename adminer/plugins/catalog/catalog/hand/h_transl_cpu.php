<?php
	error_reporting(E_ALL);
	header('Content-Type: text/html; charset=utf-8');
	include('../../../../include/fns_db.php');
	include('../../config.php');
	include('../inc/fns_translit.php');
	$_cp=explode(DIRECTORY_SEPARATOR,__DIR__); $_cp=$_cp[count($_cp)-2];		
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	$conn=db_connect();
	$r=array();
	// $r['path']=__FILE__;
	require_once("../inc/fns_translit.php");
	$text=$_GET['text'];
	$text=translit_alias($text);
	$r['text']=$text;
	db_disconnect($conn);
	echo json_encode($r);
	exit;
?>