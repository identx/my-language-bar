<?php
if(!isset($loc)) exit(0);
// if(!isset($_SESSION['user']['admin']) && $_SESSION['user']['admin']<=0){
if(true){
	$exist=Array();
	$need=Array('attrs','attr_groups','brands','images','nodes',
		'templs','templ_attrs','tvrs','values');
	foreach($need as $k=>$n) $need[$k]=_DB_PREF_.$n;
	$res=db_getItems("SHOW TABLES;",$conn);
	foreach($res as $r) {
		foreach($r as $rr)
		array_push($exist,$rr);	
	};
	$noexist=array_diff($need,$exist);
	if(sizeof($noexist)>0){
		if(isset($_POST['make_new_dbtable'])){
			foreach($noexist as $ne){
				$q='';
				switch($ne){
				case _DB_PREF_.'attrs':
$q="CREATE TABLE `"._DB_PREF_."attrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type` int(11) NOT NULL,
  `value` text NOT NULL,
  `unit` varchar(10) NOT NULL,
  `options` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;"; break;
				case _DB_PREF_.'attr_groups':
$q="CREATE TABLE `"._DB_PREF_."attr_groups` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; break;
				case _DB_PREF_.'brands':
$q="CREATE TABLE `"._DB_PREF_."brands` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(250) NOT NULL,
	`url` varchar(250) NOT NULL,
	`img` varchar(10) NOT NULL,
	`descr` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;"; break;
				case _DB_PREF_.'images':
$q="CREATE TABLE IF NOT EXISTS `"._DB_PREF_."images` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_tvr` int(11) NOT NULL,
	`index` int(11) NOT NULL,
	`name` varchar(60) NOT NULL,
	`caption` varchar(60) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
"; break;
				case _DB_PREF_.'nodes':
$q="CREATE TABLE `"._DB_PREF_."nodes` (
	`id` int(11) NOT NULL auto_increment,
	`root` int(11) NOT NULL,
	`name` varchar(250) NOT NULL,
	`link` varchar(250) NOT NULL,
	`scomment` text NOT NULL,
	`fcomment` text NOT NULL,
	`img` varchar(150) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `root` (`root`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"; break;
				case _DB_PREF_.'templs':
$q="CREATE TABLE `"._DB_PREF_."templs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"; break;
				case _DB_PREF_.'templ_attrs':
$q="CREATE TABLE `"._DB_PREF_."templ_attrs` (
	`templ` int(11) NOT NULL,
	`attr` int(11) NOT NULL,
	KEY `templ` (`templ`,`attr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;"; break;
				case _DB_PREF_.'tvrs':
$q="CREATE TABLE `"._DB_PREF_."tvrs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`cat` int(11) NOT NULL,
	`templ` int(11) NOT NULL,
	`opts` smallint(11) NOT NULL DEFAULT '0',
	`article` varchar(30) NOT NULL,
	`brand` int(5) NOT NULL,
	`name` varchar(200) NOT NULL,
	`name_def` varchar(200) NOT NULL,
	`alias` varchar(100) NOT NULL,
	`price` int(11) NOT NULL,
	`price_d` int(11) NOT NULL DEFAULT '0', 
	`count` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `cat` (`cat`,`templ`),
	KEY `cat_2` (`cat`),
	KEY `templ` (`templ`),
	KEY `templ_2` (`templ`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"; break;
		case _DB_PREF_.'values':
$q="CREATE TABLE `"._DB_PREF_."values` (
	`tvr` int(11) NOT NULL,
	`attr` int(11) NOT NULL,
	`t1` smallint(6) DEFAULT NULL,
	`t2` int(11) DEFAULT NULL,
	`t3` double DEFAULT NULL,
	`t4` date DEFAULT NULL,
	`t5` varchar(250) DEFAULT NULL,
	`t6` text,
	`t7` text,
	KEY `tvr` (`tvr`,`attr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;"; break;
				default: echo $ne."\r\n";
				};
				if(strlen($q)>0){
					$qq=explode(";\n",$q);
					foreach($qq as $q){
						if(!strpos(substr($q,strlen($q)-7,7),';')) $q.=';';
						db_query($q);
					};
				};
			};
			if(!is_dir('tmp')) mkdir('tmp');
			$TAG['content'].=template_get('_makequery_noerror_form',Array());
			// $TAG['content'].=template_get('_login_form',Array());
		} else $TAG['content'].=template_get('_makequery_form',Array());
		return;
	} else {
		// $TAG['content'].=template_get('_login_form',Array());
	};
};
switch($_GET['p']){
case 'attrs':
	$TAG['curpath']='?p=attrs';
	$file='attrs.php';
break;
case 'cats':
	$TAG['curpath']='?p=cats';
	$file='_cats.php';
break;
case 'brands':
	$TAG['curpath']='?p=brands';
	$file='brands.php';
break;
case 'tovars':
	$TAG['curpath']='?p=tovars';
	$file='tovars.php';
break;
case 'action':
	$TAG['curpath']='?p=action';
	$file='action.php';
break;
default:
	$TAG['curpath']='?p=';
	$file='_info.php';
};
require_once($file);
?>
