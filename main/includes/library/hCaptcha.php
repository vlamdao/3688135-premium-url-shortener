<?php 
/**
 * ====================================================================================
 *                           Premium URL Shortener (c) KBRmedia
 * ----------------------------------------------------------------------------------
 *  @copyright - This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in illegal activity. You must delete this software immediately or buy a proper
 *  license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 *
 *	@license http://gempixel.com/license
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 *
 * @author KBRmedia (http://gempixel.com)
 * @link http://gempixel.com
 * @package Premium URL Shortener
 * @subpackage hCaptcha Implementation
 */
class hCaptcha {
	/**
	 * Verify Constant
	 */
  const VERIFY = "https://hcaptcha.com/siteverify";
  /**
   * [$_secret description]
   * @var [type]
   */
  private $secret;
  /**
   * [$errorCode description]
   * @var [type]
   */
  private $errorCode;
  /**
   * [$success description]
   * @var [type]
   */
  private $success;
  /**
   * Check Keys
   * @author KBRmedia <https://gempixel.com>
   * @version 1.0
   * @param   [type] $secret [description]
   */
	public function __construct($secret) {

    if ($secret == null || $secret == "") {    
      return print("hCaptcha has not been setup properly. Please contact the administrator.");
    }

    $this->secret = $secret;
  }  
  /**
   * Verify Token with Google Server
   * @author KBRmedia <https://gempixel.com>
   * @version 1.0
   * @return  boolean Passed or not
   */
  public function verify(){

  	$ip = $_SERVER["REMOTE_ADDR"];

  	$response = $_POST["h-captcha-response"];

  	if ($this->secret == null || $this->secret == "") return false;

    if ($response == null || strlen($response) == 0) {
      $this->success = false;
      $this->errorCodes = 'Missing input';
      return false;
    }

    $response = $this->http([
      'secret' => $this->secret,
      'response' => $response    	
    ]);

    if($response->success == true) {
    	$this->success = true;
      return true;
    } else {
    	$this->success = false;
      return false;
    }
  }    
  /**
   * Send cURL Request to Google
   * @author KBRmedia <https://gempixel.com>
   * @version 1.0
   * @param   array  	$data
   * @return  object	$response Decode Response from Google
   */
  private function http(array $data){

    $parameters = http_build_query($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, self::VERIFY);
    curl_setopt($curl, CURLOPT_POST, count($data));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($curl);

    if($error = curl_error($curl)){
    	error_log($error);
    }

    curl_close($curl);        
    return json_decode($response);
  }
  /**
   * V2 Block
   * @author GemPixel <https://gempixel.com>
   * @version 1.0
   * @param   [type] $public [description]
   * @return  [type]         [description]
   */
  public static function render($public){
    return '<div class="h-captcha" data-sitekey="'.$public.'"></div>
            <script src="https://www.hCaptcha.com/1/api.js?hl='.Main::lang().'" async defer></script>
            <script>
              var recaptcha = () => {
                return hcaptcha.reset();
              }
            </script>            
            ';
  }
}