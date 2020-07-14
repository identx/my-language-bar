<?php 
header("Content-type: text/html; charset=utf-8"); 
session_start();
include_once "include/sajax.php";
include('config.php');
include('install.php');


$_SESSION['madmin']['js'].='<script type="text/javascript">var plugin_dir="'.$plugin_dir.'", site_path="'.$SITE_PATH.'";</script>
	<script type="text/javascript" src="'.$plugin_dir.'/js/script.js"></script>
	<link type="text/css" href="'.$plugin_dir.'/style.css" rel="stylesheet" />';

function addContent() {
$sql = "SELECT * FROM `mcat_constant`";
$res = db_query($sql);
$add = '<form action="" method="POST" id="myform"></form><table class="table">';
foreach($res  as $value) {
	$add.='
	<tr>
	<td><span>'.$value['param'].'</span></td>
	<td><input form="myform" type="text" id="val-'.$value['id'].'" value="'.$value['value'].'" ></td>
	<td><input form="myform" type="text" id="attr-'.$value['id'].'" value="'.$value['attr'].'" ></td>
	<td><button class="edit" data-edit="'.$value['id'].'">Изменить</button></td>
	<td><button class="del" data-id="'.$value['id'].'">Удалить</button></td>
	</tr>';
} 
$add.='</table>';
return $add;
}


// Добавление записи
function addConstant() {
	$json = $_POST['rsargs'][0];
	$data=str_replace('%u','\\u',$json);
	$dec = json_decode($data);
	$param = trim($dec[0]->value);
	$value = trim($dec[1]->value);
	$attr = trim($dec[2]->value);
	if((!empty($param)) && (!empty($value)) && (!empty($attr))) {
		$sq = "INSERT INTO `mcat_constant` ( param, value, attr) VALUES ( '$param','$value','$attr')";
		$q = db_query($sq); 
	}
	return addContent();
}


//Изменение записи 
function editConstant() {
	$json = $_POST['rsargs'][0];
	$data=str_replace('%u','\\u',$json);
	$dec = json_decode($data);
	$id_ = trim($dec->v);
	$val_ = trim($dec->val);
	$attr_ = trim($dec->attr);

	$sql = "UPDATE `mcat_constant` SET value='$val_', attr='$attr_' WHERE id = '$id_'";
	$query = db_query($sql);
	return addContent();
}

//Удаление записи 

function deleteConstant() {

	$js = $_POST['rsargs'][0];
	$sql = "DELETE FROM `mcat_constant` WHERE id = '$js'";
	$query = db_query($sql);
	return addContent();
}


	global $sajax_request_type;
	$sajax_request_type  =  "POST";
	sajax_init();
	sajax_export("addConstant");
	sajax_export("editConstant");
	sajax_export("deleteConstant");
	sajax_handle_client_request();
	echo '<script>';	
	sajax_show_javascript();
	echo '</script>';
?>



<section class="desc">
		<div class="d-desc">
			<h2>Список констант</h2>
				<div class="table">
			<?php echo addContent(); ?>
		</div>
</div>
	</section>
	<section class="add">
		<div class="d-add">
			<h2>Добавить константу</h2>
			<?php 
			?>	
		<div>
			
			<table>
				<form action="" method="POST" class="form_one">
				<tr>
					<td><input  type="text" name="param" placeholder="Название"></td>
					<td><input  type="text" name="value" placeholder="алиас"><br/></td>
				</tr>
				<tr>
					<td colspan="2"><input style="width: 98%" type="text" name="attr" placeholder="значение"></td>
				</tr>
				</form>
				<tr>
					<td colspan="2"><button style="float: right;" class="button">Добавить</button></td>
				</tr>

			</table>					
			
		</div>
		<div class="dell"></div>
		</div>
		<div id="loading"></div>
		</section>
