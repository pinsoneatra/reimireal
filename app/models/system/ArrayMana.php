<?php
namespace models\system;

class ArrayMana {

  public function __construct(){

  }

  protected function change_key($array, $old_key, $new_key) {
     if (!array_key_exists($old_key, $array))
         return $array;

     $keys = array_keys($array);
     $keys[array_search($old_key, $keys)] = $new_key;

     return array_combine($keys, $array);
 }

}

 ?>
