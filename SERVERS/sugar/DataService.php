<?php
 namespace sugar {include_once "DAO.php";class DataService {static function update($values) {$DAO = self::getObject();return $DAO->update($values);}
        static function getRecords($whereCol, $whereOperator, $where) {$DAO = self::getObject();return $DAO->retrieve($whereCol, $whereOperator, $where);}
        static function delete($id) {$DAO = self::getObject();return $DAO->delete($id);}
        private static function getObject() {return new\sugar\DAO();}
    }
}