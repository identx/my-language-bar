<?php /*?>*/
if(!isset($loc)) exit(0);

global $conf;

function tvrs_snippet($one){
	global  $conf;
	$img=($one['img']!='')?$conf['path_to_image'].current(array_keys($conf['size'])).'-'.$one['img']:'images/no-foto.png';
	return '<div class="tvrs_snippet '.($one['opts']&1==1?'alpha':'').'">
			<a href="tools.php?p=tovars&id='.$one['id'].'" >
				<span class="img_wrapper"><img src="'.$img.'"/></span>
				<span class="sn_name" title="'.$one['name'].'">'.$one['name'].'</span>			
			</a>
			<div class="sn_extra"><span class="sn_price">'.$one['price'].' руб.</span>
				<span class="sn_count">'.$one['count'].' шт.</span>
			<label><input type="checkbox" class="sn_ckecker" name="tovar[]" value="'.$one['id'].'" />
			ID '.$one['id'].'</label></div></div>';
}

function tvrs_nodes(){
	$q="
	SELECT `"._DB_PREF_."nodes`.*, count(`"._DB_PREF_."tvrs`.`id`) as `cnt`
	FROM `"._DB_PREF_."tvrs`
		RIGHT JOIN `"._DB_PREF_."nodes` ON `"._DB_PREF_."nodes`.`id`=`"._DB_PREF_."tvrs`.`cat`
	GROUP BY `"._DB_PREF_."nodes`.`name`
	ORDER BY `"._DB_PREF_."nodes`.`root` DESC;
	";
	return db_getItems($q);
};

function tvrs_options($arr,$cat,$t=''){
	$res='';
	foreach($arr as $a){
		$res.='<option value="'.$a['id'].'" '.(($a['id']==$cat)?'selected="selected"':'').'>'.$t.$a['name'].' ('.$a['cnt'].')'.'</option>';
		if(sizeof($a['c'])>0){
			$res.=tvrs_options($a['c'],$cat,$t.'&nbsp;&nbsp;&nbsp;');
		};
	};
	return $res;
};

function tvrs_del(){
	global $conf;
	if(isset($_GET['del'])){
		$del=$_GET['del'];
		//1.Товар 2. Атрибуты 3. Имаи
		$sql="DELETE FROM `"._DB_PREF_."tvrs` WHERE `id`='$del'";
		db_query($sql);
		$sql="DELETE FROM `"._DB_PREF_."values` WHERE `tvr`='$del'";	
		db_query($sql);
		$sql="SELECT `name` FROM `"._DB_PREF_."images` WHERE `id_tvr`='$del'";
		$res=db_getItems($sql);
		foreach($res as $one)
			foreach($conf['size'] as $pref=>$sz){
				unlink($conf['path_to_image'].$pref.'-'.$one['name']);
			}
		$sql="DELETE FROM `"._DB_PREF_."images` WHERE `id_tvr`='$del'";	
		db_query($sql);
	}
}

tvrs_del();
$nd=tvrs_nodes();
$nds=Array();
$f=0;
foreach($nd as $n){
	if($f<$n['id']) $f=$n['id'];
	$nds[$n['id']]=$n;
	$nds[$n['id']]['c']=Array();
	for($i=$n['id']+1;$i<=$f;$i++){
		if($nds[$i]['root']==$n['id']){
			$nds[$n['id']]['c'][$i]=$nds[$i];
			unset($nds[$i]);
		};
	};
};

$sql="SELECT * FROM `"._DB_PREF_."templs`";
$tmps=db_getItems($sql);
foreach($tmps as $o)
	$templs[$o['id']]=$o['name'];
$cat=isset($_GET['cat'])?$_GET['cat']:current(current($nds));
$cat_list=tvrs_options($nds,$cat);

