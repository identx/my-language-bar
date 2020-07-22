<?php 

    header("Content-type:application/json");

$response = json_encode($_GET);

// file_put_contents('1.txt', $response);
echo $response;



?>


