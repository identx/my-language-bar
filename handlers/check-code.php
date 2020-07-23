<?php 
include('../include/config.php');
include('../include/fns_db.php');
db_connect();
$sql = "SELECT SUBSTR(md5(`id`), 1, 7) AS code, `name`, `id` FROM `mstu_tvrs`";
$res = db_query($sql);


$r = [];
foreach ($res as $value) {
	if($value['code'] != $_POST['code']) {
		$r['info'] = '<div class="alert alert-danger">
		<strong>Пользователь не найден.</strong>
		</div>';
		$r['status'] = false;
	}else {
		$r['info'] = '<div class="alert alert-success">
		<strong data-id="'.$value['id'].'">'.$value['name'].'</strong>
		</div>';
		$r['status'] = true;
	}
}

echo json_encode($r);

?>

