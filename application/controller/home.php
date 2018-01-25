<?php

class Home extends Controller{
  function __construct(){
    $this->checkAccess('user');
  }
  public function index(){
    $items = ItemModel::model()->findAll();
    
    $this->renderView('home/index.php',array(
      'items' => $items,
    ));
  }
}
