<?php

namespace {
    include_once 'DataConnection_1.php';
    date_default_timezone_set('America/Chicago');

//
//

    class ServerDatabase {

        public $conn;

        /*         * *****************************************************************************
         * 
         *                              GENERAL FUNCTIONS
         * 
         * **************************************************************************** */

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=" . DataConnection::h() . ";dbname="
                        . DataConnection::d(), DataConnection::u(), DataConnection::p());
                $this->conn->exec("set names utf8");
            } catch (PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }

            return $this->conn;
        }

        public function closeConnection() {
            $this->conn = null;
        }

        public function insert($tableName, array $columns, array $values) {
            $colCount = count($columns);

            if ($colCount != count($values)) {
                return 0;
            }

            $query = "INSERT INTO " . $tableName . " SET ";

            $query .= $this->sequenceColumnNamesWithQuestionMarks($columns);

            $data = $this->bindValuesAndSend($query, $values, $colCount);

            return $data;
        }

        public function delete($table, $whereCol, $where, $whereOperator) {

            $query = "DELETE FROM " . $table;

            $query .= $this->concatWhereClause($whereCol, $where, $whereOperator);

            $data = $this->send($query);

            return $data;
        }

        public function select($tableName, $columns, $whereCol, $where, $whereOperator) {

            $query = "SELECT";

            $query .= $this->sequenceColumnsForSelect($columns);

            $query .= " FROM " . $tableName;

            $query .= $this->concatWhereClause($whereCol, $where, $whereOperator);

            $data = $this->send($query);

            return $data;
        }

        public function update($tablename, array $columns, array $values, array $whereCol, array $where, array $whereOperator) {
            $colCount = count($columns);

            $query = "UPDATE $tablename SET ";

            $query .= $this->sequenceColumnNamesWithQuestionMarks($columns);

            $query .= $this->concatWhereClause($whereCol, $where, $whereOperator);

            $data = $this->bindValuesAndSend($query, $values, $colCount);

            return $data;
        }

        public function createTable($name, $parameters) {


            $query = "CREATE TABLE IF NOT EXISTS $name(";

            $query .= "id INT AUTO_INCREMENT,"
                    . $parameters;

            $query .= ",PRIMARY KEY (id)";
            $query .= ")  ENGINE=INNODB;";

            $this->send($query);
        }

        /*            Sub Functions   
         *   
         *  -> It is recommended to not edit past
         *  this point, but certainly look through if you are trying to figure
         *  out how the above works              */

        public function send($query) {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            // execute query
            return $stmt;
        }

//>> Use this to send special queries Straight From Data Access Object

        private function sequenceColumnsForSelect($columns) {
            $query = "";
            if ($columns == null) {
                $query .= " *";
            } else {
                $colCount = count($columns);
                for ($i = 0; $i < $colCount; $i++) {
                    $query .= " " . $columns[$i];
                    if ($i < $colCount - 1) {
                        $query .= ", ";
                    }
                }
            }
            return $query;
        }

        private function sequenceColumnNamesWithQuestionMarks($columns) {
            $colCount = count($columns);
            $query = "";
            for ($i = 0; $i < $colCount; $i++) {
                $query .= $columns[$i] . "=?";
                if ($i < $colCount - 1) {
                    $query .= ", ";
                }
            }
            return $query;
        }

        private function concatWhereClause($whereCol, $where, $whereOperator) {
            $whereCount = count($whereCol);

            $query = " WHERE ";

            for ($i = 0; $i < $whereCount; $i++) {
                $query .= $whereCol[$i] . " $whereOperator[$i] ";
                $query .= $where[$i] . " ";

                if ($i < $whereCount - 1) {
                    $query .= " AND ";
                }
            }

            return $query;
        }

        private function bindValuesAndSend($query, $values, $colCount) {
            try {
                $stmt = $this->conn->prepare($query);

                for ($i = 0; $i < $colCount; $i++) {
                    $stmt->bindParam($i + 1, $values[$i]);
                }

                if ($stmt->execute()) {
                    return true;
                }
                return implode(", ", $stmt->errorInfo());
                //return $query;
            } catch (Exception $e) {
                return "update error " . $e;
            }
        }

    }

}