<?php if(!isset($loc)) exit(0);

function nodes($conn){
	$q="
SELECT *
FROM `"._DB_PREF_."nodes` WHERE `id`>0
ORDER BY `root` DESC;";
	return db_getItems($q,$conn);
};

function node($id,$conn){
	$q="
SELECT *
FROM `"._DB_PREF_."nodes`
WHERE `id`=$id;";
	return db_getItem($q,$conn);
};

function node_add($root,$name,$link,$scmt,$fcmt,$conn){
	$scmt=nl2br($scmt);
	$fcmt=nl2br($fcmt);
	$q="
INSERT INTO `"._DB_PREF_."nodes` (`root`,`name`,`link`,`scomment`,`fcomment`)
VALUES ($root,\"$name\",\"$link\",\"$scmt\",\"$fcmt\");";
	db_query($q,$conn);
	return db_insert_id($conn);
};

function node_edit($id,$name,$link,$scmt,$fcmt,$conn){
	$scmt=nl2br($scmt);
	$fcmt=nl2br($fcmt);
	$q="
UPDATE `"._DB_PREF_."nodes`
SET `name`=\"$name\", `link`=\"$link\", `scomment`=\"$scmt\", `fcomment`=\"$fcmt\"
WHERE `id`=$id;";
	db_query($q,$conn);
};

function node_del_sub($arr){
	$idl=$arr['id'];
	foreach($arr['c'] as $c)
		$idl.=','.node_del_sub($c);
	return $idl;
};

function node_del($id,$conn){
	$nd=nodes($conn);
	$nds=Array();
	$f=0;
	$idl="";
	foreach($nd as $n){
		if($f<$n['id']) $f=$n['id'];
		$nds[$n['id']]=$n;
		$nds[$n['id']]['c']=Array();
		for($i=$n['id']+1;$i<=$f;$i++){
			if($nds[$i]['root']==$n['id']){
				$nds[$n['id']]['c'][$i]=$nds[$i];
				unset($nds[$i]);
			};
		};
		if($n['id']==$id){
			$idl=node_del_sub($nds[$n['id']]);
			break;
		};
	};
	$q="DELETE FROM `"._DB_PREF_."nodes` WHERE `id` IN ($idl);";
	db_query($q,$conn);
};

if(isset($_POST['nid'])){
	if($_POST['nid']>0) {
		node_edit($_POST['nid'],$_POST['name'],$_POST['url'],$_POST['scomment'],$_POST['fcomment'],$conn);
		$TAG['content'].=template_get('_CAT_BACK',Array('root'=>$_POST['root'],'child'=>$_POST['nid']));
		return;
	} elseif($nid==0) {
		$nid=node_add($_POST['root'],$_POST['name'],$_POST['url'],$_POST['scomment'],$_POST['fcomment'],$conn);
		$TAG['content'].=template_get('_CAT_BACK',Array('root'=>$_POST['root'],'child'=>$nid));
		return;
	};
} elseif(isset($_GET['add'])) {
	$TAG['content'].=template_get('_CAT_E',Array('nid'=>0,'root'=>$_GET['add'],'name'=>'',
		'url'=>'','scomment'=>'','fcomment'=>''));
	return;
} elseif(isset($_GET['edit'])) {
	if(isset($_GET['del'])){
		node_del($_GET['edit'],$conn);
		echo 'ok';
		exit(0);
	} else {
		$r=node($_GET['edit'],$conn);
		$TAG['content'].=template_get('_CAT_E',Array('nid'=>$r['id'],'root'=>$r['root'],'name'=>$r['name'],
			'url'=>$r['link'],'scomment'=>$r['scomment'],'fcomment'=>$r['fcomment']));
	};
	return;
};

$nd=nodes($conn);
$nds=Array();
$f=0;

foreach($nd as $n){
	if($f<$n['id']) $f=$n['id'];
	$nds[$n['id']]=$n;
	$nds[$n['id']]['c']=Array();
	for($i=$n['id']+1;$i<=$f;$i++){
		if($nds[$i]['root']==$n['id']){
			$nds[$n['id']]['c'][$i]=$nds[$i];
			unset($nds[$i]);
		};
	};
};
function f11($root,$arr){
	if(sizeof($arr)<=0) return '<div><div class="mas_item mas_l"><a href="?p=cats&add='.$root.'" class="btn ui-button ui-widget ui-state-default"><span class="ui-icon ui-icon-circle-plus"></span></a></div></div>';
	$t='';
	foreach($arr as $a){
		$t.='<div class="mas_rt"><div class="mas_item'.($root==0?' mas_f':'').(sizeof($a['c'])<=0?' mas_n':'').'"><a href="?p=cats&edit='.$a['id'].'" class="btn ui-button ui-widget ui-state-default">'.$a['name'].'</a><a href="#" class="btn ui-button ui-widget ui-state-default mas_del"><span class="ui-icon ui-icon-circle-close"></span></a></div><div class="mas'.(sizeof($a['c'])<=0?'_ntc':'').'">'.f11($a['id'],$a['c']).'</div></div>';
	};
	$t.='<div><div class="mas_item mas_l'.($root==0?' mas_f':'').'"><a href="?p=cats&add='.$root.'" class="btn ui-button ui-widget ui-state-default"><span class="ui-icon ui-icon-circle-plus"></span></a></div></div>';
	return $t;
};
$TAG['content'].='<div id="mas_cont">
<div id="mas_scont">
'.f11(0,$nds).'<div class="cb"></div></div></div>';
?>
