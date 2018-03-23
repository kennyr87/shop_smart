<?php
/**
 * Class to load template pages.
 *
 * Modified from simple MVC framework view class.
 *
 * @link https://github.com/simple-mvc-framework/v2/blob/master/app/core/view.php
 * @package ShopSmart
 */
class View {
	
	/**
	 * Array of HTTP headers.
	 * 
	 * @var array
	 */
	private static $headers = array();
	
	/**
	 * Include template file.
	 * 
	 * @param string $path Path to file from views folder.
	 * @param array $data Data to pass to template file.
	 * @param array $error
	 */
	public static function render_template($path, $data = false, $error = false) {

		if (! (headers_sent()) ) {
			foreach (self::$headers as $header) {
				header($header, true);
			}
		}
		
		$file_root = DOCROOT . "/include/templates/";
		
		require_once $file_root . $path;
	}

	/**
	 * Add HTTP header to headers array.
	 * 
	 * @param string $header HTTP header text.
	 */
	public function addheader($header) {
	    self::$headers[] = $header;
	}

	/**
 	* Add an array with headers to the view.
 	* 
 	* @param array $headers
 	*/
	public function addheaders($headers = array()) {
	    
    	foreach($headers as $header) {
    	    $this->addheader($header);
    	}
    }
}
?>