<?php

include_once '/var/www/html' . '/SERVERS/SHARED/ServerDatabase.php';
include_once '/var/www/html' . '/SERVERS/GameSocket/FindPort.php';
include_once 'DataService.php';

//
//routes URLs to appropriate data

class GateController {

    //
    //Routes various requests to appropriate database tables, etc.
    //@param type $pSplit
    //@param type $pathSplit
    //@return type
    //
  //***********************************COMPLETE A DATABASE TRANSACTION ROUTER 

    function route($pSplit, $pathSplit) {
        switch ($pSplit[2]) {//All strings sent to DataService are "RAW"            
            case "Info":
                $id = $pathSplit[0];
                $start = $pathSplit[1];
                $perpage = $pathSplit[2];
                return json_encode(\Tournament\DataService::tournamentInfo($id, $start, $perpage));
            case "Mine":
                $id = $pathSplit[0];
                $start = $pathSplit[1];
                $perpage = $pathSplit[2];
                return json_encode(\Tournament\DataService::myTournaments($id, $start, $perpage));
            case "TournamentPlayers":
                $id = $pathSplit[0];
                $t = $pathSplit[1];     
//                $data = DataService::TournamentPlayers($id, $t);
//                if($data)
                return json_encode(\Tournament\DataService::TournamentPlayers($id, $t));
            case "enter":
                $id = $pathSplit[0];
                $t = $pathSplit[1];  
                return \Tournament\DataService::enterTournament($id, $t);
            case "matchset":
                $tid = $pathSplit[0];
                $id = $pathSplit[1];                
                return json_encode(\Tournament\DataService::getTournamentMatchsetFor($tid, $id));
            case "stats":
                $pid = $pathSplit[0];
                 return json_encode(\Tournament\DataService::playerStatsRecord($pid));
            default: return "";
        }
    }

}

?>
