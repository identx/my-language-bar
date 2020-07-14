<?php
/* Magneex administrator v.1.2 */
/*испаравлена загрузка конфигов*/

session_start();

header('Content-type: text/html; charset=utf-8');

error_reporting(0);
// error_reporting(E_ALL);



require_once('include/fns_db.php');
$dbp=isset($_CONFIG['dbprefix'])?$_CONFIG['dbprefix']:'mgnx_';
require('include/fns_output.php');

try {
	db_connect();
} catch(Exception $e){
		switch($e->getMessage()){
			case 1045:
				$error='Не удается подключиться к серверу '.$DB_CONF['server']; break;
			case 1044:
				$error='Не удается найти базу данных '.$DB_CONF['db']; break;
			default:
				$error='Ошибка подключения к БД';
		}
		echo '<div style="text-align:center;padding-top:30px;font-size:16px">ОШИБКА '.$e->getMessage().':<br />'.$error.'</div>';
		exit();
}

if(!isset($_SESSION["m_user"]["id"]) || (($_SESSION["m_user"]["rights"]&1)<=0)){
	echo check_user();
};

if((($_SESSION["m_user"]["rights"]&1)<=0)){
	unset($_POST['login']);
	echo check_user();
	exit;
};

//готовим перечень плагинов
$filelist = scandir('plugins/');
$plugins=Array();
$invis_plugs['before']=Array();
$invis_plugs['after']=Array();
unset($_SESSION['madmin']);

foreach ($filelist as $key=> $one){
	if (($one == '.') or ($one == '..') or !(is_dir('plugins/'.$one))) continue;
	if (!file_exists('plugins/'.$one.'/config.php')) continue;
	unset($conf);
	include('plugins/'.$one.'/config.php');
	array_push($plugins,Array('dir'=>$one,'event'=>($conf['invisible']==true?$conf['event']:''),'name'=>$conf['plugname']));
}
unset($conf);

if(true){
	$r=db_query("SELECT * FROM `".$dbp."plugins`;");
	$sql='';
	foreach($plugins as $p){
		$f=false;
		foreach($r as $ri){ if($p['dir']==$ri['name']){ $f=true; break; }; };
		if(!$f) $sql.=(strlen($sql)>0?',':'').'("'.$p['dir'].'")';
	};
	if(strlen($sql)>0) db_query('INSERT INTO `'.$dbp.'plugins` (`name`) VALUES '.$sql.';');
	$r=db_query("SELECT * FROM `".$dbp."plugins`;");
	foreach($plugins as $k => $p)
		foreach($r as $ri)
			if($p['dir']==$ri['name']){
				if(($_SESSION["m_user"]["rights"] & (1<<$ri['id']))<=0){
					unset($plugins[$k]);
					break;
				};
				$plugins[$k]['id']=$ri['id'];
				break;
			};
	$_SESSION["m_user"]['has']=array();
	foreach($plugins as $p)
		$_SESSION["m_user"]['has'][]=$p['dir'];
	foreach($plugins as $k => $p){
		if(strlen($p['event'])<=0){
			unset($plugins[$k]['event']);
			continue;
		};
		array_push($invis_plugs[$p['event']],$p);
		unset($plugins[$k]);
	};
	
};

function plugin_run($pname,$plabel=''){
	ob_start();
	if(isset($pname)){
		$plugin_dir='plugins/'.$pname;
		if (file_exists($plugin_dir.'/index.php')){
			include($plugin_dir.'/index.php'); 
			}else {
			echo 'Ошибка подключения плагина '.$plabel.'<br />';
		}
	}
	$plugin=ob_get_contents();
	ob_end_clean();
	ob_get_contents();
	return $plugin;
}

//// выводим плагины
foreach($invis_plugs['before'] as $one)
	$plugin.=plugin_run($one['dir'],$one['name']);
	
if(isset($_GET['plugin'])){
	$plugin=plugin_run($_GET['plugin']);	
}

foreach($invis_plugs['after'] as $one)
	$plugin.=plugin_run($one['dir'],$one['name']);
////
$result.=output_header($plugins);
$result.="
<script>
	$(function(){
		$('#cont iframe').height($('#cont').height());
	})
</script>";
$result.=$plugin;
$result.=output_footer();


echo $result;
db_disconnect($conn);
?>
