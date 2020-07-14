<?php
if(!isset($loc)) return;

function mlower($s){ return mb_strtolower($s); };

function template_get($name,$par){	
	global $template;
	$v=$template[strtoupper($name)];
	if(sizeof($v)<=0) return '';
	$s=Array(); $r=Array();
	foreach($par as $k => $p){ array_push($s,'<!--*'.strtoupper($k).'*-->'); array_push($r,$p); };
	return preg_replace('/<!--\*[^\*]*?\*-->/i','',str_replace($s,$r,$v)); // нужно будет уйти от регулярки
};

$template=Array();
preg_match_all('/(?><!--\*TEMPLATE\*-->)([\s\S.]*?)(?><!--\*TEMPLATE\*-->)/i',$ft,$mt);
$tmpl="";
if(sizeof($mt)>0){ $mt=$mt[1]; foreach($mt as $m){ $tmpl.=$m; }; unset($mt); };
preg_match_all('/<!--\*(_[A-Z_0-9]*)\*-->([\s\S.]*?)<!--\*\1\*-->/i',$tmpl,$mt);
if(sizeof($mt)>0){
	$ms=$mt[2]; $mt=$mt[1];
	for($i=0;$i<sizeof($mt);$i++){ $template[$mt[$i]]=$ms[$i]; };
};

?>
