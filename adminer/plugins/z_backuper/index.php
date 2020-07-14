<?php	
	global $_CONFIG;
	$d=(isset($_CONFIG['dir']))?$_CONFIG['dir']:'/';
	$_cp=explode($_CONFIG['dir'],__FILE__); 
	$_cp=$_cp[count($_cp)-2];
	
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	
	echo '<iframe style="width:100%;height:100%;min-height:800px" src="plugins/'.$_cp.'/dumper.php"></iframe>';
?>