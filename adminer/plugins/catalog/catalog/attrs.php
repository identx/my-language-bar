<?php 
if(!isset($loc)) exit(0);
if(!isset($_GET['id'])){	
	if(isset($_POST['edit'])){
		$_id=$_POST['edit'];
		$ttname=$_POST['name']; 
		if($_POST['edit']==0){
			$_id=db_query("INSERT INTO `"._DB_PREF_."templs` VALUES ('','".addslashes($ttname)."')");
		}
		$ttid=$_id;
		$q="UPDATE `"._DB_PREF_."templs` SET `name`='".addslashes($ttname)."' WHERE `id`='$ttid';";
		db_query($q);
		$attr_cur=Array();
		$attr_new=Array();
		$q="SELECT DISTINCT `attr` FROM `"._DB_PREF_."templ_attrs` WHERE (`templ`='$ttid');";
		$res=db_getItems($q);
		foreach($res as $r) array_push($attr_cur,$r['attr']);		
		if(isset($_POST['attr'])) 
			foreach($_POST['attr'] as $k=>$t) {
				array_push($attr_new,$k);
				($t!=0)?$attr_upd[$k]=$t:'';
			}
		$attr_add=Array();
		$attr_del=Array();
		$attr_del=array_diff($attr_cur,$attr_new);
		$attr_add=array_diff($attr_new,$attr_cur);
		$attr_del_all=implode(',',$attr_del);
		$q="DELETE FROM `"._DB_PREF_."templ_attrs` WHERE (`templ`=$ttid)AND(`attr` IN ($attr_del_all));";
		db_query($q);
		foreach($attr_add as $a){
			$q="INSERT INTO `"._DB_PREF_."templ_attrs` (`templ`,`attr`) VALUES ($ttid,$a);";
			db_query($q);
		};
		foreach($attr_upd as $k=>$a){
			$q="UPDATE `"._DB_PREF_."templ_attrs` SET `order`=$a WHERE (`attr`=$k AND `templ`=$ttid);";
			db_query($q);
		};
		if($_POST['cat_templ']!='') db_query("UPDATE `"._DB_PREF_."nodes` SET `template`=$ttid WHERE `id` IN ($_POST[cat_templ]);");
	}
	
	if($_GET['del']>0){
		db_query("DELETE FROM `"._DB_PREF_."templs` WHERE `id`='$_GET[del]';");
		db_query("DELETE FROM `"._DB_PREF_."templ_attrs` WHERE `templ`='$_GET[del]';");	
	} 
	
	$q="SELECT * FROM `"._DB_PREF_."templs` WHERE `id`>0 ORDER BY `name` ASC;";
	$tts=db_getItems($q); $cont='';
	foreach($tts as $one){			
		$cont.='<a href="?p=attrs&id='.$one['id'].'" class="tvr_item hvr ui-button ui-state-default">'.$one['name'].'</a>';
	};

	/*////ПЛАВАЮЩЕЕ МЕНЮ////*/
	$cont.='<div class="fixedMenu">
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" href="?p=attrs&id=0">Добавить</a>	
	</div>';
	 
}else{
	
	$sql="SELECT COUNT(*) as `cnt` FROM `"._DB_PREF_."tvrs` WHERE `templ`='$_GET[id]';";
	$tovars=current(db_getItem($sql));
	///получаем все группы///
	$sql="SELECT * FROM `"._DB_PREF_."attr_groups`;";
	$_grps=db_query($sql);
	$groups=Array(0=>'Без группы');
	foreach($_grps as $one)
		$groups[$one['id']]=$one['name'];
	
	///получаем все атрибуты///
	$sql="SELECT * FROM `"._DB_PREF_."templs` WHERE (`id`='$_GET[id]');";
	$tmpl=db_getItem($sql);
	$cont.='
	<form action="?p=attrs" class="form_attrs" method="post">
	<h3>Название шаблона</h3>
	<input type="text" class="text" name="name" value="'.htmlspecialchars($tmpl['name']).'" /> &nbsp; Товаров: <span id="attr_tovars_total">'.$tovars.'</span><br />
	<input type="hidden" name="edit" value="'.$_GET['id'].'" />
	<input type="hidden" name="cat_templ" id="cat_templ" value="" />
	<h3>Атрибуты</h3>';
	/*////ПЛАВАЮЩЕЕ МЕНЮ////*/
	$cont.='<div class="fixedMenu">
		<a href="#" id="attr_group_add" class="cb btn ui-button ui-state-default">+ группу</a>
		<a href="#" id="attr_add" class="cb btn ui-button ui-state-default">+ атрибут</a>
		<a href="#" id="attr_sorter" class="cb btn ui-button ui-state-default">Настройка фильтра</a>
			<span class="step2"></span>
		<a href="?p=attrs&del='.$_GET['id'].'" id="attr_templ_del" class="btn ui-button ui-state-default">Удалить шаблон</a> 
			<span class="step2"></span>
		<input type="submit" value="Сохранить" class="btn  ui-state-default" />
		<a href="?p=attrs" class=" btn ui-button ui-state-default">Назад</a>	
	</div>';
	
	///получаем атрибуты текущего шаблона///
	$attr_cur=Array();
	$sql="SELECT DISTINCT `attr`,`order` FROM `"._DB_PREF_."templ_attrs` WHERE `templ`='$_GET[id]';";
	$res=db_getItems($sql);
	foreach($res as $one) $attr_cur[$one['attr']]=$one['order'];
	///получаем все атрибуты///
	$sql="SELECT `"._DB_PREF_."attrs`.*	FROM `"._DB_PREF_."attrs` ORDER BY `group`, `title` ASC;";
	$attrs=db_getItems($sql);
	$curr_group=-1;
	$cont.='<div id="attributes">';
	foreach($attrs as $one){
		if($one['group']>$curr_group){
			if(isset($groups[$one['group']])){				
				$cont.=($one['group']>0?'</div>':'').'<h4 class="attr_group" data-id="'.$one['group'].'" id="group'.$one['group'].'"><span>'.$groups[$one['group']].'</span> <a href="#">...</a></h4><div>';
				unset($groups[$one['group']]);
			}
			$curr_group=$one['group'];
		}
		$chk=(isset($attr_cur[$one['id']]))?'checked="checked"':'';
		$unit=($one['unit']!='')?' ('.$one['unit'].')':'';
		$cont.='<span class="attr_item" id="attr'.$one['id'].'" data-info="'.str_replace('"','\'',json_encode($one)).'"><label><input type="checkbox" name="attr['.$one['id'].']" value="'.$attr_cur[$one['id']].'" '.$chk.' />'.$one['title'].$unit.'</label> <a href="#">&hellip;</a></span>';
	}
	foreach($groups as $key=>$one){
		$cont.='</div><h4 class="attr_group" data-id="'.$key.'" id="group'.$key.'"><span>'.$one.'</span><a href="#">...</a></h4><div>';
	}
	$cont.='</div></div>';
	$cont.='<br class="cb">

	</form>
	
	<form id="dialog_group" class="nodisplay">
		Название <br />
		<input type="text" id="dialog_ge_name" class="" value=""/>
		<input type="hidden" id="dialog_ge_id" value="0" />
	</form>
	
	<form id="dialog_attr_edit" title="Добавление атрибута" class="nodisplay">
		<input type="hidden" id="dialog_ae_id" value="0" />
		Название (RU):<br />
		<input type="text" id="dialog_ae_title" data-alias="dialog_ae_name" value="" class="txt" /><br />
		Имя переменной (EN):<br />
		<input type="text" id="dialog_ae_name" maxlength="50" value="" class="txt"/><br />
		Ед. измерения:<br />
		<input type="text" id="dialog_ae_unit" maxlength="10" value="" class="txt"/><br />
		Группа:<br />
		<select id="dialog_ae_group" class="txt"></select><br />
		Тип данных:<br />
		<select id="dialog_ae_type" class="txt">
			<option value="5" rel="string">строка до 250 символов</option>
			<option value="2" rel="int">целое число</option>
			<option value="6" rel="text">текст до 65535 символов</option>
			<option value="1" rel="bool">логический (да/нет)</option>
			<option value="3" rel="double">числовой вещественный</option>
			<option value="4" rel="date">дата</option>
			<option value="7" rel="list">выпадающий список</option>
		</select><br />
		<div class="data_select">
			Каждый элемент с новой строки:<br />
			<textarea id="dialog_ae_value" class="txt h1"></textarea>
		</div>
		<label><input type="checkbox" id="dialog_ae_opt1" value="1"  /> НЕ выводить в карточке</label>
	</form>
	
	<div id="attr_sorter_dialog">
		<p>Категории, фильтруемые этим набором атрибутов</p>
		<select multiple size="5" class="txt" id="as_cats"><option value="">-не использовать-</option>';
	$sql="SELECT * FROM `"._DB_PREF_."nodes` WHERE `root`>-1;";
	$nodes=db_query($sql);
		foreach($nodes as $one)
			$cont.='<option '.($one['template']==$tmpl['id']?'selected="selected"':'').' value="'.$one['id'].'">'.$one['name'].'</option>';
		$cont.='</select>	
		<p>Атрибуты, участвуемые в фильтрации</p>
		<div id="as_sortlist"></div>
	</div>
	';
}

$TAG['content']=$cont.'<br class="cb" />';

?>

