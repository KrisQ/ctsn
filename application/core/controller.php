<?php
class Controller{
  /**
   * @var null Database Connection
   */
  public $db = null;
  /**
   * @var null Model
   */
  public $model = null;
  /**
   * Whenever controller is created, open a database connection too and load "the model".
   */
  function __construct()
  {
      $this->openDatabaseConnection();
  }
  //REDIRECT
  public function redirect($location){
		$this->renderView('_templates/redirector.php', array(
			'location' => $location
		));
	}

  public function checkAccess($type = 'user'){

  }

  public function hasAccess($type = 'user'){
    $user = $this->fetchFromSESSION('user');
    switch ($type) {
      case 'user':
        if($user->usrlvl == 'G'){
          return false;
        }
        break;
      case 'admin':
        if($user->usrlvl != 'A'){
          return false;
        }
      default:
    }
    return true;
  }

  /**
   * Open the database connection with the credentials from application/config/config.php
   */
  private function openDatabaseConnection(){

  }
  /**
    * Renders a view from the view folder.
    * $view:  name to render. you can specify that it's in
    *         the _template subdirectory like so:
    *         $this->renderView('_templates/header.php');
    * $data:  array of data to be used in the view. EX:
    *         array('foo'=>'foobar', 'boo'=$boo) would allow
    *         usage of the variables $boo and $foo in the view
    */
  public function renderView($view, $data = null)
  {
      //extracts all data in array to be used as variables
      //in the view
      if(is_array($data)){
        extract($data);
      }
      require APP . 'view/' . $view;
  }

  public function renderError($code = 0, $message = null){
    if(!$message){
      if($code == 404){
        $message = "Not found.";
      }else if($code == 403){
        $message = "Unauthorized.";
      }
    }
    $this->renderView('error/index.php', array(
      'errorCode' => $code,
      'message'   => $message
    ));
  }

  public function fetchFromGET($queryParam){
    if(isset($_GET[$queryParam])){
      if($_GET[$queryParam]){
        return $_GET[$queryParam];
      }
    }
    return null;
  }

  public function fetchFromPOST($queryParam){
    if(isset($_POST[$queryParam])){
      if($_POST[$queryParam]){
        return $_POST[$queryParam];
      }
    }
    return null;
  }

  public function fetchFromSESSION($param){
    if(isset($_SESSION[$param])){
      if($_SESSION[$param]){
        return $_SESSION[$param];
      }
    }
    return null;
  }
}
