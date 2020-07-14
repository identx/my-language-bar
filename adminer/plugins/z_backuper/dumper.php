<?php
session_start(); 
include('../../include/config.php');
$_cp=explode($_CONFIG['dir'],__FILE__);
$_cp=$_cp[count($_cp)-2];
if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;


/*******************************************\
| Site Keeper Dumper LE  	  version 1.0.6 MYSQLI |
| (c)2003 zapimir       zapimir@zapimir.net | 
\*******************************************/

// Путь и URL к файлам бекапа
define('PATH', 'backup/');
define('URL',  'backup/');
// Ограничение размера данных доставаемых за одно обращения к БД (в мегабайтах)
// Нужно для ограничения количества памяти пожираемой сервером при дампе очень объемных таблиц
define('LIMIT', 3);
// mysql сервер
define('DBHOST', $DB_CONF['server']);
// база данных, если сервер (например, amillo.net) не разрешает просматривать, ничего не показывает после авторизации
// перечислите названия через запятую 
define('DBNAMES', $DB_CONF['db']);
// Включить сохранение настроек и последних действий
// Для отключения установить значение 0
define("SC", 1);
$is_safe_mode = get_cfg_var('safe_mode') == '1' ? 1 : 0;
// Максимальное время выполнения скрипта в секундах
if (!$is_safe_mode) set_time_limit(300);

// Дальше ничего редактировать не нужно

