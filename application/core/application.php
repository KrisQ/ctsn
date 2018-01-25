<?php
class Application{

	/** @var null The controller */
	private $url_controller = null;

	/** @var null The method (of the above controller), often also named "action" */
	private $url_action = null;

	/** @var array URL parameters */
	private $url_params = array();

  const SHOW_LOG_MESSAGES = true;

	//SESSION TIMEOUT IN SECONDS!!
	const SESSION_TIMEOUT = 1800;
	// const SESSION_TIMEOUT = 30;
	const USER_UPDATE_FORMAT = 'Y-m-d-H.i.s';
	/**
	* "Start" the application:
	* Analyze the URL elements and calls the according controller/method or the fallback
	*/
	public function __construct(){
		error_reporting(E_ALL ^ E_STRICT);
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		$this->splitUrl();
		$this->_loadAllModels();
		$this->_checkUserTimeout();

		if (!$this->url_controller) {
			require_once(APP . 'view/_templates/header.php');
			require APP . 'controller/home.php';
			$page = new Home();
			$page->index();
			require_once(APP . 'view/_templates/footer.php');
		}else if (file_exists(APP . 'controller/' . $this->url_controller . '.php')) {
			if($this->isAjax()){
				header("Content-type: application/json");
			}else{
				require_once(APP . 'view/_templates/header.php');
			}
			// if so, then load this file and create this controller
			// example: if controller would be "car", then this line would translate into: $this->car = new car();
			require APP . 'controller/' . $this->url_controller . '.php';
			$this->url_controller = new $this->url_controller();
			// check for method: does such a method exist in the controller ?
			if (method_exists($this->url_controller, $this->url_action)) {
				if (!empty($this->url_params)) {
					// Call the method and pass arguments to it
					call_user_func_array(array(
						$this->url_controller,
						$this->url_action
					), $this->url_params);
				} else {
					// If no parameters are given, just call the method without parameters, like $this->home->method();
					$this->url_controller->{$this->url_action}();
				}
			} else {
				if (strlen($this->url_action) == 0) {
					// no action defined: call the default index() method of a selected controller
					$this->url_controller->index();
				} else {
					header('location: ' . URL . 'error');
				}
			}
			if(!$this->isAjax()){
				require_once(APP . 'view/_templates/footer.php');
			}
		} else {
			header('location: ' . URL . 'error');
		}
	}

	private function _checkUserTimeout(){
		if (isset($_SESSION['user'])) {
			if (isset($_SESSION['cart'])) {

			}else{
				$cart = CartModel::model()->create(array(
					'user_id' => $_SESSION['user']->id
				));
				if ($cart) {
					$_SESSION['cart'] = $cart;
					UserModel::model()->update(array(
						'cart_id' => $cart->id,
						'id' => $_SESSION['user']->id
					));
				}else{
					die("Could not create cart.");
				}
			}
		}else{

		}
	}

	/**
	 * Check if it's an Ajax Call
	 */
	 public function isAjax() {
 		if ($this->url_controller == 'ajax') {
 			return true;
 		}
 		if (Helper::contains('ajax',$this->url_action)) {
 			return true;
 		}
 		$isAjax = false;
 		$isAjax =  isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
 		return $isAjax;
 	}

	/**
	 * Load All Models
	 */
	private function _loadAllModels(){
    //  Require ALL of the models in the model directory for global usage
    // require_once(APP . 'model/item.php');
        foreach(scandir(APP . "model") as $model){
            if(is_file(APP . "model/$model")){
                require_once(APP . "model/$model");
            }
        }
    }

    /**
	 * Get and split the URL
	 */
	private function splitUrl(){
		if (isset($_GET['url'])) {
			// split URL
			$url = trim($_GET['url'], '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);
			// Put URL parts into according properties
			// By the way, the syntax here is just a short form of if/else, called "Ternary Operators"
			// @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
			$this->url_controller = isset($url[0]) ? $url[0] : null;
			$this->url_action     = isset($url[1]) ? $url[1] : null;
			// Remove controller and action from the split URL
			unset($url[0], $url[1]);
			// Rebase array keys and store the URL params
			$this->url_params = array_values($url);

		}
	}
}
