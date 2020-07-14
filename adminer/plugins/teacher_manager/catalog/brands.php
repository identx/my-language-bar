<?php /*?>*/
if(!isset($loc)) exit(0);

global $conf;

function brand_snippet($one){
	global  $conf;
	$img=($one['img']!='')?$conf['path_to_brand'].$one['img']:'images/no-foto.png';
	return '<div class="tvrs_snippet">
			<a href="tools.php?p=brands&id='.$one['id'].'" >
				<span class="img_wrapper"><img src="'.$img.'"/></span>
				<span class="sn_name" title="'.$one['name'].'">'.$one['name'].'</span>			
			</a>
			<div class="sn_extra">'.($one['url']!=''?'<a class="sn_price" href="'.$one['url'].'">'.$one['url'].'</a>':'').'
			</div></div>';
}
if($_GET['del']>0){
	$sql="SELECT `img` FROM `"._DB_PREF_."brands` WHERE `id`='$_GET[del]' LIMIT 1;";
	$br=current(db_query($sql));
	if($br['img']){
		$res=db_query("DELETE FROM `"._DB_PREF_."brands` WHERE `id`='$_GET[del]' LIMIT 1;");
		unlink($conf['path_to_brand'].$br['img']);
	}
}
if(!isset($_GET['id'])){
	$sql="SELECT * FROM `"._DB_PREF_."brands`;";
	$res=db_query($sql);
	$TAG['content'].='<div class="tvrs_list">';
	foreach($res as $one){
		$TAG['content'].=brand_snippet($one);
	}
	$TAG['content'].='</div>';
	/*////ПЛАВАЮЩЕЕ МЕНЮ////*/
	$TAG['content'].='<div class="fixedMenu">
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" href="?p=brands&id=0">Добавить</a>	
	</div>';
}else{	

	$sql="SELECT * FROM `"._DB_PREF_."brands` WHERE `id`='$_GET[id]';";
	$brand=current(db_query($sql));
	$ID=$_GET['id']*1;
	$TAG['content'].='<form id="brand_editor" data-tid="'.$ID.'">';
		$TAG['content'].='<div id="attributes" class="cb">
		<h2>Добавить/изменить бренд</h2>
		<div class="attribute"><label>Название</label><input id="brand_name" type="text" placeholder="название" class="attr_val" value="'.htmlspecialchars($brand['name']).'"/></div>
		</div>';
		$TAG['content'].='
		<div class="image_upload" id="uploadForm">
			<a class="upload_button btn ui-button ui-state-default" href="#">+ Добавить изображение</a>
			<input id="uploader" type="file" multiple="false" style="display: none;" accept="image/jpeg,image/png,image/gif" />	
			<div class="img_list" id="brand_image" >';
			if($brand['img'])
				$TAG['content'].='<div data-id="'.$brand['id'].'" data-new="0" data-del="0"><img src="'.$conf['path_to_brand'].$brand['img'].'" /><a alt="Удалить" href="#" title="Удалить изображение" class="delete" ></a></div>';
		$TAG['content'].='</div></div>';		
		
	$TAG['content'].='</form>';

	$TAG['content'].='<div class="fixedMenu">
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" href="?p=brands&del='.$_GET['id'].'" id="tvrs_del" href="#">Удалить</a>
		<span class="step"></span>
		<a class="btn ui-button ui-state-default" id="brand_save" href="?p=brands">Сохранить</a>
		<a class="btn ui-button ui-state-default" href="?p=brands">Отмена</a>	
	</div>';
}
?>
