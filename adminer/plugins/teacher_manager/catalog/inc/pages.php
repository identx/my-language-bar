<?php if(!isset($loc)) return;
// item текущей страницы подсвечивается классом .cur

function template_menu_select($dir){
	global $TAG;
	for($i=0;$i<sizeof($TAG['menu_items']);$i++){
		$m=$TAG['menu_items'][$i];
		if(!preg_match('/href=\".*'.$dir.'.*\"/i',$m)) continue;
		if(preg_match('/^(.*)class="([^"]*?)"(.*)$/i',$m,$sc)){ $TAG['menu_items'][$i]=preg_replace('/class="([^"]*?)"/i','class="cur '.$sc[2].'"',$m); break; };
		if(preg_match('/^(.*)class=\'([^\']*?)\'(.*)$/i',$m,$sc)){ $TAG['menu_items'][$i]=preg_replace('/class=\'([^\']*?)\'/i','class=\'cur '.$sc[2].'\'',$m); break; };
		$TAG['menu_items'][$i]=preg_replace('/<li/i','<li class="cur" ',$m);
		break;
	};
};

function template_menu_go($dir){
	global $TAG;
	/*
	$src=$_SERVER["pages"][0]['file'];
	$tit=$_SERVER['pages'][0]['title'];
	*/
	$src=NULL; $tit=NULL;
	template_menu_select($dir);
	for($i=0;$i<sizeof($_SERVER["pages"]);$i++){	
		if($_SERVER["pages"][$i]["link"]!=$dir) continue;
		if(!file_exists("pgs/".$_SERVER["pages"][$i]["file"])) break;
		$_SERVER['cur_page']=$_SERVER["pages"][$i];
		$src=$_SERVER["pages"][$i]["file"];
		$tit=$_SERVER['pages'][$i]['title'];
	};
	if(is_null($src)){
		$opt3=404;
		$src='error.php';
		$tit='Ошибка 404 | ';
	};
	$TAG['title']=$tit;
	return ("pgs/".$src);
};

require_once(template_menu_go($opt1));

?>