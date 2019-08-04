<?php

include_once 'Gateway/GatewayController.php';
$path = $_SERVER['PATH_INFO'];

$pathSplit = array();
$count = 0;
foreach (getallheaders() as $name => $value) {
    $pathSplit[$count++] = $value;
    //echo "$name: $value\n";
}

GatewayController::init($path, $pathSplit);