<?php 

include($_SERVER['DOCUMENT_ROOT'].'/include/class.catalog.php');
include($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
include($_SERVER['DOCUMENT_ROOT'].'/include/fns_db.php');
$con = db_connect();
$response = array('status' => false);

$res = $_POST;

// echo '<pre>';
// print_r($res);
// echo '</pre>';


$cat = new Catalog('mcat_');
$items = $cat->getItems(array(array('cat'=>array(1))));
$option = '';
$selected = '';
foreach ($items as $item) {
	
	$one_item = $cat->getItem($item['id']);
	if($res['value'] == $one_item['id']){
		$selected = 'selected="selected"';
	}else {
		$selected = '';
	}
	$option .= '<option value="'.$one_item['id'].'" '.$selected.'>'.$one_item['attrs']['fio-roditelya']['val'].'</option>';
	
}
$response['option'] = $option;
$response['status'] = true;



db_disconnect();

echo json_encode($response);

?>
