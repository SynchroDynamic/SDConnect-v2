<?php

namespace Model {
    include_once dirname(__DIR__, 1) . '/model/WhereSet.php';

    class Outgoing {

        private $id;
        private $tableName;
        private $columns;
        private $transactionId;
        private $WhereSet; //WhereSet Object

        function __construct() {
            $this->WhereSet = array();
        }

        static function existingOutgoing($id, $tableName, $columns, $transactionId, $WhereSet) {
            $instance = new Outgoing();
            $instance->id = $id;
            $instance->tableName = $tableName;
            $instance->columns = $columns;
            $instance->transactionId = $transactionId;
            $instance->WhereSet = $WhereSet;
            return $instance;
        }

        static function newOutgoing() {
            return new Outgoing();
        }

        function getId() {
            return $this->id;
        }

        function getTableName() {
            return $this->tableName;
        }

        function getColumns() {
            return $this->columns;
        }

        function getTransactionId() {
            return $this->transactionId;
        }

        function setId($id) {
            $this->id = $id;
        }

        function setTableName($tableName) {
            $this->tableName = $tableName;
        }

        function setColumns($columns) {
            $this->columns = $columns;
        }

        function setTransactionId($transactionId) {
            $this->transactionId = $transactionId;
        }

        function getWhereSets() {
            return $this->WhereSet;
        }

        function getWhereSetByIndex($i) {
            return $this->WhereSet[$i];
        }

        function whereSetSize() {
            return count($this->WhereSet);
        }

        function setWhereSets($WhereSet) {
            $this->WhereSet = $WhereSet;
        }

        function addWhereSet($set) {
            $this->WhereSet[count($this->WhereSet) - 1] = $set;
        }

    }

}

