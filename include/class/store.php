<?php
/**
 * Store class.
 * 
 * @see Base
 * @package ShopSmart
*/
class Store extends Base {
    
    /**
     * Store's id.
     * 
     * @var int
    */
    public $store_id = 0;
    
    /**
     * Store's name.
     * 
     * @var string
    */
    public $store_name;
    
    /**
     * Store's address as Address object.
     * 
     * @var object
    */
    public $address;
    
    /**
     * Store's phone number
     * 
     * @var string
    */
    public $tel_number;
    
    /**
     * Constructor.
     * 
     * @param int $id Optional store id.
     * @param array $store Optional. {
     *      Store name and address.
     * 
     *      @param string $store_name Store's name.
     *      @param int $address_id ID of address.
     * }
    */
    public function __construct( $id = 0, $store = array() ) {
        if ( $id && is_numeric( $id ) ) {
            $store_data = self::get_store_data_by( 'id', $id );
        } else if ( (! empty( $store )) && is_array( $store )) {
            $store_data = self::get_store_data_by( 'name', $store );
        }
        
        if ( $store_data instanceof stdClass ) {
            $this->init( $store_data );
        }
    }
    
    /**
     * Initalize object's properties.
     * 
     * @param object Result object.
     * 
     * @return void
    */
    public function init( stdClass $store_data ) {
        if ( $this->store_exists ) {
            $this->flush_object();
        }
        
        $this->data = $store_data;
        $this->store_id = $store_data->store_id;
        $this->store_name = $store_data->store_name;
        $this->tel_number = $store_data->tel_number;
        
        if ( $address_obj = Address::get_address_by_id( $store_data->address_id )) {
            $this->address = $address_obj;
        }
    }
    
    /**
     * Get store object by store's id.
     * 
     * @param int $id Store's id.
     * 
     * @return object|false Instance of Store or false on failure.
    */
    static public function get_store_by_id( $id ) {
        if ( $store_data = self::get_store_data_by( 'id', $id )) {
            $store = new Store();
            $store->init( $store_data );
            return $store;
        } else {
            return false;
        }
    }
    
    /**
     * Get store object by store's name and address.
     * 
     * @param string $name Store's name.
     * @param int $address ID of address.
     * 
     * @return object|false Instance of Store or false on failure.
    */
    static public function get_store_by_name( $name, $address ) {
        if ( $store_data = self::get_store_data_by( 'name', $name, $address )) {
            $store = new Store();
            $store->init( $store_data );
            return $store;
        } else {
            return false;
        }
    }
    
    /**
     * Get Store objects for all markets.
     * 
     * @return array|false Array of store objects, false on failure.
    */
    static public function get_all_stores() {
        if (! $store_data = self::get_store_data_by() ) {
            return false;
        }
        
        if ( is_array( $store_data )) {
            $return_array = array();
            
            foreach ( $store_data as $data ) {
                $store = new Store(); 
                $store->init( $data );
                array_push( $return_array, $store );
            }
            return $return_array;
        } else {
            return false;
        }
    }
    
    /**
     * Get store data by particular field.
     * 
     * Returns all stores be default.
     * 
     * @param string $db_field Optional.  DB field to query against: 'id' or 'name'.
     * @param string $value Optional. Value or values to match.
     * @param array $value 
     * 
     * Optional. Value to match. {
     *      If matching 'store name', must provide 'address_id' field.
     * 
     *      @param string $store_name Name of store to match.
     *      @param int $address_id ID of address to match.
     * }
     * 
     * @return object|false Result object or false on failure.
    */
    static private function get_store_data_by( $db_field = '', $value = '' ) {
        global $db;
        
        $command = "SELECT * FROM markets";
        
        if ( empty( $db_field )) {
            $return_val = $db->get_results( $command );
        } else {
            $value = func_get_args();
            array_shift( $value );
            
            // If values were passed as array, set value to that array
            if (is_array( $value[0] )) {
                $value = $value[0];
            }
            
            if ( empty( $value )) {
                return false;
            }
            
            // Make sure id searched for is integer
            if ($db_field === 'id') {
                if (! ( is_numeric( $value[0] ))) {
                    return false;
                } else {
                    $value[0] = intval( $value[0] );
                }
            }
            
            switch ( $db_field ) {
                case 'id':
                    $db_field = 'store_id';
                    break;
                case 'name':
                    $db_field = 'store_name';
                    break;
            }
            
            $command .= " WHERE $db_field = '%s'";
            
            // Adjust query for query on store_name
            if ( $db_field === 'store_name' ) {
                if (! is_numeric( $value[1] )) {
                    return false;
                } else {
                    $value[1] = intval( $value[1] );
                    $command .= " AND address_id = %d";
                }
            }
            
            $command .= " AND date_deactivated <= 0;";
            
            $return_val = $db->get_row( $db->prepare($command, $value) );
        }
        
        if ( $return_val) {
            return $return_val;
        } else {
            return false;
        }
    }
    
