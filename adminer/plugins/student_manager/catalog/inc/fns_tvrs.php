<?php
if(!isset($loc)) exit(0);

function transliteIt($str){
    $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
		"-"=>"-"," "=>"-","_"=>"_",
		"A"=>"a","B"=>"b","C"=>"c","D"=>"d","E"=>"e",
		"F"=>"f","G"=>"g","H"=>"h","J"=>"j","K"=>"k",
		"L"=>"l","M"=>"m","N"=>"n","O"=>"o","P"=>"p",
		"Q"=>"q","R"=>"r","S"=>"s","T"=>"t","U"=>"u",
		"V"=>"v","W"=>"w","X"=>"x","Y"=>"y","Z"=>"z"
    );
    return strtr($str,$tr);
};
/* BEGIN CATEGORIES */

function c_list_count($id,$conn){
	$q="
SELECT `nodes`.*, count(`tvrs`.`id`) as `cnt`
FROM `tvrs`
	RIGHT JOIN `nodes` ON `nodes`.`id`=`tvrs`.`node`
WHERE `nodes`.`id`=$id
GROUP BY `nodes`.`name`
ORDER BY `nodes`.`root` DESC, `nodes`.`name` ASC;";
	$r=db_getItem($q,$conn);
	if(sizeof($r)<=0) return 0;
	return $r['cnt'];
};

function c_lists_count_of($id,$conn){
	$q="
SELECT `nodes`.*, count(`tvrs`.`id`) as `cnt`
FROM `tvrs`
	RIGHT JOIN `nodes` ON `nodes`.`id`=`tvrs`.`node`
WHERE `nodes`.`root`=$id
GROUP BY `nodes`.`name`
ORDER BY `nodes`.`root` DESC, `nodes`.`name` ASC;";
	$nd=db_getItems($q,$conn);
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
	return $nds;
};

function c_lists_count($conn){
	$q="
SELECT `nodes`.*, count(`tvrs`.`id`) as `cnt`
FROM `tvrs`
	RIGHT JOIN `nodes` ON `nodes`.`id`=`tvrs`.`node`
GROUP BY `nodes`.`name`
ORDER BY `nodes`.`root` DESC, `nodes`.`name` ASC;";
	$nd=db_getItems($q,$conn);
	$nds=Array();
	$f=0;
	foreach($nd as $n){
		if($f<$n['id']) $f=$n['id'];
		$nds[$n['id']]=$n;
		$nds[$n['id']]['c']=Array();
		for($i=$n['id']+1;$i<=$f;$i++){
			if(isset($nds[$i]) && ($nds[$i]['root']==$n['id'])){
				$nds[$n['id']]['c'][$i]=$nds[$i];
				unset($nds[$i]);
			};
		};
	};
	return $nds;
};

function c_lists($conn){
	$q="
SELECT *
FROM `nodes`
ORDER BY `root` DESC, `nodes`.`name` ASC;";
	$nd=db_getItems($q,$conn);
	$nds=Array();
	$f=0;
	foreach($nd as $n){
		if($f<$n['id']) $f=$n['id'];
		$nds[$n['id']]=$n;
		$nds[$n['id']]['c']=Array();
		for($i=$n['id']+1;$i<=$f;$i++){
			if(isset($nds[$i])&&($nds[$i]['root']==$n['id'])){
				$nds[$n['id']]['c'][$i]=$nds[$i];
				unset($nds[$i]);
			};
		};
	};
	return $nds;
};

function c_link($l,$conn){
	$q="
SELECT *
FROM `nodes`
WHERE `link`=\"$l\";";
	return db_getItem($q,$conn);
};
	
function c_list($l,$conn){
	$q="
SELECT *
FROM `nodes`
WHERE `id` IN ($l)
ORDER BY `nodes`.`name` ASC;";
	return db_getItems($q,$conn);
};

function c_link_count($l,$conn){
	$q="
SELECT `nodes`.*, count(`tvrs`.`id`) as `cnt`
FROM `tvrs`
	RIGHT JOIN `nodes` ON `nodes`.`id`=`tvrs`.`node`
WHERE `link`=\"$l\"
GROUP BY `nodes`.`name`
ORDER BY `nodes`.`root` DESC, `nodes`.`name` ASC;";
	return db_getItem($q,$conn);
};

function c_id($id,$conn){
	$q="
SELECT *
FROM `nodes`
WHERE `id`=$id;";
	return db_getItem($q,$conn);
};


