<?php
if(!isset($loc)) exit(0);

function af_getUID($sid,$conn=NULL){
	$ip=$_SERVER["REMOTE_ADDR"];
	$sql="DELETE FROM `sessions` WHERE (`last`<=DATE_SUB(NOW(),INTERVAL 60 MINUTE));";
	db_query($sql,$conn);
	$sql="UPDATE `sessions` SET `last`=NOW() WHERE (`sid`=\"$sid\")AND(`ip`=\"$ip\");";
	db_query($sql,$conn);
	$sql="SELECT `users`.*
FROM `users` INNER JOIN `sessions` ON `users`.`id`=`sessions`.`uid` 
WHERE (`sessions`.`sid`=\"$sid\")AND(`sessions`.`ip`=\"$ip\");";
	$result=db_query($sql, $conn);
	if(!$result || db_result_length($result)<=0){
		af_delUID($sid,$conn);
		return 0;
	};
	$res=db_result_array($result);
	$_SESSION["user"]=Array();
	foreach($res as $k => $r){
		if(is_numeric($k)) continue;
		$_SESSION["user"][$k]=$r;
	};
	return $res["id"];
}

function af_setUID($sid,$user,$pass,$conn=NULL){
	$ip=$_SERVER["REMOTE_ADDR"];
	$sql="DELETE FROM `sessions` WHERE (`sid`=\"$sid\");";
	db_query($sql,$conn);
	$sql="INSERT INTO `sessions` (`sid`, `uid`, `last`,`ip`) VALUES (\"$sid\", 
(SELECT `users`.`id` FROM `users` WHERE (`users`.`login` like \"$user\")and(`users`.`pass` = PASSWORD(\"$pass\")) LIMIT 0,1),  
NOW(), \"$ip\");";
	db_query($sql, $conn);
}

function af_delUID($sid,$conn=NULL){
	$ip=$_SERVER["REMOTE_ADDR"];
	$sql="DELETE FROM `sessions` WHERE (`sessions`.`sid`=\"$sid\")AND(`sessions`.`ip`=\"$ip\");";
	db_query($sql,$conn);
	unset($_SESSION["user"]);
}

?>
