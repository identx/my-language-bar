<?php
// error_reporting(0);
$sql="SHOW TABLES LIKE '".$conf['base']."constant';";
$res=db_query($sql);
if(count($res)==0){
	$sql="
	CREATE TABLE `mcat_constant` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
	  `param` varchar(255) NOT NULL,
	  `value` varchar(255) NOT NULL,
	  `attr` varchar(255) NOT NULL DEFAULT '1',
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	db_query($sql);
	$sql="
	INSERT INTO `mcat_constant` (`param`, `value`, `attr`) VALUES
		('телефон', 'phone', '88005553535')";
	$res=db_query($sql);
	if(!$res) echo 'Ошибка создания таблицы плагина<br />';
}
$res='';
?>
