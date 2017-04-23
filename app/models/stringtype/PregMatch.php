<?php

namespace models\stringtype;

use models\system\Systema;

class PregMatch {

    public function __construct() {
        $this->system = new Systema();
    }

    public function pregMatchAll($matches,$item,$url=false){
        $data = array();
        switch($item){
            case "image":
                if($url==true){
                    preg_match_all('/https?:\/\/[^ ]+?(?:\.jpg|\.png|\.gif)/i',$matches,$results, PREG_OFFSET_CAPTURE);
                }
                break;
            case "url":
                preg_match_all('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$matches,$results, PREG_OFFSET_CAPTURE);
                break;
            default :
                echo $matches;
                break;
        }
        foreach($results as $result){
            if(is_array($result)){
                foreach($result as $r){
                    array_push($data, array($r[0]));
                }
            }
        }
        return $data;
    }

}

?>
