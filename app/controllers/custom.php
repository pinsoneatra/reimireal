<?php

/*
  The default home controller, called when no controller/method has been passed to application.
 */

use models\custom\CustomClass;

class Custom extends Controller {

    public $custom;

    public function __construct() {
        $this->custom = new CustomClass();
    }

    public function errorpage($pages = '') {
        $this->page = "404";
        $this->view('404/error');
    }

    public function index() {
        $this->page = "custom";
        $this->custom->custom("ReimiReal");
    }
}

?>
