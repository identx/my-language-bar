<?php 

$SITE_PATH='';	  
 
 $_CONFIG['uploadURL'] = "../../assets";    
 $_CONFIG['thumbsDir'] = "_thumbs";    
 $_CONFIG['thumbWidth'] = 310;    
 $_CONFIG['thumbHeight'] = 310;    
 $_CONFIG['maxImageWidth'] = 1200;    
 $_CONFIG['maxImageHeight'] = 1200;     
 $_CONFIG['method'] = 'crop'; 
    
 $_CONFIG['dbprefix'] = 'mgcore_';	    
 $_CONFIG['dir'] = DIRECTORY_SEPARATOR;	 
 	
  
 //  $DB_CONF=Array(		
	// "server"	=>"localhost",		
	// "username"	=>"langbar",
	// "password"	=>"Q1w2e3r4t5y6!1",
	// "db"		=>"langbar",
	// "port"		=>3306,		
	// "charset"	=>"UTF8"	
 // ); 
		
	$DB_CONF=Array(
		"server"	=>"localhost",
		"username"	=>"root",
		"password"	=>"",
		"db"		=>"language-bar",
		"port"		=>3306,
		"charset"	=>"UTF8"
	); 
$DB;	
?>
