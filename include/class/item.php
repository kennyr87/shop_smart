<?php
/**
 * Item class.
 * 
 * @see Base
 * @package ShopSmart
*/
class Item extends Base {
   
   /**
    * Item's id.
    * 
    * This is the food item's PLU or barcode.
    * 
    * @var string
   */
   public $item_id;
   
   /**
    * Item's company
    * 
    * @var string
   */
   public $company;
   
   /**
    * Item's name.
    * 
    * @var string
   */
   public $item_name;
   
   /**
    * Item's unit.
    * 
    * @var string
   */
   public $item_unit;
   
   /**
    * Size item is sold in.
    * 
    * @var string
   */
   public $item_size;
   
   /**
    * Category object that item belongs to.
    * 
    * @var object
   */
   public $cat = null;
  
   /**
    * Constructor.
    * 
    * @param int $item_id Optional. Item's ID.
   */
   public function __construct( $item_id = '') {
       if ( $item_id && is_string( $item_id )) {
           $item_data = self::get_item_data_by( 'id', $item_id );
       }
       
       if ( $item_data instanceof stdClass ) {
           $this->init( $item_data );
       }
   }
   
    /**
     * Initalize object's properties.
     * 
     * @param object MySQL result set.
     * 
     * @return void
    */
   public function init( stdClass $data ) {
       if ( $this->item_exists() ) {
           $this->flush_object();
       }
       
       $this->data = $data;
       $this->item_id = $data->item_id;
       $this->company = $data->company;
       $this->item_name = $data->item_name;
       $this->item_unit = $data->item_unit;
       $this->item_size = $data->item_size;
       
       if ( $cat_obj = Category::get_cat_by_id( $data->cat_id )) {
           $this->cat = $cat_obj;
       }
       
       if ( $store_obj = Store::get_store_by_id( $data->store_id )) {
           $this->store = $store_obj;
       }
   }
   
   /**
    * Get Item object by item's id.
    * 
    * @param string $item_id Item's id.
    * 
    * @return object|false Instance of Item, or false on failure.
   */
   static public function get_item_by_id( $item_id ) {
       if ( $item_data = self::get_item_data_by( 'id', $item_id )) {
           $item = new Item();
           $item->init( $item_data);
           return $item;
       } else {
           return false;
       }
   }
   
   /**
    * Get all items in certain category.
    * 
    * @param int $cat_id Category to match.
    * 
    * @param object[] Array of Item objects.
   */
   static public function get_items_by_cat( $cat_id ) {
        if (! $item_data = self::get_item_data_by( 'cat', $cat_id )) {
            return false;    
        }
       
        if ( is_array( $item_data )) {
            $return_array = array();
            
            foreach ( $item_data as $data ) {
                $item = new Item();
                $item->init( $data );
                array_push( $return_array, $item );
            }
            return $return_array;
        } else {
            return false;
        }
   }
   
   /**
    * Get item objects for all items.
    * 
    * @return object[] Array of item objects.
   */
   static public function get_all_items() {
        if (! $item_data = self::get_item_data_by() ) {
            return false;
        }
        
        if ( is_array( $item_data )) {
            $return_array = array();
            
            foreach ( $item_data as $data ) {
                $item = new Item();
                $item->init( $data );
                array_push( $return_array, $item );
            }
            return $return_array;
        } else {
            return false;
        }
   }
   
   /**
    * Get item's data.
    * 
    * Get data for all items by default.
    * 
    * @param string $db_field Optional. Field to query against: 'id', 'cat_id'.
    * @param string $value Optional.  Value to query against.
    * @param string $output Optional data type to return: 'OBJECT', 'ARRAY_A', or 'ARRAY_N'.
    * 
    * @return mixed|false Result object, array of result objects, or false on failure. 
   */
   static private function get_item_data_by( $db_field = '', $value = '', $output = 'OBJECT' ) {
       global $db;
       
       $command = "SELECT * FROM food_items";
       
       if ( empty( $db_field )) {
           $return_val = $db->get_results( $command, $output );
       } else {
           
           switch ( $db_field ) {
                case 'id':
                    $db_field = 'item_id';
                   break;
                case 'cat':
                    $db_field = 'cat_id';
                    break;
           }
           
           $command .= " WHERE $db_field = '%s';";
            
             
            if ( empty( $value )) {
               return false;
            } else {
               $value = strval( $value );
            }
            
            $query = $db->prepare( $command, $value );
            
            if ( $db_field === 'cat_id' ) {
                $return_val = $db->get_results ( $query, $output );
            } else {
                $return_val = $db->get_row( $query, $output );
            }
       }
       
       if ( $return_val ) {
           return $return_val;
       } else {
           return false;
       }
   }
   
