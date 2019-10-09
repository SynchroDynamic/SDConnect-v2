<?php

namespace user {

    class DAO {

        private $fullCols, $columns, $tableName, $conn, $date;

        function __construct() {
            $this->date = date("Y-m-d H:i:s");
            $this->conn = new \ServerDatabase();
            $this->fullCols = array('id', 'Username', 'Password', 'Email', 'RealNameFirst', 'RealNameLast', 'RegDate', 'UserToken', 'UserRegistered', 'status', 'statusPosted');
            $this->columns = array('Username', 'Password', 'Email', 'RealNameFirst', 'RealNameLast', 'RegDate', 'UserToken', 'UserRegistered', 'status', 'statusPosted');
            $this->tableName = "user";
        }

        public function update($values) {
            $this->conn->getConnection();
            $valuesWOId = $this->removeArrayElementAt(0, $values);
            $records = $this->conn->select($this->tableName, array($this->columns[0]), array("id"), array($values[0]), array("="));
            $count = $records->rowCount();
            if ($values[0] == -1) {
                $status = $this->conn->insert($this->tableName, $this->columns, $valuesWOId);
            } else {
                if ($count > 0) {
                    $status = $this->conn->update($this->tableName, $this->columns, $valuesWOId, array("id"), array($values[0]), array("="));
                } else {
                    $status = $this->conn->insert($this->tableName, $this->columns, $valuesWOId);
                }$this->conn->closeConnection();
                return $status;
            }
        }

        public function retrieve($whereCol, $whereOperator, $where) {
            $this->conn->getConnection();
            if ($whereCol == null) {
                $records = $this->conn->select($this->tableName, $this->fullCols, array("1"), array(""), array(""));
            } else {
                $records = $this->conn->select($this->tableName, $this->fullCols, $whereCol, $where, $whereOperator);
            } $this->conn->closeConnection();
            $userArray = array();
            $count = 0;
            while ($row = $records->fetch(\PDO::FETCH_ASSOC)) {
                extract($row);
                $r = array("id" => $id, "Username" => $Username, "Password" => $Password, "Email" => $Email, "RealNameFirst" => $RealNameFirst, "RealNameLast" => $RealNameLast, "RegDate" => $RegDate, "UserToken" => $UserToken, "UserRegistered" => $UserRegistered, "status" => $status, "statusPosted" => $statusPosted);
                $userArray[$count++] = $r;
            }return $userArray;
        }

        public function delete($id) {
            $this->conn->getConnection();
            $this->conn->delete($this->tableName, array("id"), array($id), array("="));
            $this->conn->closeConnection();
        }

        private function removeArrayElementAt($index, array $array) {
            $count = count($array);
            if ($count < $index) {
                return "error: index out of range!";
            }$newArray = array();
            $newCount = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($i != $index) {
                    $newArray[$newCount++] = $array[$i];
                }
            } return $newArray;
        }

        //Special Function
        //
        //
  //@return type
        //
  //******************************************************REGISTER A NEW USER

        function register($u, $p, $e, $f, $l) {
            // sanitize

            $username = htmlspecialchars(strip_tags($u));
            $email = filter_var($e, FILTER_SANITIZE_EMAIL);
            $first = htmlspecialchars(strip_tags($f));                                //
            $last = htmlspecialchars(strip_tags($l));
            $date = date("Y-m-d H:i:s");
            $pass = $this->encryptString(filter_var($p), $date);

            // $rawToken The String that is put together to build  

            include_once dirname(__DIR__, 2) . '/inc/sd-config1.php';

            $rawToken = $username . \SDC::KEYWORD . //   the (User Token)
                    $email . $last
                    . $first;

            $token1 = $this->encryptString($this->encryptString($rawToken, $date), $date);
            $token = str_replace("/", "", $token1);


            // end (User Token) creation 
            $values = array($username, $pass, $email, $first, $last, $date, $token);

            $this->conn->getConnection();
            //'Username', 'Password', 'Email', 'RealNameFirst', 'RealNameLast', 'RegDate', 'UserToken'
            $cols = array_slice($this->columns, 0, 7);
            echo implode(",",$cols);
            $data = $this->conn->insert($this->tableName, $cols, $values);
            $this->conn->closeConnection();
            return $data;
        }

        function encryptString($str, $date) {
            include_once dirname(__DIR__, 2) . '/inc/sd-config1.php';
            $key = \SDC::KEYWORD; //$this->user->getRegDate();
            $iv = $this->getIv($date);

            $ciphertext = openssl_encrypt($str, \SDC::SESS_CIPHER
                    , $key, $options = OPENSSL_RAW_DATA, $iv
            );
            $encryptedSessionId = base64_encode($ciphertext);
            return $encryptedSessionId;
        }

        public function getIv($date) {
            $ivlen = openssl_cipher_iv_length(\SDC::SESS_CIPHER);
            return substr(md5($date), 0, $ivlen);
        }

    }

}