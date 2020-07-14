<?php /*?>*/
if(!isset($loc)) exit(0);

global $conf;

	$TAG['content'].='<h3 class="attr_group2"><span>Групповые операции с атрибутами (beta)</span></h3>
	<p>Здесь очень легко что-нибудь испортить из-за невнимательности. Сделайте резервную копию.</p>
	<p>Изменять можно только атрибуты типа текст, строка, число целое и число вещественное.</p>';
	
	// <option value="5" rel="string">строка до 250 символов</option>
	// <option value="2" rel="int">целое число</option>
	// <option value="6" rel="text">текст до 65535 символов</option>
	// <option value="1" rel="bool">логический (да/нет)</option>
	// <option value="3" rel="double">числовой вещественный</option>
	// <option value="4" rel="date">дата</option>
	// <option value="7" rel="list">выпадающий список</option>
	
	$sql="SELECT * FROM `"._DB_PREF_."attrs` WHERE `type` IN (5,6,2,3);";
	$res=db_query($sql);
	foreach($res as $one){
		$all_attrs.='<option value="'.$one['id'].'">'.$one['title'].'</option>';
	}
	
	$TAG['content'].='
	<form method="POST">
		<div class="element_wrapper short">
			Атрибут
			<select class="" id="" name="attr_id">
				'.$all_attrs.'
			</select>
		</div>
		<div class="element_wrapper">
			Новое значение
			<input type="text" name="attr_newval" class="" value=""/>
		</div>
		<div class="element_wrapper cb">
			Область охвата<br />
			<input type="hidden" name="attr_method" checked="checked" class="" value="0"/>
			<label><input type="radio" name="attr_method" class="" value="-1"/> Удалить все значения</label><br />
			<label><input type="radio" name="attr_method" class="" value="1"/> Только изменить значения</label><br />
			<label><input type="radio" name="attr_method" class="" value="2"/> Изменить и добавить значения</label>
		</div>
		<div class="element_wrapper short">
			<br />
			<input type="submit" value="Изменить" name="attr_change" class="btn  ui-state-default" />
		</div>
	</form><br />';
	if(isset($_POST['attr_method']) && $_POST['attr_method']){		//если изменяем
		$sql="SELECT * FROM `"._DB_PREF_."attrs` WHERE `id`='$_POST[attr_id]' LIMIT 1;";
		$res=db_query($sql);
		$attr=$res[0];
		
		switch($_POST['attr_method']){
			case '-1':
				$sql="DELETE FROM `"._DB_PREF_."values` WHERE `attr`='$_POST[attr_id]';";
				// $q=db_query($sql);
				$TAG['content'].='<div class="element_wrapper cb"><b>Сделано изменений: '.db_affected_rows().'</b></div>';
				$report.='<div class="element_wrapper cb"><b>Удалено значений: '.db_affected_rows().'</b></div>';
			break;
			case '1':
				$sql="UPDATE `"._DB_PREF_."values` SET `t$attr[type]`='$_POST[attr_newval]' WHERE `attr`='$_POST[attr_id]';";
				$q=db_query($sql);
				$report.='<div class="element_wrapper cb"><b>Сделано изменений: '.db_affected_rows().'</b></div>';
			break;
			case '2':
				$sql="DELETE FROM `"._DB_PREF_."values` WHERE `attr`='$_POST[attr_id]';";
				$q=db_query($sql);
				$sql="SELECT `mcat_tvrs`.`id` FROM `mcat_templ_attrs`
					RIGHT JOIN `mcat_tvrs` ON `mcat_tvrs`.`templ`=`mcat_templ_attrs`.`templ`
					WHERE `attr`='$_POST[attr_id]';";
				$res=db_query($sql);
				if(count($res)>0){
					$sql="INSERT INTO `mcat_values`(`tvr`, `attr`, `t$attr[type]`) VALUES ";
					$_sqls=[];
					foreach($res as $one){
						$_sqls[]="($one[id],$_POST[attr_id],'$_POST[attr_newval]')";
					}
					$q=db_query($sql.implode(',',$_sqls));
					// echo $sql.implode(',',$_sqls);
				}
				$report.='<div class="element_wrapper cb"><b>Добавлено атрибутов: '.db_affected_rows().'</b></div>';
			break;
			default:
			break;			
		}
		$TAG['content'].=$report;
		// echo '<pre>';
		// print_r($attr);
		// echo '</pre>';
	}

	$TAG['content'].='<br class="cb"/><br /><br /><h3 class="attr_group2"><span>Массовые скидки</span></h3>
	';
	$TAG['content'].='
	<form method="POST">
		<div class="element_wrapper short">
			Применить для
			<select class="" id="" name="" disabled>
				<option>Все товары</option>
			</select>
		</div>
		<div class="element_wrapper short">
			Размер скидки в %
			<input type="text" name="discount_newval" class="spinner" placeholder="например, 15" value=""/>
		</div>
		<div class="element_wrapper cb">
			Область охвата<br />
			<input type="hidden" name="discount_method" checked="checked" class="" value="0"/>
			<label><input type="radio" name="discount_method" class="" value="1"/> Только там, где цена со скидкой = 0</label><br />
			<label><input type="radio" name="discount_method" class="" value="2"/> Только там, где цена со скидкой <> 0</label><br />
			<label><input type="radio" name="discount_method" class="" value="3"/> Везде</label><br />
		</div>
		<div class="element_wrapper short">
			<br />
			<input type="submit" value="Изменить" name="attr_change" class="btn  ui-state-default" />
		</div>
	</form><br />';
	
	if(isset($_POST['discount_method']) && $_POST['discount_method']){		//если изменяем
		$_price="`price`*".((100-$_POST['discount_newval'])/100);
		switch($_POST['discount_method']){
			case '1':
				$_sql=" `price_d`=0 ";
			break;
			case '2':
				$_sql=" `price_d`<>0 ";
			break;
			case '3':
				$_sql=" 1 ";
			break;
		}
		$sql="UPDATE `"._DB_PREF_."tvrs` SET `price_d`=$_price WHERE `price`>0 AND ".$_sql.";";
		$q=db_query($sql);
		$report.='<div class="element_wrapper cb"><b>Сделано изменений: '.db_affected_rows().'</b></div>';
		$TAG['content'].=$report;
	}
	
	$TAG['content'].='';

?>
