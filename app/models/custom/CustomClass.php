<?php
namespace models\custom;

use models\system\Database;

class CustomClass {

  private $conn;

  public function __construct(){
      $this->conn = new Database();
  }

  public function custom($name){
    echo "<h1 style='text-align:center'>".$name."</h1>";
  }
}
?>
