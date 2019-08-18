<?php
namespace test {
    class DAO {
        private $fullCols, $columns, $tableName, $conn, $date;
        function __construct() { $this->date = date("Y-m-d H:i:s"); $this->conn = new \ServerDatabase(); $this->fullCols = array('id','rank','credits','name','updated','isRegistered'); $this->columns = array('rank','credits','name','updated','isRegistered'); $this->tableName = "test";}
        public function update($values) { $this->conn->getConnection(); $valuesWOId = $this->removeArrayElementAt(0, $values); $records = $this->conn->select($this->tableName, array($this->columns[0]), array("id"), array($values[0]), array("=")); $count = $records->rowCount();
            if ($values[0] == -1) { $status = $this->conn->insert($this->tableName, $this->columns, $valuesWOId); } else {
                if ($count > 0) { $status = $this->conn->update($this->tableName, $this->columns, $valuesWOId, array("id"), array($values[0]), array("="));
        } else { $status = $this->conn->insert($this->tableName, $this->columns, $valuesWOId); }$this->conn->closeConnection(); return $status; }}
        public function retrieve($whereCol, $whereOperator, $where) { $this->conn->getConnection(); if ($whereCol == null) {
                $records = $this->conn->select($this->tableName, $this->fullCols, array("1"), array(""), array(""));
            } else { $records = $this->conn->select($this->tableName, $this->fullCols, $whereCol, $where, $whereOperator); } $this->conn->closeConnection();
            $testArray = array(); $count = 0; while ($row = $records->fetch(\PDO::FETCH_ASSOC)) { extract($row); $r = array("id" => $id,"rank" => $rank,"credits" => $credits,"name" => $name,"updated" => $updated,"isRegistered" => $isRegistered); $testArray[$count++] = $r; }return $testArray; }
        public function delete($id) { $this->conn->getConnection(); $this->conn->delete($this->tableName, array("id"), array($id), array("=")); $this->conn->closeConnection();}
        private function removeArrayElementAt($index, array $array) { $count = count($array); if ($count < $index) { return "error: index out of range!";
            }$newArray = array(); $newCount = 0; for ($i = 0; $i < $count; $i++) { if ($i != $index) { $newArray[$newCount++] = $array[$i]; }} return $newArray;}
}}