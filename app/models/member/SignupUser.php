<?php

namespace models\member;

use models\system\Systema;
use models\db\Db;
use models\datatable\Datatable;

class SignupUser {

    protected $conn;
    private $system;

    public function __construct() {
        $db = new Db();
        $this->conn = $db->DbConnect();
        $this->system = new Systema();
        $this->datatable = new Datatable();
    }

    public function getProfile() {
        if (isset($_REQUEST['code'])) {
            $code = $this->system->myescape($_REQUEST['code']);
            $sql = "SELECT * FROM `member` WHERE `code` = '{$code}' LIMIT 1";
            $result = mysqli_query($this->conn, $sql);
            $row = mysqli_fetch_assoc($result);
            return $row;
        }
    }

    public function getProfileuser($json = true) {
        $sql = "SELECT * FROM `member` WHERE `username` = '{$_SESSION['user']}' LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if ($json = true) {
            echo json_encode($row);
        } else {
            return $row;
        }
    }

    public function getProfileBySession() {
        $sql = "SELECT * FROM `member` JOIN `busstations` ON `member`.`station` = `busstations`.`code` WHERE `username` = '{$_SESSION['user']}' LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    public function checkUsername($table, $where, $name) {
        $sql = "SELECT * FROM `{$table}` WHERE `{$where}` = '{$name}'";
        $result = mysqli_query($this->conn, $sql);
        $count = mysqli_num_rows($result);
        if ($count == 1) {
            $error = 1;
            $_SESSION['usernameerror'] = $name . " Already Exit";
        } else {
            $error = 0;
        }
        return $error;
    }

    public function checkEmail($table, $where, $name) {
        $sql = "SELECT * FROM `{$table}` WHERE `{$where}` = '{$name}'";
        $result = mysqli_query($this->conn, $sql);
        $count = mysqli_num_rows($result);
        if ($count == 1) {
            $error = 1;
            $_SESSION['emailerror'] = $name . " Already Exit";
        } else {
            $error = 0;
        }
        return $error;
    }

    public function singmeup() {
        if (isset($_POST['signup'])) {
            $firstname = $this->system->myescape($_POST['firstname']);
            $lastname = $this->system->myescape($_POST['lastname']);
            $email = $this->system->myescape($_POST['email']);
            $mobile = $this->system->myescape($_POST['mobile']);
            $username = $this->system->myescape($_POST['username']);
            $password = trim($_POST['password']);
            $usercode = $username . uniqid() . "121";
//            $agree =
            $userError = $this->checkUsername('member', 'username', $username);
            $emailError = $this->checkEmail('member', 'email', $email);

            if ($userError == 0 && $emailError == 0) {
                $sql = "INSERT INTO `member` (`firstname`,`lastname`,`username`,`password`,`email`,`mobile`,`register_date`,`permission`,`agree`,`usercode`)";
                $sql .= " VALUES";
                $sql .= " ('{$firstname}','{$lastname}','{$username}','{$password}','{$email}','{$mobile}',NOW(),'normal',1,'{$usercode}')";
                $result = mysqli_query($this->conn, $sql);
                $this->system->redirect_to(URL . "login/");
            }
            $this->system->redirect_to(LINK);
        }
    }

    public function checkExit($action) {
        $valid = true;
        if ($action == 'username') {
            $user = $this->system->myescape($_POST['username']);
            $getusers = $this->conn->query("SELECT * FROM `member` WHERE `username` = '{$user}'");
        } else if ($action == 'email') {
            $email = $this->system->myescape($_POST['email']);
            $getusers = $this->conn->query("SELECT * FROM `member` WHERE `email` = '{$email}'");
        }
        $check = mysqli_num_rows($getusers);
        if ($check >= 1) {
            $valid = false;
        }
        echo json_encode(array(
            'valid' => $valid,
        ));
    }

    public function checkExitUpdate($action) {
        $valid = true;
        if ($action == 'username') {
            $user = $this->system->myescape($_POST['username']);
            $exit = $this->system->myescape($_POST['exitfile']);
//            if ($user !== $exit) {
            $getusers = $this->conn->query("SELECT * FROM `member` WHERE `username` = '{$user}' AND `username` <> '{$exit}'");
//            }
            $check = mysqli_num_rows($getusers);
            if ($check >= 1) {
                $valid = false;
            }
        } else if ($action == 'email') {
            $email = $this->system->myescape($_POST['email']);
            $exit = $this->system->myescape($_POST['exitfile']);
//            if ($email !== $exit) {
            $getusers = $this->conn->query("SELECT * FROM `member` WHERE `email` = '{$email}' AND `email` <> '{$exit}'");
//            }
            $check = mysqli_num_rows($getusers);
            if ($check >= 1) {
                $valid = false;
            }
        }

        echo json_encode(array(
            'valid' => $valid,
        ));
    }

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

    public function singupAgent() {
        if (isset($_POST['register'])) {
            var_dump($_POST);
//            $firstname = $this->system->myescape($_POST['firstname']);
//            $lastname = $this->system->myescape($_POST['lastname']);
            $permission = $this->system->myescape($_POST['permission']);
            $phone = $this->system->myescape($_POST['phone']);
            $username = $this->system->myescape($_POST['username']);
            $password = $this->system->password_encrypt($_POST['password']);
            $email = $this->system->myescape($_POST['email']);

            $status = isset($_POST['status']) == "on" ? "true" : "false";

            $target_dir = $this->system->targetFile('member');

            $logo = uniqid() . $this->system->preMatch($_FILES["img"]["name"]);

            $usercode = $username . uniqid() . "101";

            if (file_exists($_FILES['img']['tmp_name']) || is_uploaded_file($_FILES['img']['tmp_name'])) {
                $this->resizeImage($_FILES['img']['tmp_name'], $logo, "member", 400);
                $sql = "INSERT INTO `member` (`username`,`password`,`email`,`phone`,`logo`,`register_date`,`permission`,`usercode`,`status`)";
                $sql .= " VALUES ('{$username}','{$password}','{$email}','{$phone}','{$logo}',NOW(),'{$permission}','{$usercode}','{$status}')";
                $result = mysqli_query($this->conn, $sql);
                //echo $sql;
            }
            $this->system->redirect_to(URL . "/user/");
        }
    }

    public function comparePassword($code, $password, $dbPassword) {
        $check = false;
        $sql1 = "SELECT * FROM `member` WHERE `id` = '{$code}'";
        $result1 = mysqli_query($this->conn, $sql1);
        $row = mysqli_fetch_assoc($result1);

        if ($password == $row['password']) {
            $check = true;
        } else {
            $hashPassword = crypt($password, $dbPassword);
            $sql = "SELECT * FROM `member` WHERE `id` = '{$code}' AND `password` = '{$hashPassword}' LIMIT 1";
            $result = mysqli_query($this->conn, $sql);
            $count = mysqli_num_rows($result);
            $check = $count == 1 ? true : false;
        }
        return $check;
    }

    public function comparePasswordBySession($password, $dbPassword) {
        $check = false;
        $sql1 = "SELECT * FROM `member` WHERE `username` = '{$_SESSION['user']}'";
        $result1 = mysqli_query($this->conn, $sql1);
        $row = mysqli_fetch_assoc($result1);

        if ($password == $row['password']) {
            $check = true;
        } else {
            $hashPassword = crypt($password, $dbPassword);
            $sql = "SELECT * FROM `member` WHERE `username` = '{$_SESSION['user']}' AND `password` = '{$hashPassword}' LIMIT 1";
            $result = mysqli_query($this->conn, $sql);
            $count = mysqli_num_rows($result);
            $check = $count == 1 ? true : false;
        }
        return $check;
    }

    public function updateAgent() {
        if (isset($_POST['username']) && isset($_REQUEST['code']) && isset($_POST['email'])) {

            $code = $this->system->myescape($_REQUEST['code']);
            // $firstname = $this->system->myescape($_POST['firstname']);
            // $lastname = $this->system->myescape($_POST['lastname']);
            $permission = $this->system->myescape($_POST['permission']);
            $phone = $this->system->myescape($_POST['phone']);
            $username = $this->system->myescape($_POST['username']);

            $email = $this->system->myescape($_POST['email']);

            //$code = $this->system->myescape($_POST['code']);

            $status = isset($_POST['status']) == "on" ? "true" : "false";

            $target_dir = $this->system->targetFile('member');

            $logo = uniqid() . $this->system->preMatch($_FILES["img"]["name"]);

            $checkimg = $this->conn->query("SELECT * FROM `member` WHERE `code` = '{$code}' LIMIT 1");
            $rowimg = mysqli_fetch_assoc($checkimg);

            // if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
            if (!file_exists($_FILES['img']['tmp_name']) || !is_uploaded_file($_FILES['img']['tmp_name'])) {

                $sql = "UPDATE `member` SET";
                $sql .= " `username` = '{$username}',`email` = '{$email}',";
                $sql .= " `phone` = '{$phone}',";
                $sql .= " `update_date` = NOW(),`permission` = '{$permission}', `status` = '{$status}'";
                $sql .= " WHERE `code` = '{$code}'";
            } else {
                $this->resizeImage($_FILES['img']['tmp_name'], $logo, "member", 400);
                $sql = "UPDATE `member` SET";
                $sql .= " `username` = '{$username}',`email` = '{$email}',";
                $sql .= " `phone` = '{$phone}',`logo` = '{$logo}',";
                $sql .= " `update_date` = NOW(),`permission` = '{$permission}', `status` = '{$status}'";
                $sql .= " WHERE `code` = '{$code}'";

                //echo $sql;
                if(unlink($target_dir . $rowimg['logo'])){
                    echo "Deleted!";
                } else {
                    echo "File Not Found!";
                }
            }
            $result = mysqli_query($this->conn, $sql);
            $this->system->redirect_to(URL . "/users/");
        }
    }

    public function updateAgentpassword() {
        if (isset($_REQUEST['code'])&&isset($_POST['submit'])) {
                $code = $this->system->myescape($_REQUEST['code']);
                $password = $_POST['password'];
                $newpassword = $this->system->password_encrypt($_POST['password']);

                $sql = "UPDATE `member` SET";
                $sql .= " `password` = '{$newpassword}'";
                $sql .= " WHERE `code` = '{$code}'";

                $result = mysqli_query($this->conn, $sql);
                //echo $sql;
                $this->system->redirect_to(URL . "/users/");
            
        }
    }

    public function updateAgentBySession() {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['phone']) && isset($_POST['email'])) {
            $station = $this->system->myescape($_POST['busstop']);
            $phone = $this->system->myescape($_POST['phone']);
            $username = $this->system->myescape($_POST['username']);

            $email = $this->system->myescape($_POST['email']);

            //$code = $this->system->myescape($_POST['code']);

            $status = isset($_POST['status']) == "on" ? "true" : "false";

            $password = $_POST['password'];
            $newpassword = $this->system->password_encrypt($_POST['password']);

            $target_dir = $this->system->targetFile('member');

            $logo = basename($_FILES["img"]["name"]);
            $logo = $this->system->preMatch($logo);
            $logo = $username . date('YmdHis') . $logo;
            $target_file = $target_dir . $logo;

            $checkimg = $this->conn->query("SELECT * FROM `member` WHERE `username` = '{$_SESSION['user']}' LIMIT 1");

            $rowimg = mysqli_fetch_assoc($checkimg);

            $check = $this->comparePasswordBySession($password, $rowimg['password']);


            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $sql = "UPDATE `member` SET";
                $sql .= " `station` = '{$station}',`username` = '{$username}',`email` = '{$email}',";
                $sql .= " `phone` = '{$phone}',`logo` = '{$logo}',";
                $sql .= " `update_date` = NOW(), `status` = '{$status}'";
                $sql .= $check == true ? "" : ",`password` = '{$newpassword}'";
                $sql .= " WHERE `username` = '{$_SESSION['user']}'";

                //echo $sql;
                unlink($target_dir . $rowimg['logo']);
            } else {
                $sql = "UPDATE `member` SET";
                $sql .= " `station` = '{$station}',`username` = '{$username}',`email` = '{$email}',";
                $sql .= " `phone` = '{$phone}',";
                $sql .= " `update_date` = NOW(), `status` = '{$status}'";
                $sql .= $check == true ? "" : ",`password` = '{$newpassword}'";
                $sql .= " WHERE `username` = '{$_SESSION['user']}'";
//                echo $sql;
            }
            $_SESSION['user'] = $username;
            $result = mysqli_query($this->conn, $sql);
            //echo $sql;
            $this->system->redirect_to(URL);
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

    public function getdata() {
        if (isset($_POST['code'])) {
            $arr = array();
            $code = $this->system->myescape($_POST['code']);
            $sql = "SELECT `member`.`id` ,`member`.`username`,`member`.`password`, `member`.`email`, `member`.`phone`, `member`.`logo`,"
                    . "`member`.`register_date`, `member`.`permission`, `busstations`.`code`, `busstations`.`name`,"
                    . "IF(`member`.`status`=1,'true','false') `state`"
                    . " FROM `member` JOIN `busstations` ON `member`.`station` = `busstations`.`code` WHERE `member`.`id` = '{$code}'";
            $result = mysqli_query($this->conn, $sql);
            while ($rows = mysqli_fetch_assoc($result)) {
                array_push($arr, array(
                    "memberId" => $rows['id'],
                    "username" => $rows['username'],
                    "email" => $rows['email'],
                    "phone" => $rows['phone'],
                    "image" => $rows['logo'],
                    "registerDate" => $rows['register_date'],
                    "permission" => $rows['permission'],
                    "stationcode" => $rows['code'],
                    "stationName" => $rows['name'],
                    "status" => $rows['state'],
                    "password" => $rows['password']
                ));
            }
            echo json_encode($arr);
        }
    }

    public function delete() {
        if (isset($_POST['code'])) {
            $code = $this->system->myescape($_POST['code']);
            $prepare = $this->conn->query("SELECT * FROM `member` WHERE `id` = '{$code}'");
            $row = mysqli_fetch_assoc($prepare);
            if ($row > 0) {
                $this->system->deleteFile($row['logo'], 'member');
                $delete = $this->conn->query("DELETE FROM `member` WHERE `id` = '{$code}'");
            }
        }
    }

    public function load($type) {
        switch ($type) {
            case 'datatable':
                $this->datatable();
                break;
            default:
                echo $type;
        }
    }

}

?>