   /**
    * Insert new item in database.
    * 
    * If item already exists, perform update operation.
    * 
    * @param array $item_data {
    *
    *       Optional. Array of item's data.
    *       
    *       @param string $item_id  Item's id.
    *       @param string $company Item's company.
    *       @param string $item_name Item's name.
    *       @param string $item_unit Item's unit.
    *       @param float $item_size  Size item is sold in.
    *       @param int $cat_id       Item's category.
    * } 
    * 
    * @return int|Error ID of new item or Error object on failure.
   */
   static public function insert_item ( $item_data = array() ) {
       global $db;
       
       if ( empty( $item_data )) {
           if (! empty( $_POST['item_id'] )) {
               $item_data['item_id'] = $_POST['item_id'];
           }
        
            if (! empty( $_POST['company'] )) {
                $item_data['company'] = $_POST['company'];
            }
            
            if (! empty( $_POST['item_name'] )) {
               $item_data['item_name'] = $_POST['item_name'];
            }
            
            if (! empty( $_POST['item_unit'] )) {
               $item_data['item_unit'] = $_POST['item_unit'];
            }
            
            if (! empty( $_POST['item_size'] )) {
               $item_data['item_size'] = $_POST['item_size'];
            }
            
            if (! empty( $_POST['cat_id'] )) {
               $item_data['cat_id'] = $_POST['cat_id'];
            }
        } 
        
        trim_values( $item_data );
        
        //Make sure item_id is upper case
        $item_data['item_id'] = strtoupper( $item_data['item_id'] );
        
        // Make sure category exists
        $cat_id = isset( $item_data['cat_id'] ) ? intval( $item_data['cat_id'] ) : 0;
        
        $cat_obj = Category::get_cat_by_id( $cat_id );
        
        if (! Category::is_cat( $cat_obj )) {
            return new Error( 'invalid_cat_id', 'Category does not exist.' );
        }
        
        // Merge old and new items if item already exists
        $item_id = isset( $item_data['item_id'] ) ? strval( $item_data['item_id'] ) : '';
        
        $old_item_data = self::get_item_data_by( 'id', $item_id );
        
        if ( $old_item_data instanceof stdClass ) {
            $old_item = get_object_vars( $old_item_data );
            $item_data = array_merge( $old_item, $item_data );
            $return_val = $item_data['ID'];
            $update = true;
        }
        
        // Validate user data
        
        $invalid_item_id = "[^-\d\040A-Z$%+\/.]";
        
        if ( empty( $item_data['item_id'] )) {
            return new Error( 'invalid_item_id', 'Cannot add item without item id.' );
        } else if ( preg_match( "/$invalid_item_id/", $item_data['item_id'] )) {
            return new Error( 'invalid_item_id', 'Item id must contain upper case letters, ' 
                            . 'numbers, spaces and the special characters ($ % + - . /).'); 
        } else if ( strlen( $item_data['item_id']) > 35 ) {
            return new Error( 'invalid_item_id', 'Item id must be 35 characters or less.' );
        }
        
        // RegEx for name format
        $invalid_name = "[^-'\040a-z.]";
        
        // Company is optional data
        if (! empty( $item_data['company'] )) {
            if ( preg_match( "/$invalid_name/i", $item_data['company'] )) {
                return new Error( 'invalid_company_name', 'Company name must contain hyphens, apostrophes, spaces or periods only.');
            } else if ( strlen( $item_data['company'] ) > 35 ) {
                return new Error( 'invalid_company_name', 'Company name must be 35 characters or less. ' );
            }
        }
        
        if ( empty( $item_data['item_name'] )) {
            return new Error( 'invalid_item_name', 'Cannot add item without item name.' );
        } else if ( preg_match( "/$invalid_name/i", $item_data['item_name'] )) {
            return new Error('invalid_item_name', 'Item name must contain hyphens, apostrophes, spaces or periods only.');
        } else if ( strlen( $item_data['item_name'] ) > 35 ) {
            return new Error( 'invalid_item_name', 'Item name must be 35 characters or less.' );
        }
        
        $valid_size = "^(?:\d{1,4}|\d{1,4}\.\d{1,2}|\.\d{1,2})$";
        
        if ( empty( $item_data['item_size'] )) {
            return new Error( 'invalid_item_size', 'Cannot add item without a size.' );
        } else if (! ( preg_match( "/$valid_size/", $item_data['item_size'] ))) {
            return new Error( 'invalid_item_size', 'Item size must be an up to four digit number, with two decimal places.' );
        }
        
        $valid_unit = "^(?:Each|OZ|g|LB|kg|FL OZ|L|GAL|PT|QT|ml)$";
        
        if ( empty( $item_data['item_unit'] )) {
            return new Error( 'invalid_item_unit', 'Cannot add item without unit.');
        } else if (! ( preg_match( "/$valid_unit/", $item_data['item_unit'] ))) {
            return new Error( 'invalid_item_unit', "Unit must be a valid unit.");
        }
        
        // Check to see if performing update operation
        if ( $update === true ) {
            $command = "UPDATE food_items SET company = '%s', item_name = '%s'," 
                     . " item_unit = '%s', item_size = %.2f, cat_id = %d WHERE item_id = '%s';";
            $query = $db->prepare(
                $command, 
                $item_data['company'],
                $item_data['item_name'], 
                $item_data['item_unit'], 
                $item_data['item_size'], 
                $item_data['cat_id'], 
                $item_data['item_id']
            );
        } else {
            $command = "INSERT INTO food_items VALUES (DEFAULT, '%s', '%s', '%s', '%s', %.2f, %d);";
            $query = $db->prepare(
                $command, 
                $item_data['item_id'],
                $item_data['company'],
                $item_data['item_name'], 
                $item_data['item_unit'], 
                $item_data['item_size'], 
                $item_data['cat_id']
            );
        }
        
        if ( $return_val_1 = $db->query( $query )) {
            $return_item_id = isset( $return_val ) ? $return_val : $return_val_1;
            return $return_item_id;
        } else {
            return new Error( 'invalid_query', 'Could not add item.' );
        }
   }
   
