<?php

include_once dirname(__DIR__) . "/SHARED/ServerDatabase.php";
include_once "DataService.php";

class GateController {

    function route($pSplit, $pathSplit) {
        switch ($pSplit[2]) {
            case "Update":return \user\DataService::update($pathSplit);
            case "Get":$col = empty($pathSplit[0]) ? null : $pathSplit[0];
                $op = empty($pathSplit[1]) ? null : $pathSplit[1];
                $w = empty($pathSplit[2]) ? null : $pathSplit[2];
                if ($col == null) {
                    $whereCol = $whereOperator = $where = null;
                } else {
                    $whereCol = explode(",", $col);
                    $whereOperator = explode(",", $op);
                    $where = explode(",", $w);
                }
                return json_encode(\user\DataService::getRecords($whereCol, $whereOperator, $where));
            case "Delete":
                return json_encode(\user\DataService::delete($pathSplit[0]));
            case "Register":
                $username = $pathSplit[0];
                $password = $pathSplit[1];
                $email = $pathSplit[2];
                $realNameFirst = $pathSplit[3];
                $realNameLast = $pathSplit[4];
                $reg = \User\DataService::register($username, $password, $email, $realNameFirst, $realNameLast);
                if ($reg->rowCount() > 0) {
                    return 1;
                } else {
                    return -1;
                }
            default: return "";
        }
    }

}
