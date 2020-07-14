<?php if(!isset($loc)) exit(0); //ы

function firms($conn){
	$q="
SELECT *
FROM `firms`;";
	return db_getItems($q,$conn);
};

function firm($id,$conn){
	$q="
SELECT *
FROM `firms`
WHERE `firms`.`id`=$id;";
	return db_getItem($q,$conn);
};

function firm_edit($id,$fname,$sname,$inn,$kpp,$conn){
	$q="
UPDATE `firms` 
SET `fullname`=\"$fname\",`shortname`=\"$sname\",`inn`=\"$inn\",`kpp`=\"$kpp\"
WHERE `id`=$id;";
	db_query($q,$conn);
};

function firm_add($fname,$sname,$inn,$kpp,$conn){
	$q="
INSERT INTO `firms` (`fullname`,`shortname`,`inn`,`kpp`)
VALUES (\"$fname\",\"$sname\",\"$inn\",\"$kpp\");";
	db_query($q,$conn);
	return db_insert_id();
};

function firm_conns($id,$conn){
	$q="
SELECT *
FROM `firm_conn`
WHERE (`firm_conn`.`firm`=$id);";
	return db_getItems($q,$conn);
};

function firm_conn($id,$conn){
	$q="
SELECT *
FROM `firm_conn`
WHERE (`firm_conn`.`id`=$id);";
	return db_getItem($q,$conn);
};

function firm_conn_add($firm,$fio,$val,$conn){
	$q="
INSERT INTO `firm_conn` (`firm`,`fio`,`value`)
VALUES ($firm,\"$fio\",\"$val\");";
	db_query($q,$conn);
	return db_insert_id();
};

function firm_conn_edit($id,$fio,$val,$conn){
	$q="
UPDATE `firm_conn`
SET `fio`=\"$fio\",`value`=\"$val\"
WHERE `id`=$id;";
	db_query($q,$conn);
};

function firm_by_conn($c,$conn){
	$q="
SELECT `firms`.*
FROM `firm_conn`
	INNER JOIN `firms` ON `firms`.`id`=`firm_conn`.`firm`
WHERE (`firm_conn`.`value`=\"$c\");";
	return db_getItem($q,$conn);
};

function firm_accs($id,$conn){
	$q="
SELECT `firm_acc`.*, `banks`.*
FROM `firm_acc`
	INNER JOIN `banks` ON `banks`.`id`=`firm_acc`.`bank`
WHERE (`firm_acc`.`firm`=$id);";
	return db_getItems($q,$conn);
};

function firm_acc($id,$conn){
	$q="
SELECT `firm_acc`.*, `banks`.*
FROM `firm_acc`
	INNER JOIN `banks` ON `banks`.`id`=`firm_acc`.`bank`
WHERE (`firm_acc`.`id`=$id);";
	return db_getItem($q,$conn);
};

function firm_acc_add($firm,$bik,$bank,$kor,$val,$conn){
	$q="
SELECT * 
FROM `banks`
WHERE `bik`=\"$bik\";";
	$res=db_getItem($q,$conn);
	if(sizeof($res)>0){
		$bid=$res['id'];
	} else {
		$q="
		INSERT INTO `banks` (`bik`,`fullname`,`shortname`,`kor`) 
		VALUES (\"$bik\",\"$bank\",\"$bank\",\"$kor\");";
		db_query($q,$conn);
		$bid=db_insert_id();
	};
	if($bid<=0) return 0;
	$q="
INSERT INTO `firm_acc` (`firm`,`bank`,`value`)
VALUES ($firm,$bid,\"$val\");";
	db_query($q,$conn);
	return db_insert_id();
};

function firm_acc_edit($id,$val,$conn){
	$q="
UPDATE `firm_acc`
SET `bik`=\"$bik\", `bank`=\"$bank\", `kor`=\"$kor\", `value`=\"$val\"
WHERE `id`=$id;";
	db_query($q,$conn);
};

function firm_by_acc($acc,$conn){
	$q="
SELECT `firms`.*
FROM `firm_acc`
	INNER JOIN `firms` ON `firms`.`id`=`firm_acc`.`firm`
WHERE (`firm_acc`.`value`=\"$acc\");";
	return db_getItem($q,$conn);
};

function banks($conn){
	$q="
SELECT *
FROM `banks`;";
	return db_getItems($q,$conn);
};

function bank($id,$conn){
	$q="
SELECT *
FROM `banks`
WHERE `id`=$id;";
	return db_getItem($q,$conn);
};

function bank_edit($id,$bik,$fname,$sname,$kor,$conn){
	$q="
UPDATE `banks`
SET `bik`=\"$bik\", `fullname`=\"$fname\", `shortname`=\"$sname\", `kor`=\"$kor\"
WHERE `id`=$id;";
	db_query($q,$conn);
};

function bank_add($bik,$fname,$sname,$kor,$conn){
	$q="
INSERT INTO `banks` (`bik`,`fullname`,`shortname`,`kor`)
VALUES (\"$bik\",\"$fname\",\"$sname\",\"$kor\");";
	db_query($q,$conn);
	return db_insert_id();
};

function bank_by_bik($bik,$conn){
	$q="
SELECT *
FROM `banks`
WHERE `bik`=\"$bik\";";
	return db_getItem($q,$conn);
};

function nodes($conn){
	$q="
SELECT *
FROM `nodes`
ORDER BY `root` DESC;";
	return db_getItems($q,$conn);
};

function node($id,$conn){
	$q="
SELECT *
FROM `nodes`
WHERE `id`=$id;";
	return db_getItem($q,$conn);
};

function node_add($root,$name,$scmt,$fcmt,$req,$lim,$mult,$price,$conn){
	$scmt=nl2br($scmt);
	$fcmt=nl2br($fcmt);
	$q="
INSERT INTO `nodes` (`root`,`name`,`scomment`,`fcomment`,`require`,`limits`,`multiple`,`price`)
VALUES ($root,\"$name\",\"$scmt\",\"$fcmt\",\"$req\",\"$lim\",\"$mult\",\"$price\");";
	db_query($q,$conn);
	return db_insert_id();
};

function node_edit($id,$name,$scmt,$fcmt,$req,$lim,$mult,$price,$conn){
	$scmt=nl2br($scmt);
	$fcmt=nl2br($fcmt);
	$q="
UPDATE `nodes`
SET `name`=\"$name\", `scomment`=\"$scmt\", `fcomment`=\"$fcmt\", `require`=\"$req\", `limits`=\"$lim\", 
	`multiple`=\"$mult\", `price`=\"$price\"
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
	$q="DELETE FROM `nodes` WHERE `id` IN ($idl);";
	db_query($q,$conn);
};

function tmp_img($sid,$time,$conn){
	$q="
SELECT * 
FROM `tmp_imgs`
WHERE (`sid`=\"$sid\")AND(`stamp`=$time)
ORDER BY `stamp` ASC;";
	return db_getItems($q,$conn);
};

function tmp_img_clear($sid,$time,$conn){
	$q="
DELETE FROM `tmp_imgs`
WHERE (`sid`=\"$sid\")AND(`stamp`=$time);";
	db_query($q,$conn);
};

function img_node($id,$conn){
	$q="
SELECT `img_node`.*, `img_dir`.`dir` as `dir`
FROM `img_node`
	INNER JOIN `img_dir` ON `img_node`.`type`=`img_dir`.`id`
WHERE (`img_node`.`node`=$id);";
	return db_getItems($q,$conn);
};

function img_node_1($id,$conn){
	$q="
SELECT `img_node`.*, `img_dir`.`dir` as `dir`
FROM `img_node`
	INNER JOIN `img_dir` ON `img_node`.`type`=`img_dir`.`id`
WHERE (`img_node`.`id`=$id);";
	return db_getItem($q,$conn);
};

function img_node_newid($conn){
	$q="SELECT max(`id`) FROM `img_node`;";
	$r=db_getItem($q,$conn);
	return ($r[0]-0+1);
};

function img_node_add($id,$node,$name,$conn){
	$q="
INSERT INTO `img_node` (`id`,`type`,`node`,`name`)
VALUES ($id,2,$node,\"$name\");";
	db_query($q,$conn);
	return db_insert_id();
};

function img_node_del($id,$conn){
	$res=img_node_1($id,$conn);
	$q="
DELETE FROM `img_node`
WHERE `id`=$id;";
	db_query($q,$conn);
	return $res['dir'].'/'.$res['name'];
};

?>