<?php 
include('../include/config.php');
include('../include/fns_db.php');
include('../include/class.catalog.php');

db_connect();

$catalog = new Catalog('mcur_');


$id = [$_POST['id_course']];

$catalog->showHiddens(true);
$packages = $catalog->getItems([['cat'=>$id]]);

foreach ($packages as $package) {
	$res .= '<option value="'.$package['price_d'].'">'.$package['name'].' '.num2str($package['name'], ['урок','урока','уроков']).' - '.number_format($package['price_d'], 0, '', ' ').' ₽</option>';
}


echo json_encode($res);

function num2str($n, $text_forms) {  
		$n = abs($n) % 100; 
		$n1 = $n % 10;
		if ($n > 10 && $n < 20) { return $text_forms[2]; }
		if ($n1 > 1 && $n1 < 5) { return $text_forms[1]; }
		if ($n1 == 1) { return $text_forms[0]; }
		return $text_forms[2];
	}



?>