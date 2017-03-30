<?php

class App {

    protected $controller;
    protected $method = 'index';
    protected $params = array();
    protected $home = 'home';
    protected $error = 'errorpage';

    public function __construct() {
        // Database Connection

        $url = $this->parseUrl();
        //echo $this->controller;
        if (file_exists('app/controllers/' . $url[0] . '.php')) {
            // url[0] = home(controller), url[1] = index(method), url[2] = (params)
            $this->controller = $url[0];
            unset($url[0]);
        } elseif ($url[0] == '') {
            $this->controller = $this->home;
            unset($url[0]);
        } else {
            $this->controller = $this->error;
            unset($url[0]);
        }

        require_once 'app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                // echo 'methods';
                $this->method = $url[1];
                unset($url[1]);
            } else {
                // modified method
                if ($url[1] !== 'index') {
                    $this->method = $this->error;
                    unset($url[1]);
                }
            }
        }

        $this->params = $url ? array_values($url) : array();
        call_user_func_array(array($this->controller, $this->method), $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            //echo $_GET['url'];
            $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
            return $url;
        }
    }

}

?>
