<?php

namespace Tournament {
    include_once 'DAO.php';                                       // Make these more dynamic?

    class DataService {
        /*         * *********************************************************************
         * 
         *              Tournament_Info Table
         * 
         * ******************************************************************** */

        static function tournamentInfo($id, $start, $perpage) {
            $DAO = self::getObject();
            return $DAO->getTournaments($id, $start, $perpage);
        }

        static function tournamentInfoForTiming() {
            $DAO = self::getObject();
            return $DAO->getTournamentsForTiming(1, 0, 5000);
        }

        static function tournamentInfoArchive($tournamentID) {
            $DAO = self::getObject();
            return $DAO->archiveTournament($tournamentID);
        }

        static function updateTournaments($t) {
            $DAO = self::getObject();
            $count = count($t);
            $ret = "";
            if ($count == 0) {
                return "No Update Needed";
            }
            for ($i = 0; $i < $count; $i++) {
                $ret .= ($t[$i]["id"]);
                $temp = $DAO->updateTournament($t[$i]);
                if ($temp) {
                    $ret .= " Successful Update ";
                } else {
                    $ret .= " ERROR : $temp ";
                }
                $ret .= "\n\n";
            }
            return $ret;
        }

        static function myTournaments($id, $start, $perpage) {
            $DAO = self::getObject();
            return $DAO->myTournaments($id, $start, $perpage);
        }

        static function createTournament($title, $description, $firstPrize, $secondPrize, $thirdPrize, $fee, $size, $startDate, $roundInterval) {
            $DAO = self::getObject();

            $columns = array("name", "description", "firstPrize", "secondPrize", "thirdPrize", "start", "r1end", "r2end", "r3end", "r4end", "end", "fee", "size", "startStatus", "r1endStatus"
                , "r2endStatus", "r3endStatus", "r4endStatus", "endStatus");

            $dates = array();
            for ($i = 0; $i < 7; $i++) {
                $minutes = ($roundInterval * $i);
                $temp = strtotime("+$minutes seconds", strtotime($startDate));
                $dates[$i] = date('Y-m-d H:i:s', $temp);
            }


            $values = array($title, $description, $firstPrize, $secondPrize, $thirdPrize, $startDate, $dates[1], $dates[2], $dates[3], $dates[4], $dates[5], $fee, $size
                , 0, 0, 0, 0, 0, 0);
            return $DAO->addTournament($columns, $values);
        }

        static function deleteTournament($tid) {

            $DAO = self::getObject();
            $DAO->deleteTournament($tid);
        }

        /*         * *********************************************************************
         * 
         *              Tournament_Competitor Table
         * 
         * ******************************************************************** */

        static function enterTournament($id, $tid) {
            $DAO = self::getObject();
            //first get last seed
            $players = $DAO->getPlayersForTournament($tid);

            $count = count($players);
            $seed = $count + 1;
            $columns = array("playerId", "tournamentId", "seed", "r1", "r2", "r3", "r4", "w", "l", "d", "ready");
            $values = array($id, $tid, $seed, 0, 0, 0, 0, 0, 0, 0, 0);
            return $DAO->enterTournament($columns, $values);
        }

        static function addPlacedPlayerToArchive($t, $id, $place) {
            $DAO = self::getObject();
            $DAO->placePlayer($t, $id, $place);
        }

        static function updatePlayerNOCONTEST($userID, $key, $tid, $increment) {
            $DAO = self::getObject();
            //$DAO->updatePlayerRound($nextRound, $userID, $tid, $column, -1, $addColumn);
            $columns = array();
            $columns[0] = "playerId";
            $columns[1] = $key;

            $values = array();
            $values[0] = $userID;
            $values[1] = 1;

            return $DAO->updateTournamentCompetitor($columns, $values, $userID, $tid);
        }

