<?php
	session_start(); 	
	header("Content-Type: text/html; charset=utf-8");
	include('../../../include/fns_db.php');
	include('../config.php');
	$d=$_CONFIG['dir'];
	$_cp=explode($_CONFIG['dir'],__DIR__); $_cp=$_cp[count($_cp)-2];
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	$conn=db_connect();
	
	$sql="INSERT INTO `$dbase`(`id`, `name`,`alias`, `anounce`, `keyws`,  `text`, `img`, `imp`, `category`, `show`,`date`, `modified`,`source`) VALUES(NULL,'".addslashes($_POST['name'])."','".substr(newsTranslite($_POST['name']),0,50)."','".addslashes(strip_tags($_POST['anno']))."','".addslashes($_POST['keyw'])."','$_POST[text]','".addslashes($_POST['img'])."','$_POST[imp]','$_POST[cat]','$_POST[show]','$_POST[date]',`modified`=NOW(),'".addslashes($_POST['source'])."')"; ////substr->mb_substr????
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

function newsTranslite($str){
		$str=preg_replace("/[^\w\x7F-\xFF\s\-\d]/"," ",$str);
		$str=trim(preg_replace("/\s(\S{1,2})\s/"," ",preg_replace("/ +/i"," ","$str")));
		$tr=Array("А"=>"a","Б"=>"b","В"=>"v","Г"=>"g","Д"=>"d","Е"=>"e","Ё"=>"e","Ж"=>"j","З"=>"z","И"=>"i","Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n","О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t","У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch","Ш"=>"sh","Щ"=>"shh","Ъ"=>"","Ы"=>"y","Ь"=>"","Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"j","з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h","ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"","ы"=>"","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","-"=>"-"," "=>"-","_"=>"_","A"=>"a","B"=>"b","C"=>"c","D"=>"d","E"=>"e","F"=>"f","G"=>"g","H"=>"h","J"=>"j","K"=>"k","L"=>"l","M"=>"m","N"=>"n","O"=>"o","P"=>"p","Q"=>"q","R"=>"r","S"=>"s","T"=>"t","U"=>"u","V"=>"v","W"=>"w","X"=>"x","Y"=>"y","Z"=>"z");
		return strtr($str,$tr);
};

?>