    /**
     * Insert store in database.
     * 
     * If store already exists, perform update operation.
     * 
     * @param array $store_data { 
     *      Optional.  Array of store's data.
     *      
     *      @param int $store_id Optional. Store's id.
     *      @param string $store_name Store's name.
     *      @param int $address_id ID of address.
     *      @param string $tel_number Store's phone number.
     * }
     * 
     * @return int|Error ID of store, Error object on failure.
    */
    static public function insert_store( $store_data = array() ) {
        global $db;
        
        if ( empty( $store_data )) {
            if (! empty( $_POST['store_id'] )) {
                $store_data['store_id'] = $_POST['store_id'];
            }
            
            if (! empty( $_POST['store_name'] )) {
                $store_data['store_name'] = $_POST['store_name'];
            }
            
            if (! empty( $_POST['address_id'] )) {
                $store_data['address_id'] = $_POST['address_id'];
            }
            
            if (! empty( $_POST['tel_number'] )) {
                $store_data['tel_number'] = $_POST['tel_number'];
            }
        }
       
        trim_values( $store_data );
        
        // Make sure address exists
        $address_id = isset( $store_data['address_id'] ) ? intval( $store_data['address_id'] ) : 0;
        
        $address_obj = Address::get_address_by_id( $address_id );

        if (! $address_obj instanceof Address ) {
            return new Error( 'invalid_address_id', 'Address does not exists.' );
        }
        
        // Merge old and new store data if store already exists
        $store_id = isset( $store_data['store_id'] ) ? intval( $store_data['store_id'] ) : 0;
        
        if ( !( empty( $store_id )) && is_numeric( $store_id )) {
            $old_store_data = self::get_store_data_by( 'id', $store_id );
        } else {
            $old_store_data = self::get_store_data_by( 'name', $store_data['store_name'], $address_id );    
        }
        
        if ( $old_store_data instanceof stdClass ) {
            $old_store = get_object_vars( $old_store_data );
            $store_data = array_merge( $old_store, $store_data );
            $return_val = $store_data['store_id'];
            $update = true;
        }
        
        // Validate user data
        
        // RegEx for name format
        $invalid_name = "[^-'\040a-z.]";
        
        if ( empty( $store_data['store_name'] )) {
            return new Error( 'empty_store_name', 'Cannot add store without a store name.' );
        } else if ( preg_match( "/$invalid_name/i", $store_data['store_name'] )) {
            return new Error( 'invalid_store_name', 'Store name must contain hyphens, apostrophes, spaces or periods only.' );
        } else if ( strlen( $store_data['store_name'] > 35 )) {
            return new Error( 'invalid_store_name', 'Store name must be 35 characters or less.' );
        }
        
        //RegEx for phone format
        $valid_phone = "^\d{3}-\d{3}-\d{4}$";
        
        if ( empty( $store_data['tel_number'] )) {
            return new Error( 'invalid_tel_number', 'Cannot add store without a telephone number.' );
        } else if (! preg_match( "/$valid_phone/", $store_data['tel_number'] )) {
            return new Error( 'invalid_tel_number', 'Telephone number must be in 000-000-0000 form.' );
        }
        
        // Check to see if performing update operation
        if ( $update === true ) {
            $command = "UPDATE markets SET store_name = '%s', address_id = %d, tel_number = '%s' WHERE store_id = '%s';";
            $query = $db->prepare( $command, $store_data['store_name'], $address_id, $store_data['tel_number'], $store_data['store_id']);
        } else {
            $command = "INSERT INTO markets VALUES (DEFAULT, '%s', %d, '%s', DEFAULT);";
            $query = $db->prepare( $command, $store_data['store_name'], $address_id, $store_data['tel_number'] );
        }
        
        if ( $return_val_1 = $db->query( $query )) {
            $return_store_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_store_id;
        } else {
            return new Error('invalid_query', 'Could not add store.');
        }
    }
    
    /**
     * Add deactivate flag to store.
     * 
     * @param int $id Store's id.
     * 
     * @return bool True on success, false otherwise.
    */
    static public function delete_store( $id ) {
        global $db;
        
        $id = intval( $id );
        $store = new Store( $id );
        
        if (! $store->exists() ) {
            return false;
        }
        
        $command = "UPDATE markets SET date_deactivated = NOW() WHERE store_id = %d";
        
        if ( $result = $db->query( $db->prepare( $command, $id ))) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check to see if store exists.
     * 
     * @return bool True if store exists, false on failure.
    */
    public function store_exists() {
        return ! ( empty( $this->store_id ));
    }
    
    /**
     * Checks to see if value is real store.
     * 
     * @param mixed Value to check.
     * 
     * @return bool True of value is Store object, false otherwise.
    */
    static public function is_store( $value ) {
        if (! $value instanceof Store ) {
            return false;
        }
        
        if ( $value->store_exists() ) {
            return true;
        } else {
            return false;
        }
    }
}
?>