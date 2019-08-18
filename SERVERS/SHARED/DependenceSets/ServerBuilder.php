<?php

namespace Processor {

    class ServerBuilder {

        static function buildDAO($gate, $columns, $writeParameters) {

            $fullcolString = $writeParameters;
            $getString = "";
            $params1 = str_replace("'", "", $writeParameters);
            $params = explode(",", $params1);
            for ($i = 0; $i < count($params); $i++) {
                if ($i > 0) {
                    $getString .= ",";
                }
                $getString .= '"' . $params[$i] . '" => ' . '$' . $params[$i];
            }


            $DAO = '<?php' . "\n";
            $DAO .= 'namespace ' . $gate . ' {' . "\n";
            $DAO .= '    class DAO {' . "\n";
            $DAO .= '        private $fullCols, $columns, $tableName, $conn, $date;' . "\n";
            $DAO .= '        function __construct() { $this->date = date("Y-m-d H:i:s"); $this->conn = new \ServerDatabase(); $this->fullCols = ' . "array(" . $fullcolString . ");" . ' $this->columns = ' . "array(" . $columns . ");" . ' $this->tableName = "' . $gate . '";}' . "\n";
            $DAO .= '        public function update($values) { $this->conn->getConnection(); $valuesWOId = $this->removeArrayElementAt(0, $values); $records = $this->conn->select($this->tableName, array($this->columns[0]), array("id"), array($values[0]), array("=")); $count = $records->rowCount();' . "\n";
            $DAO .= '            if ($values[0] == -1) { $status = $this->conn->insert($this->tableName, $this->columns, $valuesWOId); } else {' . "\n";
            $DAO .= '                if ($count > 0) { $status = $this->conn->update($this->tableName, $this->columns, $valuesWOId, array("id"), array($values[0]), array("="));' . "\n";
            $DAO .= '        } else { $status = $this->conn->insert($this->tableName, $this->columns, $valuesWOId); }$this->conn->closeConnection(); return $status; }}' . "\n";
            $DAO .= '        public function retrieve($whereCol, $whereOperator, $where) { $this->conn->getConnection(); if ($whereCol == null) {' . "\n";
            $DAO .= '                $records = $this->conn->select($this->tableName, $this->fullCols, array("1"), array(""), array(""));' . "\n";
            $DAO .= '            } else { $records = $this->conn->select($this->tableName, $this->fullCols, $whereCol, $where, $whereOperator); } $this->conn->closeConnection();' . "\n";
            $DAO .= '            $' . $gate . 'Array = array(); $count = 0; while ($row = $records->fetch(\PDO::FETCH_ASSOC)) { extract($row); $r = array(' . $getString . '); $' . $gate . 'Array[$count++] = $r; }return $' . $gate . 'Array; }' . "\n";
            $DAO .= '        public function delete($id) { $this->conn->getConnection(); $this->conn->delete($this->tableName, array("id"), array($id), array("=")); $this->conn->closeConnection();}' . "\n";
            $DAO .= '        private function removeArrayElementAt($index, array $array) { $count = count($array); if ($count < $index) { return "error: index out of range!";' . "\n";
            $DAO .= '            }$newArray = array(); $newCount = 0; for ($i = 0; $i < $count; $i++) { if ($i != $index) { $newArray[$newCount++] = $array[$i]; }} return $newArray;}' . "\n";
            $DAO .= '}}';

            return $DAO;
        }

        static function buildDataService($gate) {
            $DS = '<?php' . "\n";
            $DS .= ' namespace ' . $gate . ' {include_once "DAO.php";class DataService {static function update($values) {$DAO = self::getObject();return $DAO->update($values);}' . "\n";
            $DS .= '        static function getRecords($whereCol, $whereOperator, $where) {$DAO = self::getObject();return $DAO->retrieve($whereCol, $whereOperator, $where);}' . "\n";
            $DS .= '        static function delete($id) {$DAO = self::getObject();return $DAO->delete($id);}' . "\n";
            $DS .= "        private static function getObject() {return new" . '\\' . $gate . '\\DAO();}' . "\n";
            $DS .= '    }' . "\n";
            $DS .= '}';
            return $DS;
        }

        static function buildGate($gate) {

            $g = '<?php' . "\n";
            $g .= 'include_once dirname(__DIR__) . "/SHARED/ServerDatabase.php";include_once "DataService.php";' . "\n";
            $g .= 'class GateController {' . "\n";
            $g .= '    function route($pSplit, $pathSplit) {switch ($pSplit[2]) {case "Update":return \\' . $gate . '\\DataService::update($pathSplit);' . "\n";
            $g .= '            case "Get":$col = empty($pathSplit[0]) ? null : $pathSplit[0];$op = empty($pathSplit[1]) ? null : $pathSplit[1];' . "\n";
            $g .= '                $w = empty($pathSplit[2]) ? null : $pathSplit[2]; if ($col == null) {$whereCol = $whereOperator = $where = null;' . "\n";
            $g .= '                } else {$whereCol = explode(",", $col);$whereOperator = explode(",", $op);$where = explode(",", $w);}' . "\n";
            $g .= '                return json_encode(\\' . $gate . '\\DataService::getRecords($whereCol, $whereOperator, $where));' . "\n";
            $g .= '            case "Delete":' . "\n";
            $g .= '                return json_encode(\\' . $gate . '\\DataService::delete($pathSplit[0]));' . "\n";
            $g .= '            default: return "";' . "\n";
            $g .= '        }' . "\n";
            $g .= '    }' . "\n";
            $g .= '}';
            return $g;
        }

    }

}

