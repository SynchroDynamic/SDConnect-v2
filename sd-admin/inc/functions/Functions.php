<?php

//Lets seperate all functions that can be, and post to my "AlgorithmFactory"

namespace admin {
    include_once dirname(__DIR__, 3) . "/SERVERS/SHARED/ServerDatabase.php";

    class Functions {

        public static function getGates() {
            $conn = new \ServerDatabase();
            $conn->getConnection();
            $rows = $conn->send("SELECT `id`, `gateName`, `status`, `changed` FROM `gates` WHERE 1");
            $conn->closeConnection();
            $twenty = array();
            $count = 0;
            while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                extract($row);

                $st = "ON";
                if ($status == 0) {
                    $st = "OFF";
                }
                $record = array(
                    "id" => $id,
                    "gateName" => $gateName,
                    "status" => $st,
                    "changed" => $changed
                );
                $twenty[$count++] = $record;
            }
            return $twenty;
        }

        public static function getGateByName($name) {
            $conn = new \ServerDatabase();
            $conn->getConnection();
            $rows = $conn->send("SELECT `id`, `gateName`, `status`, `changed` FROM `gates` WHERE `gateName` = '" . $name . "'");
            $conn->closeConnection();
            $twenty = array();
            $count = 0;
            while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);

                $record = array(
                    "id" => $id,
                    "gateName" => $gateName,
                    "status" => $status,
                    "changed" => $changed
                );
                $twenty[$count++] = $record;
            }
            return $twenty;
        }

        public static function getTablesAndCols() {
            $final = array();
            $finalCount = 0;
            $conn = new \ServerDatabase();
            $conn->getConnection();

            $gates = self::getGates();
            $gateCount = count($gates);
            for ($i = 0; $i < $gateCount; $i++) {
                $cols = $conn->send("DESCRIBE " . $gates[$i]['gateName']);
                $colCount = $cols->rowCount();
                $temp = array(
                    $gates[$i]['gateName']
                );

                $ret = $cols->fetchAll(\PDO::FETCH_COLUMN, 0);
                $currentCount = 1;
                for ($j = 0; $j < $colCount; $j++) {
                    $temp[$currentCount++] = $ret[$j];
                }
                $final[$finalCount++] = $temp;
            }
            $conn->closeConnection();
            return $final;
        }

        public static function getTablesAndColsByName($name) {
            $final = array();
            $finalCount = 0;
            $conn = new \ServerDatabase();
            $conn->getConnection();


            $cols = $conn->send("DESCRIBE " . $name);
            $colCount = $cols->rowCount();
            //echo $colCount;
            $temp = array(
                $name
            );
            $currentCount = 1;
            while ($row = $cols->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);
                $tempPiece = array(
                  
                    "Field" => $Field,
                    "Type" => $Type
                );
                $temp[$currentCount++] = $tempPiece;
                
            }
           
           // echo implode("::", $final);
            $conn->closeConnection();
            return $temp;
        }

        public static function addIncoming(array $dataArray) {
            $count = count($dataArray);
            $ret = 0;
            $conn = new \ServerDatabase();
            $conn->getConnection();

            $columns = array("name", "type", "makeupId");
            for ($i = 0; $i < $count; $i++) {
                $temp = $conn->insert("incomingparameters", $columns, $dataArray[$i]);
                if ($temp) {
                    $ret++;
                }
            }
            $conn->closeConnection();

            return $ret;
        }

        public static function addOutgoingTablesAndColumns(array $dataArray, $tid) {
            $count = count($dataArray);
            $ret = 0;
            $conn = new \ServerDatabase();
            $conn->getConnection();
            $words = $conn->delete("outgoingparameters", array("makeupId"), array($tid), array("="));
            //echo implode("::", $words->errorInfo());
            $columns = array("tableName", "columns", "makeupId");
            for ($i = 0; $i < $count; $i++) {
                $ret += $conn->insert("outgoingparameters", $columns, $dataArray[$i]);
            }
            $conn->closeConnection();
            return $ret;
        }

        public static function addOutgoingWhereStatements(array $dataArray, array $outIds) {
            $count = count($dataArray);
            $ret = 0;
            $conn = new \ServerDatabase();
            $conn->getConnection();

            foreach ($outIds as $o) {
                $conn->delete("whereparameters", array("outId"), array($o['id']), array("="));
            }

            $columns = array("outId", "whereCol", "whereOperator", "whereValue");
            for ($i = 0; $i < $count; $i++) {
                $ret = $conn->insert("whereparameters", $columns, $dataArray[$i]);
            }
            $conn->closeConnection();
            return $ret;
        }

        public static function deleteOldWhereStatements(array $outIds) {
            $conn = new \ServerDatabase();
            $conn->getConnection();

            foreach ($outIds as $o) {
                $conn->delete("whereparameters", array("outId"), array($o['id']), array("="));
            }
            $conn->closeConnection();
        }

        public static function getOutgoingIds($tid) {

            $conn = new \ServerDatabase();
            $conn->getConnection();
            $columns = array("id", "tableName");
            $ret = $conn->select("outgoingparameters", $columns, array("makeupId"), array($tid), array("="));
            $conn->closeConnection();
            $twenty = array();
            $countRow = 0;
            while ($row = $ret->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);
                $record = array(
                    "id" => $id,
                    "name" => $tableName
                );
                $twenty[$countRow++] = $record;
            }
            return $twenty;
        }

        public static function addGeneral($id, array $transactionArray) {
            $ret = 0;
            $transactionID = -1;
            $conn = new \ServerDatabase();
            $conn->getConnection();
            $columns = array("name", "type", "hasParameters", "hasData", "gateId");
            $whereCol = array("gateId");
            $whereValue = array($id);
            $whereOper = array("=");
            $pstate = $conn->select("transactionmakeup", array("id", $columns[0], $columns[1], $columns[2], $columns[3], $columns[4]), $whereCol, $whereValue, $whereOper);

            $tCount = $pstate->rowCount();

            $retState = $pstate->fetchAll(\PDO::FETCH_ASSOC);
            $transactionExists = false;

            for ($j = 0; $j < $tCount; $j++) {
                echo $retState[$j]['name'] . " == $transactionArray[0]";
                if ($retState[$j]['name'] == $transactionArray[0]) {
                    $transactionID = $retState[$j]['id'];
                    $transactionExists = true;
                    break;
                }
            }

            if (!$transactionExists) {
                $ret += $conn->insert("transactionmakeup", $columns, $transactionArray);
                $newPState = $conn->select("transactionmakeup", array("id", "name"), array("name"), array("'$transactionArray[0]'"), array("="));
                $tCount1 = $newPState->rowCount();
                $newRetState = $newPState->fetchAll(\PDO::FETCH_ASSOC);

                for ($j = 0; $j < $tCount1; $j++) {
                    //echo $newRetState[$j]['name'] . " == $transactionArray[0]";
                    if ($newRetState[$j]['name'] == $transactionArray[0]) {
                        return $newRetState[$j]['id'];
                    }
                }
                return 'error' . implode(",", $newPState->errorInfo());
            } else {
                $ret += $conn->update("transactionmakeup", $columns, $transactionArray, array("id"), array($transactionID), array("="));
            }



            $conn->closeConnection();

            return $transactionID;
        }

        public static function getTransactionData($id1) {

            try {
                $tranArray = array();
                $data = array();
                $conn = new \ServerDatabase();
                $conn->getConnection();
                $gColumns = array("id", "name", "type", "hasParameters", "hasData", "gateId");
                $general = $conn->select("transactionmakeup", $gColumns, array($gColumns[0]), array($id1), "=");

                $count = 0;
                while ($row = $general->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                    extract($row);
                    $temp = \Model\Transaction::newTransaction();
                    $temp->setGenralSettings($id, $name, $type, $hasParameters, $hasData, $gateId);
                    $tranArray["$id"] = $temp;
                }
                $iColumns = array("id", "name", "type", "makeupId");
                $incoming = $conn->select("incomingparameters", $iColumns, array($iColumns[3]), array($id1), "=");

                while ($row = $incoming->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                    extract($row);
                    $tranArray["$makeupId"]->setIncomingParameters($id, $name, $type, $makeupId);
                }

                $tables = self::getTablesAndCols();

                $oColumns = array("id", "tableName", "columns", "makeupId");

                $outgoing = $conn->select("outgoingparameters", $oColumns, array($oColumns[3]), array($id1), "=");

                $tempOutArray = array();

                while ($row = $outgoing->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                    extract($row);

                    $tempOutArray["$id"] = \Model\Outgoing::existingOutgoing($id, $tableName, $columns, $makeupId, array());


                    $whereArray = self::getWheres($conn, $id);
                    $wCount = count($whereArray);
                    for ($w = 0; $w < $wCount; $w++) {
                        $key = $whereArray[$w]->getTransactionId();
                        $tempOutArray["$key"]->addWhereSet($whereArray[$w]);
                    }
                }

                foreach ($tempOutArray as $key => $value) {
                    $tranArrayKey = $value->getTransactionId();
                    $tranArray["$tranArrayKey"]->setOutgoingObject($value);
                }

                $finalTransactionModel = self::turnArraysToIterable($tranArray);
                return $finalTransactionModel;
                //return turnArraysToIterable($tranArray);
            } catch (Exception $e) {
                return $e->readLine();
            }
        }

        private static function turnArraysToIterable($transaction) {

            $temp = array();
            $count = 0;
            foreach ($transaction as $key => $trans) {
                $incomingArray = array();
                $inCount = 0;
                foreach ($trans->getIncoming() as $keyIn => $in) {
                    $incomingArray[$inCount++] = $in;
                }
                $outArray = array();
                $outCount = 0;
                foreach ($trans->getOutgoing() as $keyOut => $out) {
                    $tempW = array();
                    $tWC = 0;
                    foreach ($out->getWhereSets() as $keyW => $w) {
                        //echo "<script>alert('$keyW');</script>";
                        $tempW[$tWC++] = $w;
                    }
                    $out->setWhereSets($tempW);
                    $outArray[$outCount++] = $out;
                }
                $trans->setIncoming($incomingArray);
                $trans->setOutgoing($outArray);
                $temp[$count++] = $trans;
            }
            return $temp;
        }

        static function getWheres($conn, $idl) {
            $wColumns = array("id", "outId", "whereCol", "whereOperator", "whereValue");
            $tempWheres = $conn->select("whereparameters", $wColumns, array($wColumns[1]), array($idl), "=");

            $tranArray = array();
            $count = 0;

            while ($row = $tempWheres->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                extract($row);
                $tranArray[$count++] = \Model\WhereSet::existingWhereSet($id, $outId, $whereCol, $whereOperator, $whereValue);
            }

            return $tranArray;
        }

        public static function getTransactionsForGate($id) {
            $conn = new \ServerDatabase();
            $conn->getConnection();
            $gColumns = array("id", "name", "type", "hasParameters", "hasData", "gateId");
            $general = $conn->select("transactionmakeup", $gColumns, array($gColumns[5]), array($id), "=");
            return $general->fetchAll(\PDO::FETCH_ASSOC);
        }

    }

}