function c_id_count($id,$conn){
	$q="
SELECT `nodes`.*, count(`tvrs`.`id`) as `cnt`
FROM `tvrs`
	RIGHT JOIN `nodes` ON `nodes`.`id`=`tvrs`.`node`
WHERE `id`=\"$id\"
GROUP BY `nodes`.`name`
ORDER BY `nodes`.`root` DESC, `nodes`.`name` ASC;";
	return db_getItem($q,$conn);
};

function c_child($id,$conn){
	$q="
SELECT *
FROM `nodes`
WHERE `root`=$id
ORDER BY `nodes`.`name` ASC;";
	return db_getItems($q,$conn);
};

function c_child_count($id,$conn){
	$q="
SELECT `nodes`.*, count(`tvrs`.`id`) as `cnt`
FROM `tvrs`
	RIGHT JOIN `nodes` ON `nodes`.`id`=`tvrs`.`node`
WHERE `root`=\"$id\"
GROUP BY `nodes`.`name`
ORDER BY `nodes`.`root` DESC, `nodes`.`name` ASC;";
	return db_getItems($q,$conn);
};

/* END CATEGORIES */
/* BEGIN TOVARS */

function t_where($attrs,$conn){
	if(sizeof($attrs)<=0 || !is_array($attrs))
		return Array();
	$q='';
	foreach($attrs as $k => $v){
		$q.=(strlen($q)>0?'OR':'')."(`attrs`.`name`=\"$k\")";
	};
	$q='SELECT * FROM `attrs` WHERE '.$q.';';
	$atl=db_getItems($q,$conn);
	// выбрать атрибуты
	$q='';
	foreach($atl as $v)
		$q.=(strlen($q)>0?'OR':'')."((`values`.`attr`=\"$v[id]\")AND(`values`.`t$v[type]`=\"".$attrs[$v['name']]."\"))";
	$q="
SELECT `values`.*, `tvrs`.`id` as `__tid`, `tvrs`.`node` as '__tnode',
	`attrs`.`type` as `__atp`, `attrs`.`name` as `__an`, `attrs`.`title` as `__att`, `attrs`.`value` as `__aval`,
	`tvrs`.`templ` as `ttempl`
FROM `templs`
	INNER JOIN `tvrs` ON `tvrs`.`templ`=`templs`.`id`
	INNER JOIN `templ_attrs` ON `templ_attrs`.`templ`=`templs`.`id`
	INNER JOIN `attrs` ON `attrs`.`id`=`templ_attrs`.`attr`
	LEFT JOIN `values` ON (`values`.`attr`=`attrs`.`id` AND `values`.`tvr`=`tvrs`.`id`)
WHERE (`tvrs`.`id` IN (SELECT DISTINCT `values`.`tvr` FROM `values` WHERE ".$q."));
	";
	$tsa=db_getItems($q,$conn);
	if(sizeof($tsa)<=0)
		return Array();
	$r=Array();	
	foreach($tsa as $t){
		$id=$t['__tid'];
		if(!isset($r[$id]))
			$r[$id]=Array('id'=>$t['__tid'],'node'=>$t['__tnode']);
		if($t['__atp']==10){
			$iids=$t['t'.$t['__atp']]; // плохая херня =) нужно в 2а запроса сделать ;)
			$r[$id][$t['__an']]=Array();
			$q="SELECT * FROM `imgs` WHERE (`id` IN ($iids)) ORDER BY `ind`;";
			$rimg=db_getItems($q,$conn);
			foreach($rimg as $ri) array_push($r[$id][$t['__an']],$ri);
			continue;
		};
		if($t['__atp']==9){ // плохая херня =) нужно в 2а запроса сделать ;)
			$iif=$t['t'.$t['__atp']];
			$q="SELECT * FROM `firms` WHERE (`id` = $iif);";
			$r[$id][$t['__an']]=db_getItem($q,$conn);;
			continue;
		};
		$r[$id][$t['__an']]=$t['t'.$t['__atp']];
	};
	return $r;
};

