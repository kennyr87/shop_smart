<?php
/**
 * Session Class.
 *
 * Modified from simple MVC framework session class.
 * 
 * @link https://github.com/simple-mvc-framework/v2/blob/master/app/helpers/session.php
 * @package ShopSmart
 */
class Session {

	/**
	 * Determine if session has started.
	 * 
	 * @var boolean
	 */
	private static $_sessionStarted = false;
	
	/**
	 * Start session if not started.
	 * 
	 * @return void
	 */
	public static function init() {

		if (self::$_sessionStarted == false) {
			session_start();
			self::$_sessionStarted = true;
		}
	}

	/**
	 * Add value to a session.
	 * 
	 * @param array|string $key Name of the data to save if string. 
	 * 		Key value/pairs if array.
	 * @param string $value Optional data to save if $key is string.
	 * 
	 * @return void
	 */
	public static function set_session( $key, $value = false ) {
		
		self::init();

		if (is_array($key) && $value === false) {

			foreach ($key as $name => $value) {
				$_SESSION[$name] = $value;
			}

		} else {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Extract session value.
	 * 
	 * @param string $key Key of value to extract.
	 * 
	 * @return string
	 */
	public static function pull_session($key) {
		$return_val = $_SESSION[$key];
		
		if ( isset( $_SESSION[$key] )) {
			unset( $_SESSION[$key] );
			return $return_val;
		} else {
			return false;
		}
	}

	/**
	 * Get session value.
	 * 
	 * @param  string  $key  Key of session of value to get.
	 * 
	 * @return string
	 */
	public static function get_session( $key ) { 
		$return_val = $_SESSION[$key];
		
		if ( isset( $_SESSION[$key] )) {
			return $return_val;
		} else {
			return false;
		}
	}
	
	/**
	 * Get session id.
	 * 
	 * @return string
	 */
	public static function get_session_id() {
		return session_id();
	}

	/**
	 * Get session array.
	 * 
	 * @return array Array of session indexes.
	 */
	public static function display_session() {
		return $_SESSION;
	}
	
	/**
	 * Empty and destroy the session.
	 * 
	 * @param string Optional key of session to destroy.
	 * 
	 * @return void
	 */
	public static function destroy( $key = '' ) {
		if ( self::$_sessionStarted == true )  {
			if ( empty( $key ) ) {
				session_unset();
				session_destroy();
			} else {
				unset( $_SESSION[$key] );
			}
		}
	}
}
?>