<?php

namespace models\member;

use models\system\Systema;
use models\system\Database;

header('Cache-control: private'); // IE 6 FIX
// always modified
header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
// HTTP/1.1
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
// HTTP/1.0
header('Pragma: no-cache');

class Autologin {

    private $conn;
    private $system;
//    $config_username = 'user';
//$config_password = 'demo123';

    public $cookie_name = 'reimireal';
    public $cookie_time = (3600 * 24 * 30); // 30 days

    // (86400 * 30) 1 day

    public function __construct() {
        $this->system = new Systema();
        $this->conn = new Database();
    }

    public function autoLog() {
//        if (isSet($cookie_name)) {
        // Check if the cookie exists
        if (isSet($_COOKIE[COOKIE])) {

            $cookie = explode('airi', $_COOKIE[COOKIE]);

            $username = $cookie[0];
            $password = $cookie[1];
            // Make a verification

//            $sql1 = "SELECT * FROM member WHERE username = '{$username}'";
//            $result1 = mysqli_query($this->conn, $sql1);
//            $row = mysqli_fetch_assoc($result1);

//            $dbPassword = $row['password'];

//            $hashPassword = crypt($password, $dbPassword);

            $result = $this->conn->select("member",array("username"=>$username,"password"=>$password));
            // Mysql_num_row is counting table row
            $count = $result->count();

            if ($count == 1) {
                $row = $result->row_array();
                //session_start();
                $_SESSION['user'] = $username;
                $_SESSION['permission'] = $row['permission'];
//                $this->autoLogin($name, $hashPassword);
                setcookie('reimireal', $username . 'airi' . $password, time() + (86400 * 30), "/");
//                $this->system->redirect_to(URLS . "/listmedia/");
            } else {
                //echo "<script>alert('Username and Password is worng!');window.location.href='../Login.php';</script>";
//                $this->system->redirect_to(URL . "/login/");
            }
        }
//        }
//        return $config_username;
    }

}

?>
