<?php

/* 
 * check is null or is empty and return null or value not null
 * 
 * 
 */

namespace models\validation;

class CheckNull {
    
    public function __construct() {
        
    }
    
    public function isNull($value){
        if(is_null($value)||$value==""){
            return null;
        } else {
            return $value;
        }
    }
}