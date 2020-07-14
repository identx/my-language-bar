<?php
if(!$is_handler)
die;     
error_reporting(0);
$file="_uploads/".$_POST['hash'].'.original';
$r=array('status'=>false);
if(filesize($file)<=4*1024*1024){
	$r['status']=true;
}else{
	@unlink($file);
	@unlink($file.'_ready');
	$r['error']='Файл превышает допустимый размер!';
}
checkFolder();
die(json_encode($r));
?>