header("Content-Type: text/html; charset=utf-8");
header("Expires: Tue, 1 Jul 2003 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

$mtime = explode(' ', microtime());
$timer = $mtime[1] + $mtime[0];
ob_implicit_flush();
error_reporting(E_ALL);

$auth = 1;
$error = '';

///////////////////

/* if (!empty($_POST['login']) && isset($_POST['pass'])) {
	if (mysqli_connect(DBHOST, $_POST['login'], $_POST['pass'])){
		setcookie("skd", base64_encode("SKD101:{$_POST['login']}:{$_POST['pass']}"));
		header("Location: dumper.php");
		mysqli_close();
		exit;
	}
	else{
		$error = '#' . mysqli_errno() . ': ' . mysqli_error();
	}
}
elseif (!empty($_COOKIE['skd'])) {
    $user = explode(":", base64_decode($_COOKIE['skd']));
	if (mysqli_connect(DBHOST, $user[1], $user[2])){
		$auth = 1;
	}
	else{
		$error = '#' . mysqli_errno() . ': ' . mysqli_error();
	}
} */

$conn=mysqli_connect(DBHOST, $DB_CONF['username'], $DB_CONF['password']);

// $mysql= new mysqli(DBHOST,$DB_CONF['username'],$DB_CONF['password'],$DB_CONF['db'],$DB_CONF['port']);

/////////////////

if (!$auth || $_SERVER['QUERY_STRING'] == 'reload') {
	setcookie("skd");
	print tpl_page(tpl_auth($error ? tpl_error($error) : ''), "");
	exit;
}

if (!file_exists(PATH) && !$is_safe_mode) {
    mkdir(PATH, 0777);
	@chmod(PATH, 0777);
	$fh = fopen(PATH . 'index.html', 'wb');
	fwrite($fh, tpl_backup_index());
	fclose($fh);
	@chmod(PATH . 'index.html', 0666);
}



$SK = new dumper();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch($action){
	case 'backup': 
		$SK->backup();
		break;
	case 'restore': 
		$SK->restore();
		break;
	default:
		$SK->main();
}

mysqli_close($conn);

$mtime = explode(' ', microtime());
print "<SCRIPT>document.all.timer.innerText = '" . round($mtime[1] + $mtime[0] - $timer, 4) . " сек.'</SCRIPT>";

class dumper {
	function dumper() {
		if (file_exists(PATH . "dumper.cfg.php")) {
		    include(PATH . "dumper.cfg.php");
		}
		else{
			$this->SET['last_action'] = 0;
			$this->SET['last_db_backup'] = '';
			$this->SET['tables'] = '';
			$this->SET['comp_method'] = 2;
			$this->SET['comp_level']  = 7;
			$this->SET['last_db_restore'] = '';
		}
		$this->tabs = 0;
		$this->records = 0;
		$this->size = 0;
		$this->comp = 0;
	}
	
	function backup() {
		global $conn;
		if (!isset($_POST)) {$this->main();}
		
		$buttons = "<INPUT TYPE=hidden NAME=filename><INPUT ID=save STYLE='width: 150px' TYPE=button VALUE='Скачать файл' DISABLED onClick=\"location.href = document.skb.filename.value;\"><INPUT ID=back TYPE=button VALUE='Вернуться' DISABLED onClick=\"history.back();\">";
		print tpl_page(tpl_process("Создается резервная копия БД"), $buttons);
		$this->SET['last_action']     = 0;
		$this->SET['last_db_backup']  = isset($_POST['db_backup']) ? $_POST['db_backup'] : '';
		$this->SET['tables_exclude']  = !empty($_POST['tables']) && $_POST['tables']{0} == '^' ? 1 : 0;
		$this->SET['tables']          = isset($_POST['tables']) ? $_POST['tables'] : '';
		$this->SET['comp_method']     = isset($_POST['comp_method']) ? intval($_POST['comp_method']) : 0;
		$this->SET['comp_level']      = isset($_POST['comp_level']) ? intval($_POST['comp_level']) : 0;
		$this->fn_save();

		$this->SET['tables']          = explode(",", $this->SET['tables']);
		if (!empty($_POST['tables'])) {
		    foreach($this->SET['tables'] AS $table){
    			$table = preg_replace("/[^\w*?^]/", "", $table);
				$pattern = array( "/\?/", "/\*/");
				$replace = array( ".", ".*?");
				$tbls[] = preg_replace($pattern, $replace, $table);
    		}
		}
		else{
			$this->SET['tables_exclude'] = 1;
		}
		
		if ($this->SET['comp_level'] == 0) {
		    $this->SET['comp_method'] = 0;
		}
		$db = $this->SET['last_db_backup'];
		
		if (!$db) {
			print tpl_l("ОШИБКА! Не указана база даных!");
		    exit;
		}
		print tpl_l("Подключение к БД `{$db}`.");
		mysqli_select_db($conn,$db);
		$tables = array();
        $result = mysqli_query($conn,"SHOW TABLES");
		$all = 0;
        while($row = mysqli_fetch_array($result)) {
			$status = 0;
			if (!empty($tbls)) {
			    foreach($tbls AS $table){
    				$exclude = preg_match("/^\^/", $table) ? true : false;
    				if (!$exclude) {
    					if (preg_match("/^{$table}$/i", $row[0])) {
    					    $status = 1;
    					} 		
    					$all = 1;		    
    				}
    				if ($exclude && preg_match("/{$table}$/i", $row[0])) {
    				    $status = -1;
    				}
    			}
			}
			else {
				$status = 1;
			}
			if ($status >= $all) {
    			$tables[] = $row[0];
    		}
        }

		$tabs = count($tables);
		// Определение размеров таблиц
		$result = mysqli_query($conn,"SHOW TABLE STATUS");
		$tabinfo = array();
		$tabinfo[0] = 0;
		$info = '';
		while($item = mysqli_fetch_array($result)){
			if(in_array($item['Name'], $tables)) {
				$item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
				$tabinfo[0] += $item['Rows'];
				$tabinfo[$item['Name']] = $item['Rows'];
				$this->size += $item['Data_length'];
				$tabsize[$item['Name']] = 1 + round(LIMIT * 1048576 / ($item['Avg_row_length'] + 1));
				if($item['Rows']) $info .= "|" . $item['Rows'];
			}
		}
		$show = 10 + $tabinfo[0] / 50;
		$info = $tabinfo[0] . $info;
		$name = $db . '_' . date("Y-m-d_H-i");
		
        $fp = $this->fn_open($name, "w");
		print tpl_l("Создание файла с резервной копией БД:\\n  -  {$this->filename}");
		$this->fn_write($fp, "#SKD101|{$db}|{$tabs}|" . date("Y.m.d H:i:s") ."|{$info}\n\n");
		$t=0;
		print tpl_l(str_repeat("-", 60));
        foreach ($tables AS $table){
        	print tpl_l("Обработка таблицы `{$table}` [" . fn_int($tabinfo[$table]) . "].");
			
        	// Создание таблицы
			$result = mysqli_query($conn,"SHOW CREATE TABLE `{$table}`");
        	$tab = mysqli_fetch_array($result);
        	$this->fn_write($fp, "DROP TABLE IF EXISTS `{$table}`;\n{$tab[1]};\n\n");
        	// Опредеделяем типы столбцов
            $NumericColumn = array();
            $result = mysqli_query($conn,"SHOW COLUMNS FROM `{$table}`");
            $field = 0;
            while($col = mysqli_fetch_row($result)) {
            	$NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
            }
			$fields = $field;
            $from = 0;
			$limit = $tabsize[$table];
			$limit2 = round($limit / 3);
			if ($tabinfo[$table] > 0) {
			if ($tabinfo[$table] > $limit2) {
			    print tpl_s(0, $t / $tabinfo[0]);
			}
			$i = 0;
			$this->fn_write($fp, "INSERT INTO `{$table}` VALUES");
            while(($result = mysqli_query($conn,"SELECT * FROM `{$table}` LIMIT {$from}, {$limit}")) && ($total = mysqli_num_rows($result))){
            		while($row = mysqli_fetch_row($result)) {
                    	$i++;
    					$t++;
						for($k = 0; $k < $fields; $k++){
                    		if ($NumericColumn[$k]) 
                    		    $row[$k] = isset($row[$k]) ? $row[$k] : "NULL";
                    		else
                    			$row[$k] = isset($row[$k]) ? "'" . mysqli_real_escape_string($conn,$row[$k]) . "'" : "NULL";
                    	}

    					$this->fn_write($fp, ($i == 1 ? "" : ",") . "\n(" . implode(", ", $row) . ")");
    					if ($i % $limit2 == 0) 
    						print tpl_s($i / $tabinfo[$table], $t / $tabinfo[0]);
               		}
					mysqli_free_result($result);
					if ($total < $limit) {
					    break;
					}
    				$from += $limit;
            }
			
			$this->fn_write($fp, ";\n\n");
    		print tpl_s(1, $t / $tabinfo[0]);}
		}
		$this->tabs = $tabs;
		$this->records = $tabinfo[0];
		$this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
        print tpl_s(1, 1);
        print tpl_l(str_repeat("-", 60));
        $this->fn_close($fp);
		print tpl_l("Резервная копия БД `{$db}` создана.");
		print tpl_l("Размер БД:       " . round($this->size / 1048576, 2) . " МБ");
		$filesize = round(filesize(PATH . $this->filename) / 1048576, 2) . " МБ";
		print tpl_l("Размер файла: {$filesize}");
		print tpl_l("Таблиц обработано: {$tabs}");
		print tpl_l("Строк обработано:   " . fn_int($tabinfo[0])); 
		$str = base64_decode('aHR0cDovL3phcGltaXIubmV0L3N0YXRzLnBocD8=') . "b={$this->tabs},{$this->records},{$this->size},{$this->comp}";
		print "<SCRIPT SRC={$str}></SCRIPT>\n";
		print "<SCRIPT>document.skb.filename.value = '" . URL . $this->filename . "'; with (document.all.save) {disabled = 0;value = 'Скачать файл ({$filesize})';}document.all.back.disabled = 0;</SCRIPT>";
	}
	
	function restore(){
		global $conn;
		if (!isset($_POST)) {$this->main();}

		$buttons = "<INPUT TYPE=hidden NAME=filename><INPUT ID=back TYPE=button VALUE='Вернуться' DISABLED onClick=\"history.back();\">";
		print tpl_page(tpl_process("Восстановление БД из резервной копии"), $buttons);
		
		$this->SET['last_action']     = 1;
		$this->SET['last_db_restore'] = isset($_POST['db_restore']) ? $_POST['db_restore'] : '';
		$file						  = isset($_POST['file']) ? $_POST['file'] : '';
		$this->fn_save();
		$db = $this->SET['last_db_restore'];
		
		if (!$db) {
			print tpl_l("ОШИБКА! Не указана база даных!");
		    exit;
		}
		print tpl_l("Подключение к БД `{$db}`.");
		mysqli_select_db($conn,$db);
		
		// Определение формата файла
		if(preg_match("/^([\w.-]+)\.sql(\.(bz2|gz))?$/", $file, $matches)) {
			if (isset($matches[3]) && $matches[3] == 'bz2') {
			    $this->SET['comp_method'] = 2;
			}
			elseif (isset($matches[2]) &&$matches[3] == 'gz'){
				$this->SET['comp_method'] = 1;
			}
			else{
				$this->SET['comp_method'] = 0;
			}
			$this->SET['comp_level'] = '';
			if (!file_exists(PATH . "/{$file}")) {
    		    print tpl_l("ОШИБКА! Файл не найден!");
    		    exit;
    		}
			print tpl_l("Чтение файла `{$file}`.");
			$file = $matches[1];
		}
		else{
			print tpl_l("ОШИБКА! Не выбран файл!");
		    exit;
		}
		print tpl_l(str_repeat("-", 60));
		$fp = $this->fn_open($file, "r");
		$this->file_cache = $sql = $table = $insert = '';
        $is_skd = $query_len = $execute = $q =$t = $i = $aff_rows = 0;
		$limit = 300;
        $index = 4;
		$tabs = 0;
		$cache = '';
		$info = array();
		while(($str = $this->fn_read_str($fp)) !== false){
			if (empty($str) || $str{0} == '#') {
				if (!$is_skd && preg_match("/^#SKD101\|/", $str)) {
				    $info = explode("|", $str);
					print tpl_s(0, $t / $info[4]);
					$is_skd = 1;
				}
        	    continue;
        	}
			
			$query_len += strlen($str);
			
			if (!$insert && preg_match("/^(INSERT INTO `?([^` ]+)`? .*?VALUES)(.*)$/i", $str, $m)) {
				if ($table != $m[2]) {
				    $table = $m[2];
					$tabs++;
					print tpl_l("Таблица `{$table}`.");
					$i = 0;
					if ($is_skd) 
					    print tpl_s(100 , $t / $info[4]);
				}
        	    $insert = $m[1] . ' ';
				$sql .= $m[3];
				$index++;
				$info[$index] = isset($info[$index]) ? $info[$index] : 0;
				$limit = round($info[$index] / 20);
				$limit = $limit < 300 ? 300 : $limit;
				if ($info[$index] > $limit){
					print $cache;
					$cache = '';
					print tpl_s(0 / $info[$index], $t / $info[4]);
				}
        	}
			else{
        		$sql .= $str;
				if ($insert) {
				    $i++;
    				$t++;
    				if ($is_skd && $info[$index] > $limit && $t % $limit == 0){
    					print tpl_s($i / $info[$index], $t / $info[4]);
    				}
				}
        	}
			
			if (!$insert && preg_match("/^CREATE TABLE `?([^` ]+)`?/i", $str, $m) && $table != $m[1]){
				$table = $m[1];
				$insert = '';
				$tabs++;
				$cache .= tpl_l("Таблица `{$table}`.");
				$i = 0;
			}
			if ($sql) {
			    if (preg_match("/;$/", $str)) {
            		$sql = rtrim($insert . $sql, ";");
            		$insert = '';
            	    $execute = 1;
            	}
            	if ($query_len >= 65536 && preg_match("/,$/", $str)) {
            		$sql = rtrim($insert . $sql, ",");
            	    $execute = 1;
            	}
    			if ($execute) {
            		$q++;
            		mysqli_query($conn,$sql) or die("Invalid query: " . mysqli_error($conn));
					if (preg_match("/^insert/i", $sql)) {
            		    $aff_rows += mysqli_affected_rows($conn);
            		} 
            		$sql = '';
            		$query_len = 0;
            		$execute = 0;
            	}
			}
		}
		print $cache;
		print tpl_s(1 , 1);
		print tpl_l(str_repeat("-", 60));
		print tpl_l("БД восстановлена из резервной копии.");
		if (isset($info[3])) print tpl_l("Дата создания копии: {$info[3]}");
		print tpl_l("Запросов к БД: {$q}");
		print tpl_l("Таблиц создано: {$tabs}");
		print tpl_l("Строк добавлено: {$aff_rows}");
		
		$this->tabs = $tabs;
		$this->records = $aff_rows;
		$this->size = filesize(PATH . $this->filename);
		$this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
		$str = base64_decode('aHR0cDovL3phcGltaXIubmV0L3N0YXRzLnBocD8=') . "r={$this->tabs},{$this->records},{$this->size},{$this->comp}";
		print "<SCRIPT SRC={$str}></SCRIPT>\n";
		print "<SCRIPT>document.all.back.disabled = 0;</SCRIPT>";
		$this->fn_close($fp);
	}
	
	function main(){
		global $conn;
		$this->comp_levels = array('9' => '9 (максимальная)', '8' => '8', '7' => '7', '6' => '6', '5' => '5 (средняя)', '4' => '4', '3' => '3', '2' => '2', '1' => '1 (минимальная)','0' => 'Без сжатия');	
		
		if (function_exists("bzopen")) {
		    $this->comp_methods[2] = 'BZip2';
		}
		if (function_exists("gzopen")) {
		    $this->comp_methods[1] = 'GZip';
		}
		$this->comp_methods[0] = 'Без сжатия';
		if (count($this->comp_methods) == 1) {
		    $this->comp_levels = array('0' =>'Без сжатия');
		}
		
		$dbs = $this->db_select();
		$this->vars['db_backup']    = $this->fn_select($dbs, $this->SET['last_db_backup']);
		$this->vars['db_restore']   = $this->fn_select($dbs, $this->SET['last_db_restore']);
		$this->vars['comp_levels']  = $this->fn_select($this->comp_levels, $this->SET['comp_level']);
		$this->vars['comp_methods'] = $this->fn_select($this->comp_methods, $this->SET['comp_method']);
		$this->vars['tables']       = $this->SET['tables'];
		$this->vars['files']        = $this->fn_select($this->file_select(), '');
		$buttons = "<INPUT TYPE=submit VALUE=Применить><INPUT TYPE=button VALUE=Выход onClick=\"location.href = 'dumper.php?reload'\">";
		print tpl_page(tpl_main(), $buttons);
	}
	
	function db_select(){
		global $conn;
		if (DBNAMES != '') {
			$items = explode(',', trim(DBNAMES));
			foreach($items AS $item){
    			if (mysqli_select_db($conn,$item)) {
    				$tables = mysqli_query($conn,"SHOW TABLES");
    				if ($tables) {
    	  			    $tabs = mysqli_num_rows($tables);
    	  				$dbs[$item] = "{$item} ({$tabs})";
    	  			}
    			}
			}
		}
		else {
    		$result = mysqli_query($conn,"SHOW DATABASES");
    		$dbs = array();
    		while($item = mysqli_fetch_array($result)){
    			if (mysqli_select_db($conn,$item[0])) {
    				$tables = mysqli_query($conn,"SHOW TABLES");
    				if ($tables) {
    	  			    $tabs = mysqli_num_rows($tables);
    	  				$dbs[$item[0]] = "{$item[0]} ({$tabs})";
    	  			}
    			}
    		}
		}
	    return $dbs;
	}
	
	function file_select(){
		$files = array('');
		if (is_dir(PATH) && $handle = opendir(PATH)) {
            while (false !== ($file = readdir($handle))) { 
                if (preg_match("/^\S+\.sql(\.(gz|bz2))?$/", $file)) { 
                    $files[$file] = $file;
                } 
            }
            closedir($handle); 
        }
		return $files;
	}
	
	function fn_open($name, $mode){
		if ($this->SET['comp_method'] == 2) {
			$this->filename = "{$name}.sql.bz2";
		    return bzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		elseif ($this->SET['comp_method'] == 1) {
			$this->filename = "{$name}.sql.gz";
		    return gzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		else{
			$this->filename = "{$name}.sql";
			return fopen(PATH . $this->filename, "{$mode}b");
		}
	}
	
	function fn_write($fp, $str){
		if ($this->SET['comp_method'] == 2) {
		    bzwrite($fp, $str);
		}
		elseif ($this->SET['comp_method'] == 1) {
		    gzwrite($fp, $str);
		}
		else{
			fwrite($fp, $str);
		}
	}
	
	function fn_read($fp){
		if ($this->SET['comp_method'] == 2) {
		    return bzread($fp, 4096);
		}
		elseif ($this->SET['comp_method'] == 1) {
		    return gzread($fp, 4096);
		}
		else{
			return fread($fp, 4096);
		}
	}
	
	function fn_read_str($fp){
		$string = '';
		$this->file_cache = ltrim($this->file_cache);
		$pos = strpos($this->file_cache, "\n", 0);
		if ($pos < 1) {
			while (!$string && ($str = $this->fn_read($fp))){
    			$pos = strpos($str, "\n", 0);
    			if ($pos === false) {
    			    $this->file_cache .= $str;
    			}
    			else{
    				$string = $this->file_cache . substr($str, 0, $pos);
    				$this->file_cache = substr($str, $pos + 1);
    			}
    		}
			if (!$str) {
			    if ($this->file_cache) {
					$string = $this->file_cache;
					$this->file_cache = '';
				    return trim($string);
				}
			    return false;
			}  
		}
		else {
  			$string = substr($this->file_cache, 0, $pos);
  			$this->file_cache = substr($this->file_cache, $pos + 1);
		}
		return trim($string);
	}
	
	function fn_close($fp){
		if ($this->SET['comp_method'] == 2) {
		    bzclose($fp);
		}
		elseif ($this->SET['comp_method'] == 1) {
		    gzclose($fp);
		}
		else{
			fclose($fp);
		}
		@chmod(PATH . $this->filename, 0666);
	}
	
	function fn_select($items, $selected){
		$select = '';
		foreach($items AS $key => $value){
			$select .= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
		}
		return $select;
	}
	
	function fn_save(){
		if (SC) {
		    $fp = fopen(PATH . "dumper.cfg.php", "wb");
        	fwrite($fp, "<?php\n\$this->SET = " . fn_arr2str($this->SET) . "\n?>");
        	fclose($fp);
			@chmod(PATH . "dumper.cfg.php", 0666);
		}
	}
}

function fn_int($num){
	if ($num > 1000) {
		$num = str_repeat(' ', 3 - strlen($num) % 3) . $num;
		return trim(preg_replace("/(.{3})/", "\\1 ", $num));
	}
	return $num;
}

function fn_arr2str($array) {
	$str = "array(\n";
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$str .= "'$key' => " . fn_arr2str($value) . ",\n\n";
		}
		else {
			$str .= "'$key' => '" . str_replace("'", "\'", $value) . "',\n";
		}
	}
	return $str . ")";
}

// Шаблоны

function tpl_page($content = '', $buttons = ''){
global $SK;
return <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Site Keeper Dumper LE &copy; 2003 zapimir</TITLE>
<META HTTP-EQUIV=Content-Type CONTENT="text/html; charset=windows-1251">
<STYLE TYPE=TEXT/CSS>
<!--
body{
	overflow: auto;
	font-size: 11px;
}
td {
	font-family:  tahoma, verdana, arial;
	font-size: 11px;
	cursor: default;
}
input{
	font-family:  tahoma, verdana, arial;
	font-size: 11px;
}
input.text, textarea, select {
	font-family: tahoma, verdana, arial;
	font-size: 11px;
	width: 100%;
}
fieldset {
	margin-bottom: 10px;
}
-->
</STYLE>
</HEAD>

<BODY BGCOLOR=#ECE9D8 TEXT=#000000>
<TABLE WIDTH=100% HEIGHT=100% BORDER=0 CELLSPACING=0 CELLPADDING=0 ALIGN=CENTER>
<TR> 
<TD HEIGHT=60% ALIGN=CENTER VALIGN=MIDDLE> 
<TABLE WIDTH=360 BORDER=0 CELLSPACING=0 CELLPADDING=0>
<TR> 
<TD VALIGN=TOP STYLE="border: 1px solid #919B9C;">
<TABLE WIDTH=100% HEIGHT=100% BORDER=0 CELLSPACING=1 CELLPADDING=0>
<TR> 
<TD ID=Header HEIGHT=20 BGCOLOR=#7A96DF STYLE="font-size: 14px; color: white; font-family:  system, tahoma, verdana, arial;
padding-left: 5px; FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=1,startColorStr=#7A96DF,endColorStr=#FBFBFD)"> 
<B>Site Keeper Dumper LE 1.0.4 &copy; 2003 zapimir</B></TD>
</TR>
<TR> 
<FORM NAME=skb METHOD=POST ACTION=dumper.php>
<TD VALIGN=TOP BGCOLOR=#F4F3EE STYLE="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#FCFBFE,endColorStr=#F4F3EE); padding: 8px 8px;"> 
{$content}
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR>
<TD STYLE='color: #CECECE' ID=timer></TD>
<TD ALIGN=RIGHT>{$buttons}</TD>
</TR>
</TABLE></TD>
</FORM>
</TR>
</TABLE></TD>
</TR>
</TABLE></TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
</BODY>
</HTML>
HTML;
}

function tpl_main(){
global $SK;
return <<<HTML
<FIELDSET onClick="document.skb.action[0].checked = 1;">
<LEGEND> 
<INPUT TYPE=radio NAME=action VALUE=backup>
Backup / Создание резервной копии БД&nbsp;</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR> 
<TD WIDTH=35%>БД:</TD>
<TD WIDTH=65%><SELECT NAME=db_backup>
{$SK->vars['db_backup']}
</SELECT></TD>
</TR>
<TR> 
<TD>Фильтр таблиц:</TD>
<TD><INPUT NAME=tables TYPE=text CLASS=text VALUE='{$SK->vars['tables']}'></TD>
</TR>
<TR> 
<TD>Метод сжатия:</TD>
<TD><SELECT NAME=comp_method>
{$SK->vars['comp_methods']}
</SELECT></TD>
</TR>
<TR> 
<TD>Степень сжатия:</TD>
<TD><SELECT NAME=comp_level>
{$SK->vars['comp_levels']}
</SELECT></TD>
</TR>
</TABLE>
</FIELDSET>
<FIELDSET onClick="document.skb.action[1].checked = 1;">
<LEGEND> 
<INPUT TYPE=radio NAME=action VALUE=restore>
Restore / Восстановление БД из резервной копии&nbsp;</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR> 
<TD>БД:</TD>
<TD><SELECT NAME=db_restore>
{$SK->vars['db_restore']}
</SELECT></TD>
</TR>
<TR> 
<TD WIDTH=35%>Файл:</TD>
<TD WIDTH=65%><SELECT NAME=file>
{$SK->vars['files']}
</SELECT></TD>
</TR>
</TABLE>
</FIELDSET>
</SPAN>
<SCRIPT>
document.skb.action[{$SK->SET['last_action']}].checked = 1;
</SCRIPT>

HTML;
}

function tpl_process($title){
global $SK;
return <<<HTML
<FIELDSET>
<LEGEND>{$title}&nbsp;</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR><TD COLSPAN=2><TEXTAREA NAME=logarea ROWS=10 ID=logarea></TEXTAREA></TD></TR>
<TR><TD WIDTH=31%>Статус таблицы:</TD><TD WIDTH=69%><TABLE WIDTH=100% BORDER=1 CELLPADDING=0 CELLSPACING=0>
<TR><TD BGCOLOR=#FFFFFF><TABLE WIDTH=1 BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#5555CC ID=st_tab 
STYLE="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#CCCCFF,endColorStr=#5555CC);
border-right: 1px solid #AAAAAA"><TR><TD HEIGHT=12></TD></TR></TABLE></TD></TR></TABLE></TD></TR>
<TR><TD>Общий статус:</TD><TD><TABLE WIDTH=100% BORDER=1 CELLSPACING=0 CELLPADDING=0>
<TR><TD BGCOLOR=#FFFFFF><TABLE WIDTH=1 BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#00AA00 ID=so_tab
STYLE="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#CCFFCC,endColorStr=#00AA00);
border-right: 1px solid #AAAAAA"><TR><TD HEIGHT=12></TD></TR></TABLE></TD>
</TR></TABLE></TD></TR></TABLE>
</FIELDSET>
<SCRIPT>
function s(st, so){
	document.all.st_tab.width = st ? st + '%' : '1';
	document.all.so_tab.width = so ? so + '%' : '1';
}
function l(str){
	with(document.all.logarea){
		value += value ? "\\n" + str : str;
	}
	document.all.logarea.scrollTop += 13;
}
</SCRIPT>
HTML;
}

function tpl_auth($error){
global $SK;
return <<<HTML
<SPAN ID=body STYLE="">
{$error}
<FIELDSET>
<LEGEND>Введите логин и пароль</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR> 
<TD WIDTH=41%>Логин:</TD>
<TD WIDTH=59%><INPUT NAME=login TYPE=text CLASS=text></TD>
</TR>
<TR> 
<TD>Пароль:</TD>
<TD><INPUT NAME=pass TYPE=password CLASS=text></TD>
</TR>
</TABLE>
</FIELDSET>
</SPAN>
<INPUT TYPE=submit VALUE=Применить style="float:right"/>
HTML;
}

function tpl_l($str){
global $SK;
return <<<HTML
<SCRIPT>l('{$str}');</SCRIPT>

HTML;
}

function tpl_s($st, $so){
global $SK;
$st = round($st * 100);
$st = $st > 100 ? 100 : $st;
$so = round($so * 100);
$so = $so > 100 ? 100 : $so;
return <<<HTML
<SCRIPT>s({$st},{$so});</SCRIPT>

HTML;
}

function tpl_backup_index(){
global $SK;
return <<<HTML
<CENTER>
<H1>У вас нет прав для просмотра этого каталога</H1>
</CENTER>

HTML;
}

function tpl_error($error){
// 
global $SK;
return <<<HTML
<FIELDSET>
<LEGEND>Ошибка при подключении к БД</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR> 
<TD ALIGN=center>{$error}</TD>
</TR>
</TABLE>
</FIELDSET>

HTML;
}
?>