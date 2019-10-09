<?php

namespace user {
    include_once "DAO.php";

    class DataService {

        static function update($values) {
            $DAO = self::getObject();
            return $DAO->update($values);
        }

        static function getRecords($whereCol, $whereOperator, $where) {
            $DAO = self::getObject();
            return $DAO->retrieve($whereCol, $whereOperator, $where);
        }

        static function delete($id) {
            $DAO = self::getObject();
            return $DAO->delete($id);
        }

        private static function getObject() {
            return new\user\DAO();
        }

        //Special Function
        //@param string $u username
        //@param string $p rawPassword
        //@param string $e email
        //@param string $f first name
        //@param string $l last name
        //
  //********************************************************REGISTER NEW USER

        static function register($u, $p, $e, $f, $l) {
            $userDao = self::getObject();

            // get posted data
            if (!empty($u) && !empty($p) && !empty($e) && !empty($f) && !empty($l)) {

                $regStatus = $userDao->register($u, $p, $e, $f, $l);
                return $regStatus;
            }
        }

    }

}