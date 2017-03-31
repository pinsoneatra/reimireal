<?php

namespace models\system;

class Systema {

    private $conn;

    public function __construct() {

    }

    public function redirect_to($new_location) {
        header("Location: " . $new_location);
        exit;
    }

    public function setError($error) {
        switch ($error) {
            case "403":
                header('HTTP/1.0 403 Forbidden');
                die('You are not allowed to access this file.');
                break;
        }
    }

    public function exceptionMsgError() {
        try {
            throw new \Exception("Some error message");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

?>
