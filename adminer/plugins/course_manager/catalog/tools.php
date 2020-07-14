<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$loc=1;

$TAG=Array('menu_items'=>Array(), 'error'=>'',);

// require_once("config.php");	

require_once("../../../include/config.php");	
require_once("../config.php");	
require_once("../../../include/fns_db.php");	
require_once("inc/fns_acc.php");	
db_connect();

// BEGIN авторизация mysql таблицы users и session
@session_start();
$sid=@session_id();



if(file_exists("_bef.php")) require_once("_bef.php");	// передача управления в скрипт по умолчанию до работы скрипта
require_once("inc/fns_tmpls.php");
require_once("inc/fns_output.php"); //NEW!

require_once("pages.php");
if(file_exists("_aft.php")) require_once("_aft.php");	// передача управления в скрипт по умолчанию после работы скрипта
// BEGIN обработка глобальных переменных

$ft=preg_replace('/(?><!--\*TEMPLATE\*-->)([\s\S.]*?)(?><!--\*TEMPLATE\*-->)/i','',$ft); // чистим шаблоны
foreach($TAG as $k => $v){ $ft=str_replace("<!--*".strtoupper($k)."*-->",$v,$ft); };

db_disconnect();

echo preg_replace('/<!--\*[^\*]*?\*-->/i','',$ft); // чистим теги перед выводом
?>