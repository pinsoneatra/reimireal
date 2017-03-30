<?php

namespace models\system;

class StringMana {

    public function __construct() {
        
    }

    public function customEcho($x, $length) {
        if (strlen($x) <= $length) {
            echo $x;
        } else {
            $y = substr($x, 0, $length) . '...';
            echo $y;
        }
    }

    public function preMatch($doc) {
        $doc = preg_replace("/[^a-zA-Z0-9.]/", "", $doc);
        return $doc;
    }
    
}

?>
