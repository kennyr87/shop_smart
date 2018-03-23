<?php
/**
 * Cart class.
 * 
 * Handles adding and removing items from the cart.
 * 
 * @see Base
 * @package ShopSmart
*/
class Cart extends Base {
    
    /**
     * Cart id.
     * 
     * @var int
    */
    public $cart_id = 0;
    
    /**
     * Cart's name.
     * 
     * @var string
    */
    public $cart_name;
    
    /**
     * Stores the store object that cart is for.
     * 
     * @var object
    */
    public $store = null;
    
    /**
     * Stores items in cart.
     * 
     * Array of Item objects.
     * 
     * @var array
    */
    public $cart_items = array();
    
    /**
     * Constructor.
     * 
     * @param int $id Optional cart id.
    */
    public function __construct( $id = 0 ) {
        if ( $id && is_numeric($id) ) {
            $cart_data = self::get_cart_data_by( 'id', $id );
        }
        
        if ( $cart_data instanceof stdClass ) {
            $this->init( $cart_data );
        }
    }
    
    /**
     * Sets up cart properties.
     * 
     * @param object $data MySQL result set.
     * 
     * @return void
    */
    public function init( stdClass $data ) {
        
        if ( $this->cart_exists() ) {
            $this->flush_object();
        }
        
        $this->data = $data;
        $this->cart_id = $data->cart_id;
        $this->cart_name = $data->cart_name;
        
        if ( $store = Store::get_store_by_id( $data->store_id )) {
            $this->store = $store;
        }

        $cart_items = self::get_cart_item_data_by( 'cart', array( $this->cart_id ));
        
        if ( is_array($cart_items) ) {
            foreach ( $cart_items as $item ) {
                if ( $item instanceof stdClass ) {
                    $item_obj = new Item( $item->item_id );
                    $item_obj->item_count = $item->item_count;
                    $item_obj->item_price = $item->item_price;
                    array_push( $this->cart_items, $item_obj );
                }
            }
        }
    }
    
    /**
     * Get cart object by cart's id.
     * 
     * @param int $id Cart's id.
     * 
     * @return object|false Instance of Cart or false on error.
    */
    static public function get_cart_by_id( $id ) {
        if ( $cart_data = self::get_cart_data_by( 'id', $id )) {
            $cart = new Cart();
            $cart->init( $cart_data );
            return $cart;
        } else {
            return false;
        }
    }
    
    /**
     * Get member's cart for a particulare store.
     * 
     * @param int $store Store's id.
     * @param int $member Member's id.
     * 
     * @return object|false Instance of Cart or false on error.
    */
    static public function get_cart_by_store( $store, $member ) {
        if ( $cart_data = self::get_cart_data_by( 'store', $store, $member) ) {
            $cart = new Cart();
            $cart->init( $cart_data );
            return $cart;
        } else {
            return false;
        }
    }
    
    /**
     * Get carts for a particular member.
     * 
     * @param int $member Member's id.
     * 
     * @return array|false Array of Cart objects or false on failure.
    */
    static public function get_carts_by_member ( $member ) {
        if (! ($cart_data = self::get_cart_data_by( 'member', $member ))) {
            return false;
        }
        
        if ( is_array( $cart_data )) {
            $cart_array = array();
            foreach ( $cart_data as $data ) {
                $cart = new Cart();
                $cart->init( $data );
                array_push( $cart_array, $cart );
            }
            return $cart_array;
        } else {
            return false;
        }
    }
    
