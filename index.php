<?php

include_once 'Gateway/GatewayController.php';
$path = $_SERVER['PATH_INFO'];

$pathSplit = array();
$count = 0;

$json = file_get_contents('php://input');
$data = json_decode($json);
//echo "COUNT " . count($data);
if (isset($data)) {
    for ($i = 0; $i < count($data); $i++) {
        $pathSplit[$count++] = $data[i];
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
