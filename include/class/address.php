<?php
/**
 * Address class.
 * 
 * @see Base
 * @package ShopSmart
*/
class Address extends Base {
    
    /**
     * ID of address.
     * 
     * @var int
    */
    public $address_id;
   
    /**
     * Street address.
     * 
     * @var string
    */ 
    public $address;
    
    /**
     * Address's city.
     * 
     * @var string
    */ 
    public $city;
    
    /**
     * Address's state.
     * 
     * @var string
    */
    public $state;
    
    /**
     * Address's zip code.
     * 
     * @var string
    */ 
    public $zip_code;
    
    /**
     * Constructor.
     * 
     * @param int $id Optional. ID of address.
     * @param array $address {
     * 
     *      Optional.  Array of address data.
     * 
     *      @param string $address Street address.
     *      @param string $city Address's city.
     *      @param string $state Address's state.
     * }
    */ 
    public function __construct( $id = 0, $address = array() ) {
        if ( $id && is_numeric( $id )) {
            $address_data = self::get_address_data_by( 'id', $id );
        } else if ( $address && is_array( $address )) {
            $address_data = self::get_address_data_by( 'street', $address );
        }
        
        if ( $address_data instanceof stdClass ) {
            $this->init( $address_data );
        }
    }
    
    /**
     * Initialize object properties.
     * 
     * @param object $data Result object.
     * 
     * @return void
    */
    public function init( stdClass $data ) {
       if ( $this->address_exists() ) {
           $this->flush_object();
       }
       
       $this->data = $data;
       $this->address_id = $data->address_id;
       $this->address = $data->address;
       $this->city = $data->city;
       $this->state = $data->state;
       $this->zip_code = $data->zip_code;
    }
    
    
    /**
     * Get Address object by address id.
     * 
     * @param int $id Address id.
     * 
     * @return object|false Instance of Address or false on failure.
    */
    static public function get_address_by_id( $id ) {
        if (! is_numeric( $id )) {
            return false;
        }
        
        if ( $address_data = self::get_address_data_by( 'id', $id )) {
            $address_obj = new Address();
            $address_obj->init( $address_data );
            return $address_obj;
        } else {
            return false;
        }
    }
    
    /**
     * Get Address object by address.
     * 
     * @param string $args  Address's street address, city, and state.
     * @param array $args
     * 
     *       Optional. Value to match. {
     *       If matching 'address', must also provide 'city' and 'state'.
     *      
     *       @param string $address Street address.
     *       @param string $city Address's city.
     *       @param string $state Address's state.
     * }
     * 
     * @return object|false Instance of Address or false on failure.
    */
    static public function get_address_by_street( $args ) {
        
        if ( empty( $args )) {
            return false;
        }
        
        $args = func_get_arg();
        
        if ( is_array( $arg[0] )) {
            $args = $arg[0];
        }
        
        if ( $address_data = self::get_address_data_by( 'street', $args)) {
            $address_obj = new Address();
            $address_obj->init( $address_data );
            return $address_obj;
        } else {
           return false;
        }
    }
    