    /**
     * Gets price data for certain item.
     * 
     * @param string $db_field DB field to query against: 'city', 'state', 'zip'.
     * @param string $item_id ID of item.
     * @param string $value City, state, or zip to match.
     * @param $string $value Optional. If matching 'city', value of 'state' to match.
     * 
     * @return object[]|bool Array of result objects or false on failure.
    */
    static private function get_item_price_data_by( $db_field, $item_id, $value ) {
        global $db;
        
        if ( empty( $value )) {
            return false;
        }
        
        // Put values to match in array 

        $value = func_get_args();
        array_shift( $value );
        array_unshift( $value, $item_id );

        switch ( $db_field ) {
            case 'city':
                $db_field = 'city';
                break;
            case 'state':
                $db_field = 'state';
                break;
            case 'zip':
                $db_field = 'zip_code';
                break;
        }
        
        $command = <<<SQL
SELECT max_item.store_id, store_name, items.item_id, items.item_price, DATE_FORMAT(date, '%%b %%e, %%Y') AS date 
FROM (SELECT MAX(CI.item_date) AS date, carts.store_id FROM cart_items AS CI
JOIN carts ON CI.cart_id = carts.cart_id WHERE CI.item_id = '%s' GROUP BY carts.store_id) AS max_item
JOIN cart_items AS items ON items.item_date = max_item.date
JOIN markets AS M on M.store_id = max_item.store_id
JOIN street_address AS streets ON M.address_id = streets.address_id
WHERE items.item_id = '%s' AND $db_field = '%s'        
SQL;
        
        if ( $db_field === 'city' ) {
            $command .= " AND state = '%s';";
        }
        
        $query = $db->prepare( $command, $value );
        $result = $db->get_results( $query );
        
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Gets prices for item by city.
     * 
     * @param string $item_id Item's id.
     * @param string $city City of store's to search.
     * @param string $state State of store's to search.
     * 
     * @return object[]|false Array of result objects or false on failure.
    */
    static public function get_item_price_by_city ( $item_id, $city, $state ) {
        if (! $item_data = self::get_item_price_data_by( 'city', $item_id, $city, $state )) {
            return false;
        } else {
            return $item_data;
        }
    }
    
   /**
    * Check to see if item exists.
    * 
    * @return bool True if store exists, false otherwise.
   */
   public function item_exists() {
       return ! (empty( $this->item_id ));
   }
   
    /**
     * Check to see if value is real item.
     * 
     * @param mixed $value Value to check.
     * 
     * @return bool True if value is Item, false otherwise.
    */
    static public function is_item( $value ) {
        if (! $value instanceof Item ) {
            return false;
        }
        
        if ( $value->item_exists() ) {
            return true;
        } else {
            return false;
        }
    }
}
?>