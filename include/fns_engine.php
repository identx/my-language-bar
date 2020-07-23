<?php

function engine_get_content($opts){
	switch($opts[2]){
		default:
		$a=engine_page('404');
		break;
		case '':
		$a=engine_page('index');
		break;
		case 'teachers':
		$a=engine_page('teachers');
		break;
		case 'teacher':
		$a=engine_page('teacher');
		break;
		case 'courses':
		$a=engine_page('courses');
		break;
		case 'payment':
		$a=engine_page('payment');
		break;
		case 'vacancies':
		$a=engine_page('vacancies');
		break;
		case 'contacts':
		$a=engine_page('contacts');
		break;
		case 'privacy':
		$a=engine_page('privacy');
		break;
		case 'checkout':
		$a=engine_page('checkout');
		break;
		case 'payment-success':
		$a=engine_page('payment-success');
		break;
		case 'payment-failed':
		$a=engine_page('payment-failed');
		break;
		
	}
	return $a;
};

function engine_redirect($path='',$wsp=false){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".((!$wsp)?"/":"")."".$path);
	exit;
};

function engine_page($page,$EXTRAPARAMS=''){
	$page=($page=='')?'index':$page;
	ob_start();
	include_once('./pages/p_'.$page.'.php');
	$res=ob_get_contents();
	ob_clean();
	return $res;
};



?>