<?php
/* [*******News v1.1*******]
	+ устранены дыры в безопасности
	+ добавлена возможность создания категорий
*/

include('config.php');
// echo '<pre>';
 // print_r($_SESSION); 
 // echo '</pre>';


if ($_SESSION['madmin'][$dbase]!=true){
	$init=create_table();	
	if($init) {
		$_SESSION['madmin'][$dbase]=true;
	} else {
		echo 'Не удается создать таблицу';
		$_SESSION['madmin'][$dbase]=false;
	}
}

$_SESSION['madmin']['title']=$plugname;
$_SESSION['madmin']['js'].='<script type="text/javascript">var plugin_dir="'.$plugin_dir.'", site_path="'.$SITE_PATH.'", news_page="'.$news_page.'";</script>
	<script type="text/javascript" src="'.$plugin_dir.'/fns_news.js"></script>
	<link type="text/css" href="'.$plugin_dir.'/style.css" rel="stylesheet" />';
$_SESSION['user']['ui']=true;
$_SESSION['user']['wysiwyg']=true;

$res.='
<table id="main_table">
	<tr>
		<td id="td_newslist">
			<div id="tabs">
				<ul>
				<li><a href="#tabs-news">'.$conf['plugname'].'</a></li>
				</ul>
				<div id="tabs-news">
						<div class="news_toolbar">
							<a href="#" id="tb_add">+ Добавить</a>
							<a href="#" id="tb_del">- Удалить</a>
						</div>
						<ul id="newslist" class="newslist">				
						</ul>
				</div>
			</div>
		</td>
		<td>
			<form class="" id="editor_container" name="editor_container" method="post">
				<input type="hidden" name="news_id" id="news_id" value=""/>
				<label>*ФИО</label><input type="text" value="" name="news_title" id="news_title" class="editor-meta" required/><br />
				<label>*Cтрана, город</label><textarea id="news_announce" name="news_announce" class="editor-meta" required></textarea><br /><br />
				<label id="news_img_label">Изображение <span>(показать)</span><br /><img src="" id="news_img_show" alt="" /></label><input type="text" value="" name="news_img" id="news_img" class="editor-meta"/> <input type="button" id="news_img_select" value="Выбрать"/>				
				<br /><br />
				<textarea id="editor" name="editor"></textarea><br />
				
							
				
				<table id="news_table"><tr>
				<label for="news_show">Показывать</label><select id="news_show"><option value="1">Да</option><option value="0">Нет</option></select><br />
				</select>
				<br /></td>
					<td><input type="submit" value="Добавить" id="editor_save"/><br />
				</tr></table>

			</form>			
		</td>
	</tr>
</table>
<div id="dialog_ok" class="hidden"></div>
<div id="dialog-del" class="hidden">
	Это действие необратимо. Удалить элемент?
</div>
<div id="dialog-del-cat" class="hidden">
	Это действие необратимо. Удалить категорию?
	<p>Статей в этой категории: <span></span></p>
</div>
<div id="dialog-cat_add" class="hidden">
	<form>
		<input type="text" id="cat_add"/>
	</form>
</div>
<div id="dialog-news_move" class="hidden">
	<h4>Удаленная категория содержала записей: <span>7</span></h4>
	<form>
		<label for="news_category2">Категория для перемещения статей: </label><select id="news_category2"/><option value="0">Стандартная</option></select><br /><br />
		<p>Что сделать с этими статьями?</p>
	</form>
</div>
<div id="loading"></div>
';

//echo '<pre>';
// var_dump($req);
// echo '</pre>';

echo $res;

function create_table(){
	global $dbase,$dbase_cats;
	$sql="SHOW TABLES LIKE '$dbase';";
	$res=db_query($sql);
	$t1=db_num_rows();
	$sql="SHOW TABLES LIKE '$dbase_cats';";
	$res=db_query($sql);
	$t2=db_num_rows();
	if ($t1*$t2>0) return true;  //надо бы еще проверять структуру таблицы
	$sql="CREATE TABLE IF NOT EXISTS `$dbase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `alias` varchar(255) NOT NULL,
  `anounce` text NOT NULL,
  `keyws` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `imp` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `show` tinyint(1) NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	db_query($sql);
	$sql="CREATE TABLE IF NOT EXISTS `$dbase_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;";
	db_query($sql);
	$sql="SHOW TABLES LIKE '$dbase';";
	$res=db_query($sql);
	$t1=db_num_rows();
	$sql="SHOW TABLES LIKE '$dbase_cats';";
	$res=db_query($sql);
	$t2=db_num_rows();
	if ($t1*$t2>0){
		return true; 
	}else
		return false;
}
?>
