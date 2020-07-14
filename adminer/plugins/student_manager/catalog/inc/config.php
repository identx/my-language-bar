<?php
if(!isset($loc)) exit(0);
// $_SERVER["db"]["port"]=3306;
// $_SERVER["db"]["host"]="localhost";
// $_SERVER["db"]["user"]="root";
// $_SERVER["db"]["pass"]="";
// $_SERVER["db"]["base"]="kofr";

// $_SERVER["db"]["port"]=3306;
// $_SERVER["db"]["host"]="big-host-1.mysql";
// $_SERVER["db"]["user"]="big-host-1_kofr";
// $_SERVER["db"]["pass"]="g3ya6shq";
// $_SERVER["db"]["base"]="big-host-1_kofr";

function mlower($s){ return iconv('Windows-1251','UTF-8',strtolower(iconv('UTF-8','Windows-1251',$s))); };
// function mlower($s){ return mb_strtolower($s); };

$MAILLIST=Array();
?>