        static function updatePlayerInManager($player, $oldID, $newID, $tid) {
            $DAO = self::getObject();

            $columns = array();

            $columns[0] = "seed";
            $columns[1] = "r1";
            $columns[2] = "r2";
            $columns[3] = "r3";
            $columns[4] = "r4";
            $columns[5] = "w";
            $columns[6] = "l";
            $columns[7] = "d";
            $columns[8] = "playerId";

            $values = array();
            $values[0] = $player["seed"];
            $values[1] = $player["r1"];
            $values[2] = $player["r2"];
            $values[3] = $player["r3"];
            $values[4] = $player["r4"];
            $values[5] = $player["w"];
            $values[6] = $player["l"];
            $values[7] = $player["d"];
            $values[8] = $newID;

            return $DAO->updatePlayerInManager($columns, $values, $oldID, $tid);
        }

        static function updatePlayer($userID, $key, $tid) {
            $DAO = self::getObject();

            $columns = array();
            $columns[0] = $key;
            $columns[1] = "tournamentId";

            $values = array();
            $values[0] = 1;
            $values[1] = $tid;

            return $DAO->updateTournamentCompetitor($columns, $values, $userID, $tid);
        }

        static function TournamentPlayers($id, $t) {
            $DAO = self::getObject();
            $DAO->updateStatus($t, $id);
            return $DAO->getPlayersForTournament($t);
        }

        static function getAllTournamentPlayers($t) {
            $DAO = self::getObject();
            return $DAO->getPlayersForTournament($t);
        }

        static function archiveNOPLACETournamentPlayer($tid, $pid) {

            $DAO = self::getObject();
            $DAO->archiveTournamentPlayers($tid, 0, 0, $pid);
        }

        static function aggregateTop3($first, $second, $third, $t) {
            $DAO = self::getObject();
            //insert place and credits won to each player first,second,third
            $DAO->archiveTournament($t["id"]);

            //$DAO->insertVictor($first[""],);   
//            $DAO->placePlayer($t["id"], $firstID, "first");
//            $DAO->placePlayer($t["id"], $secondID, "second");
//            $DAO->placePlayer($t["id"], $thirdID, "third");

            $firstRet = $DAO->archiveTournamentPlayers($t["id"], 1, $t["firstPrize"], $first);
            $secRet = $DAO->archiveTournamentPlayers($t["id"], 2, $t["secondPrize"], $second);
            $thirdRet = $DAO->archiveTournamentPlayers($t["id"], 3, $t["thirdPrize"], $third);

            return "1st Archive Message: " . $firstRet . "\n 2nd AM: " . $secRet . "\n 3rd AM: " . $thirdRet . "\n";
        }

        /*         * *********************************************************************
         * 
         *              Tournament_Comp Arhive
         * 
         * ******************************************************************** */

        static function playerStatsRecord($pid) {
            $DAO = self::getObject();
            return $DAO->getPlayerStats($pid);
        }

        /*         * *********************************************************************
         * 
         *              Tournament_Matchset Table
         * 
         * ******************************************************************** */

        static function countMatchSetsForTournament($tid) {
            $DAO = self::getObject();
            return $DAO->getTournamentMatchsets($tid);
        }

        static function getMatchSetsRoundForTournament($tid) {
            $DAO = self::getObject();
            return $DAO->getTournamentMatchsetsRound($tid);
        }

        static function getTournamentMatchsetFor($tid, $id) {
            $DAO = self::getObject();
            return $DAO->getMatchsetsFor($tid, $id);
        }

        static function getMatchsetsByToken($token) {
            $DAO = self::getObject();
            return $DAO->getMatchsetByToken($token);
        }

        static function createMatchSets($player1ID, $player1Score, $player1Name, $player2ID, $player2Score, $player2Name, $round, $tid) {
            $DAO = self::getObject();

            $DAO->createMatchset($player1ID, $player1Score, $player1Name, $player2ID, $player2Score, $player2Name, $round, $tid);
        }

        static function deleteMatchset($tid) {
            $DAO = self::getObject();
            return $DAO->deleteMatchSetForTournament($tid);
        }

