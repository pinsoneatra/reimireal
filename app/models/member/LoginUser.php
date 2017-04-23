<?php

namespace models\member;

use models\system\Systema;
use models\system\Database;

class LoginUser {

    protected $conn;
    private $system;

    public $cookie_time; // 30 days

    public function __construct() {
        $this->system = new Systema();
        $this->conn = new Database();
        $this->cookie_time  = (3600 * 24 * 30);
    }

    public function normallogin($post,$redirect) {
        if (isset($_POST[$post])) {

            $result = $this->conn->select("member",array("username"=>$_POST['username'],"password"=>$_POST['password']));

            $count = $result->count();
            if ($count == 1) {
                $_SESSION['user'] = $_POST['username'];
                $this->system->redirect_to($redirect);
            } else {
//                $_SESSION['loginerror'] = "Username And Password Wrong!";
                $this->system->redirect_to($redirect);
            }
        }
    }

    public function checkUser() {
        if (isset($_POST['log'])) {
            $name = $_POST['loginname'];
            $pass = $_POST['loginpass'];
            $sql = "SELECT * FROM `usr_customers` JOIN `usr_mtusers` USING (`username`) WHERE `usr_customers`.`loginname`='" . $name . "' AND `usr_mtusers`.`password`='" . $pass . "'";

            $result = mysqli_query($this->conn, $sql) or die(mysqli_error());

            $count = mysqli_num_rows($result);
            if ($count == 1) {
                $this->names = $name;
                //session_start();
                $_SESSION['loginname'] = $this->names;
                //$system->redirect_to('' . URL . '/login/');
            }
            return $_SESSION['loginname'];
        }
    }

    public function login22() {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            //session_start();
            $success = false;
            $name = $_POST['username'];
            $username = str_replace(' ', '_', $name);
            $password = $_POST['password'];

            $sql1 = "SELECT * FROM member WHERE username = '{$username}'";
            $result1 = mysqli_query($this->conn, $sql1);
            $row = mysqli_fetch_assoc($result1);

            $dbPassword = $row['password'];
            $hashPassword = crypt($password, $dbPassword);
            /* if($hashPassword == $dbPassword){
              // Password is match
              $_SESSION['member_id'] = $row['member_id'];
              redirect_to($pageURL);
              } */
            $sql = "SELECT * FROM member WHERE username='{$username}' and password='{$hashPassword}' LIMIT 1";
            $result = mysqli_query($this->conn, $sql);

            // Mysql_num_row is counting table row
            $count = mysqli_num_rows($result);

            // If result matched $myusername and $mypassword, table row must be 1 row
            if ($count == 1) {
                $row = mysqli_fetch_assoc($result);
                // Register $myusername, $mypassword and redirect to file "login_success.php"
                $_SESSION['user'] = $name;
                $_SESSION['permission'] = $row['permission'];
                //$this->autoLogin($name, $hashPassword);
                setcookie('reimireal', $name . 'airi' . $hashPassword, time() + (86400 * 30), "/");
                //$this->system->redirect_to(URL . "/ticket/");
                $success = true;
            } else {
                //$this->system->redirect_to(URL);
                $success = false;
            }
            echo json_encode($success);
        }
    }

    public function logout() {
        if (isSet($_SESSION['user'])) {
            unset($_SESSION['user']);
            if (isSet($_COOKIE[COOKIE])) {
                setcookie(COOKIE, '', time() - (86400 * 30), "/");
            }
            $this->system->redirect_to(URL."/login/");
        }
    }

}

?>
