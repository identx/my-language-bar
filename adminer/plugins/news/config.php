<?php
	/* Добавлены категории */
	global $dbase,$dbase_cats,$news_page;
	
	$conf['plugname']="Отзывы";
	$conf['version']='1.3.1';
	$conf['lastRecords']=50;
	
	
	$_SESSION['KCFINDER']['method'] = 'fit';
	$_SESSION['KCFINDER']['thumbWidth'] = 225;
    $_SESSION['KCFINDER']['thumbHeight'] = 225;
	
	$dbase='review';
	$dbase_cats='reviews-cat';
	$news_page='/';
	// $news_page='новости';
	
?>
