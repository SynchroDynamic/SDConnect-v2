<?php

include_once "var/www/html/inc/sd-config.php";

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
    //*****************************************MULTIPLE DATABASES ON ONE SERVER

    static function init($path, array $postArray) {
        // Check if path is available or not empty        
        if (isset($path)) {//To be a REST Call, you need to have a path sequence
            // Do a path split
            $pSplit = explode('/', ltrim($path));
        } else {
            die('404: This is not a browser accessible site'); //you are trying to axis the INDEX File
        }
        if ($pSplit[0] == "sd-admin") {
            header("Location: $path");
        } else {
            try {
                //A unique way to traffic data to different server areas. 
                include_once SDC::ROOT_PATH . SDC::SERVER_FOLDER . $pSplit[1] . SDC::CONTROLLER_PATH;
                $Controller = new GateController();
                //Send the data through the routed directory, and output
                echo $Controller->route($pSplit, $postArray);
            } catch (Exception $e) {//Shoot we found an error
                echo "error: " . $e->getMessage();
            }
        }
    }

}
