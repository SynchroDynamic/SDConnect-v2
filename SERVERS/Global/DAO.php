<?php

include_once "/var/www/html/SERVERS/SHARED/ServerDatabase.php";
include_once "var/www/html/inc/sd-config.php";

namespace {

    date_default_timezone_set('America/Chicago');

//
//Turns an sql query into an Object  (Data Access Object)

    class DAO {

        const COLUMNS = array();

        private $conn;
        private $date;
        private $callList;

        function __construct() {
            $this->date = date('Y-m-d H:i:s');
            $this->conn = new \ServerDatabase();
        }
        
        public function setCallList($gateName){
            
            
        }
        

        public function enterTournament($columns, $values) {
            $this->conn->getConnection();
            $stmt = $this->conn->insert("Tournament_Competitor", $columns, $values);
            $this->conn->closeConnection();
            return $stmt;
        }

        public function getTournaments($id1, $start1, $perpage) {
            $this->conn->getConnection();
            $stmt = $this->conn->getTournaments($id1, $start1, $perpage);
            $this->conn->closeConnection();
            $twenty = array();
            $count = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                extract($row);
                $statRecord = array(
                    "id" => $id,
                    "name" => $name,
                    "description" => $description,
                    "firstPrize" => $firstPrize,
                    "secondPrize" => $secondPrize,
                    "thirdPrize" => $thirdPrize,
                    "start" => $start,
                    "r1end" => $r1end,
                    "r2end" => $r2end,
                    "r3end" => $r3end,
                    "r4end" => $r4end,
                    "end" => $end,
                    "fee" => $fee,
                    "size" => $size
                );
                $twenty[$count++] = $statRecord;
            }
            return $twenty;
        }

