<?php

namespace Model {

    //From Table: whereparameters
    class WhereSet {

        private $id;
        private $transactionId;
        private $whereColumn;
        private $whereOperator;
        private $whereValue;

        function __construct() {
            
        }

        static function existingWhereSet($id, $transactionId, $whereColumn, $whereOperator, $whereValue) {
            $instance = new WhereSet();
            $instance->id = $id;
            $instance->transactionId = $transactionId;
            $instance->whereColumn = $whereColumn;
            $instance->whereOperator = $whereOperator;
            $instance->whereValue = $whereValue;
            return $instance;
        }

        static function newWhereSet() {
            return new WhereSet();
        }

        function getId() {
            return $this->id;
        }

        function getTransactionId() {
            return $this->transactionId;
        }

        function getWhereColumn() {
            return $this->whereColumn;
        }

        function getWhereOperator() {
            return $this->whereOperator;
        }

        function getWhereValue() {
            return $this->whereValue;
        }

        function setId($id) {
            $this->id = $id;
        }

        function setTransactionId($transactionId) {
            $this->transactionId = $transactionId;
        }

        function setWhereColumn($whereColumn) {
            $this->whereColumn = $whereColumn;
        }

        function setWhereOperator($whereOperator) {
            $this->whereOperator = $whereOperator;
        }

        function setWhereValue($whereValue) {
            $this->whereValue = $whereValue;
        }

        public function __toString() {
            return "{ ID: $this->id, TID: $this->transactionId, WHERE: $this->whereColumn $this->whereOperator $this->whereValue }";
        }

    }

}