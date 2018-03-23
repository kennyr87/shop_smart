<?php
/**
 * Class for contructing and handling errors.
 * 
 * Based on WordPress Error API.
 * 
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp-error.php
 * @package ShopSmart
*/
class Error {
    
    /**
     * List of error messages.
     * 
     * @var array
    */
    private $errors = array();
    
    /**
     * List of data for errors.
     * 
     * @var array
    */
    private $error_data = array();
    
    /**
     * Construct Error object.
     * 
     * @param string|int $code Error code.
     * @param string $message Error message.
     * @param mixed $data Optional error data.
    */
	public function __construct( $code = '', $message = '', $data = '' ) {
        if (! (empty($code) )) {
            $this->errors[$code] = $message;
            
            if (!(empty($data))) {
                $this->error_data[$code] = $data;
            }
        }
    }
    
    /**
     * Get all error codes.
     * 
     * @return array List of error codes, or empty array if no codes exist.
    */
    public function get_error_codes() {
        if (empty($this->errors)) {
            return array();
        } else {
            return array_keys($this->errors);
        }
    }
    
    /**
     * Get first error code in list of codes.
     * 
     * @return string|int
    */
    public function get_error_code() {
        $codes = $this->get_error_codes();
        
        if (empty($codes)) {
            return '';    
        } else {
            return $codes[0];
        }
    }
    
    /**
     * Get all error messages or message for specific code.
     * 
     * @param string|int $code Optional key of code in code array.
     * 
     * @return array Array of all error messages, empty array if no code found.
    */
    
    public function get_error_messages($code = '') {
        $messages = array();
        if (empty($code)) {
            foreach ($this->errors as $msg) {
                array_push($messages, $msg);
            }
        } else if ( isset($this->errors[$code] )) {
            array_push( $messages, $this->errors[$code] );
        }
        return $messages;
    }
    
    /**
     * Get single error message.
     * 
     * Get first message that matches specific error code, or first message in code array
     * if no code given.
     * 
     * @param string|int $code Optional key of code in code array.
     * 
     * @return string Error message.
    */
    public function get_error_msg($code = '') {
        if (empty($code)) {
            $code = $this->get_error_code();
        }
        
        $messages = $this->get_error_messages($code);
        
        if ( empty($messages) ) {
            return '';
        } else {
            return $messages[0];
        }
    }

    /**
     * Get all error data or data for specific code.
     * 
     * @param string|int $code Optional key of code in code array.
     * 
     * @return array Array of all error messages, empty array if no code found.
    */
    
    public function get_all_error_data( $code = '' ) {
        $data_array = array();
        
        if ( empty( $code )) {
            foreach ( $this->error_data as $data ) {
                array_push( $data_array, $data );
            }
        } else if ( isset( $this->data[$code] )) {
            array_push($data_array, $this->error_data[$code] );
        }
        return $data_array;
    }
    
    /**
     * Get single error code data.
     * 
     * Get data that matches specific error code, or first data in code array
     * if no code given.
     * 
     * @param string|int $code Optional key of code in code array.
     * 
     * @return mixed Error data.
    */
    public function get_error_data( $code = '' ) {
        if ( empty( $code) ) {
            $code = $this->get_error_code();
        }
        
        $data = $this->get_all_error_data( $code );
        
        if ( empty( $data )) {
            return '';
        } else {
            return $data[0];
        }
    }
    
    /**
     * Kill script execution and display HTML error message.
     * 
     * @param string $msg Error message to display.
     * 
     * @return string Display HTML message and kills script execution.
    */
    static public function error_msg( $error = 'There was an error.  Please try again.' ) {
        $html = <<<HTML
<div class='col-md-12 alert alert-danger' role='alert'>
<p>$error</p>
</div>
HTML;
        echo $html;
        exit;
    }
    
    /**
    * Check whether variable is a Error.
    *
    * Returns true if $thing is an object of the Error class.
    *
    * @param mixed $thing Check if unknown variable is an Error object.
    * 
    * @return bool True, if Error. False, if not Error.
    */
    static public function is_error($thing) {
        return ( $thing instanceof Error );
    }
}
?>