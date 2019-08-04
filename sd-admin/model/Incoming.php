<?php

namespace Model {

    class Incoming {

        private $id;
        private $name;
        private $type;
        private $transactionId;

        function __construct() {
            
        }

        static function existingIncoming($id, $name, $type, $transactionId) {
            $instance = new Incoming();
            $instance->id = $id;
            $instance->name = $name;
            $instance->type = $type;
            $instance->transactionId = $transactionId;
            return $instance;
        }

        static function newIncoming() {
            return new Incoming();
        }

        function getId() {
            return $this->id;
        }

        function getName() {
            return $this->name;
        }

        function getType() {
            return $this->type;
        }

        function getTransactionId() {
            return $this->transactionId;
        }

        function setId($id) {
            $this->id = $id;
        }

        function setName($name) {
            $this->name = $name;
        }

        function setType($type) {
            $this->type = $type;
        }

        function setTransactionId($transactionId) {
            $this->transactionId = $transactionId;
        }

    }

}

