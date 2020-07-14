<?php
	header("Content-Type: text/html; charset=utf-8");
	include('../../../../include/fns_db.php');
	include('../../config.php');
	$_cp=explode(DIRECTORY_SEPARATOR,__DIR__); $_cp=$_cp[count($_cp)-2];		
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	$response=array('status'=>false);
	$conn=db_connect();
// file_put_contents('11111.txt',$_SERVER["HTTP_UPLOAD_ID"]);
// if(!$is_handler)
// die;     
$uploaddir="../temp/";
$hash=$_SERVER["HTTP_UPLOAD_ID"];
$rows=false;
if(true){
	if($_SERVER["REQUEST_METHOD"]=="GET"){
		if($_GET["action"]=="abort"){
			if(is_file($uploaddir."/".$hash.".html5upload"))
				unlink($uploaddir."/".$hash.".html5upload");
			print "ok abort";
			return;
		}
		if($_GET["action"]=="done"){
			if(is_file($uploaddir."/".$hash.".original"))
				unlink($uploaddir."/".$hash.".original");
			rename($uploaddir."/".$hash.".html5upload",$uploaddir."/".$hash.".original");
			$fw=fopen($uploaddir."/".$hash.".original_ready","wb");if ($fw) fclose($fw);
			$rows=true;
		}
	} elseif($_SERVER["REQUEST_METHOD"]=="POST"){
		if(!is_dir($uploaddir))
			@mkdir($uploaddir,0775,true);
		$filename=$uploaddir."/".$hash.".html5upload";
		if (intval($_SERVER["HTTP_PORTION_FROM"])==0) 
			$fout=fopen($filename,"wb");
		else
			$fout=fopen($filename,"ab");
		if (!$fout) {
			header("HTTP/1.0 500 Internal Server Error");
			print "Can't open file for writing.";
			return;
		}
		$fin=fopen("php://input","rb");
		if($fin){
			while(!feof($fin)){
				$data=fread($fin,1024*1024);
				fwrite($fout,$data);
			}
			fclose($fin);
		}
		fclose($fout);
	}
	header("HTTP/1.0 200 OK");
	print ($rows)?"-;;-\n":"ok\n";
} else {
	header("HTTP/1.0 500 Internal Server Error");
	print "Wrong session hash.";
}
?>
