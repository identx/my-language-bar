<?php
session_start(); $_cp=explode(DIRECTORY_SEPARATOR,__DIR__); $_cp=$_cp[count($_cp)-1];
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;

include('config.php');
/*Получение префикса БД*/
global $_CONFIG;
$dbp=$_CONFIG['dbprefix'];
$dbase_2=$dbp.'users';
/**/
/*Список плагинов*/
$filelist = scandir('plugins/');
$plugins_2=Array();
foreach ($filelist as $key=> $one){
	if (($one == '.') or ($one == '..') or !(is_dir('plugins/'.$one))) continue;
	if (!file_exists('plugins/'.$one.'/config.php')) continue;
	$conf='';
	include('plugins/'.$one.'/config.php');
	array_push($plugins_2,Array('dir'=>$one,'name'=>$conf['plugname']));
}
	$full_plugins_2=$plugins_2;
/**/
$_SESSION['madmin']['title']=$plugname;
$_SESSION['madmin']['js'].='<script type="text/javascript">var plugin_dir="'.$plugin_dir.'", site_path="'.$SITE_PATH.'";</script>
	<script type="text/javascript" src="'.$plugin_dir.'/fns_users.js"></script>
	<link type="text/css" href="'.$plugin_dir.'/style.css" rel="stylesheet" />';
$_SESSION['user']['ui']=true;
$_SESSION['user']['wysiwyg']=false;
$user_id=$_SESSION['m_user']['id'];
$SQL="SELECT `name`,`email` FROM `$dbase_2` WHERE `id`='$user_id'";
$quer=db_query($SQL);
global $plugins;
/*Собиралка всех пользователей*/
function all_users($dbase_2){
	$SQL="SELECT * FROM `$dbase_2`";
	$alluser=db_query($SQL);
	$users='<ul>';
	foreach ($alluser as $elem) {
		$off=(($elem['rights']&1)!=1)?'[disabled]':'';
		if($elem['id']!=1){
			$users.='<li class="clickableli" data-id="'.$elem['id'].'" data-name="'.$elem['login'].'">'.$elem['login'].$off.'</li>';
		}
	}
	$users.='</ul>';
	return $users;
}

/*============================*/
/*Собиралка плагинов, доступных пользователю*/
function acc_plug(){
	global $plugins;
	$userplg='<ul>';
	foreach ($plugins as $e){
		$userplg.='<li>'.$e['name'].'</li>';
	}
	$userplg.='</ul>';
	return $userplg;
}
function acc_plug_user($dbp,$full_plugins_2){
	$li='<ul id="ulplugred" style="display:none">';
	$SQL="SELECT * FROM `".$dbp."plugins`;";
		$rpl=db_query($SQL);
		foreach ($rpl as $elem){
			$flag=0;
			foreach ($full_plugins_2 as $elem2){
				if($elem['name']==$elem2['dir']){
					$li.='<li><input data-id="'.$elem['id'].'" type="checkbox" class="acccheck" />'.$elem2['name'].'</li>';
					$flag=1;
					break;
				} 
			}
			if (!$flag){
				$li.='<li style="display:none"><input data-id="'.$elem['id'].'" type="checkbox" class="acccheck" /></li>';
			}
		}
	$li.='</ul>';
		
	return $li;
}
/*============================*/
$tabs=($_SESSION['m_user']['id']==1)?'<li><a href="#tabs-cabinet">Личный кабинет</a></li>
			<li><a href="#tabs-admin">Администратор</a></li>':'<li><a href="#tabs-cabinet">Личный кабинет</a></li>';
$res.='
	<div id="tabs">
		<ul>
			'.$tabs.'
		</ul>
		<div id="tabs-cabinet">
			<form class="form_cab" id="u_form_1" method="post" action="hand.php">
				<fieldset>
					<legend>Реквизиты</legend>
					<label>Имя:</label><input type="text" name="t_name" id="username" value="'.$quer[0]['name'].'" /><br />
					<label>E-mail:</label><input type="text" name="t_email" id="usermail" value="'.$quer[0]['email'].'" />
				</fieldset><br />
				<fieldset>
					<legend>Смена пароля</legend>
					<label>Текущий пароль: </label><input type="password" name="t_passcur" id="curpswd" /><br />
					<label>Новый пароль: </label><input type="password" name="t_passnew" id="newpswd" />
					<label>Повторить пароль: </label><input type="password" name="t_passnew" id="newpswd2" />
					<input type="hidden" name="mode_h" value="changereq" />
				</fieldset><br />
				<a href="#" class="button" id="firstsubmit">Изменить</a>
			</form>
			<div class="pluglist">
				<b>Доступные плагины:</b>
				'.acc_plug().'
			</div>
			<div class="cb"></div>
		</div>
	<div id="loading"></div>
	<div id="dialog_ok" class="hidden"></div>
	<div id="dialog_false" class="hidden"></div>
	';
if($_SESSION['m_user']['id']==1){
	$res.='<div id="tabs-admin">
			<table width="100%">
				<tr>
					<td width="250" style="vertical-align: top">
						<div class="pluglist">
						<b>Пользователи:</b>
						'.all_users($dbase_2).'
						</div>
					</td>
					<td style="padding:0 20px">
						<b>Управление пользователями</b><br /><br />
						<form class="form_cab" id="user_ruler" style="float:none;height:0;overflow:hidden;">
							<b id="hideuserid" data-id=""></b><br /><br />
							<label>Новый пароль: </label><input type="password" id="t_passnew"/><br />
							<label>Повтор пароля: </label><input type="password" id="t_passnew2"/><br />
							<label>Имя</label><input type="text" id="redname" /><br />
							<label>Email</label><input type="text" id="redemail" /><br />
							<label>Активен</label><input type="checkbox" id="is_off" /><br /><br />
							<a href="#" id="red_change" class="button">Изменить</a><br /><br />
							<a href="#" id="red_delete" class="button">Удалить пользователя</a>
							<a href="#" id="red_delete_true" class="button">Подтвердить удаление</a>
							</div>
						</form><br />
						
						<div class=""><hr />
						<div class="" id="adduserform">
							<form class="form_cab" style="float:none">
							<label>Login*</label><input type="text" id="newlogin"  />
							<label>Пароль*</label><input type="password" id="newpass" />
							<label>Пароль еще раз*</label><input type="password" id="newpass2" />
							<label>Имя</label><input type="text" id="newname" />
							<label>Email</label><input type="text" id="newemail" />
							</form>
						</div>
						<br />
	
						<a href="#" id="adduser" class="button">Добавить пользователя</a> 
						
					</td>
					<td width="250" style="vertical-align: top">
						<div class="pluglist">
						<b>Доступные плагины:</b>
						'.acc_plug_user($dbp,$full_plugins_2).'
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div id="dialog_msg" title="Удалить пользователя?" class="hidden">Подтвердить удаление пользователя?</div>
';
}
		

//echo '<pre>';
// var_dump($req);
// echo '</pre>';

echo $res;
?>