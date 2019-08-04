<?php

include_once dirname(__DIR__, 2) . "/inc/sd-config1.php";

class DataConnection {

    //Connection variables
    // specify your database constants
    private static $h = SDC::IP;
    private static $d = SDC::DATABASE_NAME;
    private static $u = SDC::DB_USERNAME;
    private static $p = SDC::DB_PASS;

    public static function h() {
        return self::$h;
    }

    public static function d() {
        return self::$d;
    }

    public static function u() {
        return self::$u;
    }

    public static function p() {
        return self::$p;
    }

}
