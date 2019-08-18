<?php
include_once dirname(__DIR__) . "/SHARED/ServerDatabase.php";include_once "DataService.php";
class GateController {
    function route($pSplit, $pathSplit) {switch ($pSplit[2]) {case "Update":return \test\DataService::update($pathSplit);
            case "Get":$col = empty($pathSplit[0]) ? null : $pathSplit[0];$op = empty($pathSplit[1]) ? null : $pathSplit[1];
                $w = empty($pathSplit[2]) ? null : $pathSplit[2]; if ($col == null) {$whereCol = $whereOperator = $where = null;
                } else {$whereCol = explode(",", $col);$whereOperator = explode(",", $op);$where = explode(",", $w);}
                return json_encode(\test\DataService::getRecords($whereCol, $whereOperator, $where));
            case "Delete":
                return json_encode(\test\DataService::delete($pathSplit[0]));
            default: return "";
        }
    }
}