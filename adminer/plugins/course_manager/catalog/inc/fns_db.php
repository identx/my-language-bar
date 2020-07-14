<?
if(!isset($loc)) exit(0);
require_once("config.php");
function db_connect(){
  $result=mysql_connect(
    $_SERVER["db"]["host"].
      (isset($_SERVER["db"]["port"])?":".$_SERVER["db"]["port"]:":3306"),
    $_SERVER["db"]["user"],
    $_SERVER["db"]["pass"]);
  if(!$result)
    return false;
  @mysql_query("SET NAMES 'utf8';");
  //@mysql_query("SET NAMES 'cp1251';");
  if(!@mysql_select_db($_SERVER["db"]["base"]))
    return false;
  return $result;
}

function db_query($sql,$conn=NULL){
  if($conn!=NULL)
    return @mysql_query($sql, $conn);
  else
    return @mysql_query($sql);
}

function db_result_row($result){
  return @mysql_fetch_row($result);
}

function db_result_array($result){
  return @mysql_fetch_array($result);
}

function db_insert_id($conn=NULL){
  if($conn!=NULL)
    return @mysql_insert_id($conn);
  else
    return @mysql_insert_id();
}

function db_result_length($result){
  return @mysql_num_rows($result);
}

function db_result_free($result){
  return @mysql_free_result($result);
}

function db_disconnect($conn=NULL){
  if($conn==NULL)
    return false;
  @mysql_close($conn);
  return true;
}

function db_getItems($q,$conn=NULL){ // return array of SQL result
	$res=db_query($q,$conn);
	if(($n=db_result_length($res))<=0)
		return Array();
	$r=Array();
	for($i=0;$i<$n;$i++)
		$r[$i]=db_result_array($res);
	return $r;
};

function db_getItem($q,$conn=NULL){ // return one item of SQL result
	$res=db_query($q,$conn);
	if(($n=db_result_length($res))<=0)
		return Array();
	return db_result_array($res);
};

?>
