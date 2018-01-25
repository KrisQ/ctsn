<?php
class Helper{
  const SHOW_LOG_MESSAGES = true;
  public static function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
  }
  public static function convertTimeToInt($format, $time){
    if(!$time){
      return 0;
    }
    $time = str_replace('.000000', '',$time);

    $time = DateTime::createFromFormat($format, $time);
    //  you have to format it to this to convert it to an int
    return strtotime($time->format('Y-m-d H:i:s'));
  }

  public static function consoleLog($message){
    if(is_array($message)){
      extract($message);
    }
    require APP . 'view/_templates/js_logger.php';
  }
  public static function getCardType($short){
    $name = '';
    switch ($short) {
      case 'MC':
        $name = "Master Card";
        break;
      case 'VI':
        $name = "VISA";
        break;
      case 'AM':
        $name = "American Express";
        break;
      case 'DC':
        $name = "Discover";
        break;
      default:
        # code...
        break;
    }
    return $name;
  }

  public static function fetchFromGET($queryParam){
    if(isset($_GET[$queryParam])){
      if($_GET[$queryParam]){
        return $_GET[$queryParam];
      }
    }
    return null;
  }

  public static function fetchFromPOST($queryParam){
    if(isset($_POST[$queryParam])){
      if($_POST[$queryParam]){
        return $_POST[$queryParam];
      }
    }
    return null;
  }

  public static function fetchFromSESSION($param){
    if(isset($_SESSION[$param])){
      if($_SESSION[$param]){
        return $_SESSION[$param];
      }
    }
    return null;
  }

  // States List
  public static function getStates(){
		return array(
			'AL'=>'Alabama',
			'AK'=>'Alaska',
			'AZ'=>'Arizona',
			'AR'=>'Arkansas',
			'CA'=>'California',
			'CO'=>'Colorado',
			'CT'=>'Connecticut',
			'DE'=>'Delaware',
			'DC'=>'District of Columbia',
			'FL'=>'Florida',
			'GA'=>'Georgia',
			'HI'=>'Hawaii',
			'ID'=>'Idaho',
			'IL'=>'Illinois',
			'IN'=>'Indiana',
			'IA'=>'Iowa',
			'KS'=>'Kansas',
			'KY'=>'Kentucky',
			'LA'=>'Louisiana',
			'ME'=>'Maine',
			'MD'=>'Maryland',
			'MA'=>'Massachusetts',
			'MI'=>'Michigan',
			'MN'=>'Minnesota',
			'MS'=>'Mississippi',
			'MO'=>'Missouri',
			'MT'=>'Montana',
			'NE'=>'Nebraska',
			'NV'=>'Nevada',
			'NH'=>'New Hampshire',
			'NJ'=>'New Jersey',
			'NM'=>'New Mexico',
			'NY'=>'New York',
			'NC'=>'North Carolina',
			'ND'=>'North Dakota',
			'OH'=>'Ohio',
			'OK'=>'Oklahoma',
			'OR'=>'Oregon',
			'PA'=>'Pennsylvania',
			'RI'=>'Rhode Island',
			'SC'=>'South Carolina',
			'SD'=>'South Dakota',
			'TN'=>'Tennessee',
			'TX'=>'Texas',
			'UT'=>'Utah',
			'VT'=>'Vermont',
			'VA'=>'Virginia',
			'WA'=>'Washington',
			'WV'=>'West Virginia',
			'WI'=>'Wisconsin',
			'WY'=>'Wyoming',
		);
	}

    // Months list
    public static function getMonths(){
      return array(
        '01'=>'January',
        '02'=>'February',
        '03'=>'March',
        '04'=>'April',
        '05'=>'May',
        '06'=>'June',
        '07'=>'July',
        '08'=>'August',
        '09'=>'September',
        '10'=>'October',
        '11'=>'November',
        '12'=>'December'
      );
    }

    // Year range based on direction and range
    public static function getYears($direction, $range){
        $currentYear = date('Y');
        if ($direction == 'past') {
            $rangeYear = $currentYear - $range;
            return array_combine(range($rangeYear, $currentYear), range($rangeYear, $currentYear));
        }
        else {
            $rangeYear = $currentYear + $range;
            return $years = array_combine(range($currentYear, $rangeYear), range($currentYear, $rangeYear));
        }
    }

    /**
	 * Log Messages
	 */
	public static function log($message)
    {
  	 if (self::SHOW_LOG_MESSAGES) {
  		echo "<pre>";
  		print_r($message);
  		echo "</pre>";
  	 }
	}
}