    /**
     * Get address's data.
     * 
     * @param string $db_field DB field to query against: 'id', 'street'.
     * @param int|string $value Value or values to match.
     * @param array $value
     * 
     *      Optional. Value to match. {
     *      If matching 'address', must also provide 'city' and 'state'.
     *      
     *      @param string $address Street address.
     *      @param string $city Address's city.
     *      @param string $state Address's state.
     * }
     * 
     * @return object|false Result object, or false on failure.
    */ 
    private static function get_address_data_by( $db_field, $value ) {
        global $db;
        
        if ( empty( $value )) {
            return false;
        }
        
        $value = func_get_args();
        array_shift( $value );
        
        // If values were passed as array, set values to that array
        if (is_array( $value[0] )) {
            $value = $value[0];
        }
        
        // Make sure address_id is an integer
        if ( $db_field === 'id' ) {
            if (! ( is_numeric( $value[0] ))) {
                return false;
            } else {
                $value[0] = intval( $value[0] );
            }
        }
        
        switch ( $db_field ) {
            case 'id':
                $db_field = 'address_id';
                break;
            case 'street':
                $db_field = 'address';
                break;
            default:
                return false;
        }
        
        if ( $db_field === 'address' ) {
            $command = "SELECT * FROM street_address WHERE $db_field = '%s' AND city = '%s' AND state = '%s';";
        } else {
            $command = "SELECT * FROM street_address WHERE $db_field = %d";
        }
        
        $result = $db->get_row( $query = $db->prepare( $command, $value ));
        
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Insert new address in database.
     * 
     * If address already exists, perform update operation.
     * 
     * @param array $item_data {
     *
     *       Optional. Array of address's data.
     * 
     *       @param int $address_id Optional address id.
     *       @param string $address Street address.
     *       @param string $city Address's city.
     *       @param string $state Address's state.
     *       @param string $zip_code Address's zip code.
     * } 
     * 
     * @return int|Error ID of address on success, Error object on failure.
    */
    static public function insert_address( $address_data = array() ) {
        global $db;
        
        if ( empty( $address_data )) {
            if (! empty( $_POST['address_id'] )) {
                $address_data['address_id'] = $_POST['address_id'];
            }
            
            if (! empty( $_POST['address'] )) {
                $address_data['address'] = $_POST['address'];
            }
            
            if (! empty( $_POST['city'] )) {
                $address_data['city'] = $_POST['city'];
            }
            
            if (! empty( $_POST['state'] )) {
                $address_data['state'] = $_POST['state'];
            }
            
            if (! empty($_POST['zip_code'] )) {
                $address_data['zip_code'] = $_POST['zip_code'];
            }
        }
        
        trim_values( $address_data );
        
        // Merge old and new address if address already exists
        $address_id = isset( $address_data['address_id'] ) ? intval( $address_data['address_id'] ) : 0;
        
        if ( isset( $address_data['address'], $address_data['city'], $address_data['state'] )) {
            $address = array( $address_data['address'], $address_data['city'], $address_data['state'] ); 
        } else {
            $address = array();
        }
        
        if ( $address_id && is_numeric( $address_id )) {
            $old_address_data = self::get_address_data_by( 'id', $address_id );
        } else if ( $address && is_array( $address )) {
            $old_address_data = self::get_address_data_by( 'street', $address );
        }
        
        if ( $old_address_data instanceof stdClass ) {
            $old_address = get_object_vars( $old_address_data );
            $address_data = array_merge( $old_address, $address_data );
            $return_val = $address_data['address_id'];
            $update = true;
        }
        
        // Validate user data
        
        $invalid_address = "[^-'\040a-z\d.]";
        
        if ( empty( $address_data['address'] )) {
            return new Error( 'invalid_address_street', 'Cannot add address without a street.');
        } else if ( strlen( $address_data['address'] ) > 50 ) {
            return new Error( 'invalid_address_street', 'Address street city be 50 characters or less.');
        } else if ( preg_match( "/$invalid_address/i", $address_data['address'] )) {
            return new Error( 'invalid_address_street', 'Address must contain numbers, hyphens, apostrophes, spaces or periods only.');
        }
        
        $invalid_city = "[^-'\040a-z.]";
        
        if ( empty( $address_data['city'] )) {
            return new Error( 'invalid_address_city', 'Cannot add address without a city.');
        } else if ( strlen( $address_data['city'] ) > 35 ) {
            return new Error( 'invalid_address_city', 'City must be 50 characters or less.');
        } else if ( preg_match( "/$invalid_city/i", $address_data['city'] )) {
            return new Error( 'invalid_address_city', 'City name must contain hyphens, apostrophes, spaces or periods only.');
        }
        
        $valid_states = "^(?:(A[KLRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA"
                      . "|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]" 
                      . "|UT|V[AIT]|W[AIVY]))$";
                      
        if ( empty( $address_data['state'] )) {
            return new Error( 'invalid_address_state', 'Cannot add address without a state.');
        } else if (! preg_match( "/$valid_states/i", $address_data['state'] )) {
            return new Error( 'invalid_address_state', 'State must a valid U.S. state abbreviation.');
        }
        
        $valid_zip = "^\d{5}(?:-\d{4})?$";
        
        if ( empty( $address_data['zip_code'] )) {
            return new Error( 'invalid_address_zip', 'Cannot add address without a zip code.');
        } else if (! preg_match( "/$valid_zip/", $address_data['zip_code'] )) {
            return new Error( 'invalid_address_zip', 'Zip code must be a valid U.S. zip code.');
        }
        
        // Check to see if performing update operation
        if ( $update === true ) {
            $command = "UPDATE street_address SET address = '%s', city = '%s', "
                     . "state = '%s', zip_code = '%s' WHERE address_id = %d;";
            $query = $db->prepare(
                $command, 
                $address_data['address'], 
                $address_data['city'], 
                $address_data['state'], 
                $address_data['zip_code'],
                $address_data['address_id']
            );
        } else {
            $command = "INSERT INTO street_address VALUES (DEFAULT, '%s', '%s', '%s', '%s');";
            $query = $db->prepare(
                $command, 
                $address_data['address'], 
                $address_data['city'], 
                $address_data['state'], 
                $address_data['zip_code']
            );
        }

        if ( $return_val_1 = $db->query( $query )) {
            $return_address_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_address_id;
        } else {
            return new Error( 'invalid_query', 'Could not add address.' );
        }
    }
    
    /**
     * Check to see if if address exists.
     * 
     * @return bool True of address exists, false otherwise.
    */ 
    public function address_exists() {
        return ! ( empty( $this->address_id ));
    }
}
?>