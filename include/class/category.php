<?php
/**
 * Category class.
 * 
 * @see Base
 * @package ShopSmart
*/
class Category extends Base {
    
    /**
     * Category's id.
     * 
     * @var int
    */
    public $cat_id = 0;
    
    /**
     * Category's name.
     * 
     * @var string
    */
    public $cat_name;
    
    /**
     * Constructor.
     * 
     * @param int $cat_id Category's id.
    */
    public function __construct( $cat_id = 0, $cat_name = '' ) {
        if ( (! empty( $cat_id )) && is_numeric( $cat_id )) {
            $cat_data = self::get_cat_data_by( 'id', $cat_id );
        } else if ( (! empty( $cat_name )) && is_string( $cat_name )) {
            $cat_data = self::get_cat_data_by( 'name', $cat_name);
        }
        
        if ( $cat_data instanceof stdClass ) {
            $this->init( $cat_data );
        }
    }
    
    /**
     * Initialize object's properties.
     * 
     * @param object $cat_data MySQL result set.
     * 
     * @return void
    */
    public function init( stdClass $cat_data ) {
        if ( $this->cat_exists() ) {
            $this->flush_object();
        }
        
        $this->data = $cat_data;
        $this->cat_id = $cat_data->cat_id;
        $this->cat_name = $cat_data->cat_name;
    }
    
    /**
     * Get Category object by category's id.
     * 
     * @param int $cat_id category's id.
     * 
     * @return object|false Category object, or false on failure.
    */
    static public function get_cat_by_id( $cat_id ) {
        if ( $cat_data = self::get_cat_data_by( 'id', $cat_id )) {
            $cat = new Category();
            $cat->init( $cat_data );
            return $cat;
        } else {
            return false;
        }
    }
    
    /**
     * Get Category object by category's name.
     * 
     * @param string $cat_name Category's name.
     * 
     * @return object|false Category object, or falie on failure.
    */
    static public function get_cat_by_name( $cat_name ) {
        if ( $cat_data = self::get_cat_data_by( 'name', $cat_name )) {
            $cat = new Category();
            $cat->init( $cat_data );
            return $cat;
        } else {
            return false;
        }
    }
    
    /**
     * Get Category objects for all categories.
     * 
     * @return array|false Array of category objects, or false on error.
    */
    static public function get_all_cats() {
        if (! $cat_data = self::get_cat_data_by() ) {
            return false;
        }
        
        if ( is_array( $cat_data )) {
            $return_array = array();
            
            foreach ( $cat_data as $data ) {
                $cat =  new Category();
                $cat->init( $data );
                array_push( $return_array, $cat );
            }
            return $return_array;
        } else {
            return false;
        }
    }
    
    /**
     * Get associative array of all categories.
     * 
     * @return array|false Array of associative arrays, or false on error.
    */
    static public function get_all_cats_as_arr() {
        
        if (! $cat_data = self::get_cat_data_by( '', 0, 'ARRAY_A' ) ) {
            return false;
        }
        
        if ( is_array( $cat_data )) {
            return $cat_data;
        } else {
            return false;
        }
    }
    
    /**
     * Get category's data.
     * 
     * If getting all data, leave arguments blank.
     * 
     * @param string $db_field Optional DB field to query: 'id', 'name'.
     * @param int|string $value Optional. Value to query.
     * 
     * @return mixed|false Result object, array of result sets, or false on failure. 
    */
    static private function get_cat_data_by( $db_field = '', $value = 0, $output = 'OBJECT' ) {
        global $db;
        
        $command = "SELECT * FROM categories";
        
        if ( empty( $db_field )) {
            $return_val = $db->get_results( $command, $output );
        } else {
            // Make sure id searched for is int
            if ( $db_field === 'id' ) {
                if (! is_numeric( $value )) {
                    return false;
                } else {
                    $value = intval( $value );
                }
            }
           
            switch ( $db_field ) {
                case 'id':
                    $db_field = 'cat_id';
                    break;
                case 'name':
                    $db_field = 'cat_name';
                    break;
                default:
                    return false;
            }
            
            $command = $db->prepare( $command . " WHERE $db_field = '%s';", $value );
            $return_val = $db->get_row( $command );
        }
        
        if ( $return_val ) {
            return $return_val; 
        } else {
            return false;
        }
    }
    
    /**
     * Insert row into categories table.
     * 
     * If category exists, perform update operation.
     * 
     * @param array $cat_data
     * 
     *      Optional.  Array of category data. {
     * 
     *      @param int $cat_id Optional. Category's id.
     *      @param string $cat_name Category's name.
     * }
     * 
     * @return int|Error ID of new category or number of row updated.
     *                   Error object on failure.
    */
    public static function insert_cat( $cat_data = array() ) {
        global $db;
        
        if ( empty( $cat_data )) {
            if (! empty( $_POST['cat_id'] )) {
                $cat_data['cat_id'] = $_POST['cat_id'];
            }
            
            if (! empty( $_POST['cat_name'] )) {
                $cat_data['cat_name'] = $_POST['cat_name'];
            }
        }
        
        trim_values( $cat_data );
        
        // Check to see if category exists
        $cat_id = isset( $cat_data['cat_id'] ) ? intval( $cat_data['cat_id'] ) : 0;
        $cat_name = isset( $cat_data['cat_name'] ) ? strval( $cat_data['cat_name'] ) : '';
        
        if ( (! empty( $cat_id )) && is_numeric( $cat_id )) {
            $old_cat_data = self::get_cat_data_by( 'id', $cat_id );
        } else if ( (! empty( $cat_name )) && is_string( $cat_name )) {
            $old_cat_data = self::get_cat_data_by( 'name', $cat_name);
        }
        
        if ( $old_cat_data instanceof stdClass ) {
            $old_cat = get_object_vars( $old_cat_data );
            $cat_data = array_merge( $old_cat, $cat_data );
            $return_val = $cat_data['cat_id'];
            $update = true;
        }
        
        // Validate user data
        
        $invalid_name = "[^a-z\040-'.]";
        
        if ( empty( $cat_data['cat_name'] )) {
            return new Error( 'invalid_cat_name', "Category must have a name.");
        } else if ( preg_match( "/$invalid_name/i", $cat_data['cat_name'] )) {
            return new Error( 'invalid_cat_name', "Category name must contain hyphens, apostrophes, spaces or periods only.");
        } else if ( strlen( $cat_data['cat_name'] ) > 35 ) {
            return new Error( 'invalid_cat_name', "Category name must be 35 characters or less.");
        }
        
        if ( $update === true ) {
            $command = "UPDATE categories SET cat_name = '%s' WHERE cat_id = %d";
            $query = $db->prepare( $command, $cat_data['cat_name'], $cat_data['cat_id'] );
        } else {
            $command = "INSERT INTO categories VALUES (DEFAULT, '%s')";
            $query = $db->prepare( $command, $cat_data['cat_name'] );
        }
        
        if ( $return_val_1 = $db->query( $query )) {
            $return_cat_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_cat_id;
        } else {
            return new Error( 'invalid_query', 'Could not add category.');
        }
    }
    
    /**
     * Check to see if category exists.
     * 
     * @retrun bool True if category exists, false otherwise.
    */    
    public function cat_exists() {
        return ! (empty( $this->cat_id ));
    }

    /**
     * Check to see if value is real category.
     * 
     * @param mixed $value Value to check.
     * 
     * @return bool True if value is Category, false otherwise.
    */
    static public function is_cat( $value ) {
        if (! $value instanceof Category ) {
            return false;
        }
        
        if ( $value->cat_exists() ) {
            return true;
        } else {
            return false;
        }
    }
}
?>
