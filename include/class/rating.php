<?php
/**
 * Food ratings class.
 * 
 * @see Base
 * @package ShopSmart
*/
class Rating extends Base {
    
    /**
     * Rating's id.
     * 
     * @var int
    */
    public $rating_id = 0;
    
    /**
     * Member that rated itm.
     * 
     * @var object
    */
    public $member;
    
    /**
     * Item being rated.
     * 
     * @var object
    */
    public $item;
    
    /**
     * Item's rating.
     * 
     * @var string
    */
    public $item_rating;
    
    /**
     * Constructor.
     * 
     * @param int $id Optional. ID of rating.
     * @param array $item_data {
     * 
     *      Optional.  Array of item data.
     * 
     *      @param int $member_id ID of member.
     *      @param int $item_id ID of item.
     * }
    */
    public function __construct( $id = 0, $item_data = array() ) {
        if ( (! empty( $id )) && is_numeric( $id )) {
            $rating_data = self::get_rating_data_by( 'id', $id );
        } else if ( (! empty( $item_data )) && is_array( $item_data )) {
            $rating_data = self::get_rating_data_by( 'member', $item_data );
        }
        
        if ( $rating_data instanceof stdClass ) {
            $this->init( $rating_data );
        }
    }
    
    /**
     * Initalize object's properties.
     * 
     * @param object $rating_data Result object.
     * 
     * @return void
    */
    public function init( stdClass $rating_data ) {
        if ( $this->rating_exists() ) {
            $this->flush_object();
        }
        
        $this->data = $rating_data;
        $this->rating_id = $rating_data->ID;
        $this->item_rating = $rating_data->item_rating;
        
        if ( $member_obj = Member::get_member_by( 'id', $rating_data->member_id )) {
            $this->member = $member_obj;
        }
        
        if ( $item_obj = Item::get_item_by_id( $rating_data->item_id )) {
            $this->item = $item_obj;
        }
    }
    
    /**
     * Get rating by rating's ID.
     * 
     * @param int $rating_id Rating's ID.
     * 
     * @return object|false Rating object, false on error.
    */ 
    public static function get_rating_by_id ( $rating_id ) {
        if ( $rating_data = self::get_rating_data_by( 'id', $rating_id )) {
            $rating = new Rating();
            $rating->init( $rating_data );
            return $rating;
        } else {
            return false;
        }
    }
    
    /**
     * Get rating by member's and item's ID.
     * 
     * Arguments to this function can be given as integers or array.
     * 
     * @param int $member_id Member's ID.
     * @param int $item_id Item's ID.
     * 
     * @param array $values {
     *      
     *      Array of values to query against.
     * 
     *      @param int $member_id Member's ID.
     *      @param int $item_id Item's ID.
     * }
     * 
     * @return object|false Rating object, false on error.
    */
    public static function get_rating_by_member_and_item( $values ) {
        // First get args
        $values = func_get_args();
        
        if ( is_array( $values[0] )) {
            $values = $values[0];
        }
        
        if ( $rating_data = self::get_rating_data_by( 'member', $values )) {
            $rating = new Rating();
            $rating->init( $rating_data);
            return $rating;
        } else {
            return false;
        }
    }
    
    /**
     * Get ratings by a particular field.
     * 
     * @param string $db_field: 'member', 'item'.
     * @param int $value Value to query against.
     * 
     * @return array|false Array of Rating objects or false on failure.
    */
    public static function get_ratings_by( $db_field, $value ) {
        if (! $rating_data = self::get_rating_data_by( $db_field, $value )) {
            return false;
        }
        
        if ( is_array( $rating_data )) {
            $return_array = array();
            
            foreach ( $rating_data as $data ) {
                $rating =  new Rating();
                $rating->init( $rating_data );
                array_push( $return_array, $rating );
            }
            return $return_array;
        } else {
            return false;
        }
    }
    