function t_id($id,$conn){
	$q="
SELECT `values`.*, `tvrs`.`id` as `__tid`, `tvrs`.`node` as '__tnode',
	`attrs`.`type` as `__atp`, `attrs`.`name` as `__an`, `attrs`.`title` as `__att`, `attrs`.`value` as `__aval`,
	`tvrs`.`templ` as `ttempl`
FROM `templs`
	INNER JOIN `tvrs` ON `tvrs`.`templ`=`templs`.`id`
	INNER JOIN `templ_attrs` ON `templ_attrs`.`templ`=`templs`.`id`
	INNER JOIN `attrs` ON `attrs`.`id`=`templ_attrs`.`attr`
	LEFT JOIN `values` ON (`values`.`attr`=`attrs`.`id` AND `values`.`tvr`=`tvrs`.`id`)
WHERE (`tvrs`.`id`=$id);
	";
	$tsa=db_getItems($q,$conn);
	if(sizeof($tsa)<=0)
		return Array();
	$r=Array();
	$id=-1;
	foreach($tsa as $t){
		if(!isset($r[$id])) {
			$id++;
			$r[$id]=Array('id'=>$t['__tid'],'node'=>$t['__tnode']);
		};
		if($t['__atp']==10){
			$iids=$t['t'.$t['__atp']]; // плохая херня =) нужно в 2-3и запроса сделать ;)
			$r[$id][$t['__an']]=Array();
			$q="SELECT * FROM `imgs` WHERE (`id` IN ($iids)) ORDER BY `ind`;";
			$rimg=db_getItems($q,$conn);
			foreach($rimg as $ri) array_push($r[$id][$t['__an']],$ri);
			continue;
		};
		if($t['__atp']==9){ // плохая херня =) нужно в 2-3и запроса сделать ;)
			$iif=$t['t'.$t['__atp']];
			$q="SELECT * FROM `firms` WHERE (`id` = $iif);";
			$r[$id][$t['__an']]=db_getItem($q,$conn);
			continue;
		};
		$r[$id][$t['__an']]=$t['t'.$t['__atp']];
	};
	return $r;
};

function t_list($list,$conn){
	$q="
SELECT `values`.*, `tvrs`.`id` as `__tid`, `tvrs`.`node` as '__tnode',
	`attrs`.`type` as `__atp`, `attrs`.`name` as `__an`, `attrs`.`title` as `__att`, `attrs`.`value` as `__aval`,
	`tvrs`.`templ` as `ttempl`
FROM `templs`
	INNER JOIN `tvrs` ON `tvrs`.`templ`=`templs`.`id`
	INNER JOIN `templ_attrs` ON `templ_attrs`.`templ`=`templs`.`id`
	INNER JOIN `attrs` ON `attrs`.`id`=`templ_attrs`.`attr`
	LEFT JOIN `values` ON (`values`.`attr`=`attrs`.`id` AND `values`.`tvr`=`tvrs`.`id`)
WHERE (`tvrs`.`id` IN ($list));
	";
	$tsa=db_getItems($q,$conn);
	if(sizeof($tsa)<=0)
		return Array();
	$r=Array();	
	foreach($tsa as $t){
		$id=$t['__tid'];
		if(!isset($r[$id]))
			$r[$id]=Array('id'=>$t['__tid'],'node'=>$t['__tnode']);
		if($t['__atp']==10){
			$iids=$t['t'.$t['__atp']]; // плохая херня =) нужно в 2а запроса сделать ;)
			$r[$id][$t['__an']]=Array();
			$q="SELECT * FROM `imgs` WHERE (`id` IN ($iids)) ORDER BY `ind`;";
			$rimg=db_getItems($q,$conn);
			foreach($rimg as $ri) array_push($r[$id][$t['__an']],$ri);
			continue;
		};
		if($t['__atp']==9){ // плохая херня =) нужно в 2а запроса сделать ;)
			$iif=$t['t'.$t['__atp']];
			$q="SELECT * FROM `firms` WHERE (`id` = $iif);";
			$r[$id][$t['__an']]=db_getItem($q,$conn);;
			continue;
		};
		$r[$id][$t['__an']]=$t['t'.$t['__atp']];
	};
	return $r;
};

function t_list_sort($list,$filtr,$conn){
};