        public function getTournamentsForTiming($id1, $start1, $perpage) {
            $this->conn->getConnection();
            $stmt = $this->conn->getTournaments($id1, $start1, $perpage);
            $this->conn->closeConnection();
            $twenty = array();
            $count = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                extract($row);
                $statRecord = array(
                    "id" => $id,
                    "name" => $name,
                    "description" => $description,
                    "firstPrize" => $firstPrize,
                    "secondPrize" => $secondPrize,
                    "thirdPrize" => $thirdPrize,
                    "start" => $start,
                    "r1end" => $r1end,
                    "r2end" => $r2end,
                    "r3end" => $r3end,
                    "r4end" => $r4end,
                    "end" => $end,
                    "fee" => $fee,
                    "size" => $size,
                    "startStatus" => $startStatus,
                    "r1endStatus" => $r1endStatus,
                    "r2endStatus" => $r2endStatus,
                    "r3endStatus" => $r3endStatus,
                    "r4endStatus" => $r4endStatus,
                    "endStatus" => $endStatus
                );
                $twenty[$count++] = $statRecord;
            }
            return $twenty;
        }

        public function myTournaments($id1, $start1, $perpage) {
            $this->conn->getConnection();
            $stmt = $this->conn->myTournaments($id1, $start1, $perpage);
            $this->conn->closeConnection();
            $twenty = array();
            $count = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                extract($row);
                $statRecord = array(
                    "id" => $id,
                    "name" => $name,
                    "description" => $description,
                    "firstPrize" => $firstPrize,
                    "secondPrize" => $secondPrize,
                    "thirdPrize" => $thirdPrize,
                    "start" => $start,
                    "r1end" => $r1end,
                    "r2end" => $r2end,
                    "r3end" => $r3end,
                    "r4end" => $r4end,
                    "end" => $end,
                    "fee" => $fee,
                    "size" => $size
                );
                $twenty[$count++] = $statRecord;
            }
            return $twenty;
        }

        public function getPlayersForTournament($t) {
            $this->conn->getConnection();
            $stmt = $this->conn->getTournamentPlayers($t);
            $this->conn->closeConnection();
            $twenty = array();
            $count = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);
                if ($seed != null) {
                    $statRecord = array(
                        "seed" => $seed,
                        "Username" => $Username,
                        "r1" => $r1,
                        "r2" => $r2,
                        "r3" => $r3,
                        "r4" => $r4,
                        "w" => $w,
                        "l" => $l,
                        "d" => $d,
                        "status" => $status,
                        "ready" => $ready
                    );
                    $twenty[$count++] = $statRecord;
                }
            }

            return $twenty;
        }

        public function getPlayerStats($pid) {
            $this->conn->getConnection();
            $stmt = $this->conn->getPlayerStats($pid);
            $this->conn->closeConnection();

            $twenty = array();
            $count = 0;

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                extract($row);
                $statRecord = array(
                    "w" => $w,
                    "l" => $l,
                    "d" => $d,
                    "gold" => $gold,
                    "silver" => $silver,
                    "bronze" => $bronze,
                    "total" => $total
                );


                $twenty[$count++] = $statRecord;
            }

            return $twenty;
        }

        public function getPlayersFromArchive($tid) {
            $this->conn->getConnection();
            $stmt = $this->conn->getArchiveTournamentPlayers($tid);
            $this->conn->closeConnection();

            $twenty = array();
            $count = 0;

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                extract($row);

                if ($seed != null) {
                    $statRecord = array(
                        "seed" => $seed,
                        "Username" => $Username,
                        "r1" => $r1,
                        "r2" => $r2,
                        "r3" => $r3,
                        "r4" => $r4,
                        "w" => $w,
                        "l" => $l,
                        "d" => $d,
                        "status" => $status,
                        "ready" => $ready
                    );

                    $twenty[$count++] = $statRecord;
                }
            }

            return $twenty;
        }

        public function updateStatus($status, $id) {
            $this->conn->getConnection();
            $this->conn->updateStatus($status, $id);
            $this->conn->closeConnection();
        }

        public function placePlayer($t, $id, $place) {
            $this->conn->getConnection();
            $this->conn->placePlayer($t, $id, $place);
            $this->conn->closeConnection();
        }

        public function getReadyState() {
            
        }

        public function checkReady($isReady, $tournamentID, $clientId) {


            $this->conn->getConnection();
            //first check which tournaments the client is in

            if ($isReady == 0) {
                //update all "ready" of client to 0
                $this->conn->update("Tournament_Competitor", array("ready"), array(0), "playerId", $clientId, false);
                return 0;
            } else if ($isReady == -1) {
                //Clear All "ready" state
                $this->conn->deleteAllReady();
                return 0;
            } else {
                $this->conn->update("Tournament_Competitor", array("ready"), array($tournamentID), "playerId", $clientId, false);
                $stmt = $this->conn->getTournamentPlayers($tournamentID);
                $players = array();
                $count = 0;
                $clientObject = null;
                $nextCompObject = null;
                $nextCompetitorSeed = -1;
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                    extract($row);
                    $record = array(
                        "seed" => $seed,
                        "Username" => $Username,
                        "r1" => $r1,
                        "r2" => $r2,
                        "r3" => $r3,
                        "r4" => $r4,
                        "w" => $w,
                        "l" => $l,
                        "d" => $d,
                        "status" => $status,
                        "ready" => $ready
                    );
                    if ($UserId == $clientId) {
                        $clientObject = $record;
                    }
                    $players[$count++] = $record;
                }


                if ($clientObject == null) {
                    $this->conn->closeConnection();
                    return 3;
                }

                if ($count == 1) {
                    $this->conn->updateStatus($tournamentID, $clientId);
                }
                $this->conn->closeConnection();


                if ($clientObject["seed"] % 2 == 0) {
                    $nextCompetitorSeed = $clientObject["seed"] - 1;
                } else {
                    $nextCompetitorSeed = $clientObject["seed"] + 1;
                }

                for ($i = 0; $i < $count; $i++) {

                    if ($players[$i]["seed"] == $nextCompetitorSeed) {
                        $nextCompObject = $players[$i];
                        break;
                    }
                }
                $isNextReady = false;
                if ($nextCompObject != null) {
                    if ($nextCompObject["ready"] == $tournamentID && $nextCompObject["status"] == $tournamentID) {
                        $isNextReady = true;
                    }
                }

                //then if client ready state count is 0, return 1;
                if ($count == 0) {
                    return 4;
                }
                //else if clientreadycount == 1 && clientreadyID == $tournamentID, return 1;
                else if ($count > 0 && $clientObject["ready"] == $tournamentID) {
                    if ($isNextReady) {
                        return 1;
                    }
                    return 0;
                }
                //else if clientreadycount > 0, return 2;
                else if ($count > 0) {
                    return 2;
                }
                return 0; //Automation Return
            }
        }

        public function addTournament($columns, $values) {
            $this->conn->getConnection();
            $ret = $this->conn->insert("Tournament_Info", $columns, $values);
            $this->conn->closeConnection();
            return $ret;
        }

        public function deleteMatchSetForTournament($tid) {
            $this->conn->getConnection();
            $ret = $this->conn->delete($tid, "tournamentMatchset", "tournamentId");
            $this->conn->closeConnection();
            return $ret;
        }

        public function updateAllUserStatus() {
            //first get all users where status <> 0
            $this->conn->getConnection();
            $date2 = new \DateTime(date('Y-m-d H:i:s') . "");
            $stmt = $this->conn->betterSelect("user", array("UserId", "status", "statusPosted"), "status", 0, false, "<>");

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {//In the future this will need to be made into a 2d array
                extract($row);

                if (is_null($statusPosted)) {
                    $this->conn->updateStatus($status, $UserId);
                }

                $date = new \DateTime($statusPosted);

                $diffInSeconds = $date2->getTimestamp() - $date->getTimestamp();

                if ($status == 1 && $diffInSeconds > 59) {

                    $this->conn->updateStatus(0, $UserId);
                } else if ($status > 1 && $diffInSeconds > 59) {
                    $this->conn->updateStatus(1, $UserId);
                }
            }
            $this->conn->closeConnection();
            return $stmt->errorInfo();
        }

        public function archiveTournament($tournamentID) {
            $this->conn->getConnection();
            $ar = $this->conn->archiveTournament($tournamentID);
            $this->conn->delete($tournamentID, "Tournament_Info", "id");
            $this->conn->closeConnection();
            return $ar;
        }

        public function deleteTournament($tournamentID) {
            $this->conn->getConnection();
            $this->conn->delete($tournamentID, "Tournament_Info", "id");
            $this->conn->delete($tournamentID, "Tournament_Competitor", "tournamentId");
            $this->conn->closeConnection();
        }

        public function deleteTournamentPlayer($t, $pid) {
            $this->conn->getConnection();
            $this->conn->deleteTournamentPlayers($t, $pid);
            $this->conn->closeConnection();
        }

        public function updateTournament($t) {
            $this->conn->getConnection();
            $columns = array();
            $columns[0] = "startStatus";
            $columns[1] = "r1endStatus";
            $columns[2] = "r2endStatus";
            $columns[3] = "r3endStatus";
            $columns[4] = "r4endStatus";
            $columns[5] = "endStatus";

            $values = array();
            $values[0] = $t["startStatus"];
            $values[1] = $t["r1endStatus"];
            $values[2] = $t["r2endStatus"];
            $values[3] = $t["r3endStatus"];
            $values[4] = $t["r4endStatus"];
            $values[5] = $t["endStatus"];

            $stmt = $this->conn->update("Tournament_Info", $columns, $values, "id", $t["id"], false);
            $this->conn->closeConnection();
            return $stmt;
        }

        public function updatePlayerRound($roundKey, $id, $tid, $column, $roundOutcome, $addColumn) {

            $columns = array();
            $columns[0] = $roundKey;
            $columns[1] = $column;

            $values = array();
            $values[0] = $roundOutcome;
            $values[1] = $addColumn;
            $this->conn->getConnection();
            $stmt = $this->conn->updateTournamentCompetitor($columns, $values, $id, $tid);
            $this->conn->closeConnection();
            return $stmt;
        }

        public function updatePlayerInManager($columns, $values, $id, $tid) {

            $this->conn->getConnection();

            $whereCol = array();
            $where = array();
            $whereOperator = array();


            $whereCol[0] = "playerId";
            $whereOperator[0] = "=";
            $where[0] = $id;


            $whereCol[1] = "tournamentId";
            $whereOperator[1] = "=";
            $where[1] = $tid;


            $stmt = $this->conn->updateInfWhereVar("Tournament_Competitor", $columns, $values, $whereCol, $where, $whereOperator);
            $this->conn->closeConnection();
            return $stmt;
        }

        public function updateTournamentCompetitor($columns, $values, $userID, $tid) {
            $this->conn->getConnection();
            $stmt = $this->conn->updateTournamentCompetitor($columns, $values, $userID, $tid);
            $this->conn->closeConnection();
            return $stmt;
        }

        public function archiveTournamentPlayers($t, $place, $c, $pid) {
            $this->conn->getConnection();
            $stmt = $this->conn->archiveTournamentPlayers($t, $place, $c, $pid);
            $this->conn->deleteTournamentPlayers($t, $pid);
            $this->conn->closeConnection();
            return $stmt;
        }

        public function getTournamentMatchsets($tid) {
            $this->conn->getConnection();
            $stmt = $this->conn->select("tournamentMatchset", array("matchID"), "tournamentId", $tid, false);
            $this->conn->closeConnection();
            return $stmt->rowCount();
        }

        public function getTournamentMatchsetsRound($tid) {
            $this->conn->getConnection();
            $stmt = $this->conn->select("tournamentMatchset", array("round"), "tournamentId", $tid, false);
            $this->conn->closeConnection();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);
                return $round;
            }
            return -1;
        }

        public function getMatchsetsFor($tid, $id) {
            $this->conn->getConnection();
            $columns = array("matchID", "player1ID", "player1Score", "player1Name", "player2ID", "player2Score", "player2Name", "sharedToken", "round", "tournamentId");
            $stmt = $this->conn->select("tournamentMatchset", $columns, "tournamentId", $tid, false);
            $this->conn->closeConnection();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);

                if ($player1ID == $id || $player2ID == $id) {
                    return array(
                        "player1ID" => $player1ID,
                        "player1Score" => $player1Score,
                        "player1Name" => $player1Name,
                        "player2ID" => $player2ID,
                        "player2Score" => $player2Score,
                        "player2Name" => $player2Name,
                        "sharedToken" => $sharedToken,
                        "round" => $round,
                        "tournamentId" => $tournamentId
                    );
                }
            }
            return null;
        }

        public function getMatchsetsByToken($token, $id) {
            $this->conn->getConnection();
            $columns = array("matchID", "player1ID", "player1Score", "player1Name", "player2ID", "player2Score", "player2Name", "sharedToken", "round", "tournamentId");
            $stmt = $this->conn->betterSelect("tournamentMatchset", $columns, "sharedToken", $token, true, "=");
            $this->conn->closeConnection();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);

                if ($player1ID == $id || $player2ID == $id) {
                    return array(
                        "player1ID" => $player1ID,
                        "player1Score" => $player1Score,
                        "player1Name" => $player1Name,
                        "player2ID" => $player2ID,
                        "player2Score" => $player2Score,
                        "player2Name" => $player2Name,
                        "sharedToken" => $sharedToken,
                        "round" => $round,
                        "tournamentId" => $tournamentId
                    );
                }
            }
            return null;
        }

        public function createMatchset($player1ID, $player1Score, $player1Name, $player2ID, $player2Score, $player2Name, $round, $tid) {
            $this->conn->getConnection();

            $columns = array();
            $columns[0] = "player1ID";
            $columns[1] = "player1Score";
            $columns[2] = "player1Name";
            $columns[3] = "player2ID";
            $columns[4] = "player2Score";
            $columns[5] = "player2Name";
            $columns[6] = "sharedToken";
            $columns[7] = "round";
            $columns[8] = "tournamentId";

            $values = array();
            $values[0] = $player1ID;
            $values[1] = $player1Score;
            $values[2] = $player1Name;
            $values[3] = $player2ID;
            $values[4] = $player2Score;
            $values[5] = $player2Name;
            $values[6] = $this->encryptString($this->encryptString(date("Y-m-d H:i:s") . rand(0, 10)));
            $values[7] = $round;
            $values[8] = $tid;


            $this->conn->insert("tournamentMatchset", $columns, $values);
            $this->conn->closeConnection();
        }

        public function encryptString($str) {
            $key = ""; //$this->user->getRegDate();
            $iv = $this->getIv();

            $ciphertext = openssl_encrypt($str, SDC::SESS_CIPHER
                    , $key, $options = OPENSSL_RAW_DATA, $iv
            );
            $encryptedSessionId = base64_encode($ciphertext);
            return $encryptedSessionId;
        }

        public function getIv() {
            $ivlen = openssl_cipher_iv_length(SDC::SESS_CIPHER);
            return substr(md5($this->date), 0, $ivlen);
        }

    }

}