    /**
     * Get rating data by a particular field.
     * 
     * If only one argument provided for 'member' or 'item', then gets all ratings for that value.
     * 
     * @param string $db_field DB field to query against: 'id', 'member', 'item'.
     * @param int $rating_id Value to query against when searching by 'id'.
     * 
     * @param int $member_id Member's ID to query against when searching by 'member'.
     * @param int $item_id Optional. Item's ID to query against when seaching by 'member'.
     * 
     * @param int $item_id Item's ID to query against when searching by 'item'.
     * @param int $member_id Optional. Member's ID to query against when searching by 'item'.
     * 
     * @param array $values {
     * 
     *      Array of values to query against when searching by 'member'.
     * 
     *      @param int $member_id Member's ID.
     *      @param int $item_id Optional. Item's ID.
     * }
     * 
     * @param array $values {
     * 
     *      Array of values to query against when searching by 'item'.
     * 
     *      @param int $item_id Item's ID.
     *      @param int $member_id Optional. Member's ID.
     * }
     * 
     * @return mixed|false MySQL result set, array of result sets, or false on failure.
    */
    private static function get_rating_data_by( $db_field, $values ) {
        global $db;
        
        // First, get function args
        $values = func_get_args();
        array_shift( $values );
        
        if ( is_array( $values[0] )) {
            $values = $values[0];
        }
        
        // Make sure all values are numeric
        for ($num = count( $values), $i = 0; $num > $i; $i++ ) {
            if (! is_numeric( $values[$i] )) {
                return false;
            }
        }
        
        // Build query
        switch ( $db_field ) {
            case 'id':
                $db_field = 'ID';
                break;
            case 'member':
                $db_field = 'member_id';
                break;
            case 'item':
                $db_field = 'item_id';
                break;
            default:
                return false;
        }
        
        $command = "SELECT * FROM food_ratings WHERE $db_field = %d";
        
        if ( count( $values ) > 1) {
            if ( $db_field === 'member_id' ) {
                $db_field_2 = 'item_id';
            } else if ( $db_field === 'item_id' ) {
                $db_field_2 = 'member_id';
            } else {
                return false;
            }
            $command .= " AND $db_field_2 = %d;";
        }
        
        if ( $db_field === 'ID' || isset( $db_field_2) ) {
            $return_val = $db->get_row( $db->prepare( $command, $values ));
        } else {
            $return_val = $db->get_results( $db->prepare( $command, $values ));
        }
        
        if ( $return_val ) {
            return $return_val;
        } else {
            return false;
        }
    }
    
    /**
     * Insert item rating into DB.
     * 
     * Performs update operation if rating already exists.
     * 
     * @param array $rating_data {
     *
     *      Optional. Array of rating's data.
     *      
     *      @param int $rating_id Optional.  Rating's ID.
     *      @param int $member_id Member's ID.
     *      @param int $item_id Item's ID.
     *      @param string $item_rating Item's rating.
     * }
    */
    static public function insert_rating( $rating_data = array() ) {
        global $db;
        
        if ( empty( $rating_data )) {
            if (! empty( $_POST['rating_id'] )) {
                $rating_data['rating_id'] = $_POST['rating_id'];
            }
            
            if (! empty( $_POST['member_id'] )) {
                $rating_data['member_id'] = $_POST['member_id'];
            }
            
            if (! empty( $_POST['item_id'] )) {
                $rating_data['item_id'] = $_POST['item_id'];
            }
            
            if (! empty( $_POST['item_rating'] )) {
                $rating_data['item_rating'] = $_POST['item_rating'];
            }
        }
        
        trim_values( $rating_data );
        
        // Make sure member exists
        $member_id = isset( $rating_data['member_id'] ) ? intval( $rating_data['member_id'] ) : 0;
        
        $member_obj = Member::get_member_by( 'id', $member_id );
        
        if (! $member_obj->member_exists() ) {
            return new Error( 'invalid_member_id', 'Member does not exists.' );
        }
        
        // Make sure item exists
        $item_id = isset( $rating_data['item_id'] ) ? intval( $rating_data['item_id'] ) : 0;
        
        $item_obj = Item::get_item_by_id( $item_id );
        
        if (! $item_obj->item_exists() ) {
            return new Error( 'invalid_item_id', 'Item does not exist.' );
        }
        
        // Check to see if rating already exists
        $rating_id = isset( $rating_data['rating_id'] ) ? intval( $rating_data['rating_id'] ) : 0;
        
        if ( (! empty( $rating_id )) && is_numeric( $rating_id )) {
            $old_rating_data = self::get_rating_data_by( 'id', $rating_id );
        } else {
            $old_rating_data = self::get_rating_data_by( 'member', $member_id, $item_id );
        }
        
        if ( $old_rating_data instanceof stdClass ) {
            $old_rating = get_object_vars( $old_rating_data );
            $rating_data = array_merge( $old_rating, $rating_data );
            $return_val = $rating_data['ID'];
            $update = true;
        }
        
        // Validate user data
        $valid_ratings = "^(?:A|B|C|D)$";
        
        if ( empty( $rating_data['item_rating']) ) {
            return new Error( 'invalid_item_rating', 'Rating must have a item rating.' );
        } else if (! preg_match( "/$valid_ratings/i", $rating_data['item_rating']) ); {
            return new Error( 'invalid_item_rating', 'Rating must be A, B, C, or D.');
        }
        
        if ( $update === true ) {
            $command = "UPDATE food_ratings SET member_id = %d, item_id = %d, " 
                     . "item_rating = '%s' WHERE ID = %d";
            $query = $db->prepare(
                $command, 
                $member_id, 
                $item_id, 
                $rating_data['item_rating'], 
                $rating_data['rating_id'] 
            );
        } else {
            $command = "INSERT INTO food_ratings VALUES (DEFAULT, %d, %d, '%s');";
            $query = $db->prepare(
                $command, 
                $member_id, 
                $item_id, 
                $rating_data['item_rating']
            );
        }
        
        if ( $return_val_1 = $db->query( $query )) {
            $return_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_id;
        } else {
            return new Error( 'invalid_query', 'Could not add item rating.' );
        }
    }
    
    /**
     * Check to see if rating exists.
     * 
     * @return bool True if item exists, false otherwise.
    */
    public function rating_exists() {
        return ! ( empty( $this->rating_id ));
    }
}
?>