<?php

require_once dirname(__DIR__) . "/inc/sd-config1.php";

//
//This class allows for one PHP application to serve 
//files from multiple databases 
//
//This class is the map, and it maps to certain gates of data
//
//

class GatewayController {

    //
    //Switches between multiple databases (ROUTER)
    //@param type $path
    //@return type
    //*****************************************MULTIPLE TABLES ON ONE SERVER Can 

    static function init($path, array $postArray) {
        echo 'HELLO';
        // Check if path is available or not empty        
        if (isset($path)) {//To be a REST Call, you need to have a path sequence
            // Do a path split
            $pSplit = explode('/', ltrim($path));
            //echo implode("::", $pSplit);
        } else {
            die('404: This is not a browser accessible site'); //you are trying to axis the INDEX File
        }

        echo "STUFF " ;//. $pSplit[1];
        
        if ($pSplit[0] == "sd-admin" || $pSplit[1] == "install.php" || $pSplit[1] == "login") {
            header("Location: $path");
        } else {



            try {
                //A unique way to traffic data to different server areas. 
                include_once SDC::ROOT_PATH . "/" . SDC::SUBFOLDER . SDC::SERVER_FOLDER . $pSplit[1] . SDC::CONTROLLER_PATH;
                //echo "<scrip>alert('" . SDC::ROOT_PATH . SDC::SUBFOLDER . SDC::SERVER_FOLDER . $pSplit[1] . SDC::CONTROLLER_PATH . "');</script>";
                $Controller = new GateController();
                //Send the data through the routed directory, and output
                $var = $Controller->route($pSplit, $postArray);
                //$var->debugDumpParams();
                if ($var == null) {
                    echo "Transaction Error";
                } else if (is_string($var)) {
                    echo $var;
                } else if ($var->errorInfo() != null && $var->errorInfo()[0] != "00000" && $var->errorInfo()[0] != "23000") {
                    echo implode(">>", $var->errorInfo());
                }
            } catch (Exception $e) {//Shoot we found an error
                echo "error: " . $e->getMessage();
            }
        }
    }

}

?>