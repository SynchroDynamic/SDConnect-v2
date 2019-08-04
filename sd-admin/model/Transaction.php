<?php

namespace Model {
    include_once dirname(__DIR__, 1) . '/model//Incoming.php';
    include_once dirname(__DIR__, 1) . '/model//Outgoing.php';

    //Model For: transactionMakeup
    class Transaction {

        private $id;
        private $name;
        private $type;
        private $hasIncoming;
        private $hasOutgoing;
        private $gateId;
        private $Incoming;
        private $Outgoing;

        function __construct() {
            $this->Incoming = array();
            $this->Outgoing = array();
        }

        static function completeTransaction($id, $name, $type, $hasIncoming, $hasOutgoing, $gateId, $Incoming, $Outgoing) {
            $instance = new Transaction();
            $instance->id = $id;
            $instance->name = $name;
            $instance->type = $type;
            $instance->hasIncoming = $hasIncoming;
            $instance->hasOutgoing = $hasOutgoing;
            $instance->gateId = $gateId;
            $instance->Incoming = $Incoming; //this is an array of Incoming Objects
            $instance->Outgoing = $Outgoing; //this is an array of Outgoing Objects
            return $instance;
        }

        static function newTransaction() {
            return new Transaction();
        }

        function setGenralSettings($id, $name, $type, $hasIncoming, $hasOutgoing, $gateId) {
            $this->id = $id;
            $this->name = $name;
            $this->type = $type;
            $this->hasIncoming = $hasIncoming;
            $this->hasOutgoing = $hasOutgoing;
            $this->gateId = $gateId;
        }

        function setIncomingParameters($id, $name, $type, $transactionId) {
            $temp = \Model\Incoming::existingIncoming($id, $name, $type, $transactionId);
            $this->Incoming[count($this->Incoming) - 1] = $temp;
        }

        function setOutgoingParameters($id, $tableName, $columns, $transactionId, $WhereSet) {
            $temp = \Model\Outgoing::existingOutgoing($id, $tableName, $columns, $transactionId, $WhereSet);
            $this->Outgoing[count($this->Outgoing) - 1] = $temp;
        }

        function setOutgoingObject($out) {
            $this->Outgoing[count($this->Outgoing) - 1] = $out;
        }

        function setIncoming($Incoming) {
            $this->Incoming = $Incoming;
        }

        function setOutgoing($Outgoing) {
            $this->Outgoing = $Outgoing;
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

        function getHasIncoming() {
            return $this->hasIncoming;
        }

        function getHasOutgoing() {
            return $this->hasOutgoing;
        }

        function getGateId() {
            return $this->gateId;
        }

        function getIncoming() {
            return $this->Incoming;
        }

        function getOutgoing() {
            return $this->Outgoing;
        }

    }

}