    /**
     * Get active cart data by particular field.
     * 
     * Gets all carts for a particular member when getting data by 'member'.
     * 
     * @param string $db_field DB field to query against: 'id' , 'member' or 'store'.
     * @param int $value Value or values to match.
     * @param array $value
     * 
     *      Optional. Value to match. {
     *      If matching 'store_id', must also provide a 'member_id'.
     * 
     *      @param int $store_id Store's id.
     *      @param int $member_id Member's id.
     * }
     * 
     * @return mixed|false Result object, array of result objects, or false on failure.
    */
    static private function get_cart_data_by( $db_field, $value ) {
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
        
        // Make sure id is integer
        if (! ( is_numeric( $value[0] ))) {
            return false;
        } else {
            $value[0] = intval( $value[0] );
        } 
        
        switch ( $db_field ) {
            case 'id':
                $db_field = 'cart_id';
                break;
            case 'store':
                $db_field = 'store_id';
                break;
            case 'member':
                $db_field = 'member_id';
                break;
            default:
                return false;
        }
        
        $command = "SELECT * FROM carts WHERE $db_field = %d";
        
        if ( $db_field === 'store_id' ) {
            if (! is_numeric( $value[1] )) {
                return false;
            } else {
                $value[1] = intval( $value[1] );
                $command .= " AND member_id = %d";                
            }
        }
        
        $command .= " AND date_deactivated <= 0;";
        
        if ( $db_field === 'member_id' ) {
            $result = $db->get_results( $db->prepare($command, $value ));
        } else {
            $result = $db->get_row( $db->prepare($command, $value ));
        }
        
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Get all cart data by particular field.
     * 
     * Gets all carts for a particular member when getting data by 'member'.
     * 
     * This gets deactivated carts also.
     * 
     * @param string $db_field DB field to query against: 'id' , 'member' or 'store'.
     * @param int $value Value or values to match.
     * @param array $value
     * 
     *      Optional. Value to match. {
     *      If matching 'store_id', must also provide a 'member_id'.
     * 
     *      @param int $store_id Store's id.
     *      @param int $member_id Member's id.
     * }
     * 
     * @return mixed|false Result object, array of result objects, or false on failure.
    */
    static private function get_all_cart_data_by( $db_field, $value ) {
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
        
        // Make sure id is integer
        if (! ( is_numeric( $value[0] ))) {
            return false;
        } else {
            $value[0] = intval( $value[0] );
        } 
        
        switch ( $db_field ) {
            case 'id':
                $db_field = 'cart_id';
                break;
            case 'store':
                $db_field = 'store_id';
                break;
            case 'member':
                $db_field = 'member_id';
                break;
            default:
                return false;
        }
        
        $command = "SELECT * FROM carts WHERE $db_field = %d";
        
        if ( $db_field === 'store_id' ) {
            if (! is_numeric( $value[1] )) {
                return false;
            } else {
                $value[1] = intval( $value[1] );
                $command .= " AND member_id = %d";                
            }
        }
        
        if ( $db_field === 'member_id' ) {
            $result = $db->get_results( $db->prepare($command, $value ));
        } else {
            $result = $db->get_row( $db->prepare($command, $value ));
        }
        
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Get data for items in a cart.
     * 
     * @param $db_field DB field to query against: 'cart' or 'item'.
     * @param $values Array of values to match. {
     * 
     *      @param int $cart_id Cart's id.
     *      @param string $item_id Optional.  Item's ID.
     * }
     * 
     * @param bool $active Flag to get only active items.
     * 
     * @return array|false Array of result objects, or false on failure.
    */
    static private function get_cart_item_data_by( $db_field, array $values, $active = true ) {
        global $db;
        
        if ( empty( $db_field )) {
            return false;
        }
        
        // Make sure id is integer
        if (! is_numeric ($values[0] )) {
            return false;
        } else {
            $values[0] = intval( $values[0] );
        }
        
        $command = <<<SQL
SELECT CI.* FROM cart_items AS CI 
JOIN food_items FI ON CI.item_id = FI.item_id
WHERE cart_id = %d        
SQL;

        if ( $db_field === 'item' ) {
            
            if (! is_string( $values[1] )) {
                return false;
            } else {
                $values[1] = strval( $values[1] );
                $command .= " AND CI.item_id = '%s'";
            }
        }
        
        if ( $active === true ) {
            $command .= " AND date_deactivated <= 0";
        }
        
        //order items by category then item name
        $command .= " ORDER BY cat_id, item_name DESC;";
        $query = $db->prepare( $command, $values );
        
        $result = $db->get_results( $query);
        
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Insert cart into carts table.
     * 
     * Performs update operation if cart already exists.
     * 
     * @param array $cart_info { 
     *      Optional array of data to update.
     * 
     *      @param int $cart_id Optional.  Cart's id.
     *      @param string $cart_name Cart's name.
     *      @param int $member_id Member's ID.
     *      @param int $store_id Store's ID.
     * }
     * 
     * @return int|Error Cart's ID or Error object if there was an error. 
    */
    static public function insert_cart( $cart_info = array() ) {
        global $db;
        
        // Check to see if data from form is needed
        if ( empty( $cart_info ) ) {

            if (! empty( $_POST['cart_name'] )) {
                $cart_info['cart_name'] = $_POST['cart_name'];
            }
            
            if (! empty( $_POST['member_id'] ) ) {
                $cart_info['member_id'] = $_POST['member_id'];
            }
            
            if (! empty( $_POST['store_id'] ) ) {
                $cart_info['store_id'] = $_POST['store_id'];
            }
            
            if (! empty( $_POST['cart_id'] )) {
                $cart_info['cart_id'] = $_POST['cart_id'];
            }
        }
        
        trim_values( $cart_info );
        
        // Make sure member exists
        $member_id = isset( $cart_info['member_id'] ) ? intval($cart_info['member_id']) : 0;
        
        if (! ( $member_id ) ) {
            return new Error( 'invalid_member_id', 'Invalid member ID.' );
        }
        
        $member = Member::get_member_by( 'id', $member_id );
        
        if (! $member->member_exists() ) {
            return new Error( 'invalid_member_id', 'Member does not exist.' );
        }
        
        // Make sure store exists
        $store_id = isset( $cart_info['store_id'] ) ? intval( $cart_info['store_id'] ): 0;
        
        if (! ( $store_id ) ) {
            return new Error( 'invalid_store_id', 'Invalid store ID.' );
        }
        
        $store = Store::get_store_by_id( $store_id );
        
        if (! $store->store_exists() ) {
            return new Error( 'invalid_store_id', 'Store does not exist.' );
        }
        
        // Get cart's original data
        $cart_id = isset( $cart_info['cart_id'] ) ? intval( $cart_info['cart_id'] ) : 0;
        
        if (! (empty( $cart_id )) && is_numeric( $cart_id )) {
            $old_cart_data = Cart::get_all_cart_data_by( 'id', $cart_id );
        } else {
            $old_cart_data = Cart::get_all_cart_data_by( 'store', $store_id, $member_id );
        }
        
        if ( $old_cart_data instanceof stdClass ) {
            $old_cart = get_object_vars( $old_cart_data );
            $cart_info = array_merge( $old_cart, $cart_info );
            $return_val = $cart_info['cart_id'];
            $update = true;
        }
        
        // RegEx for name format
        $invalid_name = "[^-'\040a-z.]";
        
        // Validate user data
        if ( empty( $cart_info['cart_name'] ) ) {
            return new Error('empty_cart_name', 'Cannot create cart without a cart name.');
        } else if (preg_match( "/$invalid_name/i", $cart_info['cart_name'] )) {
            return new Error('invalid_cart_name', 'Cart name must contain hyphens, apostrophes, spaces or periods only.');
        } else if ( strlen( $cart_name['name'] ) > 35 ) {
            return new Error('invalid_cart_name', 'Cart name must be 35 characters or less.');
        }
        
        if ( $update === true ) {
            $command = "UPDATE carts SET cart_name = '%s', date_deactivated = DEFAULT WHERE cart_id = %d;";
            $query = $db->prepare( $command, $cart_info['cart_name'], $cart_info['cart_id'] );
        } else {
            $command = "INSERT INTO carts VALUES (DEFAULT, '%s', %d, %d, DEFAULT);";
            $query = $db->prepare( $command, $cart_info['cart_name'], $cart_info['member_id'], $cart_info['store_id'] );
        }
        
        if ( $return_val_1 = $db->query( $query )) {
            $return_cart_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_cart_id;
        } else {
            return new Error( 'invalid_query', 'Could not create cart.' );
        }
    }
    
    /**
     * Check to see if cart exists
     * 
     * @return bool True if cart_id is set, false otherwise.
    */
    public function cart_exists() {
        return ! ( empty( $this->cart_id ));
    }
    
    /**
     * Check to see if value is real cart.
     * 
     * @param mixed $value Value to check.
     * 
     * @return bool True if value is Cart, false otherwise.
    */
    static public function is_cart( $value ) {
        if (! $value instanceof Cart ) {
            return false;
        }
        
        if ( $value->cart_exists() ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds deactivate flag to cart.
     * 
     * @return bool True on success, false otherwise.
    */
    public function delete_cart() {
        global $db;
        
        if (! $this->cart_exists() ) {
            return false;
        }
        
        $cart_id = $this->cart_id;
        
        $command = "UPDATE carts SET date_deactivated = NOW() WHERE cart_id = %d;";
        $result = $db->query( $db->prepare($command, $cart_id) );
        
        if ( $result ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Insert item in cart.
     * 
     * If item already exists, performs update operation.
     * 
     * @param array $item_data { 
     *      Optional.  Array of item's data.
     *      
     *      @param int $item_id ID of item.
     *      @param int $item_count Number of item's in cart.
     *      @param float $item_price Item's price.
     * }
     * 
     * @return int|Error ID of cart item or Error object on failure.
    */
    public function insert_cart_item( $item_data = array() ) {
        global $db; 
        
        if ( empty( $item_data )) {
            if (! empty( $_POST['item_id'] )) {
                $item_data['item_id'] = $_POST['item_id'];
            }
            
            if (! empty( $_POST['item_count'] )) {
                $item_data['item_count'] = $_POST['item_count'];
            }
            
           if (! empty( $_POST['item_price'] )) {
               $item_data['item_price'] = $_POST['item_price'];
           }
        }
        
        trim_values( $item_data );
        
        // Make sure item exists
        $item_id = isset( $item_data['item_id'] ) ? strval( $item_data['item_id'] ) : '';
        
        $item_obj = Item::get_item_by_id( $item_id );
        
        if (! is_item( $item_obj )) {
            return new Error('invalid_item_id', 'Item does not exist.');
        }
        
        //check if cart_item exists
        $old_item = self::get_cart_item_data_by( 'item', array( $this->cart_id, $item_id ), false );

        if ( $old_item[0] instanceof stdClass ) {
            $old_item_data = get_object_vars( $old_item[0] );
            $item_data = array_merge( $old_item_data, $item_data );
            $return_val = $item_data['ID'];
            $update = true;
        }
        
        // Validate user data
        
        if ( empty( $item_data['item_count'] )) {
            return new Error('empty_item_count', 'Need item quantity.');
        } else if (! is_numeric( $item_data['item_count'] )) {
            return new Error('invalid_item_count', 'Item count should be a number.');
        }
        
        $item_count = intval( $item_data['item_count'] );
        
        $valid_price = "^(?:\d{1,3})?(?:\.\d{1,2})?$";
        
        if ( empty( $item_data['item_price'] )) {
            return new Error( 'invalid_item_price', 'Cannot add item without a price.');
        } else if (! preg_match( "/$valid_price/", $item_data['item_price'] )) {
            return new Error( 'invalid_item_price', 'Item price must be an up to three digit number, with two decimal places.');
        }
        
        $item_price = floatval( $item_data['item_price'] );
        
        // Check to see if item already in cart
        
        if ( $update === true ) {
            $command = "UPDATE cart_items SET item_price = %.2f, item_count = %d, item_date = NOW(), date_deactivated = DEFAULT" 
                     . " WHERE cart_id = %d AND item_id = '%s';";
            $query = $db->prepare( $command, $item_price, $item_count, $this->cart_id, $item_id );
        } else {
            $command = "INSERT INTO cart_items VALUES (DEFAULT, %d, '%s', %.2f, %d, NOW(), DEFAULT);";
            $query = $db->prepare( $command, $this->cart_id, $item_id, $item_price, $item_count );
        }

        if ( $return_val_1 = $db->query( $query )) {
            $return_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_id;
        } else {
            return new Error('invalid_query', 'Could not add item to cart.');
        }
    }
    
    /**
     * Check to see if an item is in the cart.
     * 
     * @param int $item_id ID of item to check.
     * 
     * @return bool True of item is in the cart, false otherwise.
    */
    public function is_item_in_cart( $item_id ) {
        
        if ( is_numeric( $item_id )) {
            foreach ( $this->cart_items as $item ) {
                if ( $item_id == $item->item_id ) {
                    return $item;
                }
            }
        }
        return false;
    }
    
    /**
     * Adds deactivated flag to cart item.
     * 
     * @param int $item_id ID of item to remove.
     * 
     * @return bool True on success, false otherwise.
    */
    public function delete_cart_item( $item_id ) {
        global $db;
        
        $item_id = strval( $item_id );
        
        if (! $this->is_item_in_cart( $item_id )) {
            return false;
        }
        
        $command = "UPDATE cart_items SET date_deactivated = NOW() WHERE cart_id = %d AND item_id = '%s';";
        $query = $db->prepare($command, $this->cart_id, $item_id );

        if ( $db->query( $query )) {
            return true;
        } else {
            return false;
        }
    }
}
?>