function t_list_of_cat($cat,$conn){
	$q="
SELECT `values`.*, `tvrs`.`id` as `__tid`, `tvrs`.`node` as '__tnode',
	`attrs`.`type` as `__atp`, `attrs`.`name` as `__an`, `attrs`.`title` as `__att`, `attrs`.`value` as `__aval`,
	`tvrs`.`templ` as `ttempl`
FROM `templs`
	INNER JOIN `tvrs` ON `tvrs`.`templ`=`templs`.`id`
	INNER JOIN `templ_attrs` ON `templ_attrs`.`templ`=`templs`.`id`
	INNER JOIN `attrs` ON `attrs`.`id`=`templ_attrs`.`attr`
	LEFT JOIN `values` ON (`values`.`attr`=`attrs`.`id` AND `values`.`tvr`=`tvrs`.`id`)
WHERE (`tvrs`.`node` IN ($cat));
	";
	$tsa=db_getItems($q,$conn);
	if(sizeof($tsa)<=0)
		return Array();
	$r=Array();	
	foreach($tsa as $t){
		$id=$t['__tid'];
		if(!isset($r[$id]))
			$r[$id]=Array('id'=>$t['__tid'],'node'=>$t['__tnode']);
		if($t['__atp']==10){
			$iids=$t['t'.$t['__atp']]; // плохая херня =) нужно в 2а запроса сделать ;)
			$r[$id][$t['__an']]=Array();
			$q="SELECT * FROM `imgs` WHERE (`id` IN ($iids)) ORDER BY `ind`;";
			$rimg=db_getItems($q,$conn);
			foreach($rimg as $ri) array_push($r[$id][$t['__an']],$ri);
			continue;
		};
		if($t['__atp']==9){ // плохая херня =) нужно в 2а запроса сделать ;)
			$iif=$t['t'.$t['__atp']];
			$q="SELECT * FROM `firms` WHERE (`id` = $iif);";
			$r[$id][$t['__an']]=db_getItem($q,$conn);;
			continue;
		};
		$r[$id][$t['__an']]=$t['t'.$t['__atp']];
	};
	return $r;
};

/* END TOVARS */
function f_list($l,$conn){
	$q="
SELECT `firms`.*, `imgs`.`id` as `_l_id`, `imgs`.`name` as `_l_name`,
	`imgs`.`alt` as `_l_alt`, `imgs`.`descr` as `_l_descr` 
FROM `firms`
	LEFT JOIN `imgs` ON (`imgs`.`id`=`firms`.`logo`)
WHERE (`firms`.`id` IN ($l));";
	$rr=db_getItems($q,$conn);
	$res=Array();
	foreach($rr as $r){
		if(strlen($r['_l_id'])>0){
			$limg=Array('id'=>$r['_l_id'],'name'=>$r['_l_name'],'alt'=>$r['_l_alt'],'descr'=>$r['_l_descr']);
		} else $limg=Array();
		$r_=Array();
		foreach($r as $k => $v){
			if($k[0]=='_') continue;
			if(($k=='logo') && (sizeof($limg)>0)){ $r_[$k]=$limg; continue; };
			$r_[$k]=$v;
		};
		array_push($res,$r_);
		unset($limg);
	};
	return $res;
};
/* BEGIN */

function sizes($s){return ($s!='')?'<span class="size">Размеры: '.$s.'</span>':'';};
function prices($p){return (($p!='')&&($p!=0))?'<span class="price">Стоимость: '.$p.' руб.</span>':'';};
function styles($n){$ns=explode(' ',$n); return ((count($ns)>1)&&mb_strlen($n,"UTF-8")>23)?' double':'';};

function catPrint(&$TAG,$cat,$conn,$more=true){
	$breads='';
	catBreadPath($breads,$cat,$conn);
	if($more){
		foreach($breads as $b)
			$TAG['title'].=$b['name']." | ";
		$TAG['content'].='<h2 class="head">'.$breads[0]['name'].'</h2><div class="breadpath">';
	}
	$path='';
	for($i=count($breads)-1;$i>=0;$i--){
		$path.='/'.$breads[$i]['link'];
		$TAG['content'].=($more)?'<a href="'.$path.'">'.$breads[$i]['name'].'</a>'.(($i>0)?' / ':''):'';
	}
	$TAG['content'].=($more)?'</div>':'';
	$goods=t_list_of_cat($cat['id'],$conn);
	if(count($goods)){
		foreach($goods as $id=>$good)
			$TAG['content'].=template_get('_GOOD',Array("caption"=>$good['name'],'num'=>$good['imgs'][0]['name'],'price'=>prices($good['price']),'size'=>sizes($good['razmer'])));
	} else {
		$cats=c_child($cat['id'],$conn);
		if(count($cats))
			foreach($cats as $id=>$cad)
				$TAG['content'].=template_get('_CATFIGURE',Array("caption"=>$cad['name'],'link'=>$path.'/'.$cad['link'],'num'=>$cad['id'],'ex_style'=>styles($cad['name'])));
	}
};

function catBreadPath(&$breads,$cat,$conn){
	if($cat['root']>0){
		$breads[]=$cat;
		catBreadPath($breads,c_id($cat['root'],$conn),$conn);
	} else
		$breads[]=$cat;
};
?>
