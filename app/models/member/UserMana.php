<?php

namespace models\member;

use models\system\Systema;
use models\system\Database;
use models\datatable\Datatable;

class UserMana {

    protected $conn;
    private $system;

    public function __construct() {
        $this->conn = new Database();
        $this->system = new Systema();
        $this->datatable = new Datatable();
    }

    /*
     * get user profile when login
     */

    public function getProfileuser($user = array(), $json = true) {
        $row = $this->conn->select("member", $user, 1)->row_array();
        if ($json == true) {
            echo json_encode($row);
        } else {
            return $row;
        }
    }

    public function updateAgent() {
        if (isset($_POST['username']) && isset($_REQUEST['code']) && isset($_POST['email'])) {

            $status = isset($_POST['status']) == "on" ? "true" : "false";

            $target_dir = $this->system->targetFile('file/member');

            $logo = uniqid() . $this->system->preMatch($_FILES["img"]["name"]);

            $rowimg = $this->conn->select("member", array("code" => $_REQUEST['code']), 1)->row_array();

            $date = date("Y-m-d h:i:s");
            if (!file_exists($_FILES['img']['tmp_name']) || !is_uploaded_file($_FILES['img']['tmp_name'])) {
                $result = $this->conn->update("member", array("username" => $_POST['username'],
                    "email" => $_POST['email'],
                    "phone" => $_POST['phone'],
                    "update_date" => $date,
                    "permission" => $_POST['permission'],
                    "status" => $status
                        ), array("code" => $_REQUEST['code']));
            } else {
                $this->resizeImage($_FILES['img']['tmp_name'], $logo, "file/member", 400);
                $result = $this->conn->update("member", array("username" => $_POST['username'],
                    "email" => $_POST['email'],
                    "phone" => $_POST['phone'],
                    "update_date" => $date,
                    "permission" => $_POST['permission'],
                    "status" => $status,
                    "logo" => $logo
                        ), array("code" => $_REQUEST['code']));
                if (unlink($target_dir . $rowimg['logo'])) {
                    echo "Deleted!";
                } else {
                    echo "File Not Found!";
                }
            }
            //$result = mysqli_query($this->conn, $sql);
            $tmpDir = $this->system->targetFile("tmp/" . $_POST['username']);
            if ($result) {
                $_SESSION['user'] = $_POST['username'];
                if (!file_exists($tmpDir)) {
                    $oldmask = umask(0);
                    mkdir($tmpDir, 0777);
                    umask($oldmask);
                }
            }
            $this->system->redirect_to(URL . "/users/");
        }
    }

    public function checkExitUpdate($action) {
        $valid = true;
        if ($action == 'username') {
            $user = $this->conn->escape($_POST['username']);
            $exit = $this->conn->escape($_POST['exitfile']);

            $check = $this->conn->query("SELECT * FROM `member` WHERE `username` = '{$user}' AND `username` <> '{$exit}'")->count();

            if ($check >= 1) {
                $valid = false;
            }
        } else if ($action == 'email') {
            $email = $this->conn->escape($_POST['email']);
            $exit = $this->conn->escape($_POST['exitfile']);

            $check = $this->conn->query("SELECT * FROM `member` WHERE `email` = '{$email}' AND `email` <> '{$exit}'")->count();

            if ($check >= 1) {
                $valid = false;
            }
        }

        echo json_encode(array(
            'valid' => $valid,
        ));
    }


    public function checkExit($action) {
        $valid = true;
        if ($action == 'username') {
            $check = $this->conn->select("member",array("username"=>$_POST['username']))->count();
        } else if ($action == 'email') {
            $check = $this->conn->select("member",array("email"=>$_POST['email']))->count();
        }

        if ($check >= 1) {
            $valid = false;
        }
        echo json_encode(array(
            'valid' => $valid,
        ));
    }

    public function updateAgentpassword() {
        if (isset($_REQUEST['code']) && isset($_POST['submit'])) {
            $password = $_POST['password'];
            $newpassword = $this->system->password_encrypt($_POST['password']);
            $this->conn->update("member",array("password"=>$newpassword),array("code"=>$_REQUEST['code']));
            $this->system->redirect_to(URL . "/users/");
        }
    }


    public function datatable() {
        $table = "`member`";
        $columns = array(
            '`member`.`username`',
            '`member`.`email`',
            '`member`.`phone`',
            "CONCAT('<img style=\"height:130px\" src=\"/storage/file/member/',IF(`member`.`logo`='','default_poster.jpg',`member`.`logo`),'\" >')",
            '`member`.`register_date`',
            '`member`.`permission`',
            "CONCAT('<span class=\"label label-',IF(`member`.`status`='true','success','danger'),'\">',IF(`member`.`status`='true','Active','Disabled'),'</span>')",
            "CONCAT('<a class=\"label label-info \" href=\"./update/?code=',REPLACE(`member`.`code`,' ','_'),'\">Update</a>')",
            "CONCAT('<a class=\"label label-info \" href=\"./changepassword/?code=',REPLACE(`member`.`code`,' ','_'),'\">Change Password</a>')"
        );
        $this->datatable->table($table, $columns);
    }

    public function singupAgent() {
        if (isset($_POST['register'])) {

            $password = $this->system->password_encrypt($_POST['password']);
            $status = isset($_POST['status']) == "on" ? "true" : "false";

            $logo = uniqid() . $this->system->preMatch($_FILES["img"]["name"]);

            $date = date("Y-m-d h:i:s");
            if (file_exists($_FILES['img']['tmp_name']) || is_uploaded_file($_FILES['img']['tmp_name'])) {
                $this->resizeImage($_FILES['img']['tmp_name'], $logo, "file/member", 400);
                $result = $this->conn->insert("member",array(
                    "username"=>$_POST['username'],
                    "password"=>$password,
                    "email"=>$_POST['email'],
                    "phone"=>$_POST['phone'],
                    "logo"=>$logo,
                    "register_date"=>$date,
                    "permission"=>$_POST['permission'],
                    "status"=>$status
                    ));

                $tmpDir = $this->system->targetFile("tmp/" . $username);
                if ($result) {
                    if (!file_exists($tmpDir)) {
                        $oldmask = umask(0);
                        mkdir($tmpDir, 0777);
                        umask($oldmask);
                    }
                }
            }
            $this->system->redirect_to(URL . "/users/");
        }
    }

    /*
     * image profile user
     *
     */


    public function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    public function resizeImage($image, $name, $folder, $mywidth = null, $myheight = null) {
        $dir = $this->system->targetFile($folder);

        $extension = $this->getExtension($name);
        $extension = strtolower($extension); //echo $extension;
        if ($extension == "jpg" || $extension == "jpeg") {
            $src = imagecreatefromjpeg($image);
        } else if ($extension == "png") {
            $src = imagecreatefrompng($image);
        } else {
            $src = imagecreatefromgif($image);
        }

        list($width, $height) = getimagesize($image);
        $newwidth = $mywidth !== null ? $mywidth : 400;
        // $newheight = 600;
        $newheight = ($height / $width) * $newwidth;
        $tmp = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        $filename = $dir . $name;

        imagejpeg($tmp, $filename, 100);
        imagedestroy($src);
        imagedestroy($tmp);
    }

    public function delete() {
        if (isset($_POST['code'])) {

        }
    }
}

?>
