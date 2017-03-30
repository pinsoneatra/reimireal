<?php

/*
  The default home controller, called when no controller/method has been passed to application.
 */

class Home extends Controller {

    public function __construct() {
        
    }

    public function errorpage($pages = '') {
        $this->page = "404";
        $this->view('404/error');
    }

    public function index() {
        $this->page = "home";
        $this->view("home/home");
    }
}

?>