if(!isset($_GET['id'])){
	$TAG['content'].='
	<br /><div class="tvrs_category"><select id="tvrs_category" class="superselect">'.$cat_list.'</select></div>';
	$TAG['content'].='<form action="" method="get" id="tvrs_search"><input name="search" class="text short" placeholder="поиск по имени или артикулу" value="'.$_GET['search'].'"/><input type="hidden" name="p" value="tovars"/> <input type="submit" class="btn ui-button ui-state-default" value="Искать"/>
	</form>
	<form action="" method="get" id="tvrs_filter">
		<br /><label><input type="hidden" name="cat" value="'.$_GET['cat'].'" /><input type="hidden" name="p" value="tovars" /><input type="hidden" name="f_having" value="0"><input type="checkbox" '.($_GET['f_having']==1?'checked="checked"':'').' id="filter_having" name="f_having" value="1"/> Только в наличии</label>
	</form>
	<br />
	<a class="btn ui-button ui-state-default" class="tvr_addnew" href="?p=tovars&id=0&tmpl=1&cat='.$_GET['cat'].'">+ Добавить товар</a>
	<br />
	';
	
	if(isset($_GET['search'])){
		//поиск
		$s=$_GET['search'];
		$where="(`"._DB_PREF_."tvrs`.`name` LIKE '%$s%' OR `"._DB_PREF_."tvrs`.`article` LIKE '%$s%')";
	} else {
		//список товаров
		$where="(`"._DB_PREF_."tvrs`.`cat`=$cat)";
		if(isset($_GET['f_having']))$where.=" AND (`"._DB_PREF_."tvrs`.`count`>=".$_GET['f_having'].")";
	}
	$sql="
		SELECT `"._DB_PREF_."tvrs`.*, `"._DB_PREF_."templs`.`name` AS 'template',
		`"._DB_PREF_."images`.`name` AS 'img' 
		FROM `"._DB_PREF_."tvrs`
		LEFT JOIN `"._DB_PREF_."templs` ON (`"._DB_PREF_."templs`.`id`=`templ`)
		LEFT JOIN `"._DB_PREF_."images` ON (`"._DB_PREF_."images`.`id_tvr`=`"._DB_PREF_."tvrs`.`id`) AND (`"._DB_PREF_."images`.`index`=0)
		WHERE $where
		ORDER BY `template`, `"._DB_PREF_."tvrs`.`name`";
		// echo $sql;
	$res=db_getItems($sql);

	// $res=tvr_proc($res);
	$templ=0;
	$TAG['content'].='<div class="tvrs_list">';
	foreach($res as $one){
		if ($templ!=$one['templ']){
			$templ=$one['templ'];
			$TAG['content'].='<h3 class="tvrs_divider "><a class="plus btn ui-button ui-state-default" title="добавить товар" href="?p=tovars&id=0&tmpl='.$one['templ'].'&cat='.$_GET['cat'].'">+</a><span class="step"></span><a title="изменить шаблон" href="?p=attrs&id='.$one['templ'].'">'.$one['template'].'</a></h3>';
		}
		$TAG['content'].=tvrs_snippet($one);
	}
	$TAG['content'].='<br class="cb"/><br /><br /><br /><br /><br /><br /></div>';	
	$TAG['content'].='<div class="fixedMenu">
		<span class="step"></span>
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" class="tvr_addnew" href="?p=tovars&id=0&tmpl=1&cat='.$_GET['cat'].'">Добавить товар</a>	
	</div>';
}else{
	//форма редактирования
	$ID=$_GET['id'];
	if($ID==0){
		$tovar=array('id'=>0,'cat'=>($_GET['cat']>0)?$_GET['cat']:'1','templ'=>'1','opts'=>'0','article'=>'','brand'=>'0','name'=>'','name_def'=>'','alias'=>'','price'=>'0','price_d'=>'0','count'=>'1');	
	}else{
		$sql="SELECT * FROM `"._DB_PREF_."tvrs` WHERE `id`='$ID'";
		$tovar=current(db_getItems($sql));			
	}
	$tovar['templ']=($_GET['tmpl']>0)?$_GET['tmpl']:$tovar['templ'];
	$TAG['content'].='<form method="post" id="tovar_editor" data-tid="'.$ID.'">
	<div id="tovar_basic_props">
	<br /><div class="tvrs_category">Категория<br /><select name="cat" id="tvrs_category_item" class="def_val superselect">'.tvrs_options($nds,$tovar['cat']).'</select></div>';
	$TAG['content'].='<div class="tvrs_category">Шаблон<br /><select id="tvrs_category2" class="def_val superselect" name="templ"><option value="0">- нет шаблона -</option>';
	foreach($tmps as $one){
		$TAG['content'].='<option '.(($one['id']==$tovar['templ'])?'selected="selected"':'').' value="'.htmlspecialchars($one['id']).'">'.htmlspecialchars($one['name']).'</option>';
	}
	$TAG['content'].='</select> &nbsp; <a href="#" id="tovar_retempl" data-href="?p=tovars&id='.$ID.'&cat='.$tovar['cat'].'&tmpl=" class="fr btn ui-button ui-state-default">Cменить шаблон</a> </div>
	<div class="element_wrapper cb">Название<input class="def_val" type="text" id="tvr_name" data-alias="tvr_alias" name="name" value="'.htmlspecialchars($tovar['name']).'"/></div>';
	$TAG['content'].='<div class="element_wrapper">URL<input class="def_val" type="text" id="tvr_alias" name="alias" value="'.$tovar['alias'].'"/></div>';
	if($tovar['name_def']!='') $TAG['content'].='<div class="element_wrapper cb">Название из прайса<input class="def_val" type="text" disabled="disabled" name="name_def" value="'.htmlspecialchars($tovar['name_def']).'"/></div>';
	$TAG['content'].='<div class="def_val element_wrapper short cb">Цена<input type="text" class="def_val" name="price" value="'.htmlspecialchars($tovar['price']).'"/></div>';
	$TAG['content'].='<div class="def_val element_wrapper short">Цена со скидкой<input type="text" class="def_val" name="price_d" value="'.htmlspecialchars($tovar['price_d']).'"/></div>';
	$TAG['content'].='<div class="def_val element_wrapper shortest">Количество<input type="text" name="count" class="spinner def_val" value="'.htmlspecialchars($tovar['count']).'"/></div>';
	$TAG['content'].='<div class="def_val element_wrapper short cb">Артикул<input type="text" class="def_val" name="article" value="'.htmlspecialchars($tovar['article']).'"/></div>';
	$TAG['content'].='<div class="def_val element_wrapper shortest">Скрыть<br /><select class="'.(($tovar['opts'] & 1==1)?' attention':'').'" id="sel_hide" name="hide"><option value="0" >Нет</option><option '.(($tovar['opts'] & 1==1)?'selected="selected"':'').' value="1">Да</option></select></div>';
	$TAG['content'].='<div class="element_wrapper short" id="brander">Производитель<br /><select class="def_val" name="brand"><option data-img="images/noimage.png"	value="0">не выбрано</option>';
	$sql="SELECT * FROM `"._DB_PREF_."brands` ORDER BY `name`";
	$firms=db_getItems($sql);
	
	foreach($firms as $one){
		$TAG['content'].='<option data-img="'.$conf['path_to_brand'].$one['img'].'" '.(($one['id']==$tovar['brand'])?'selected="selected"':'').' value="'.$one['id'].'">'.$one['name'].'</option>';
	}
	$TAG['content'].='</select></div><div class="element_wrapper shortest"><img id="brandimg" src="images/noimage.png" /></div>
	</div>';
	/*////ИЗОБРАЖЕНИЯ////*/
	$TAG['content'].='
	<div class="image_upload" id="uploadForm">
		<a class="upload_button btn ui-button ui-state-default" href="#">+ Добавить изображение</a> 
		<input id="uploader" type="file" multiple="true" style="display: none;" accept="image/jpeg,image/png,image/gif" />	
		<div class="img_list">';
		$sql="SELECT * FROM `"._DB_PREF_."images` WHERE `id_tvr`='".$tovar['id']."' ORDER BY `index`";
		$imgs=db_query($sql);
		foreach($imgs as $one){
			$TAG['content'].='<div data-id="'.$one['id'].'" data-new="0" data-del="0"><img src="'.$conf['path_to_image'].current(array_keys($conf['size'])).'-'.$one['name'].'" title="'.$one['name'].'" /><a alt="Удалить" href="#" class="delete" ></a><input type="text" placeholder="описание изображения" value="'.htmlspecialchars($one['caption']).'"/></div>';
		}
		$TAG['content'].='</div>
	</div>';
	/*////ПЛАВАЮЩЕЕ МЕНЮ////*/
	$TAG['content'].='<div class="fixedMenu">
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" href="?p=tovars&cat='.$tovar['cat'].'&del='.$_GET['id'].'" id="tvrs_del" href="#">Удалить</a>
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" id="tvrs_save" href="'.$_SERVER['PHP_SELF'].'?p=tovars&cat='.$tovar['cat'].'">Сохранить</a>
		<a class="btn ui-button ui-state-default" href="?p=tovars&cat='.$tovar['cat'].'">Отмена</a>	
	</div>';
	
	/*////АТРИБУТЫ////*/
	if($ID==0){
		$sql="		
		SELECT `"._DB_PREF_."attrs`.*, `"._DB_PREF_."attr_groups`.`name` as `gr_name`
		FROM `"._DB_PREF_."attrs`
			LEFT JOIN `"._DB_PREF_."attr_groups` ON `"._DB_PREF_."attr_groups`.`id`=`group`
			RIGHT JOIN `"._DB_PREF_."templ_attrs` ON `"._DB_PREF_."templ_attrs`.`attr`=`"._DB_PREF_."attrs`.`id`
		WHERE `"._DB_PREF_."templ_attrs`.`templ`=$tovar[templ] ORDER BY `group`";
	}else{
		$sql="
		SELECT `"._DB_PREF_."attrs`.*, `"._DB_PREF_."attr_groups`.`name` as `gr_name`, `"._DB_PREF_."values`.*
		FROM `"._DB_PREF_."attrs`
			INNER JOIN `"._DB_PREF_."templ_attrs` ON `"._DB_PREF_."templ_attrs`.`attr`=`"._DB_PREF_."attrs`.`id`
			INNER JOIN `"._DB_PREF_."tvrs` ON `"._DB_PREF_."templ_attrs`.`templ`=$tovar[templ]
			LEFT JOIN `"._DB_PREF_."attr_groups` ON `"._DB_PREF_."attr_groups`.`id`=`group`
			LEFT JOIN `"._DB_PREF_."values` ON (`"._DB_PREF_."values`.`attr`=`"._DB_PREF_."attrs`.`id` AND `"._DB_PREF_."values`.`tvr`=`"._DB_PREF_."tvrs`.`id`)
		WHERE `"._DB_PREF_."tvrs`.`id`=$tovar[id] ORDER BY `group`";
	}
	// echo $sql;
	$attrs=db_getItems($sql);	
	$TAG['content'].='<div id="attributes" class="cb">';	
	$curr_group=-1;		
	foreach($attrs as $attr){
		if($attr['group']>$curr_group){
			$TAG['content'].='<h3 class="attr_group2"><span>'.($attr['gr_name']!=''?$attr['gr_name']:'
			Общие характеристики').'</span></h3>';
			$curr_group=$attr['group'];
		}	
		$caption=$attr['title'].($attr['unit']!=''?' ('.$attr['unit'].')':'');
		$name='a_'.$attr['id'].'_'.$attr['type'].'_'.$attr['name'];
		switch($attr['type']){
			case 1:
				/*BOOLEAN*/
				$true=array('0'=>'Нет','1'=>'Да');
				$TAG['content'].='<div class="attribute"><label>'.$caption.'</label><select name="'.$name.'" class="attr_val type_1"><option value="">- не указано -</option>';
				foreach($true as $key=>$t){
					$TAG['content'].='<option '.(($key===$attr['t1'])?'selected="selected"':'').' value="'.$key.'">'.$t.'</option>';
				}
				$TAG['content'].='</select></div>';
			break; 
			case 2: case 3: case 5: 
				/*INTEGER, FLOAT, STRING*/
				$TAG['content'].='<div class="attribute"><label>'.$caption.'</label><input name="'.$name.'" type="text" placeholder="'.($attr['type']==5?('строка'):($attr['type']==2?'целое число':'действительное число')).'" class="attr_val type_'.$attr['type'].'" value="'.htmlspecialchars($attr['t'.$attr['type']]).'"/></div>';
			break;
			case 4:
				/*DATE*/
				$TAG['content'].='<div class="attribute"><label>'.$caption.'</label><input class="attr_val type_4 " type="text" placeholder="дата" name="'.$name.'" value="'.htmlspecialchars($attr['t4']).'"/></div>';
			break;
			case 6:
				/*TEXTAREA*/
				$TAG['content'].='<div class="attribute"><label>'.$caption.'</label><textarea class="attr_val type_6" id="attr_'.$attr['name'].'" name="'.$name.'">'.$attr['t6'].'</textarea></div>';
			break; 
			case 7:
				/*SELECT*/
				$list=explode(PHP_EOL,$attr['value']);
				// $list=explode('
// ',$attr['value']);
				$TAG['content'].='<div class="attribute"><label>'.$caption.'</label><select name="'.$name.'" class="attr_val type_1"><option value="">- не указано -</option>';
				foreach($list as $t){
					$TAG['content'].='<option '.((trim($t)==trim($attr['t7']))?'selected="selected"':'').' value="'.htmlspecialchars(trim($t)).'">'.trim($t).'</option>';
				}
				$TAG['content'].='</select></div>';
			break;

		}
	}	
	$TAG['content'].='</div></form><br /><br /><br /><br /><br />';
}
?>
