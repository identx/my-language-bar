<?php
	if( !defined( __DIR__ ) )define( __DIR__, dirname(__FILE__) );
	$_cp=explode(DIRECTORY_SEPARATOR,__DIR__);
	echo '<iframe style="width:100%;height:100%" src="'.$_cp[count($_cp)-2].'/'.$_cp[count($_cp)-1].'/catalog/tools.php"></iframe>';
?>
