<?php
/**
 *  About controller
 */
class About extends Controller
{
  function __construct(){
  }
  public function index(){
    $this->renderView('about/index.php',array(
    ));
  }
}
