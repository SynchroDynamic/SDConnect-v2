<?php

include_once 'Gateway/GatewayController.php';
$path = $_SERVER['PATH_INFO'];

$pathSplit = array();
$count = 0;

$json = file_get_contents('php://input');
$data = json_decode($json);
//echo "COUNT " . $json;
if (isset($data)) {
    foreach ($data as $name => $value) {
        $pathSplit[$count++] = $value;
        //echo "$name: $value\n";
    }
}

$values = $_GET;
foreach ($values as $name => $value) {
    $pathSplit[$count++] = $value;
    //echo "$name: $value\n";
}

if (count($values) < 1) {
    $values = $_POST;
    foreach ($values as $name => $value) {
        $pathSplit[$count++] = $value;
        //echo "$name: $value\n";
    }
}


GatewayController::init($path, $pathSplit);
