<?php

class Home extends Controller{
  function __construct(){
  }
  public function index(){
    $this->renderView('home/index.php',array(
    ));
  }
}
