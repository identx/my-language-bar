<?php
function output_header(){
$res='<!DOCTYPE html>
	<html><head>
	<title>Админка: <!--*TITLE*--></title>
	<meta http-equiv="Content-Language" content="ru" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link type="text/css" href="css/jquery-ui-1.9.2.custom.css" rel="stylesheet" />
	<link type="text/css" href="css/style.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.datepicker-ru.min.js"></script>
	<script type="text/javascript" src="js/uploader.js"></script>
	<link rel="stylesheet" type="text/css" href="sel2/css/select2.min.css" media="all" />
	<script src="sel2/js/select2.full.min.js"></script>
	<script type="text/javascript" src="js/fns.js"></script>
	<body>
	<div id="head">
		<a href="?p=cats" class="btn ui-button ui-widget ui-state-default">Категории</a>
		<a href="?p=firms" class="btn ui-button ui-widget ui-state-default">Фирмы</a>
		<a href="?p=attrs" class="btn ui-button ui-widget ui-state-default">Шаблоны</a>
		<a href="?p=tvrs" class="btn ui-button ui-widget ui-state-default">Товары/Записи</a>
	</div>
	<div id="site">
	';
	return $res;
}



function output_footer(){
	$res='</div>
</body>
</html>';
	return $res;
}
?>