        static function getR4PlayerFromArchive($bracketPart, $tid) {
            $DAO = self::getObject();

            $archives = $DAO->getPlayersFromArchive($tid);
            $archiveCount = count($archives);
            for ($i = 0; $i < $archiveCount; $i++) {
                if ($bracketPart == 1 && $archives[$i]["seed"] < 5 && $archives[$i]["r2"] == -1) {
                    return $archives[$i];
                } else if ($bracketPart == 0 && $archives[$i]["seed"] > 4 && $archives[$i]["r2"] == -1) {
                    return $archives[$i];
                }
            }


            return $DAO->getPlayerFromArchive($bracketPart, $tid);
        }

        /*         * *********************************************************************
         * 
         *              Players State Checks
         * 
         * ******************************************************************** */

        static function ready($isReady, $tournamentID, $id) {
            $DAO = self::getObject();

            $response = $DAO->checkReady($isReady, $tournamentID, $id);
            $DAO->updateStatus($tournamentID, $id);

            $message = "";

            switch ($response) {//FIX THIS GARBAGE FOOL!!
                case -1:
                    // if -1 this is an automated process that does not require an intelligent response
                    //Added in case it might need such a response in future.
                    return "";
                case 0:
                    //client is not waiting for any other tournaments. also ship tournamentstatus back
                    //client's competitor IS NOT ready       
                    $players = $DAO->getPlayersForTournament($tournamentID);
                    $message = "ready:[[";
                    for ($i = 0; $i < count($players); $i++) {

                        $message .= "{" . $players[$i]["seed"] . "," . $players[$i]["Username"]
                                . "," . $players[$i]["r1"] . "," . $players[$i]["r2"] . ","
                                . $players[$i]["r3"] . "," . $players[$i]["r4"] . ","
                                . $players[$i]["w"] . "," . $players[$i]["l"] . ","
                                . $players[$i]["d"] . "," . $players[$i]["status"] . ","
                                . $players[$i]["ready"] . "}";
                        if ($i < count($players) - 1) {
                            $message .= ",";
                        }
                    }
                    $message .= "]]";
                    break;
                case 1:
                    //client is not waiting for any other tournaments. also ship tournamentstatus back
                    //client's competitor IS ready
                    $players = $DAO->getPlayersForTournament($tournamentID);
                    $message = "go:[[";
                    for ($i = 0; $i < count($players); $i++) {

                        $message .= "{" . $players[$i]["seed"] . "," . $players[$i]["Username"]
                                . "," . $players[$i]["r1"] . "," . $players[$i]["r2"] . ","
                                . $players[$i]["r3"] . "," . $players[$i]["r4"] . ","
                                . $players[$i]["w"] . "," . $players[$i]["l"] . ","
                                . $players[$i]["d"] . "," . $players[$i]["status"] . ","
                                . $players[$i]["ready"] . "}";
                        if ($i < count($players) - 1) {
                            $message .= ",";
                        }
                    }
                    $message .= "]]";
                    break;
                case 2:
                    //client is ready in another tournament. also ship tournamentstatus back
                    $players = $DAO->getPlayersForTournament($tournamentID);
                    $message = "rusure:[[";
                    for ($i = 0; $i < count($players); $i++) {

                        $message .= "{" . $players[$i]["seed"] . "," . $players[$i]["Username"]
                                . "," . $players[$i]["r1"] . "," . $players[$i]["r2"] . ","
                                . $players[$i]["r3"] . "," . $players[$i]["r4"] . ","
                                . $players[$i]["w"] . "," . $players[$i]["l"] . ","
                                . $players[$i]["d"] . "," . $players[$i]["status"] . ","
                                . $players[$i]["ready"] . "}";
                        if ($i < count($players) - 1) {
                            $message .= ",";
                        }
                    }
                    $message .= "]]";
                    break;
                case 3:
                    $message = "connectionError";
                    break;
                case 4:
                    $message = "notreg";
            }
            return $message;
        }

        static function updateStatusForAllUsers() {

            $DAO = self::getObject();
            return $DAO->updateAllUserStatus();
        }

        static function getReady() {
            
        }

        //
        //Helper method to create a database connection and create a User
        //@return \User
        //
  //*****************************************************************USER DAO <- Work on rename

        private static function getObject() {
            return new \Tournament\DAO();
        }

    }

}