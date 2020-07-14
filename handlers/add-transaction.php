<?php 
include('../include/config.php');
include('../include/fns_db.php');
include('../include/class.catalog.php');


$user = $_POST['user'];
$language = $_POST['language']; 
$package = $_POST['package'];
$course = $_POST['course'];
$sum = $_POST['amount'];
$status = 'Создан';
$time =   date("Y-m-d H:i:s");
db_connect();

$sql = "INSERT INTO `transactions` (user, sum, course, language, package, datatime, status) VALUES ('$user', '$sum', '$course', '$language', '$package', '$time', '$status')";

db_query($sql);

?>