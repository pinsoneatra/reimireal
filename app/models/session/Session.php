<?php

namespace models\session;

use models\system\Systema;

class Session {

    public function __construct() {
        // server should keep session data for AT LEAST 1 hour
//        ini_set('session.gc_maxlifetime', 3600);
        // each client should remember their session id for EXACTLY 1 hour
//        session_set_cookie_params(3600);
        // start session
        session_start();
        $this->system = new Systema();
    }

    public function unsetsession() {
        if (isset($_POST['unsetsession'])) {
            unset($_SESSION['user']);
            unset($_COOKIE['user']);
        }
    }

    public function redirectUser($redirect) {
        if (isset($_SESSION['user'])) {
            $this->system->redirect_to($redirect);
        }
    }

    public function restrictUser($redirect) {
        if (isset($_SESSION['user'])) {
            // echo $_SESSION['code']
        } else {
            $this->system->redirect_to($redirect);
        }
    }

    public function restrictAdmin($session) {
        $member = array('admin' => 'adminstator', 'manager' => 'management');
        isset($member[$session]) ? '' : $this->system->redirect_to(URL."/ticket/");
    }

}

?>
