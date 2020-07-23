<?php 
include('../include/config.php');
include('../include/fns_db.php');
include('../include/class.catalog.php');

db_connect();
function getFormSignature($sum, $account, $desc, $secretKey) {
	$hashStr = $account.'{up}'.$desc.'{up}'.$sum.'{up}'.$secretKey;
	return hash('sha256', $hashStr);
}


$user = $_POST['user'];
$language = $_POST['language']; 
$package = $_POST['package'];
$course = $_POST['course'];
$sum = $_POST['sum'];
$status = '0';
$time =   date("Y-m-d H:i:s");
$currency = $_POST['currency'];
$order_id = $_POST['account'];
$desc = $_POST['desc'];



$sql = "INSERT INTO `transactions` (user, sum, course, language, package, datatime, status) VALUES ('$user', '$sum', '$course', '$language', '$package', '$time', '$status')";

db_query($sql);

$sql = "SELECT `id` FROM `transactions` ORDER BY `id` DESC LIMIT 1 ";
$res = db_query($sql);


$order_id = $res[0]['id'];

$response = [];
$response['id'] = $order_id;
$response['sign'] = getFormSignature($sum, $order_id, $desc, '64d93f1509fb7d78953747ff1c51cdfc');

echo json_encode($response);




?>