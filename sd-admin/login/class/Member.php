<?php

namespace Phppot;

include_once dirname(__DIR__,3) . '/inc/sd-config1.php';
use \Phppot\DataSource;

class Member {

    private $dbConn;
    private $ds;

    function __construct() {
        require_once "DataSource.php";
        $this->ds = new DataSource();
    }

    function getMemberById($memberId) {
        $query = "select * FROM admin WHERE id = ?";
        $paramType = "i";
        $paramArray = array($memberId);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);

        return $memberResult;
    }

    public function processLogin($username, $password) {
        $key = \SDC::KEYWORD;
        $passwordHash = md5($key . $password . $key);
        $query = "select * FROM admin WHERE username = ? AND password = ?";
        $paramType = "ss";
        $paramArray = array($username, $passwordHash);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);
        if (!empty($memberResult)) {
            $_SESSION["userId"] = $memberResult[0]["id"];
            return true;
        }
    }

    public function addUser($username, $password) {
         $key = \SDC::KEYWORD;
        $passwordHash = md5($key . $password . $key);

        $query = "INSERT INTO admin (username, password) VALUES" .
                "('$username', '$passwordHash');";

        $this->ds->execute($query);
        
        return $this->processLogin($username, $password);
        
        
    }

}
